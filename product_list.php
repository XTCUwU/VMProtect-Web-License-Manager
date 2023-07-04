<?php
require_once "include/login.inc.php";
require_once "include/product.inc.php";
RequireAdmin();

$start = 0;
$showby = 10;

if (isset($_REQUEST["showby"]))
	$showby = intval($_REQUEST["showby"]);
if (!empty($_REQUEST["page"]))
	$start = (intval($_REQUEST["page"])-1) * $showby;

$sql = " FROM {$DB_PREFIX}vw_products WHERE 1=1";

$all_count = DbQuery("SELECT COUNT(*)" . $sql);
$blocked_count = DbQuery("SELECT COUNT(*)" . $sql . " AND active='0'");

if (!empty($_REQUEST["state"]))
{
	if ($_REQUEST["state"] == "inactive")
		$sql .= " AND active='0'";
}

$count = DbQuery("SELECT COUNT(*)" . $sql);

$objects = DbQuery("SELECT * $sql ORDER BY fullname LIMIT $start, $showby");

if (!$objects)
	$objects = array();

?>

<input type="hidden" id="totalCount" value="<?php echo $count; ?>" />
<table id="listTbl">
<tr>
	<th colspan="2"><?php echo PR_NAME_TXT; ?></th>
</tr>
<?php for ($i = 0; $i < count($objects); $i++) {
	$obj = $objects[$i];
	$obj["fullname"] = htmlspecialchars($obj["fullname"]);
	$ismode = $obj["parentid"]!="";
	$lastmode = ($i+1 < count($objects)) && ($objects[$i+1]["parentid"]=="");
?>
<tr>
	<?php if ($ismode) {?><td class="<?php echo ($lastmode?"emptylast":"empty"); ?>"></td><?php } ?>
	<td <?php echo ($ismode?"":"colspan=2"); ?> class="<?php echo ($obj["active"]?"":"blocked"); ?>"><?php echo $obj["fullname"]; ?>
		<br/>
		<div class="listItemActions">
			<?php if ($obj["parentid"] == "") { ?>
            <a class="editLink" href="#" onclick="return loadcontent('product/edit/<?php echo $obj["id"]; ?>')"><?php echo EDIT_TXT; ?></a>
            <a class="editLink" href="#" onclick="return loadcontent('product/mode/<?php echo $obj["id"]; ?>')"><?php echo PR_MODE_TXT; ?></a>
			<a class="editLink" href="#" onclick="return loadcontent('license/?filter=<?php echo urlencode($obj["fullname"]); ?>')"><?php echo PR_LICS_TXT; ?></a>
            <a class="copyLink" href="product_export.php?id=<?php echo $obj["id"]; ?>"><?php echo PR_EXPORT_TXT; ?></a>
            <?php } else { ?>
            <a class="editLink" href="#" onclick="return loadcontent('product/mode/<?php echo $obj["id"]; ?>')"><?php echo EDIT_TXT; ?></a>
			<a class="editLink" href="#" onclick="return loadcontent('license/?filter=<?php echo urlencode($obj["fullname"]); ?>')"><?php echo PR_LICS_TXT; ?></a>
            <?php } ?>
			<a class="copyLink tableLink" href="#product/copyurl/<?php echo $obj["id"]; ?>"><?php echo PR_KEYGEN_TXT; ?></a>
			<a class="deleteLink tableLink" href="#product/delete/<?php echo $obj["id"]; ?>"><?php echo DELETE_TXT; ?></a>
		</div>
	</td>
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