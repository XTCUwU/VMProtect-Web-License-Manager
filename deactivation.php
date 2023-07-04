<?php

//Usage:
//deactivation.php?hash=
//hash - Serial number hash in base64, remember that:
// All space characters should be removed
// '+' should be encoded to %2B in URL

require_once "include/dbopen.inc.php";
require_once "include/activation.inc.php";

if (empty($_GET["hash"]))
    echo $DEACT_ERROR;
else
    echo Activation::Deactivate($_GET["hash"]);
?>