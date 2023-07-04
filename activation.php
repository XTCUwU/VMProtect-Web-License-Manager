<?php

//Usage:
//activation.php?phash=&code=&hwid=

//hash	- SHA1 hash of product's public key
//code  - activation code
//hwid  - hardware id

//Valid HWIDs for testing: wATJ063UzaPC6Bpfhl4JmP+i5qg=, 5LdRYuVEZ0KuRDkI07NhkA==

require_once "include/dbopen.inc.php";
require_once "include/activation.inc.php";

if (empty($_GET["code"]) || empty($_GET["hwid"]) || empty($_GET["hash"]))
    die($ACT_BAD);

$res = Activation::Activate($_GET["code"], $_GET["hwid"], $_GET["hash"]);

switch ($res)
{
	case $ACT_BAD:
	case $ACT_USED:
	case $ACT_BANNED:
	case $ACT_EXPIRED:
		echo $res;
		break;
	default:
		echo "OK\n" . $res;
}

?>