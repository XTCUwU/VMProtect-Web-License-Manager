<?php
require_once "include/login.inc.php";
require_once "include/product.inc.php";
RequireAdmin();

$sn_fields = array("hardwareid", "expiredate", "expiredate_code", "timelimit", "maxbuilddate", "maxbuilddate_code", "data");
$sn_data = array();

if (isset($_REQUEST["p"]))
	$p = Product::FromDb($_REQUEST["p"]);
else 
if (isset($_REQUEST["id"]))
	$p = Product::FromDb($_REQUEST["id"]);

if (!isset($p) || $p===FALSE)
    if (isset($_REQUEST["parentid"]))
	    $p = Product::FromDb($_REQUEST["parentid"]);

$products = DbQuery("SELECT id, active, name FROM {$DB_PREFIX}products WHERE parentid IS NULL ORDER BY name", "id");

//Argument is not set, create blank mode
if (!isset($p) || $p===FALSE){
	$obj = new Product();
}
else
//Passed argument is root product, create new mode
if ($p->parentid == ""){
    $obj = new Product();
    $obj->parentid = $p->id;
}
//Passed argument is product mode, edit it
else
{
    $obj = $p;
    $p = Product::FromDb($obj->parentid);
	$obj->name = str_replace($p->name . " ", "", $obj->name);

	if ($obj->snattrs != "")
		$sn_data = json_decode($obj->snattrs, TRUE);
}

foreach ($sn_fields as $f)
{
	if (isset($sn_data[$f]))
		$$f = $sn_data[$f];
	else
		$$f = "";
}

