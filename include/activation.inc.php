<?php
require_once "rsa.inc.php";
require_once "product.inc.php";
require_once "license.inc.php";

//Activation error codes
$ACT_BAD = "BAD";
$ACT_BANNED = "BANNED";
$ACT_USED = "USED";
$ACT_EXPIRED = "EXPIRED";
$DEACT_OK = "OK";
$DEACT_ERROR = "ERROR";
$DEACT_UNKNOWN = "UNKNOWN";

class Activation
{
	public $id = 0;
	public $productid = 0;
	public $name = "";
	public $email = "";
	public $orderref = "";
	public $code = "";
	public $act_count = 1;
	public $expiredate = "";
	public $blocked = FALSE;
	public $createdate = "";

	function Activation()
	{
		$this->createdate = date('Y-m-d');
	}
	
	public static function FromDb($id)
	{
		global $DB_PREFIX;
		$ret = new Activation();
		$ret->id = $id;
		if (ObjectSqlLoad($ret, "{$DB_PREFIX}activations"))
			return $ret;
		else
			return FALSE;
	}

	public static function Activate($code, $hwid, $hash)
	{
		global $DB_PREFIX;
		global $ACT_BAD, $ACT_BANNED, $ACT_USED, $ACT_EXPIRED;

		if (empty($code) || empty($hwid) || empty($hash))
			return $ACT_BAD;

		$res = ObjectsSqlLoad("SELECT * FROM {$DB_PREFIX}activations WHERE code=" . Sql($code), "Activation");
		if ($res === FALSE)
			return $ACT_BAD;

		$act = $res[0];
		if ($act->blocked)
			return $ACT_BANNED;

		//Check that activation code matches product
		$p = Product::FromDb($act->productid);
		$snattrs = json_decode($p->snattrs, TRUE);

		if ($hash != base64_encode(sha1(base64_decode($p->modulus), TRUE)))
			return $ACT_BAD;

		if (isset($snattrs["hardwareid"]) && $snattrs["hardwareid"] == "none")
		{
			//Product has no HWID
			$lics = ObjectsSqlLoad("SELECT * FROM {$DB_PREFIX}licenses l WHERE l.blocked='0' AND l.activationid=" . Sql($act->id) .
				" AND NOT EXISTS(SELECT * FROM {$DB_PREFIX}hwdata h WHERE h.licenseid=l.id) ORDER BY id LIMIT 1", "License");
			if (is_array($lics) && count($lics) > 0)
				return $lics[0]->sn;
		} 
		else 
		{
			$hwdata = Hwdata::HwidDecode($hwid);
			if (is_array($hwdata) && count($hwdata) > 0)
			{
				$sql = "SELECT licenseid, SUM(value) as c FROM " .
					"(SELECT DISTINCT h.licenseid, h.type, CASE h.type WHEN 0 THEN 10 ELSE 1 END AS value FROM " .
					"{$DB_PREFIX}hwdata h LEFT JOIN {$DB_PREFIX}licenses l ON h.licenseid=l.id WHERE " .
					"l.activationid=" . Sql($act->id) . " AND l.blocked='0' AND h.value IN (" . Sql($hwdata) . ")) as t " .
					"GROUP BY t.licenseid HAVING c>=12 ORDER BY c DESC LIMIT 1";
				$res = DbQuery($sql);
				if ($res)
				{
					$l = License::FromDb($res[0]["licenseid"]);
					if ($l)
						return $l->sn;
				}
			} 
			else 
				return $ACT_BAD; //Invalid format
		}

		//If activation has expiration date
		if ($act->expiredate != NULL)
		{
			$exp_date = new DateTime($act->expiredate);
			$now = new DateTime();
			if ($exp_date < $now)
				return $ACT_EXPIRED;
		}

		//If activation count is limited
		if ($act->act_count != NULL)
		{
			$activated = DbQuery("SELECT COUNT(*) FROM {$DB_PREFIX}licenses WHERE blocked='0' AND activationid=" . Sql($act->id));
			if ($activated >= $act->act_count)
				return $ACT_USED;
		}

		$sn_data = array("hardwareid" => $hwid);
		$sn_data = $p->ApplySnAttrs($sn_data, $act->createdate);

		//Create new license
		$l = new License();
		$l->name = $act->name;
		$l->email = $act->email;
		$l->orderref = $act->orderref;
		$l->productid = $act->productid;
		$l->activationid = $act->id;
		$l->CreateSerialNumber($sn_data);
		$l->Save();
		return $l->sn;
	}

	public static function Deactivate($snhash)
	{
		global $DB_PREFIX, $DEACT_ERROR, $DEACT_UNKNOWN, $DEACT_OK;
		if (empty($snhash))
			return $DEACT_ERROR;
		
		$res = ObjectsSqlLoad("SELECT * FROM {$DB_PREFIX}licenses WHERE snhash=" . Sql($snhash), "License");
		if ($res === FALSE)
			return $DEACT_UNKNOWN;

		$l = $res[0];
		$l->blocked = TRUE;
		$l->Save();
		return $DEACT_OK;
	}

	public function Save()
	{
		global $DB_PREFIX;
		
		if ($this->code == "")
		{
			//Create new activation code here
			$alphabet = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
			$alphabet_length = strlen($alphabet) - 1;
			$p = Product::FromDb($this->productid);
			do
			{
				$code = $p->act_pattern;
				for ($i = 0; $i < strlen($code); $i++)
					if ($code[$i] == '#')
						$code[$i] = $alphabet[mt_rand(0, $alphabet_length)];
			}
			while (DbQuery("SELECT * FROM {$DB_PREFIX}activations WHERE code=" . Sql($code) . " LIMIT 1") !== FALSE);
			$this->code = $code;
		}
		
		return ObjectSqlSave($this, "{$DB_PREFIX}activations");
	}
}

?>