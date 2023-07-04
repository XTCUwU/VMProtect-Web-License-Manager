<?php
define("INSTALLATION", TRUE);
require_once "include/tz.inc.php";
require_once "include/version.inc.php";
require_once "include/lang.inc.php";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>VMProtect Web License Manager</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" type="text/css"/>
	<link rel="stylesheet" href="css/jquery-ui.css" type="text/css"/>
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/jquery-ui.js"></script>
	<script type="text/javascript" src="js/validation.php"></script>
</head>
<body>
	<div id="loginHeaderDiv">
		<table style="width:100%; height:100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td id="logoDiv"><a href="http://www.vmpsoft.com/" target="_blank" title="Visit site"><img id="logo" src="images/logo.png" /></a></td>
				<td width="20px"></td>
				<td id="loginHeaderDiv2" style="vertical-align: center;"><img style="display:block" alt="Web License Manager" src="images/header.png" title="<?php echo VERSION_TXT; ?> <?php echo $VERSION; ?>" /></td>
			</tr>
		</table>
	</div>
	<div id="loginContentDiv">
		<div id="installFormDiv">
			<table id="installFormTblCenter" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td style="vertical-align: center;">
<?php

$success = FALSE;
$exists = FALSE;

$DB_SERVER = "";
$DB_LOGIN = "";
$DB_PASSWORD = "";
$DB_NAME = "";
$DB_PREFIX = "vmp_";
@include_once "include/config.inc.php";
$DB_PASSWORD = "";

