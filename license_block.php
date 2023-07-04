<?php
require_once "include/login.inc.php";
require_once "include/license.inc.php";

if (!empty($_GET["id"]))
    $id = $_GET["id"];
elseif (!empty($_GET["p"]))
    $id = $_GET["p"];
else
    die("alert('Bad usage!')");    

$license = License::FromDb($id);

if ($license)
{
    $license->blocked = !$license->blocked;
    $license->Save();
}

?>

//JS code to update list
updatelist();