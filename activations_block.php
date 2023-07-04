<?php
require_once "include/login.inc.php";
require_once "include/activation.inc.php";

if (!empty($_GET["id"]))
    $id = $_GET["id"];
elseif (!empty($_GET["p"]))
    $id = $_GET["p"];
else
    die("alert('Bad usage!')");    

$obj = Activation::FromDb($id);

if ($obj)
{
    $obj->blocked = !$obj->blocked;
    $obj->Save();
}

?>

//JS code to update list
updatelist();