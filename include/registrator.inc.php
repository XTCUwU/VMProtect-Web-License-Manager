<?php
require_once "dbopen.inc.php";

class Registrator
{
	public $id = 0;
	public $name = "";
    public $authmode = 0;
	public $ipranges = "";
    public $login = "";
    public $password = "";
	public $active = TRUE;
	
	public static function FromDb($id)
	{
		global $DB_PREFIX;
		$ret = new Registrator();
		$ret->id = $id;
		if (ObjectSqlLoad($ret, "{$DB_PREFIX}registrators"))
			return $ret;
		else
			return FALSE;
	}
	
	public static function CheckAccess()
	{
		global $DB_PREFIX;

        //If PHP auth variables are set, search for any matching registrator
        if (isset($_SERVER["PHP_AUTH_USER"]) && isset($_SERVER["PHP_AUTH_PW"]))
        {
            $rs = ObjectsSqlLoad("SELECT * FROM {$DB_PREFIX}registrators WHERE active=1 AND authmode=1 AND login=" .
                Sql($_SERVER["PHP_AUTH_USER"]) . " AND password=" . Sql($_SERVER["PHP_AUTH_PW"]), "Registrator");
            return $rs !== FALSE;
        }

        //Check IP
        $allowed = FALSE;
        $ip = $_SERVER["REMOTE_ADDR"];
		$ip_numbers = explode(".", $ip);
		//Operate with IPs as base 256 numbers
		$ip = 	$ip_numbers[0] * 256 * 256 * 256 +
				$ip_numbers[1] * 256 * 256 +
				$ip_numbers[2] * 256 +
				$ip_numbers[3];

        $rs = ObjectsSqlLoad("SELECT * FROM {$DB_PREFIX}registrators WHERE authmode=0 AND active=1", "Registrator");
        if ($rs === FALSE)
            return FALSE;

		// For each registrator
		foreach ($rs as $r)
		{
			$ipranges = explode(",", $r->ipranges);
			// For each IP range in registrator
			foreach ($ipranges as $iprange)
			{
				preg_match('/^(\d+)\.(\d+)\.(\d+)\.(\d+)/', $iprange, $start);
				$startip = 	$start[1] * 256 * 256 * 256 +
							$start[2] * 256 * 256 +
							$start[3] * 256 +
							$start[4];
				
				if (preg_match('/-(\d+)\.(\d+)\.(\d+)\.(\d+)$/', $iprange, $end))
					$endip = $end[1] * 256 * 256 * 256 +
							$end[2] * 256 * 256 +
							$end[3] * 256 +
							$end[4];
				else
					$endip = $startip;

				if ($ip < $startip || $ip > $endip)
					continue;

				$allowed = TRUE;
				break;
			}

			if ($allowed)
				break;
		}
		return $allowed;
	}
	
	public function Save()
	{
		global $DB_PREFIX;
		return ObjectSqlSave($this, "{$DB_PREFIX}registrators");
	}
}


?>