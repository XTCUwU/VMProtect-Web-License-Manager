<?php

class Hwdata
{
	public $id = 0;
	public $licenseid = 0;
	public $type = 0;
	public $value = 0;

	function Hwdata()
	{
	}

	public function Save()
	{
		global $DB_PREFIX;
		return ObjectSqlSave($this, "{$DB_PREFIX}hwdata");
	}

	public static function HwidEncode($hwdata)
	{
		$str = "";
		foreach ($hwdata as $device)
			$str .= pack("V", $device);
		return base64_encode($str);
	}

	public static function HwidDecode($hwid)
	{
		if (empty($hwid))
			return FALSE;
		$data = unpack("V*", base64_decode($hwid));
        //x64 system PHP bug workaround, to keep numbers unsigned
        foreach ($data as $i => $num)
        {
            if ($data[$i] < 0)
                $data[$i] += 4294967296;
        }
        return $data;
	}
}

?>