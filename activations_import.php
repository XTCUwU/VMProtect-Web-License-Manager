<?php
require_once "include/login.inc.php";
require_once "include/activation.inc.php";
require_once "include/product.inc.php";

$products = DbQuery("SELECT id, fullname, active FROM {$DB_PREFIX}vw_products ORDER BY fullname");
    
if ($_SERVER["REQUEST_METHOD"]=="POST")
{
	$lines = preg_split("/[\n\r]+/s", $_POST["codes"]);
	$skipped = 0;
	//Format: CODE[;NAME][;EMAIL]
	foreach ($lines as $line)
	{
		$vals = explode(';', $line);
		$code = strtoupper($vals[0]);
		if (!preg_match("/^[A-Z0-9-]*$/", $code))
		{
			$skipped++;
			continue;
		}
		if (DbQuery("SELECT COUNT(*) FROM {$DB_PREFIX}activations WHERE code=" . Sql($code)) != 0)
			continue;
		$a = new Activation();
		if (!empty($vals[1]))
			$a->name = $vals[1];
		if (!empty($vals[2]))
			$a->email = $vals[2];
		$a->productid = $_POST["productid"];
		$a->act_count = $_POST["act_count"];
		$a->code = $code;
		$a->Save();
	}
	if ($skipped > 0)
		echo "addError('#codes', '" . $skipped . " " . ACT_ECODES_TXT . "')";
	else
		echo "loadcontent('activations');";
}
else
{
?>
<h1><?php echo H_IMPORTACT_TXT; ?></h1>
<div class="formDiv">
<form id="editForm" action="activations_import.php" method="POST">
	<table class="formTbl">
		<tr><th>
			<label for="productid"><?php echo LIC_PROD_TXT; ?></label>
		</th><td>
			<select name="productid" id="productid" class="required">
				<?php if (is_array($products)) foreach ($products as $p) { ?>
				<option value="<?php echo $p["id"]; ?>" <?php echo (!$p["active"]?"disabled":""); ?>><?php echo htmlspecialchars($p["fullname"]); ?></option>
				<?php } ?>
			</select>
		</td></tr>
		<tr><th>
			<label for="codes"><?php echo ACT_CODES_TXT; ?></label>
		</th><td>
			<textarea id="codes" name="codes" class="required" rows="9"></textarea>
		</td></tr>
		<tr><th>
			<label for="act_count"><?php echo ACT_COUNT_TXT; ?></label>
		</th><td>
			<input type="text" name="act_count" id="act_count" class="numeric" value="1" />
		</td></tr>
		<tr>
			<td></td>
			<td style="padding-top:10px">
                <a class="greenBtn" onclick="return saveForm('script')"><span><?php echo IMPORT_TXT; ?></span><em></em></a>
			</td>
		</tr>
	</table>
</form>
</div>
<?php
}
?>