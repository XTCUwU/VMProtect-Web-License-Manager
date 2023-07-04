<?php
require_once "include/login.inc.php";
require_once "include/activation.inc.php";
require_once "include/product.inc.php";

if (isset($_REQUEST["p"]))
	$obj = Activation::FromDb($_REQUEST["p"]);
else 
if (isset($_REQUEST["id"]))
	$obj = Activation::FromDb($_REQUEST["id"]);

if (!isset($obj) || $obj===FALSE)
	$obj = new Activation();

$products = DbQuery("SELECT id, fullname, active  FROM {$DB_PREFIX}vw_products ORDER BY fullname");
    
if ($_SERVER["REQUEST_METHOD"]=="POST")
{
	if (empty($_POST["blocked"]))
        $_POST["blocked"] = FALSE;
	if ($_POST["act_count"] == "unlimited")
	    $_POST["act_count"] = "";

    //Protect of changing code
    unset($_POST["code"]);

    ObjectFromArray($obj, $_POST);
	$obj->Save() or die("alert('" . V_ERROR_TXT . ": '" . str_replace("'", "\\'", mysqli_error($mysqli_link)) . ");");
	echo "loadcontent('activations');";
}
else
{
	if ($obj->id==0)
		echo "<h1>" . H_NEWACT_TXT ."</h1>";
	else
		echo "<h1>" . H_EDITACT_TXT . "</h1>";
?>

<div class="formDiv">
<form id="editForm" action="activations_edit.php" method="POST">
	<input type="hidden" id="id" name="id" value="<?php echo $obj->id; ?>" />
	<table class="formTbl">
		<tr><th>
			<label for="productid"><?php echo LIC_PROD_TXT; ?></label>
		</th><td>
			<select name="productid" id="productid" class="required noedit">
				<?php if (!empty($products)) foreach ($products as $p) { ?>
				<option value="<?php echo $p["id"]; ?>" <?php echo (!$p["active"]?"disabled":""); ?>><?php echo htmlspecialchars($p["fullname"]); ?></option>
				<?php } ?>
			</select>
		</td></tr>
		<tr><th>
			<label for="name"><?php echo LIC_NAME_TXT; ?></label>
		</th><td>
			<input type="text" name="name" id="name" class="required" value="<?php echo htmlspecialchars($obj->name); ?>" />
		</td></tr>
		<tr><th>
			<label for="email"><?php echo LIC_EMAIL_TXT; ?></label>
		</th><td>
			<input type="text" name="email" id="email" class="required email" value="<?php echo htmlspecialchars($obj->email); ?>" />
		</td></tr>
		<tr><th>
			<label for="createdate"><?php echo LIC_DATE_TXT; ?></label>
		</th><td>
			<input type="text" name="createdate" id="createdate" class="date" value="<?php echo $obj->createdate; ?>" />
		</td></tr>
		<tr><th>
			<label for="orderref"><?php echo LIC_ORDERREF_TXT; ?></label>
		</th><td>
			<input type="text" name="orderref" id="orderref" value="<?php echo htmlspecialchars($obj->orderref); ?>" />
		</td></tr>
		<tr><th>
			<label for="act_count"><?php echo ACT_COUNT_TXT; ?></label>
		</th><td>
			<input type="text" name="act_count" id="act_count" value="<?php echo htmlspecialchars($obj->act_count); ?>" />
		</td></tr>
        <tr><th>
            <label for="act_count"><?php echo ACT_EXPDATE_TXT; ?></label>
        </th><td>
            <input type="text" name="expiredate" id="expiredate" class="date" value="<?php echo $obj->expiredate; ?>" />
        </td></tr>
		<?php if ($obj->code!="") { ?>
		<tr><th>
			<label for="code"><?php echo ACT_CODE_TXT; ?></label>
		</th><td>
			<input type="text" name="code" id="code" readonly="readonly" value="<?php echo htmlspecialchars($obj->code); ?>" />
		</td></tr>
		<?php } ?>
		<?php if ($obj->id!=0) { ?>
		<tr><th>
			<label for="blocked"><?php echo ACT_BLOCKED_TXT; ?></label>
		</th><td>
			<input type="checkbox" id="blocked" name="blocked" class="checkbox" value="1" />
		</td></tr>
		<?php } ?>
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
		if ($('#act_count').val() != '' && $('#act_count').val() != 'unlimited')
			$('#act_count').each(validateNumeric);
	}

	function initContent(){
		$('.date').datepicker({ dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true });
		if (!isNew){
			$('.noedit').attr('disabled', 'disabled');
			$('#blocked').prop('checked', <?php echo ($obj->blocked ? "true" : "false"); ?>);
		}
		$('#productid').val('<?php echo $obj->productid; ?>');

		$('#act_count').focus(function(){
			if ($(this).val() == 'unlimited')
				$(this).removeClass('emptyText').val('');
		});
		$('#act_count').blur(function(){
			if ($(this).val() == '')
				$(this).val('unlimited').addClass('emptyText');
		});
		$('#act_count').trigger('blur');
	}
</script>

<?php
}
?>