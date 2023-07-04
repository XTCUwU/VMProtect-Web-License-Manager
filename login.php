<?php
require_once "include/dbopen.inc.php";
require_once "include/user.inc.php";
require_once "include/lang.inc.php";
session_start();

if (isset($_SESSION["cur_user"]))
	header("Location: index.php");

include_once "securimage/securimage.php";
$securimage = new Securimage();

if ($_SERVER["REQUEST_METHOD"]=="POST")
{
	$sql = "SELECT * FROM {$DB_PREFIX}users WHERE login=" . Sql($_POST["login"]);
	$u = GetObjectBySql($sql, "User");

	//Check captcha
	if (!empty($_POST["captcha_code"]))
	{
		if ($securimage->check($_POST["captcha_code"]) == FALSE)
		{
			echo "reloadCaptcha();";
			echo "addError('#captcha_code', '" . CAPTCHA_ERROR_TXT . "');";
			exit;
		}
	}

	//Login not found
	if ($u === FALSE)
	{
		echo "addError('#password', '" . LOGIN_ERROR_TXT . "');";
	}
	else
	//Wrong passsword
	if ($u->password != sha1($_POST["password"]))
	{
		$u->failures++;
		$u->Save();
		echo "addError('#password', '" . LOGIN_ERROR_TXT . "');";
		if ($u->failures > 3)
			echo "showCaptcha();";
	}
	//No errors
	else
	{
		if ($u->failures != 0)
		{
			$u->failures = 0;
			$u->Save();
		}
		$_SESSION["cur_user"] = $u;
		echo "window.location = 'index.php';";
	}
	exit;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>VMProtect Web License Manager</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" type="text/css"/>
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/jquery-ui.js"></script>
	<script type="text/javascript" src="js/jquery.form.js"></script>
    <script type="text/javascript" src="js/validation.php"></script>
	<script>
		function reloadCaptcha(){
			$('#captcha').attr('src', 'securimage/securimage_show.php?' + Math.random());
			$('#captcha_code').val('');
			clearError('#captcha_code');
			return false;
		}

		function showCaptcha(){
			reloadCaptcha();
			$('#captcha_code').removeAttr('disabled');
			$('#captchaRow').show();
			return false;
		}

		function saveForm(){
			if (validateForm())
			{
				$('form').ajaxSubmit({
					type: 'POST',
					dataType: 'script',
					success: function(data){
					},
					error: function(xhr, ajaxOptions, thrownError){
						alert(xhr.responseText);
					}
				});
			}
			return false;
		}
		
		$(function(){
			$('.autofocus').focus();
		});
	</script>
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
		<div id="loginFormDiv">
			<form action="login.php" method="post" onsubmit="return saveForm()">
				<table id="loginFormTblCenter" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td style="vertical-align: center;">
							<table id="loginFormTbl" cellpadding="0" cellspacing="0" border="0">
								<tr><td></td><td><h1><?php echo LOGIN_HEADER_TXT; ?></h1></td></tr>
								<tr><th width="50px">
									<label for="login"><?php echo USERNAME_TXT; ?></label>
								</th><td>
									<input type="text" name="login" id="login" class="required autofocus" />
								</td></tr>
								<tr><th>
									<label for="password"><?php echo PASSWORD_TXT; ?></label>
								</th><td>
									<input type="password" name="password" id="password" class="required" />
								</td></tr>
								<tr id="captchaRow" class="hidden">
									<th>
                                        <?php echo CAPTCHA_TXT; ?>
                                    </th>
									<td>
										<img id="captcha" src="" alt="CAPTCHA Image" />
                                        <a id="captchaReload" href="#" onclick="return reloadCaptcha()"><?php echo CAPTCHA_ANOTHER_TXT; ?></a>
										<input disabled="disabled" class="required" type="text" name="captcha_code" id="captcha_code" size="10" maxlength="6" />
									</td>
								</tr>
								<tr>
									<td></td>
									<td style="padding-top:10px">
                                        <input type="submit" style="display: none;"/>
                                        <a class="greenBtn" onclick="return saveForm()"><span><?php echo LOGIN_BTN_TXT; ?></span><em></em></a>
										&nbsp;
										<a href="forgot.php" style="font-weight: bold"><?php echo FORGOT_TXT; ?></a>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</form>
        </div>			
	</div>
	<div id="loginFooterDiv"><?php include "footer.inc.php"; ?></div>
</body>
</html>