<?php
require_once "include/login.inc.php";
RequireAdmin();

$id = 0;

if (isset($_REQUEST["id"]) && isset($_REQUEST["confirm"]))
{
    $id = intval($_REQUEST["id"]);
    if ($id == 1)
        die(U_EADMIN_TXT);
    if ($id == $cur_user->id)
        die(U_ESELF_TXT);
    DbQuery("DELETE FROM {$DB_PREFIX}users WHERE id=" . Sql($_REQUEST["id"])) or die(V_ERROR_TXT . ": " . mysqli_error($mysqli_link));
}
else
{
?>

<h1><?php echo U_DELETE_TXT; ?></h1>
<?php echo U_DELMSG_TXT; ?>
<br/><br/>
<form method="post" action="user_delete.php">
    <input type="hidden" name="id" value="<?php echo $_REQUEST["p"]; ?>" />
    <input type="hidden" name="confirm" value="yes" />
    <button class="deleteBtn" onclick="return saveForm()"><?php echo DELETE_TXT; ?></button>
    <button class="cancelBtn" onclick="return closeTableContent()"><?php echo CANCEL_TXT; ?></button>
</form>

<?php } ?>
