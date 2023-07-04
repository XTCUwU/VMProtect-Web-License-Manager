<?php
require_once "rsa.inc.php";
require_once "product.inc.php";
require_once "hwdata.inc.php";

function DecodeChars($str)
{
    if (preg_match_all("/\\\\x([0-9a-f]{2})/i", $str, $m))
    {
        for ($i = 0; $i < count($m[0]); $i++)
            $str = str_ireplace($m[0][$i], chr(hexdec($m[1][$i])), $str);
    }
    return $str;
}

function EncodeChars($str)
{
    $bytes = unpack("C*", $str);
    $ret = "";
    foreach ($bytes as $b)
        $ret .= sprintf("%02X ", $b);
    return $ret;
}

class License
{
	public $id = 0;
	public $productid = 0;
	public $activationid = NULL;
	public $name = "";
	public $email = "";
	public $createdate = "";
	public $orderref = "";
	public $comments = "";
	public $sn = "";
	public $snhash = "";
	public $blocked = FALSE;

	function License()
	{
		$this->createdate = date('Y-m-d');
	}
	
	public static function FromDb($id)
	{
		global $DB_PREFIX;
		$ret = new License();
		$ret->id = $id;
		if (ObjectSqlLoad($ret, "{$DB_PREFIX}licenses"))
			return $ret;
		else
			return FALSE;
	}
	
	private function PackSerialData($data)
	{
		$product = Product::FromDb($this->productid);
		
		$serial = array();
		// 1. version
		$serial[] = 1;
		$serial[] = 1;
		
		// 2. user name
		if (!empty($this->name))
		{
			$name = $this->name;
			if (strlen($name) > 255) die("User name is too long");
			$serial []= 2;
			$serial []= strlen($name);
			$serial []= $name;
		}
		
		// 3. e-mail
		if (!empty($this->email))
		{
			$email = $this->email;
			if (strlen($email) > 255) die("E-Mail is too long");
			$serial []= 3;
			$serial []= strlen($email);
			$serial []= $email;
		}
		
		// 4. hardwareid
		if (isset($data["hardwareid"]))
		{
			$hwid = base64_decode($data["hardwareid"]);
			$len = bin_strlen($hwid);
			if ($len == 0)
				die("HWID is empty");
			if ($len % 4 != 0) 
				die("Invalid HWID (not multiple of 4): ".$len);
			$serial []= 4;
			$serial []= $len;
			$serial []= $hwid;
		}
		
		// 5. date of expiration
		if (isset($data["expiredate"]))
		{
			list($y, $m, $d) = explode("-", $data["expiredate"]);
			$y = intval($y); $m = intval($m); $d = intval($d);
			if (!checkdate($m, $d, $y))
				die("Date of expiration is invalid: y=".$y." m=".$m." d=".$d);
			$serial[] = 5;
			$serial[] = $d;
			$serial[] = $m;
			$serial[] = $y % 256;
			$serial[] = intval($y / 256);
		}
		
		// 6. running time limit
		if (isset($data["timelimit"]))
		{
			$limit = $data["timelimit"];
			if ($limit < 0 || $limit > 255) 
				die("Running time limit is incorrect: ".$limit);
			$serial[] = 6;
			$serial[] = intval($limit);
		}
		
		// 7. product code
		$pc = base64_decode($product->product_code);
		$len = bin_strlen($pc);
		if ($len != 8) 
			die("Product code has invalid size: " . $len);
		$serial []= 7;
		$serial []= $pc;
		
		// 8. user data
		if (!empty($data["data"]))
		{
			$userdata = DecodeChars($data["data"]);
			$len = bin_strlen($userdata);
			if ($len > 255) 
				die("User data is too long: ".$len);
			$serial []= 8;
			$serial []= $len;
			$serial []= $userdata;
		}
		
		// 9. max build date
		if (isset($data["maxbuilddate"]))
		{
			list($y, $m, $d) = explode("-", $data["maxbuilddate"]);
			$y = intval($y); $m = intval($m); $d = intval($d);
			if (!checkdate($m, $d, $y)) 
				die("Date of max build is invalid: y=".$y." m=".$m." d=".$d);
			$serial[] = 9;
			$serial[] = $d;
			$serial[] = $m;
			$serial[] = $y % 256;
			$serial[] = intval($y / 256);
		}
		
		return $serial;
	}
	
