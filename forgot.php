<?php
require_once "include/dbopen.inc.php";
require_once "include/lang.inc.php";
require_once "include/user.inc.php";
session_start();

if (isset($_SESSION["cur_user"]))
	header("Location: index.php");

$email = "";
	
if ($_SERVER["REQUEST_METHOD"]=="POST")
{
	if (!empty($_POST["email"]))
		$email = $_POST["email"];
	$res = ObjectsSqlLoad("SELECT * FROM {$DB_PREFIX}users WHERE email=" . Sql($email), "User");
	
	if ($res == FALSE)
		echo "showError('" . RP_USERNOTFOUND_TXT . "')";
	else
	{
		$u = $res[0];
		$newpass = $u->GenPassword();
		
		if (@mail($u->email, "Your new password", "Your new password is: " . $newpass))
		{
			$u->Save();
			echo "alert('" . RP_SENT_TXT . "');window.location = 'login.php';";
		}
		else
			echo "showError('" . RP_ERROR_TXT . "')";
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
		
		function showError(str){
			$('#email').after('<label class="error">' + str + '</label>');
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
			<form method="post">
				<table id="loginFormTblCenter" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td style="vertical-align: center;">
							<table id="loginFormTbl" cellpadding="0" cellspacing="0" border="0">
								<tr><td></td><td><h1><?php echo RP_RETPASS_TXT; ?></h1></td></tr>
								<tr><th width="50px">
									<label for="email"><?php echo RP_EMAIL_TXT; ?></label>
								</th><td>
									<input type="text" name="email" id="email" class="required email autofocus" value="<?php echo $email; ?>" />
								</td></tr>
								<tr>
									<td></td>
									<td style="padding-top:10px">
										<a class="greenBtn" onclick="return saveForm()"><span><?php echo RP_SEND_TXT; ?></span><em></em></a>&nbsp;
										<button class="cancel2Btn" onclick="window.location = 'login.php';return false;"><?php echo CANCEL_TXT; ?></button>
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