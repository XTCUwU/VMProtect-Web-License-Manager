<?php
require_once "rsa.inc.php";

class Product
{
	public $id = 0;
	public $parentid = "";
	public $name = "";
	public $algorithm = "RSA";
	public $bits = 2048;
	public $active = TRUE;
	//crypt keys
	public $product_code = "";
	public $public_exp = "";
	public $private_exp = "";
	public $modulus = "";
	//activation params
	public $uses_act = FALSE;
	public $snattrs = "";
	public $act_pattern = "####-####-####";
	public $act_extracount = 0;

	function Product()
	{
	}

	//Нестандартная ситуация, загружаем из вьюхи, сохраняем в таблицу
	public static function FromDb($id)
	{
		global $DB_PREFIX;
		$ret = new Product();
		$ret->id = $id;
		if (ObjectSqlLoad($ret, "{$DB_PREFIX}vw_products"))
			return $ret;
		else
			return FALSE;
	}
	
	private function GenerateProductCode()
	{
		//base64 of 8-byte random number
		$code = pack("LL", mt_rand(), mt_rand());
		$this->product_code = base64_encode($code);
	}
	
	private function GenerateKeys()
	{
		$rsa = new RSA($this->bits);
		$rsa->GenerateKeys();
		$this->public_exp = $rsa->public_exp;
		$this->private_exp = $rsa->private_exp;
		$this->modulus = $rsa->modulus;
	}

	public function IsMode()
	{
		return $this->parentid != "";
	}

	// $create_date format: "Y-m-d"
	public function ApplySnAttrs($sn_data, $create_date)
	{
		if ($this->IsMode() && $this->snattrs != "")
		{
			$sn_defaults = json_decode($this->snattrs, TRUE);

			// Если значение = fromurl, не трогаем его (оно уже должно быть задано в кейгене или активаторе)
			if (isset($sn_defaults["hardwareid"]))
			{
				if ($sn_defaults["hardwareid"] == "none")
					unset($sn_data["hardwareid"]);
				elseif ($sn_defaults["hardwareid"] != "fromurl")
					$sn_data["hardwareid"] = $sn_defaults["hardwareid"];
			}

			if (isset($sn_defaults["timelimit"]))
				$sn_data["timelimit"] = $sn_defaults["timelimit"];
			
			if (isset($sn_defaults["data"]))
				$sn_data["data"] = $sn_defaults["data"];

			// Поля expiredate и maxbuilddate могут быть заданы датой, или числом дней от активации (сегодня)
			if (isset($sn_defaults["expiredate"]))
			{
				if (is_numeric($sn_defaults["expiredate"]))
				{
					$dt = new DateTime();
					$dt->modify("+" . intval($sn_defaults["expiredate"]) . " days");
					$sn_data["expiredate"] = $dt->format("Y-m-d");
				}
				else
					$sn_data["expiredate"] = $sn_defaults["expiredate"];
			}
			else
			//или числом дней от создания кода активации
			if (isset($sn_defaults["expiredate_code"]) && is_numeric($sn_defaults["expiredate_code"]))
			{
				$dt = new DateTime($create_date);
				$dt->modify("+" . intval($sn_defaults["expiredate_code"]) . " days");
				$sn_data["expiredate"] = $dt->format("Y-m-d");
			}

			if (isset($sn_defaults["maxbuilddate"]))
			{
				if (is_numeric($sn_defaults["maxbuilddate"]))
				{
					$dt = new DateTime();
					$dt->modify("+" . intval($sn_defaults["maxbuilddate"]) . " days");
					$sn_data["maxbuilddate"] = $dt->format("Y-m-d");
				}
				else
					$sn_data["maxbuilddate"] = $sn_defaults["maxbuilddate"];
			}
			else
			if (isset($sn_defaults["maxbuilddate_code"]) && is_numeric($sn_defaults["maxbuilddate_code"]))
			{
				$dt = new DateTime($create_date);
				$dt->modify("+" . intval($sn_defaults["maxbuilddate_code"]) . " days");
				$sn_data["maxbuilddate"] = $dt->format("Y-m-d");
			}
		}
		return $sn_data;
	}

	public function Save()
	{
		global $DB_PREFIX;
		if (!$this->IsMode())
		{
			//Create keys
			if ($this->product_code == "")
				$this->GenerateProductCode();
			if ($this->public_exp == "")
				$this->GenerateKeys();
		}
		else
		{
			//Don't save keys for modes
			$this->product_code = "";
			$this->public_exp = "";
			$this->private_exp = "";
			$this->modulus = "";
		}
		return ObjectSqlSave($this, "{$DB_PREFIX}products");
	}
}

?>