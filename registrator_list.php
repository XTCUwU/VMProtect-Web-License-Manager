<?php
require_once "include/login.inc.php";
require_once "include/registrator.inc.php";
RequireAdmin();

$start = 0;
$showby = 10;

if (isset($_REQUEST["showby"]))
	$showby = intval($_REQUEST["showby"]);
if (isset($_REQUEST["page"]))
	$start = (intval($_REQUEST["page"])-1) * $showby;

$sql = " FROM {$DB_PREFIX}registrators WHERE 1=1";

$all_count = DbQuery("SELECT COUNT(*)" . $sql);
$blocked_count = DbQuery("SELECT COUNT(*)" . $sql . " AND active='0'");

if (!empty($_REQUEST["state"]))
{
	if ($_REQUEST["state"] == "inactive")
		$sql .= " AND active='0'";
}

$count = DbQuery("SELECT COUNT(*)" . $sql);

$objects = ObjectsSqlLoad("SELECT * $sql ORDER BY id DESC LIMIT $start, $showby", "Registrator");
if (!$objects)
	$objects = array();

?>

<input type="hidden" id="totalCount" value="<?php echo $count; ?>" />
<table id="listTbl">
<tr>
	<th><?php echo R_NAME_TXT; ?></th>
	<th><?php echo R_VALUE_TXT; ?></th>
</tr>
<?php foreach ($objects as $obj) {
    if ($obj->authmode == 0)
        $val = "IP: " . $obj->ipranges;
    else
        $val = R_LOGIN_TXT . ": {$obj->login}<br/>" . R_PASSWORD_TXT . ": {$obj->password}";
?>
<tr>
	<td class="<?php echo ($obj->active?"":"blocked"); ?>">
		<?php echo htmlspecialchars($obj->name); ?>
		<br/>
		<div class="listItemActions">
			<a class="editLink" href="#" onclick="return loadcontent('registrator/edit/<?php echo $obj->id; ?>')"><?php echo EDIT_TXT; ?></a>
			<a class="deleteLink tableLink" href="#registrator/delete/<?php echo $obj->id; ?>"><?php echo DELETE_TXT; ?></a>
		</div>
	</td>
	<td><?php echo $val; ?></td>
</tr>
<?php } ?>
</table>

<script type="text/javascript">
	$('#listTbl tr:gt(0)').mouseover(function(){
		$(this).find('.listItemActions').css('visibility', 'visible');
		$(this).addClass('active');
	});
	$('#listTbl tr:gt(0)').mouseout(function(){
		$(this).find('.listItemActions').css('visibility', 'hidden');
		$(this).removeClass('active');
	});
	
	$('.allCountTxt').text('<?php echo $all_count; ?>');
	$('.blockedCountTxt').text('<?php echo $blocked_count; ?>');
	$('.tableLink').click(loadTableContent);
	$('.blockLink').click(loadScript);
</script>