	public function CreateSerialNumber($data)
	{		
		$p = Product::FromDb($this->productid);
		$serial = $this->PackSerialData($data);
		
		$serial_bin = "";
		foreach ($serial as $b)
			if (is_string($b))
				$serial_bin .= $b;
			else
				$serial_bin = $serial_bin . pack("C", $b); // convert to binary
		
		$hash = sha1($serial_bin, true);
		$serial_bin .= chr(255); // CRC chunk
		for ($i = 3; $i >= 0; $i--)
			$serial_bin .= $hash[$i]; // add CRC in reverse order (little endian)
		
		$padding_front = "\0\2";
		$size = mt_rand(8, 16);
		for ($i = 0; $i < $size; $i++)
			$padding_front .= chr(mt_rand(1, 255));
		$padding_front .= "\0";

		$content_size = bin_strlen($serial_bin) + bin_strlen($padding_front);
		$rest = $p->bits / 8 - $content_size;
		if ($rest < 0) 
			die("Content is too big to fit in key: ".$content_size.", maximal allowed is: ".($p->bits / 8));
		
		$padding_back = "";
		for ($i = 0; $i < $rest; $i++)
			$padding_back .= chr(mt_rand(0, 255));

		$serial_final = $padding_front . $serial_bin . $padding_back;

		$rsa = new RSA($p->bits);
		$rsa->public_exp = $p->public_exp;
		$rsa->private_exp = $p->private_exp;
		$rsa->modulus = $p->modulus;
		
		$sn_bin = $rsa->Encrypt($serial_final);
		$this->sn = base64_encode($sn_bin);
		
		return $serial_final;
	}

	public function UnpackSerialNumber()
	{
		$data = array();

		if ($this->sn == "")
			return $data;

		$p = Product::FromDb($this->productid);
		if ($p === FALSE)
			return "Can't find product.";

		$rsa = new RSA($p->bits);
		$rsa->public_exp = $p->public_exp;
		$rsa->private_exp = $p->private_exp;
		$rsa->modulus = $p->modulus;
		$sn = $rsa->Decrypt(base64_decode($this->sn));
		$sn_len = bin_strlen($sn);

		//skip front padding until \0
		$i = 1;
		while ($i < $sn_len && ord($sn[$i]) != 0)
			$i++;
		if ($i == $sn_len)
			return "Serial number parsing error";
		$start = ++$i;
		$end = 0;

		while ($i < $sn_len && $end == 0)
		switch (ord($sn[$i++]))
		{
			// 1. version
			case 1:
				$data["version"] = ord($sn[$i++]);
				break;
			// 2. user name
			case 2:
				$lenght = ord($sn[$i++]);
				$data["name"] = substr($sn, $i, $lenght);
				$i += $lenght;
				break;
			// 3. e-mail
			case 3:
				$lenght = ord($sn[$i++]);
				$data["email"] = substr($sn, $i, $lenght);
				$i += $lenght;
				break;
			// 4. hardwareid
			case 4:
				$lenght = ord($sn[$i++]);
				$data["hardwareid"] = base64_encode(bin_substr($sn, $i, $lenght));
				$i += $lenght;
				break;
			// 5. date of expiration
			case 5:
				$date = mktime(0, 0, 0, ord($sn[$i+1]), ord($sn[$i]), ord($sn[$i+2]) + ord($sn[$i+3])*256);
				$data["expiredate"] = date("Y-m-d", $date);
				$i += 4;
				break;
			// 6. running time limit
			case 6:
				$data["timelimit"] = ord($sn[$i++]);
				break;
			// 7. product code
			case 7:
				$data["product_code"] = base64_encode(bin_substr($sn, $i, 8));
				$i += 8;
				break;
			// 8. user data
			case 8:
				$lenght = ord($sn[$i++]);
				$data["data"] = bin_substr($sn, $i, $lenght);
				$i += $lenght;
				break;
			// 9. max build date
			case 9:
				$date = mktime(0, 0, 0, ord($sn[$i+1]), ord($sn[$i]), ord($sn[$i+2]) + ord($sn[$i+3])*256);
				$data["maxbuilddate"] = date("Y-m-d", $date);
				$i += 4;
				break;
			case 255:
				$end = $i - 1;
				break;
			default:
				return "Serial number parsing error";
		}

		if ($end == 0 || $sn_len - $end < 4)
			return "Serial number CRC error";

		$hash = sha1(bin_substr($sn, $start, $end - $start), TRUE);
		$hash = strrev(bin_substr($hash, 0, 4));
		$hash2 = bin_substr($sn, $end + 1, 4);
		if (strcmp($hash, $hash2) != 0)
			return "Serial number CRC error";

		return $data;
	}

	public function SaveHwdata()
	{
		$sn_data = $this->UnpackSerialNumber();
		if (empty($sn_data["hardwareid"]))
			return;
		$hwid = $sn_data["hardwareid"];
		$devices = Hwdata::HwidDecode($hwid);
		if (is_array($devices))
			foreach ($devices as $device)
				{
					$h = new Hwdata();
					$h->licenseid = $this->id;
					$h->type = $device & 3;
					$h->value = $device;
					$h->Save();
				}
	}

	public function Save()
	{
		global $DB_PREFIX;
		$isNew = $this->id == 0;
		if ($this->snhash == "" && $this->sn != "")
			$this->snhash = base64_encode(sha1(base64_decode($this->sn), TRUE));
		$res = ObjectSqlSave($this, "{$DB_PREFIX}licenses");
		if ($res && $isNew)
			$this->SaveHwdata();
		return $res;
	}
}

?>