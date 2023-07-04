<?php
require_once "include/login.inc.php";
require_once "include/license.inc.php";
require_once "include/product.inc.php";

if (isset($_REQUEST["p"]))
	$obj = License::FromDb($_REQUEST["p"]);
else 
if (isset($_REQUEST["id"]))
	$obj = License::FromDb($_REQUEST["id"]);

$sn_fields = array("hardwareid", "expiredate", "timelimit", "maxbuilddate", "data");
foreach ($sn_fields as $f)
	$$f = "";

if (!isset($obj) || $obj===FALSE)
	$obj = new License();
else
if ($obj->sn != "")
{
	$sn_data = $obj->UnpackSerialNumber();
	if (is_array($sn_data))
	{
        if (!empty($sn_data["data"]))
            $sn_data["data"] = EncodeChars($sn_data["data"]);
		foreach ($sn_fields as $f)
			if (isset($sn_data[$f]))
				$$f = $sn_data[$f];
	}
	else
		echo "<script>alert('$sn_data');</script>";
}

$products = DbQuery("SELECT id, active, fullname, snattrs FROM {$DB_PREFIX}vw_products ORDER BY fullname", "id");

if ($_SERVER["REQUEST_METHOD"]=="POST")
{
	if (empty($_POST["blocked"]))
		$_POST["blocked"] = "0";
	ObjectFromArray($obj, $_POST);

	$new = ($obj->id == 0);
	if ($obj->sn == "")
	{
		$sn_data = array();
		foreach ($sn_fields as $f)
			if (!empty($_POST[$f]))
				$sn_data[$f] = $_POST[$f];
		$obj->CreateSerialNumber($sn_data);
	}

	$obj->Save() or die("alert('" . V_ERROR_TXT . ": '" . str_replace("'", "\\'", mysqli_error($mysqli_link)) . ");");
	echo "loadcontent('license');";
}
else
{
	if ($obj->id==0)
		echo "<h1>" . M_NEWLIC_TXT . "</h1>";
	else
		echo "<h1>" . H_EDITLIC_TXT . "</h1>";
?>

<div class="formDiv">
<form id="editForm" action="license_edit.php" method="POST">
	<input type="hidden" id="id" name="id" value="<?php echo $obj->id; ?>" />
	<table class="formTbl">
		<tr><th>
			<label for="productid"><?php echo LIC_PROD_TXT; ?></label>
		</th><td>
			<select name="productid" id="productid" class="required noedit">
				<?php if (is_array($products)) foreach ($products as $id => $p) { ?>
					<option value="<?php echo $id; ?>" <?php echo ($p["active"]?"":"disabled"); ?>><?php echo htmlspecialchars($p["fullname"]); ?></option>
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
			<label for="comments"><?php echo LIC_COMMENTS_TXT; ?></label>
		</th><td>
			<textarea name="comments" id="comments" rows="3"><?php echo htmlspecialchars($obj->comments); ?></textarea>
		</td></tr>
		<?php if ($obj->id!=0) { ?>
		<tr><th>
			<label for="blocked"><?php echo LIC_BLOCKED_TXT; ?></label>
		</th><td>
			<input type="checkbox" id="blocked" name="blocked" class="checkbox" value="1" />
		</td></tr>
		<?php } ?>
	</table>
	<hr />
	<table id="snfields" class="formTbl">
		<tr><th>
			<label for="hardwareid"><?php echo LIC_HWID_TXT; ?></label>
		</th><td>
			<input type="text" name="hardwareid" id="hardwareid" class="noedit" value="<?php echo $hardwareid; ?>" />
		</td></tr>
		<tr><th>
			<label for="expiredate"><?php echo LIC_EXPDATE_TXT; ?></label>
		</th><td>
			<input type="text" name="expiredate" id="expiredate" class="date noedit" value="<?php echo $expiredate; ?>" />
		</td></tr>
		<tr><th>
			<label for="timelimit"><?php echo LIC_LIMIT_TXT; ?></label>
		</th><td>
			<input type="text" name="timelimit" id="timelimit" class="numeric noedit" value="<?php echo $timelimit; ?>" />
		</td></tr>
		<tr><th>
			<label for="maxbuilddate"><?php echo LIC_MAXBDATE_TXT; ?></label>
		</th><td>
			<input type="text" name="maxbuilddate" id="maxbuilddate" class="date noedit" value="<?php echo $maxbuilddate; ?>" />
		</td></tr>
		<tr><th>
			<label for="data"><?php echo LIC_DATA_TXT; ?></label>
		</th><td>
			<textarea name="data" id="data" rows="3" class="noedit"><?php echo $data; ?></textarea>
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
	var snattrs = [];
	<?php if (is_array($products)) foreach ($products as $id => $p) { ?>
		snattrs.push(jQuery.parseJSON('<?php echo $p["snattrs"]; ?>'));
	<?php } ?>

	function applyAttrs(attrs){
		if (attrs.hardwareid)
			if (attrs.hardwareid != 'none' && attrs.hardwareid != 'fromurl')
				$('#hardwareid').val(attrs.hardwareid);
		if (attrs.data)
			$('#data').val(attrs.data);
		if (attrs.timelimit)
			$('#timelimit').val(attrs.timelimit);
		var expiredate = attrs.expiredate;
		if (!expiredate)
			expiredate = attrs.expiredate_code;
		if (expiredate){
			if (/^\d+$/.test(expiredate)){
				var d = new Date();
				d.setTime(d.getTime() + Number(expiredate) * 24 * 60 * 60 * 1000);
				$('#expiredate').val($.datepicker.formatDate('yy-mm-dd', d));
			}
			else
				$('#expiredate').val(expiredate);
		}
		var maxbuilddate = attrs.maxbuilddate;
		if (!maxbuilddate)
			maxbuilddate = attrs.maxbuilddate_code;
		if (maxbuilddate){
			if (/^\d+$/.test(maxbuilddate)){
				var d = new Date();
				d.setTime(d.getTime() + Number(maxbuilddate) * 24 * 60 * 60 * 1000);
				$('#maxbuilddate').val($.datepicker.formatDate('yy-mm-dd', d));
			}
			else
				$('#maxbuilddate').val(maxbuilddate);
		}
	}

	$('#productid').change(function(){
		var selectedIndex = $(this).find("option:selected").index();
		$('#snfields input:text, #snfields textarea').val('');
		if (snattrs[selectedIndex])
			applyAttrs(snattrs[selectedIndex]);
	});

	function initContent(){
		$('.date').datepicker({ dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true });
		$('#productid').val('<?php echo $obj->productid; ?>');
		if (isNew){
			$('#productid').change();
		} else {
			$('.noedit').attr('disabled', 'disabled');
			$('#blocked').prop('checked', <?php echo ($obj->blocked ? "true" : "false"); ?>);
		}
	}
</script>
<?php
}
?>