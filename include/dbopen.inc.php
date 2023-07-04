<?php

if (empty($DB_SERVER) || empty($DB_LOGIN) || empty($DB_PASSWORD) || empty($DB_NAME))
{
	if (!file_exists("include/config.inc.php"))
		header("Location: install.php");
	require_once "config.inc.php";
}

if (!isset($VERSION))
	require_once "version.inc.php";

function Sql($value)
{
	global $mysqli_link;
	$ret = "";
	if (is_string($value))
	{
		if ($value == "")
			$ret = "NULL";
		else
		{
			if (get_magic_quotes_gpc())
				$value = stripslashes($value);
			$ret = "'" . mysqli_real_escape_string($mysqli_link, $value) . "'";
		}
	}
	else
	if (is_null($value))
		$ret = "NULL";
	else
	if (is_numeric($value))
		$ret = strval($value);
	else
	if (is_bool($value))
		$ret = $value ? "TRUE" : "FALSE";
	else
	if (is_array($value))
	{
		foreach ($value as $k => $v)
			$value[$k] =  Sql($v);
		$value = implode(",", $value);
		if (get_magic_quotes_gpc())
			$value = stripslashes($value);
		$ret = $value;
	}
	else
		$ret = "'Unsupported field type'";
	return $ret;
}

function ObjectSqlInsert($obj, $tablename = "")
{
	global $mysqli_link;
	if ($tablename == "")
		$tablename = strtolower(get_class($obj));
	$fields = array();
	$values = array();
	foreach ($obj as $key => $value)
	{
		if ($key == "id" && $value == 0)
			continue;
		$fields []= $key;
		$values []= Sql($value);
	}
	$sql = "INSERT INTO " . $tablename . " (" . implode(",", $fields) . ") VALUES (" . implode(",", $values) . ")";
	$ret = mysqli_query($mysqli_link, $sql);
	if ($ret)
		$obj->id = mysqli_insert_id($mysqli_link);
	return $ret;
}

function ObjectSqlUpdate($obj, $tablename = "")
{
	global $mysqli_link;
	if ($tablename == "")
		$tablename = strtolower(get_class($obj));
	$fields = array();
	foreach ($obj as $key => $value)
	{
		if ($key == "id")
			continue;
		$fields []= $key . "=" . Sql($value);
	}
	$sql = "UPDATE " . $tablename . " SET " . implode(",", $fields) . " WHERE id=" . Sql($obj->id);
 
	$ret = mysqli_query($mysqli_link, $sql);
	return $ret;
}

function ObjectSqlSave($obj, $tablename = "")
{
	if ($obj->id == 0)
		return ObjectSqlInsert($obj, $tablename);
	else
		return ObjectSqlUpdate($obj, $tablename);
}

function ObjectFromArray($obj, $row)
{
	foreach ($obj as $key => $value)
		if (array_key_exists($key, $row))
		{
			if (is_bool($obj->$key))
				$obj->$key = $row[$key]=="1";
			else
			if (is_array($obj->$key) && is_string($row[$key]))
				$obj->$key = explode(",", $row[$key]);
			else
			if (is_string($obj->$key) && is_array($row[$key]))
				$obj->$key = implode(",", $row[$key]);
			else
			if (is_string($obj->$key) && is_string($row[$key]))
				$obj->$key = stripslashes($row[$key]);
			else
				$obj->$key = $row[$key];
		}
}

function ObjectSqlLoad($obj, $tablename = "")
{
	global $mysqli_link;
	if ($tablename == "")
		$tablename = strtolower(get_class($obj));
	if ($obj->id == 0)
		return;
	$res = mysqli_query($mysqli_link, "SELECT * FROM " . $tablename . " WHERE id=" . Sql($obj->id));
	if ($res != FALSE && mysqli_num_rows($res) == 1)
	{
		$row = mysqli_fetch_assoc($res);
		ObjectFromArray($obj, $row);
		mysqli_free_result($res);
		return TRUE;
	}
	else
		return FALSE;
}

function ObjectsSqlLoad($sql, $classname)
{
	global $mysqli_link;
	$res = mysqli_query($mysqli_link, $sql);
	if ($res != FALSE && mysqli_num_rows($res) > 0)
	{
		$ret = array();
		while (NULL != ($row = mysqli_fetch_assoc($res)))
		{
			$obj = new $classname();
			ObjectFromArray($obj, $row);
			$ret []= $obj;
		}
		return $ret;
	}
	else
		return FALSE;
}

//Return object from first(!) row, returned by query.
function GetObjectBySql($sql, $classname)
{
	global $mysqli_link;
	$res = mysqli_query($mysqli_link, $sql);
	if ($res != FALSE && mysqli_num_rows($res) > 0)
	{
		$row = mysqli_fetch_assoc($res);
		$obj = new $classname();
		ObjectFromArray($obj, $row);
		return $obj;
	}
	else
		return FALSE;
}

function DbQuery($sql, $pk = "")
{
	global $mysqli_link;
	$res = mysqli_query($mysqli_link, $sql);

	//for INSERT, UPDATE, DELETE mysql_query returns TRUE or FALSE
	if ($res===TRUE || $res===FALSE)
		return $res;

	//handle
	if ($res)
	{
		//single value query
		if (mysqli_num_fields($res)==1 && mysqli_num_rows($res)==1)
		{
			$tmp_for_5_3 = mysqli_fetch_array($res);
			return $tmp_for_5_3[0];
		}

		//empty result
		if (mysqli_num_rows($res)==0)
			return FALSE;

		$ret = array();
		while (NULL != ($row = mysqli_fetch_assoc($res)))
			if (is_array($row) && count($row)>0)
			{
				if ($pk == "")
					$ret []= $row;
				else
					$ret[$row[$pk]] = $row;
			}
		mysqli_free_result($res);
		return $ret;
	}
	return FALSE;
}

$mysqli_link = @mysqli_connect($DB_SERVER, $DB_LOGIN, $DB_PASSWORD) or die("Database connect error: " . mysqli_connect_error());

if (!mysqli_select_db($mysqli_link, $DB_NAME))
{
?>
<html>
<body>
Can't open database '<?php echo $DB_NAME; ?>'.<br/>
<a href="install.php">Create new</a>
</body>
</html>
<?php
}
mysqli_query($mysqli_link, "SET NAMES 'utf8'");
?>