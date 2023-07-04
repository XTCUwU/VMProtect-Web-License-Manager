<?php

//Usage:
//keygen.php?productid=&customername=&customeremail=[&orderref=][&quantity=]
//keygen.php?productid=&type=avangate

require_once "include/dbopen.inc.php";
require_once "include/activation.inc.php";
require_once "include/license.inc.php";
require_once "include/product.inc.php";
require_once "include/registrator.inc.php";

if (!Registrator::CheckAccess())
	die("Access denied.");

//Input parameters, required
$productid = empty($_REQUEST["productid"]) ? "" : intval($_REQUEST["productid"]);
$type = empty($_REQUEST["type"]) ? "" : $_REQUEST["type"];

if ($type == "avangate")
{
	if (!empty($_REQUEST["COMPANY"]))
		$name = $_REQUEST["COMPANY"];
	else {
		$name = empty($_REQUEST["FIRSTNAME"]) ? "" : $_REQUEST["FIRSTNAME"];
		if (!empty($_REQUEST["LASTNAME"]))
			$name .= ($name==""?"":" ") . $_REQUEST["LASTNAME"];
	}
	$email = empty($_REQUEST["EMAIL"]) ? "" : $_REQUEST["EMAIL"];
	//Input parameters, optional
	if (!empty($_REQUEST["REFNO"]))
		$orderref = $_REQUEST["REFNO"];
	if (!empty($_REQUEST["QUANTITY"]))
		$quantity = intval($_REQUEST["QUANTITY"]);
} 
elseif ($type == "mycommerce")
{
	if (!empty($_REQUEST["company"]))
		$name = $_REQUEST["company"];
	else {
		$name = empty($_REQUEST["first_name"]) ? "" : $_REQUEST["first_name"];
		if (!empty($_REQUEST["last_name"]))
			$name .= ($name==""?"":" ") . $_REQUEST["last_name"];
	}
	$email = empty($_REQUEST["email"]) ? "" : $_REQUEST["email"];
	//Input parameters, optional
	if (!empty($_REQUEST["order_id"]))
	    $orderref = $_REQUEST["order_id"];
	if (!empty($_REQUEST["quantity"]))
		$quantity = intval($_REQUEST["quantity"]);
}
else
{
	if (!empty($_REQUEST["companyname"]))
		$name = $_REQUEST["companyname"];
	else
		$name = empty($_REQUEST["customername"]) ? "" : $_REQUEST["customername"];
	$email = empty($_REQUEST["customeremail"]) ? "" : $_REQUEST["customeremail"];
	//Input parameters, optional
	if (!empty($_REQUEST["orderref"]))
	    $orderref = $_REQUEST["orderref"];
	if (!empty($_REQUEST["quantity"]))
	    $quantity = intval($_REQUEST["quantity"]);
}

if (empty($productid) || empty($name) || empty($email))
	die("Missing required parameter.");

$product = Product::FromDb($productid);
if (!$product)
	die("Product not found.");
if (!$product->active)
	die("Product is inactive.");

//Generate activation code
if ($product->uses_act)
{
	$act = new Activation();
	$act->name = $name;
	$act->email = $email;
	$act->productid = $productid;
	if (isset($orderref))
		$act->orderref = $orderref;
	if (isset($quantity))
		$act->act_count = $quantity * (1 + $product->act_extracount);
	$act->Save();
	$code = $act->code;
}
//Generate serial number
else
{
	//Input parameters, optional
	$sn_data = array();
	$sn_fields = array("hardwareid", "expiredate", "timelimit", "maxbuilddate", "data");

	foreach ($sn_fields as $f)
		if (isset($_GET[$f]))
			$sn_data[$f] = $_GET[$f];

	$create_date = date("Y-m-d");
	$sn_data = $product->ApplySnAttrs($sn_data, $create_date);

	$license = new License();
	$license->name = $name;
	$license->email = $email;
	$license->productid = $productid;
	if (isset($orderref))
		$license->orderref = $orderref;
	$license->CreateSerialNumber($sn_data);
	$license->Save();
	$code = $license->sn;
}

//Output start
if ($type == "avangate")
{
	header("Content-Type: text/xml");
	echo "<?xml version=\"1.0\"?>\n<Data>\n<Code>{$code}</Code>\n</Data>";
}
else
	echo $code;
?>