if (!isset($_REQUEST["step"]) || $_REQUEST["step"]=="1")
{
	//Check PHP modules
	$modules = array();
	$success = TRUE;

	$modules["Mysqli"] = extension_loaded("Mysqli");
	$modules["DOM"] = extension_loaded("DOM");
	$modules["GMP / BCMath"] = extension_loaded("GMP") || extension_loaded("BCMath");

	$table = "";
	foreach ($modules as $name => $exists)
	{
        $table .= "<tr><th>{$name}</th><td>";
		if ($exists)
			$table .= "<p style=\"color:green\">" . I_OK_TXT . "</p>";
		else
		{
			$table .= "<p style=\"color:red\">" . I_FAILED_TXT . "</p>";
			$success = FALSE;
			$error = I_EMODULE_TXT;
		}
		$table .= "</td></tr>";
	}

	//Check PHP settings
	if (ini_get("register_globals")=="1")
	{
		$success = FALSE;
		$error = I_EGLOBALS_TXT;
	}
?>

<h1><?php echo I_MODULES_TXT; ?></h1>
<table id="modulesTbl">
<?php echo $table; ?>
<tr>
<?php if ($success) { ?>
	<td></td>
	<td style="padding-top:10px">
		<button class="saveBtn" onclick="location = '?step=2';"><?php echo I_CONTINUE_TXT; ?></button>
	</td>
<?php } else { ?>
	<td colspan="2" class="error2">
		<?php echo $error; ?>
	</td>	
<?php } ?>
</tr>
</table>

<?php 
}
else
if ($_REQUEST["step"]=="2")
{
	if ($_SERVER["REQUEST_METHOD"]=="POST")
	{
		$DB_SERVER = $_POST["server"];
		$DB_LOGIN = $_POST["login"];
		$DB_PASSWORD = $_POST["password"];
		$DB_NAME = $_POST["dbname"];
		$DB_PREFIX = $_POST["prefix"];
        
		$mysqli_link = @mysqli_connect($DB_SERVER, $DB_LOGIN, $DB_PASSWORD) or $error = mysqli_connect_error();
		if ($mysqli_link)
			@mysqli_select_db($mysqli_link, $DB_NAME) or $error = mysqli_error($mysqli_link);
            
		$success = !isset($error);
            
		if ($success)
		{
			$res = mysqli_query($mysqli_link, "SHOW TABLES LIKE '{$DB_PREFIX}licenses'") or $error = mysqli_error($mysqli_link);
			$exists = ($res != FALSE) && (mysqli_num_rows($res) > 0);
		}
	}
?>
<h1><?php echo I_DATABASE_TXT; ?></h1>

<?php if (isset($error)) { ?>
<p style="color:red"><?php echo $error; ?></p>
<?php } ?>

<form onsubmit="return validateForm()" method="post" <?php if ($success) echo "action=\"?step=3\""; ?>>
    <table id="loginFormTbl">
		<tr><th width="100px">
			<label for="server"><?php echo I_DBSERVER_TXT; ?></label>
		</th><td>
			<input type="text" name="server" id="server" class="required" value="<?php echo $DB_SERVER; ?>" />
		</td></tr>
		<tr><th>
			<label for="dbname"><?php echo I_DBNAME_TXT; ?></label>
		</th><td>
			<input type="text" name="dbname" id="dbname" class="required" value="<?php echo $DB_NAME; ?>" />
		</td></tr>
		<tr><th>
			<label for="login"><?php echo I_DBUSER_TXT; ?></label>
		</th><td>
			<input type="text" name="login" id="login" class="required" value="<?php echo $DB_LOGIN; ?>" />
		</td></tr>
        <tr><th>
			<label for="password"><?php echo I_DBPASS_TXT; ?></label>
		</th><td>
			<input type="text" name="password" id="password" class="required" value="<?php echo $DB_PASSWORD; ?>" />
		</td></tr>
        <tr><th>
			<label for="prefix"><?php echo I_DBPREFIX_TXT; ?></label>
		</th><td>
			<input type="text" name="prefix" id="prefix" value="<?php echo $DB_PREFIX; ?>" />
		</td></tr>
		<tr>
<?php if ($success) { ?>
		<?php if ($exists) { ?>
			<td colspan="2" style="text-align:center">
				<br/><b><?php echo I_DBEXISTS_TXT; ?></b><br/><br/>
				<button name="action" value="update" class="copyBtn"><?php echo I_UPDATE_TXT; ?></button>&nbsp;&nbsp;&nbsp;
				<button name="action" value="install" class="copyBtn"><?php echo I_INSTALL_TXT; ?></button>
			</td>
		<?php } else { ?>
			<td></td>
			<td style="padding-top:10px">
				<button name="action" value="install" class="copyBtn"><?php echo I_INSTALL_TXT; ?></button>
			</td>
		<?php } ?>
<?php } else { ?>
			<td></td>
			<td style="padding-top:10px">
				<button class="saveBtn"><?php echo I_CONNECT_TXT; ?></button>
			</td>
<?php } ?>
		</tr>
	</table>
</form>
<?php
}
else
if ($_REQUEST["step"]=="3")
{	
	if (preg_match("/^install/i", $_POST["action"]))
		$dbaction = "create";
	elseif (preg_match("/^update/i", $_POST["action"]))
		$dbaction = "update";
?>
<h1><?php echo I_ADMIN_TXT; ?></h1>
<?php if (isset($error)) { ?>
<p style="color:red"><?php echo $error; ?></p>
<?php } ?>
<form onsubmit="return validateForm()" method="post" action="?step=4"><div>
	<input type="hidden" name="dbserver" value="<?php echo $_POST["server"]; ?>" />
	<input type="hidden" name="dblogin" value="<?php echo $_POST["login"]; ?>" />
	<input type="hidden" name="dbpassword" value="<?php echo $_POST["password"]; ?>" />
	<input type="hidden" name="dbname" value="<?php echo $_POST["dbname"]; ?>" />
	<input type="hidden" name="dbprefix" value="<?php echo $_POST["prefix"]; ?>" />
	<input type="hidden" name="dbaction" value="<?php echo $dbaction; ?>" />
	<table id="loginFormTbl">
		<tr><th width="100px">
			<label for="login"><?php echo U_UN_TXT; ?></label>
		</th><td>
			<input type="text" name="login" id="login" class="required" value="" />
		</td></tr>
		<tr><th>
			<label for="email"><?php echo U_EMAIL_TXT; ?></label>
		</th><td>
			<input type="text" name="email" id="email" class="required email" value="" />
		</td></tr>
		<tr><th>
			<label for="password"><?php echo U_PASS_TXT; ?></label>
		</th><td>
			<input type="password" name="password" id="password" class="required" value="" />
		</td></tr>
		<tr><th>
			<label for="password2"><?php echo U_PASS2_TXT; ?></label>
		</th><td>
			<input type="password" name="password2" id="password2" class="required" value="" />
		</td></tr>
		<tr>
			<td></td>
			<td style="padding-top:10px">
				<button class="saveBtn"><?php echo I_CREATE_TXT; ?></button>
			</td>
		</tr>
	</table>
</div></form>
<script>
function customValidate(){
	validateEqual('password', 'password2');
}
</script>
<?php
}
else
if ($_REQUEST["step"]=="4")
{	
	$DB_SERVER = $_POST["dbserver"];
	$DB_LOGIN = $_POST["dblogin"];
	$DB_PASSWORD = $_POST["dbpassword"];
	$DB_NAME = $_POST["dbname"];
	$DB_PREFIX = $_POST["dbprefix"];
	$dbaction = $_POST["dbaction"];
	
	$mysqli_link = @mysqli_connect($DB_SERVER, $DB_LOGIN, $DB_PASSWORD) or $error = mysqli_connect_error();
	if ($mysqli_link)
		@mysqli_select_db($mysqli_link, $DB_NAME) or $error = mysqli_error($mysqli_link);
	require "include/{$dbaction}.inc.php";
	
	$adm_login = $_POST["login"];
	$adm_pass = sha1($_POST["password"]);
	$adm_email = $_POST["email"];
	$sql = "INSERT INTO {$DB_PREFIX}users (login, password, email, isadmin) VALUES ('{$adm_login}', '{$adm_pass}', '{$adm_email}', TRUE) ON DUPLICATE KEY UPDATE password='{$adm_pass}', isadmin=TRUE";
	mysqli_query($mysqli_link, $sql) or $error = mysqli_error($mysqli_link);
	
	$config = <<<EOT
<?php
require_once dirname(__FILE__) . "/tz.inc.php";
if (!defined("INSTALLATION") && file_exists("install.php"))
	die("Installaton was not completed. Please delete install.php file.");
\$DB_SERVER = "$DB_SERVER";
\$DB_LOGIN = "$DB_LOGIN";
\$DB_PASSWORD = "$DB_PASSWORD";
\$DB_NAME = "$DB_NAME";
\$DB_PREFIX = "$DB_PREFIX";
?>
EOT;
    
$f = @fopen("include/config.inc.php", "w") or $error = I_ERROR_TXT;
if ($f)
{
	fwrite($f, $config);
	fclose($f);
}
?>
<h1><?php echo I_SAVING_TXT; ?></h1>
<?php if (isset($error)) { ?>
<p style="color:red"><?php echo $error; ?></p><br/><br/>
<textarea id="id" rows="15" style="width:100%">
<?php echo $config; ?>
</textarea>
<?php } else {
    echo I_SUCCESS_TXT;
}
} ?>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div id="loginFooterDiv"><?php include "footer.inc.php"; ?></div>
</body>
</html>

