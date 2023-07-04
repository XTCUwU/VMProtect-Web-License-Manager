<?php
require_once "include/login.inc.php";
require_once "include/user.inc.php";
RequireAdmin();

if (isset($_REQUEST["p"]))
	$obj = User::FromDb($_REQUEST["p"]);
else 
if (isset($_REQUEST["id"]))
	$obj = User::FromDb($_REQUEST["id"]);

if (!isset($obj) || $obj===FALSE)
	$obj = new User();
	
if ($_SERVER["REQUEST_METHOD"]=="POST")
{
	$obj->login = $_POST["login"];
	$obj->email = $_POST["email"];
	
	$ret = DbQuery("SELECT * FROM {$DB_PREFIX}users WHERE id!=" . $obj->id . " AND login=" . Sql($obj->login));
	if ($ret)
		die("addError('#login', '" . U_ELOGIN_TXT . "');");
		
	$ret = DbQuery("SELECT * FROM {$DB_PREFIX}users WHERE id!=" . $obj->id . " AND email=" . Sql($obj->email));
	if ($ret)
		die("addError('#email', '" . U_EEMAIL_TXT . "');");
	
	$obj->isadmin = ($_POST["isadmin"]=="1");

	if (!empty($_POST["password"]))
		$obj->password = sha1($_POST["password"]);
	
	if (!$obj->Save())
		die("alert('" . V_ERROR_TXT . ": " . str_replace("'", "\\'", mysqli_error($mysqli_link)) . "');");
		
	echo "loadcontent('user');";
	exit;
}
	
//GET
$title = ($obj->id==0 ? M_NEWUSER_TXT : H_EDITUSER_TXT);

?>

<h1><?php echo $title; ?></h1>
<div class="formDiv">
<form id="editForm" action="user_edit.php" method="POST">
	<input type="hidden" id="id" name="id" value="<?php echo $obj->id; ?>" />
	<table class="formTbl">
		<tr><th>
			<label for="login"><?php echo U_UN_TXT; ?></label>
		</th><td>
			<input type="text" name="login" id="login" class="required" value="<?php echo htmlspecialchars($obj->login); ?>" />
		</td></tr>
		<tr><th>
			<label for="email"><?php echo U_EMAIL_TXT; ?></label>
		</th><td>
			<input type="text" name="email" id="email" class="required email" value="<?php echo htmlspecialchars($obj->email); ?>" />
		</td></tr>
		<tr><th>
			<label for="password"><?php echo U_PASS_TXT; ?></label>
		</th><td>
			<input type="password" name="password" id="password" value="" />
		</td></tr>
		<tr><th>
			<label for="password2"><?php echo U_PASS2_TXT; ?></label>
		</th><td>
			<input type="password" name="password2" id="password2" value="" />
		</td></tr>
		<tr><th>
			<label for="isadmin"><?php echo U_ROLE_TXT; ?></label>
		</th><td>
			<select id="isadmin" name="isadmin">
				<option value="1"><?php echo U_ADMIN_TXT; ?></option>
				<option value="0"><?php echo U_MAN_TXT; ?></option>
			</select>
		</td></tr>
		<tr>
			<td></td>
			<td style="padding-top:10px">
                <a class="greenBtn" onclick="return saveForm('script')"><span><?php echo SAVE_TXT; ?></span><em></em></a>
			</td>
		</tr>
	</table>
</form>
</div>

<script>
	var isNew = <?php echo ($obj->id==0?"true":"false"); ?>;
	
	function customValidate(){
		if (isNew)
			$('#password').each(validateRequired);
		validateEqual('password', 'password2');
	}
	
	function initContent(){
		if (!isNew)
			$('.noedit').attr('disabled', 'disabled');
		$('#isadmin').val('<?php echo ($obj->isadmin?"1":"0"); ?>');
	}
</script>
