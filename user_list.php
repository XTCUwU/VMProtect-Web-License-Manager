<?php
require_once "include/login.inc.php";
require_once "include/user.inc.php";
RequireAdmin();

$start = 0;
$showby = 10;

if (isset($_REQUEST["showby"]))
	$showby = intval($_REQUEST["showby"]);
if (isset($_REQUEST["page"]))
	$start = (intval($_REQUEST["page"])-1) * $showby;

$sql = " FROM {$DB_PREFIX}users WHERE 1=1";

$all_count = DbQuery("SELECT COUNT(*)" . $sql);
$admins_count = DbQuery("SELECT COUNT(*)" . $sql . " AND isadmin='1'");
$managers_count = DbQuery("SELECT COUNT(*)" . $sql . " AND isadmin='0'");

if (!empty($_REQUEST["state"]))
{
	if ($_REQUEST["state"] == "administrator")
		$sql .= " AND isadmin='1'";
	if ($_REQUEST["state"] == "manager")
		$sql .= " AND isadmin='0'";
}

$count = DbQuery("SELECT COUNT(*)" . $sql);

$objects = ObjectsSqlLoad("SELECT * $sql ORDER BY id DESC LIMIT $start, $showby", "User");
if (!$objects)
	$objects = array();

?>

<input type="hidden" id="totalCount" value="<?php echo $count; ?>" />
<table id="listTbl">
<tr>
	<th><?php echo U_UN_TXT; ?></th>
	<th><?php echo U_EMAIL_TXT; ?></th>
	<th><?php echo U_ROLE_TXT; ?></th>
</tr>
<?php foreach ($objects as $obj) { ?>
<tr>
	<td><?php echo htmlspecialchars($obj->login); ?>
		<br/>
		<div class="listItemActions">
			<a class="editLink" href="#" onclick="return loadcontent('user/edit/<?php echo $obj->id; ?>')"><?php echo EDIT_TXT; ?></a>
			<?php if ($obj->id!=1 && $obj->id!=$cur_user->id) { ?>
			<a class="deleteLink tableLink" href="#user/delete/<?php echo $obj->id; ?>"><?php echo DELETE_TXT; ?></a>
			<?php } ?>
		</div>
	</td>
	<td><a href="mailto:<?php echo htmlspecialchars($obj->email); ?>"><?php echo htmlspecialchars($obj->email); ?></a></td>
	<td><?php echo ($obj->isadmin ? U_ADMIN_TXT : U_MAN_TXT); ?></td>
</tr>
<?php } ?>
</table>

<script>
	$('#listTbl tr:gt(0)').mouseover(function(){
		$(this).find('.listItemActions').css('visibility', 'visible');
		$(this).addClass('active');
	});
	$('#listTbl tr:gt(0)').mouseout(function(){
		$(this).find('.listItemActions').css('visibility', 'hidden');
		$(this).removeClass('active');
	});
	$('.allCountTxt').text('<?php echo $all_count; ?>');
	$('.adminsCountTxt').text('<?php echo $admins_count; ?>');
	$('.managersCountTxt').text('<?php echo $managers_count; ?>');
	$('.tableLink').click(loadTableContent);
	$('.blockLink').click(loadScript);
</script>
