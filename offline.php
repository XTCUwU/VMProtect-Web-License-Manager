<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php
require_once "include/lang.inc.php";

//type=activation&code=M3J3-LD2E-BSXY&hwid=dMRvvI1X2iBKxUe1VqnawYuv5Mo%3D&hash=bdjWHUHAAOjQXp2jIyaewWFUmqU%3D

$success = "";
$error = "";
$url = "";

if (!empty($_POST["url"])) {
	require_once "include/dbopen.inc.php";
	require_once "include/activation.inc.php";

	$url = $_POST["url"];
	$args = explode("&", base64_decode($url));

	foreach ($args as $arg)
	{
		$a = explode("=", $arg);
		if (count($a) == 2)
			$$a[0] = urldecode($a[1]);
	}

	if (empty($type))
		$error = OFF_ESTRING_TXT;
	elseif ($type == "activation")
	{
		if (empty($code) || empty($hwid) || empty($hash))
			$error = OFF_ESTRING_TXT;
		else
			$res = Activation::Activate($code, $hwid, $hash);
	}
	elseif ($type == "deactivation")
	{
		if (empty($hash))
			$error = OFF_ESTRING_TXT;
		else
			$res = Activation::Deactivate($hash);
	}
	else
		$error = OFF_ESTRING_TXT;

	if (isset($res))
		switch ($res)
		{
			case $ACT_BAD:
				$error = OFF_ECODE_TXT;
				break;
			case $ACT_USED:
				$error = OFF_ELIMIT_TXT;
				break;
			case $ACT_BANNED:
				$error = OFF_EBLOCKED_TXT;
				break;
			case $ACT_EXPIRED:
				$error = OFF_EEXPIRED_TXT;
				break;
			case $DEACT_ERROR:
				$error = OFF_ESN_TXT;
				break;
			case $DEACT_UNKNOWN:
				$error = OFF_ESN_TXT;
				break;
			case $DEACT_OK:
				$success = OFF_DEACT_TXT;
				break;
			default:
				$sn = wordwrap($res, 70, "\n", TRUE);
		}
}
if (!isset($sn)) {	?>
<head>
	<title>VMProtect Web License Manager</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="css/style.css" type="text/css"/>
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/jquery-ui.js"></script>
	<script type="text/javascript" src="js/jquery.form.js"></script>
	<script type="text/javascript" src="js/validation.php"></script>
	<script>
		var error = '<?php echo $error; ?>';
		var success = '<?php echo $success; ?>';

		function saveForm() {
			if (validateForm()) {
				$('form').submit();
			}
			return false;
		}

		$(function () {
			$('.autofocus').focus();
			if (error != '')
				addError('#url', error);
			if (success != '')
				$('.greenMsg').text(success).show();
		});
	</script>
</head>
<body>
<div id="offlineContentDiv">
	<div id="offlineFormDiv">
		<form method="post">
			<table id="loginFormTblCenter" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td style="vertical-align: center;">
						<table id="offlineFormTbl" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td>
									<h1><?php echo OFF_STRING_TXT; ?></h1>
								</td>
							</tr>
							<tr>
								<td>
									<textarea class="required autofocus" name="url" id="url" rows="5" cols="70"><?php echo htmlspecialchars($url); ?></textarea>
								</td>
							</tr>
							<tr>
								<td style="padding-top:10px">
                                    <a class="greenBtn" onclick="return saveForm()"><span><?php echo OFF_SUBMIT_TXT; ?></span><em></em></a>
									<span class="greenMsg"></span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<div id="loginFooterDiv">
<?php
    define("FOOTER_HIDE_COPYRIGHT", TRUE);
    include_once "footer.inc.php";
?>
</div>
</body>
<?php } else { ?>
<head>
	<title>VMProtect Web License Manager</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="css/style.css" type="text/css"/>
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/jquery-ui.js"></script>
	<script type="text/javascript" src="js/ZeroClipboard.js"></script>
	<script>
		$(function () {
			if (window.clipboardData == undefined)
			{
				ZeroClipboard.setMoviePath('js/ZeroClipboard.swf');
				var clip = new ZeroClipboard.Client();
				clip.setHandCursor(false);
				clip.addEventListener( 'mouseDown', function(client){
					client.setText($('#snText').text().replace(/\n/, ''));
					$('.greenMsg').show().fadeOut(2000);
				});
				clip.glue('copyBtn', 'zeroHolder');
			}
			else
			{
				//for Internet Explorer
				$('#copyBtn').click(function(){
					window.clipboardData.setData('Text', $('#snText').text().replace(/\n/, ''));
					$('.greenMsg').show().fadeOut(2000);
				});
			}
		});
	</script>
</head>
<body>
<div id="offlineContentDiv">
	<div id="offlineFormDiv">
		<table id="loginFormTblCenter" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td style="vertical-align: center;">
					<table id="offlineFormTbl" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td>
								<h1><?php echo OFF_SN_TXT; ?></h1>
							</td>
						</tr>
						<tr>
							<td>
								<textarea id="snText" readonly="readonly" rows="5" cols="70"><?php echo $sn; ?></textarea>
							</td>
						</tr>
						<tr>
							<td style="padding-top:10px">
								<div id="zeroHolder">
									<a id="copyBtn" class="greenBtn"><span><?php echo COPYCPB_TXT; ?></span><em></em></a>
								</div>
								<span class="greenMsg"><?php echo OFF_COPIED_TXT; ?></span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>
<div id="loginFooterDiv">
<?php
    define("FOOTER_HIDE_COPYRIGHT", TRUE);
    include_once "footer.inc.php";
?>
</div>
</body>
<?php } ?>
</html>
