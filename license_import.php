<?php
require_once "include/login.inc.php";
require_once "include/product.inc.php";
require_once "include/license.inc.php";

$sn = "";
$sn_data = NULL;
$products = ObjectsSqlLoad("SELECT * FROM {$DB_PREFIX}products", "Product");

if ($_SERVER["REQUEST_METHOD"]=="POST")
{	
	$sn = $_REQUEST["sn"];
	$snhash = base64_encode(sha1(base64_decode($sn), TRUE));
	
	//Check if license exists
	$res = DbQuery("SELECT id FROM {$DB_PREFIX}licenses WHERE snhash=" . Sql($snhash) . " LIMIT 1");
	if (is_numeric($res))
	{
		echo "<script>loadcontent('license/edit/{$res}');</script>";
		exit;
	}
	
	$obj = new License();
	$obj->sn = $sn;
	
	if (is_array($products))
		foreach ($products as $p)
		{
			$obj->productid = $p->id;
			$sn_data = @$obj->UnpackSerialNumber();
			if (is_array($sn_data))
				break;
		}
	
	if (is_array($sn_data))
	{
		$sn_fields = array("hardwareid", "expiredate", "timelimit", "maxbuilddate", "data");
		foreach ($sn_fields as $f)
			if (isset($sn_data[$f]))
				$$f = $sn_data[$f];
			else
				$$f = "";
		$obj->name = $sn_data["name"];
		$obj->email =  $sn_data["email"];
	}
	
	$success = is_array($sn_data);
}

if (!is_array($sn_data)) {
?>

<div id="actions">
	<div id="help">
		<?php echo HELP_IMPORT_LICENSE; ?>
	</div>
	<h1><?php echo M_IMPORTLIC_TXT; ?></h1>
	<button class="cancelBtn" style="float:right" onclick="$('#help').toggle();"><?php echo HELP_TXT; ?></button>
</div>

<div class="formDiv">
	<form id="importForm" method="post" action="license_import.php">
		<table class="formTbl">
			<tr>
				<th><?php echo LIC_SN_TXT; ?></th>
				<td>
					<textarea id="sn" name="sn" class="required" rows="9"><?php echo htmlspecialchars($sn); ?></textarea>
				</td>
			</tr>
			<tr>
				<td></td>
				<td style="padding-top:10px">
                    <a class="greenBtn" onclick="return sendImportForm()"><span><?php echo IMPORT_TXT; ?></span><em></em></a>
				</td>
			</tr>
		</table>
	</form>	
</div>
<script>
	function sendImportForm(){
		if (validateForm()){
			$('#importForm').ajaxSubmit({
				type: 'POST',
				target: '#contentDiv',
				success: function(data){
					lockButtons(false);
					if (typeof(initContent)=='function')
						initContent();
				},
				error: function(xhr, ajaxOptions, thrownError){
					lockButtons(false);
					alert(xhr.responseText);
				}
			});
			lockButtons(true);
		}
		return false;
	}
<?php
if (isset($success) && $success == FALSE)
	echo "addError('#sn', '" . LIC_ENOTP_TXT . "');";
?>
</script>

<?php
}
else
{
?>

<h1>Import License</h1>
	<div class="formDiv">
	<form id="editForm" action="license_edit.php" method="POST">
		<input type="hidden" id="id" name="id" value="<?php echo $obj->id; ?>" />
		<input type="hidden" id="sn" name="sn" value="<?php echo $obj->sn; ?>" />
		<input type="hidden" name="productid" value="<?php echo $obj->productid; ?>" />
		<table class="formTbl">
			<tr><th>
				<label for="productid">Product</label>
			</th><td>
				<select id="productid" class="required noedit">
					<?php foreach ($products as $p) { ?>
					<option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->name); ?></option>
					<?php } ?>
				</select>
			</td></tr>
			<tr><th>
				<label for="name">Customer Name</label>
			</th><td>
				<input type="text" name="name" id="name" class="required" value="<?php echo htmlspecialchars($obj->name); ?>" />
			</td></tr>
			<tr><th>
				<label for="email">Customer Email</label>
			</th><td>
				<input type="text" name="email" id="email" class="required email" value="<?php echo htmlspecialchars($obj->email); ?>" />
			</td></tr>
			<tr><th>
				<label for="createdate">Create Date</label>
			</th><td>
				<input type="text" name="createdate" id="createdate" class="date" value="<?php echo $obj->createdate; ?>" />
			</td></tr>
			<tr><th>
				<label for="orderref">Order Ref</label>
			</th><td>
				<input type="text" name="orderref" id="orderref" value="<?php echo htmlspecialchars($obj->orderref); ?>" />
			</td></tr>
			<tr><th>
				<label for="comments">Comments</label>
			</th><td>
				<textarea name="comments" id="comments" rows="3"><?php echo htmlspecialchars($obj->comments); ?></textarea>
			</td></tr>
		</table>
		<hr />
		<table class="formTbl">
			<tr><th>
				<label for="hardwareid">Hardware ID</label>
			</th><td>
				<input type="text" name="hardwareid" id="hardwareid" class="noedit" value="<?php echo $hardwareid; ?>" />
			</td></tr>
			<tr><th>
				<label for="expiredate">Expire Date</label>
			</th><td>
				<input type="text" name="expiredate" id="expiredate" class="date noedit" value="<?php echo $expiredate; ?>" />
			</td></tr>
			<tr><th>
				<label for="timelimit">Time Limit</label>
			</th><td>
				<input type="text" name="timelimit" id="timelimit" class="numeric noedit" value="<?php echo $timelimit; ?>" />
			</td></tr>
			<tr><th>
				<label for="maxbuilddate">Max Build Date</label>
			</th><td>
				<input type="text" name="maxbuilddate" id="maxbuilddate" class="date noedit" value="<?php echo $maxbuilddate; ?>" />
			</td></tr>
			<tr><th>
				<label for="data">Data</label>
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
	var isNew = true;
		
	function initContent(){
		$('.date').datepicker({ dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true });
		$('.noedit').attr('disabled', 'disabled');
		$('#productid').val('<?php echo $obj->productid; ?>');
	}
</script>

<?php } ?>