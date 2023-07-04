<?php
require_once "include/login.inc.php";
require_once "include/product.inc.php";
require_once "include/license.inc.php";
RequireAdmin();

$weblm_url = "http://" . $_SERVER["SERVER_NAME"] . substr($_SERVER["REQUEST_URI"], 0, strrpos($_SERVER["REQUEST_URI"], "/"));

$p = Product::FromDb($_REQUEST["id"]);
$lics = ObjectsSqlLoad("SELECT l.* FROM {$DB_PREFIX}licenses l LEFT JOIN {$DB_PREFIX}products p ON l.productid=p.id WHERE p.id={$p->id} OR p.parentid={$p->id}", "License");

$filename = str_replace(" ", "_", $p->name);

header("Content-Type: text/xml; charset=utf-8");
header("Content-Disposition: attachment; filename=\"" . $filename . ".vmp\"");

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
?>
<Document>
<LicenseManager ActivationServer="<?php echo $weblm_url; ?>" ProductCode="<?php echo $p->product_code; ?>" Algorithm="<?php echo $p->algorithm; ?>" Bits="<?php echo $p->bits; ?>" PublicExp="<?php echo $p->public_exp; ?>" PrivateExp="<?php echo $p->private_exp; ?>" Modulus="<?php echo $p->modulus; ?>">
    <?php if (is_array($lics)) foreach ($lics as $l) { ?>
	<License Date="<?php echo $l->createdate; ?>" CustomerName="<?php echo $l->name; ?>" CustomerEmail="<?php echo $l->email; ?>" OrderRef="<?php echo $l->orderref; ?>" SerialNumber="<?php echo $l->sn; ?>" Blocked="<?php echo ($l->blocked ? "true" : "false"); ?>">
		<Comments><?php echo $l->comments; ?></Comments>
	</License>
	<?php } ?>
</LicenseManager>
</Document>