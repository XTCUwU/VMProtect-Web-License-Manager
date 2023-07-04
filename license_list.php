<?php
require_once "include/login.inc.php";
require_once "include/license.inc.php";

function unicode_decode($txt) {
	$str = preg_replace("/%u([0-9a-f]{4})/i", '&#x$1;', $txt);		//convert %u1234 to &#x1234;
	$str = html_entity_decode($str, null, "UTF-8");					//convert &#x1234; to utf8 string
	return $str;
}

$start = 0;
$showby = 10;

if (isset($_REQUEST["showby"]))
	$showby = intval($_REQUEST["showby"]);
if (isset($_REQUEST["page"]))
	$start = (intval($_REQUEST["page"])-1) * $showby;

$sql = " FROM {$DB_PREFIX}licenses l LEFT JOIN {$DB_PREFIX}vw_products p ON l.productid=p.id LEFT JOIN {$DB_PREFIX}activations a ON l.activationid=a.id WHERE 1=1";

if (!empty($_REQUEST["filter"]))
{
	$date_safe = preg_match("/^[0-9-]*$/", $_REQUEST["filter"]);
	$equal = Sql(unicode_decode($_REQUEST["filter"]));
	$like = Sql("%" . unicode_decode($_REQUEST["filter"]) . "%");
	$sql .= " AND (l.email LIKE $like OR l.name LIKE $like OR p.fullname LIKE $like OR l.comments LIKE $like OR l.orderref LIKE $like" .
		($date_safe ? " OR l.createdate LIKE $like" : "") . " OR a.code=$equal)";
}

$all_count = DbQuery("SELECT COUNT(*)" . $sql);
$blocked_count = DbQuery("SELECT COUNT(*)" . $sql . " AND l.blocked");

if (!empty($_REQUEST["state"]))
{
	if ($_REQUEST["state"] == "blocked")
		$sql .= " AND l.blocked";
}

$count = DbQuery("SELECT COUNT(*)" . $sql);
	
$sql = "SELECT l.*" . $sql . " ORDER BY l.id DESC LIMIT $start, $showby";

$objects = ObjectsSqlLoad($sql, "License");
if (!$objects)
	$objects = array();

$products = DbQuery("SELECT * FROM {$DB_PREFIX}vw_products", "id");

?>

<input type="hidden" id="totalCount" value="<?php echo $count; ?>" />
<table id="listTbl">
<tr>
	<th><?php echo LIC_DATE_TXT; ?></th>
	<th><?php echo LIC_PROD_TXT; ?></th>
	<th><?php echo LIC_NAME_TXT; ?></th>
	<th><?php echo LIC_EMAIL_TXT; ?></th>
</tr>
<?php foreach ($objects as $obj) { ?>
<tr>
	<td class="<?php echo ($obj->blocked?"blocked":""); ?>"><?php echo $obj->createdate; ?></td>
	<td>
	<?php
		if (isset($products[$obj->productid]["fullname"]))
			echo htmlspecialchars($products[$obj->productid]["fullname"]);
		else
			echo $obj->productid;
	?>
	</td>
	<td><?php echo htmlspecialchars($obj->name); ?>
		<br/>
		<div class="listItemActions">
			<a class="editLink" href="#" onclick="return loadcontent('license/edit/<?php echo $obj->id; ?>')"><?php echo EDIT_TXT; ?></a>
			<a class="copyLink tableLink" href="#license/copy/<?php echo $obj->id; ?>"><?php echo LIC_COPYSN_TXT; ?></a>
			<a class="blockLink" href="#license/block/<?php echo $obj->id; ?>"><?php echo ($obj->blocked?UNBLOCK_TXT:BLOCK_TXT); ?></a>
			<a class="deleteLink tableLink" href="#license/delete/<?php echo $obj->id; ?>"><?php echo DELETE_TXT; ?></a>
		</div>
	</td>
	<td><a href="mailto:<?php echo htmlspecialchars($obj->email); ?>"><?php echo htmlspecialchars($obj->email); ?></a></td>
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
