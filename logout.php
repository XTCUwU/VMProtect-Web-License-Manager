<?php
session_start();
unset($_SESSION["cur_user"]);
header("Location: login.php");
?>