if ($_SERVER["REQUEST_METHOD"]=="POST")
{
	if (empty($_POST["active"]))
        $_POST["active"] = $obj->id==0;
	ObjectFromArray($obj, $_POST);

	foreach ($sn_fields as $f)
        if (!empty($_POST[$f]))
			$sn_data[$f] = addslashes($_POST[$f]);
		else
			unset($sn_data[$f]);

	if (count($sn_data) > 0)
		$obj->snattrs = json_encode($sn_data);
	else
		$obj->snattrs = "";

	if (DbQuery("SELECT id FROM {$DB_PREFIX}products WHERE name='{$obj->name}' AND id!='{$obj->id}' AND parentid='{$obj->parentid}'"))
		die("addError('#name', 'Mode with this name already exists.');");

    if (!$obj->Save())
		die("alert('" . V_ERROR_TXT . ": " . str_replace("'", "\\'", mysqli_error($mysqli_link)) . "');");

	echo "loadcontent('product');";
	exit;
}
else
{
	$title = ($obj->id==0 ? M_NEWMODE_TXT : H_EDITMODE_TXT);
	
?>
<div id="actions">
	<h1><?php echo $title; ?></h1>
</div>

<div class="formDiv">
<form id="editForm" action="product_mode.php" method="POST">
    <input type="hidden" id="id" name="id" value="<?php echo $obj->id; ?>" />
	<table class="formTbl">
        <tr><th>
			<label for="parentid"><?php echo LIC_PROD_TXT; ?></label>
		</th><td>
			<select name="parentid" id="parentid" class="required noedit">
				<?php if (is_array($products)) foreach ($products as $id => $p) { ?>
					<option value="<?php echo $id; ?>" <?php echo ($p["active"]?"":"disabled"); ?>><?php echo htmlspecialchars($p["name"]); ?></option>
				<?php } ?>
			</select>
		</td></tr>
		<tr><th>
			<label for="name"><?php echo PR_NAME_TXT; ?></label>
		</th><td>
			<input type="text" name="name" id="name" class="required" value="<?php echo htmlspecialchars($obj->name); ?>" />
		</td></tr>
	</table>
	<table class="formTbl" id="snattrsTable">
		<tr><th>
			<label for="hardwareid"><?php echo LIC_HWID_TXT; ?></label>
		</th><td>
			<select id="hardwareid">
				<option value="1" <?php echo ($hardwareid == "none" ? "selected=\"selected\"" : ""); ?>><?php echo ACT_NONE_TXT; ?></option>
				<option value="2" <?php echo ($hardwareid == "fromurl" ? "selected=\"selected\"" : ""); ?>><?php echo ACT_FROMURL_TXT; ?></option>
				<option value="3"><?php echo ACT_VALUE_TXT; ?></option>
			</select>
			<input type="hidden" name="hardwareid" id="hardwareid_val_1" class="hidden" value="none" />
			<input type="hidden" name="hardwareid" id="hardwareid_val_2" class="hidden" value="fromurl" />
			<input type="text" name="hardwareid" id="hardwareid_val_3" class="hidden" value="<?php echo ($hardwareid != "none" && $hardwareid != "fromurl" ? $hardwareid : ""); ?>" />
		</td></tr>
		<tr><th>
			<label for="expiredate"><?php echo LIC_EXPDATE_TXT; ?></label>
		</th><td>
			<select id="expiredate">
				<option value="0"><?php echo ACT_NONE_TXT; ?></option>
				<option value="1"><?php echo ACT_DELAY_TXT; ?></option>
                <option value="2"><?php echo ACT_EXPDELAY_TXT; ?></option>
                <option value="3"><?php echo ACT_VALUE_TXT; ?></option>
			</select>
			<input type="text" name="expiredate" id="expiredate_val_1" class="numeric hidden" value="<?php echo (is_numeric($expiredate)?$expiredate:""); ?>" />
            <input type="text" name="expiredate_code" id="expiredate_val_2" class="numeric hidden" value="<?php echo $expiredate_code; ?>" />
            <input type="text" name="expiredate" id="expiredate_val_3" class="date hidden" value="<?php echo (!is_numeric($expiredate)?$expiredate:""); ?>" />
		</td></tr>
		<tr><th>
			<label for="timelimit"><?php echo LIC_LIMIT_TXT; ?></label>
		</th><td>
			<select id="timelimit">
				<option value="0"><?php echo ACT_NONE_TXT; ?></option>
				<option value="1"><?php echo ACT_VALUE_TXT; ?></option>
			</select>
			<input type="text" name="timelimit" id="timelimit_val_1" class="numeric hidden" value="<?php echo $timelimit; ?>" />
		</td></tr>
		<tr><th>
			<label for="maxbuilddate"><?php echo LIC_MAXBDATE_TXT; ?></label>
		</th><td>
			<select id="maxbuilddate">
				<option value="0"><?php echo ACT_NONE_TXT; ?></option>
				<option value="1"><?php echo ACT_DELAY_TXT; ?></option>
                <option value="2"><?php echo ACT_EXPDELAY_TXT; ?></option>
                <option value="3"><?php echo ACT_VALUE_TXT; ?></option>
			</select>
			<input type="text" name="maxbuilddate" id="maxbuilddate_val_1" class="numeric hidden" value="<?php echo (is_numeric($maxbuilddate)?$maxbuilddate:""); ?>" />
            <input type="text" name="maxbuilddate_code" id="maxbuilddate_val_2" class="numeric hidden" value="<?php echo $maxbuilddate_code; ?>" />
			<input type="text" name="maxbuilddate" id="maxbuilddate_val_3" class="date hidden" value="<?php echo (!is_numeric($maxbuilddate)?$maxbuilddate:""); ?>" />
		</td></tr>
	</table>
	<table class="formTbl">
		<tr><th>
			<label for="data"><?php echo LIC_DATA_TXT; ?></label>
		</th><td>
			<textarea name="data" id="data" rows="3"><?php echo htmlspecialchars($data); ?></textarea>
		</td></tr>
		<?php if ($obj->id!=0) { ?>
		<tr><th>
			<label for="active"><?php echo PR_ACTIVE_TXT; ?></label>
		</th><td>
			<input type="checkbox" id="active" name="active" class="checkbox" value="1" />
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
		$('#snattrsTable input:visible').each(validateRequired);
	}

	function initContent(){
		$('.date').datepicker({ dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true });
		$('.hidden').attr('disabled', 'disabled');
		if (!isNew){
			$('.noedit').attr('disabled', 'disabled');
			$('#active').prop('checked', <?php echo ($obj->active ? "true" : "false"); ?>);
		}
		$('#parentid').val('<?php echo $obj->parentid; ?>');
		
		$('#snattrsTable select').change(function(){
			// Сбрасываем контролы в начальное состояние, задизейбленные контролы не отправляются в запрос
			$('[id^="' + this.id + '_val_"] + label.error').remove();
			$('[id^="' + this.id + '_val_"]').removeClass('errorField').hide().attr('disabled', 'disabled').filter(':text').val('');
			// Показываем нужный контрол
			if (this.value > 0)
				$('#' + this.id + '_val_' + this.value).removeAttr('disabled').show();
		});

		// Проставляем значения комбобоксам
		$('[id*="_val_"]').filter(':text').each(function(){
			if ($(this).val() != '')
			{
				var s = this.id.split('_');
				$('#' + s[0]).val(s[2]);
				$(this).removeAttr('disabled').show();
			}
		});

		//Для hwid
		$('#hardwareid_val_' + $('#hardwareid').val()).removeAttr('disabled');
	}
</script>

<?php
}
?>