<?php
require_once "dbopen.inc.php";
require_once "lang.inc.php";
require_once "user.inc.php";
session_start();

$cur_user = null;

function RequireUser($redirect = FALSE)
{
	global $cur_user;
	$ret = isset($_SESSION["cur_user"]);
	if (!$ret && $redirect)
		echo "<script>window.location = 'login.php';</script>";
	else 
	if ($ret)
		$cur_user = $_SESSION["cur_user"];
	else
		die("Access denied.");
	return $ret;
}

function RequireAdmin()
{
	global $cur_user;
	if (RequireUser())
	if ($cur_user->isadmin == FALSE)
		die("Administrative rights required.");
}

RequireUser(TRUE);

?>