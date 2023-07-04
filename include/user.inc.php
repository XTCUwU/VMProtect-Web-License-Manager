<?php

class User
{
	public $id = 0;
	public $login = "";
	public $password = "";
	public $email = "";
	public $isadmin = FALSE;
	public $failures = 0;

	function User()
	{
	}
	
	public static function FromDb($id)
	{
		global $DB_PREFIX;
		$ret = new User();
		$ret->id = $id;
		if (ObjectSqlLoad($ret, "{$DB_PREFIX}users"))
			return $ret;
		else
			return FALSE;
	}
	
	public function GenPassword($length = 8, $chars = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789")
	{
		$string = "";
		$last_char = '';
		while (strlen($string) < $length)
		{
			$r = $chars[mt_rand(0, strlen($chars) - 1)];
			if ($r != $last_char)
			{
				$string .=  $r;
				$last_char = $r;
			}
		}
		$this->password = sha1($string);
		return $string;
	}
	
	public function Save()
	{
		global $DB_PREFIX;
		return ObjectSqlSave($this, "{$DB_PREFIX}users");
	}
}

?>