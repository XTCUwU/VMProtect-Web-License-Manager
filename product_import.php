<?php
require_once "include/login.inc.php";
require_once "include/product.inc.php";
require_once "include/license.inc.php";
RequireAdmin();

$MAX_FILE_SIZE = 50000;

if ($_SERVER["REQUEST_METHOD"]=="POST")
if (!empty($_FILES["pdata"]))
{
	if ($_FILES["pdata"]["error"]==2)
		die("addError($('#pdata'), '" . sprintf(PR_EFILESIZE_TXT, $MAX_FILE_SIZE) . "');");
		
	if ($_FILES["pdata"]["tmp_name"]!="")
	{
		$doc = @DOMDocument::load($_FILES["pdata"]["tmp_name"]);
		if ($doc == FALSE)
			die("addError($('#pdata'), '" . PR_EXMLFILE_TXT . "');");
		$pxml = $doc->documentElement->getElementsByTagName("LicenseManager")->item(0);
		
		if (!$pxml)
			die("addError('#pdata', '" . PR_EXMLDATA_TXT . "');");
		
		$algorithm = $pxml->getAttribute("Algorithm");
		$bits = $pxml->getAttribute("Bits");
		$product_code = $pxml->getAttribute("ProductCode");
		$public_exp = $pxml->getAttribute("PublicExp");
		$private_exp = $pxml->getAttribute("PrivateExp");
		$modulus = $pxml->getAttribute("Modulus");
		
		if (!$algorithm || !$bits || !$product_code || !$public_exp || !$private_exp || !$modulus)
			die("addError('#pdata', '" . PR_EXMLDATA_TXT . "');");
			
		//Check if product already exists
		$res = ObjectsSqlLoad("SELECT * FROM {$DB_PREFIX}products WHERE
				algorithm='{$algorithm}' AND
				bits={$bits} AND
				product_code='{$product_code}' AND
				public_exp='{$public_exp}' AND
				private_exp='{$private_exp}' AND
				modulus='{$modulus}'", "Product");
		
		if ($res === FALSE)
		{
			$p = new Product();
			$p->algorithm = $algorithm;
			$p->bits = $bits;
			$p->product_code = $product_code;
			$p->public_exp = $public_exp;
			$p->private_exp = $private_exp;
			$p->modulus = $modulus;
			$p->name = "Product #";
			$p->Save() or die(mysqli_error($mysqli_link));
			$p->name .= "{$p->id}";
			$p->Save();
		}
		else
			$p = $res[0];
		
		$lics_count = 0;
		
		foreach ($pxml->getElementsByTagName("License") as $lxml)
		{
			$l = new License();
			$l->productid = $p->id;
			$l->name = $lxml->getAttribute("CustomerName");
			$l->email = $lxml->getAttribute("CustomerEmail");
			$l->createdate = $lxml->getAttribute("Date");
			$l->orderref = $lxml->getAttribute("OrderRef");
			$l->sn = $lxml->getAttribute("SerialNumber");
			$l->blocked = (true == strtolower($lxml->getAttribute("Blocked")));
			$comment_node = $lxml->getElementsByTagName("Comments")->item(0);
			if ($comment_node)
				$l->comments = $comment_node->textContent;
			$l->Save() or die("alert('" . V_ERROR_TXT . ": " . str_replace("'", "\\'", mysqli_error($mysqli_link)) . "');");
			$lics_count++;
		}
		
		echo "alert('" . sprintf(PR_IMPORTED_TXT, $p->name, $lics_count) . "');loadcontent('product');";
	}
	exit;
}

?>

<div id="actions">
	<div id="help">
		<?php echo HELP_IMPORT_PRODUCT; ?>
	</div>
	<h1><?php echo M_IMPORTPROD_TXT; ?></h1>
	<button class="cancelBtn" style="float:right" onclick="$('#help').toggle();"><?php echo HELP_TXT; ?></button>
</div>

<div class="formDiv">
<form id="importForm" method="post" action="product_import.php" enctype="multipart/form-data">
	<table class="formTbl">
		<tr>
			<th><?php echo PR_FILE_TXT; ?></th>
			<td>
				<input type="hidden" name="MAX_FILE_SIZE" id="MAX_FILE_SIZE" value="<?php echo $MAX_FILE_SIZE; ?>" />
				<input type="file" name="pdata" id="pdata" class="file required" />
			</td>
		</tr>
		<tr>
			<td></td>
			<td style="padding-top:10px">
                <a class="greenBtn" onclick="return saveForm('script')"><span><?php echo IMPORT_TXT; ?></span><em></em></a>
			</td>
		</tr>
	</table>
</form>
</div>