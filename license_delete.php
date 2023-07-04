<?php
require_once "include/login.inc.php";

$id = "0";

if (isset($_REQUEST["id"]) && isset($_REQUEST["confirm"]))
{
    DbQuery("DELETE FROM {$DB_PREFIX}licenses WHERE id=" . Sql($_REQUEST["id"])) or die(V_ERROR_TXT . ": " . mysqli_error($mysqli_link));
}
else
{
?>

<h1><?php echo LIC_DELETE_TXT; ?></h1>
<?php echo LIC_DELMSG_TXT; ?>
<br/><br/>
<form method="post" action="license_delete.php">
    <input type="hidden" name="id" value="<?php echo $_REQUEST["p"]; ?>" />
    <input type="hidden" name="confirm" value="yes" />
    <button class="deleteBtn" onclick="return saveForm()"><?php echo DELETE_TXT; ?></button>
    <button class="cancelBtn" onclick="return closeTableContent()"><?php echo CANCEL_TXT; ?></button>
</form>

<?php } ?>