<?php
require_once "include/login.inc.php";
require_once "include/product.inc.php";
RequireAdmin();

if (isset($_REQUEST["p"]))
	$obj = Product::FromDb($_REQUEST["p"]);
else 
if (isset($_REQUEST["id"]))
	$obj = Product::FromDb($_REQUEST["id"]);

if (!isset($obj) || $obj===FALSE)
	$obj = new Product();

if ($obj->IsMode())
    die("Can't edit product mode here.");

$defpatterns = array("'####-####-####'", "'####-####-####-####'", "'#####-#####-#####-#####'");
$res = mysqli_query($mysqli_link, "SELECT DISTINCT act_pattern FROM {$DB_PREFIX}products WHERE act_pattern NOT IN (" . implode(",", $defpatterns) . ") ORDER BY act_pattern");
$patterns = array();
while (NULL != ($row = mysqli_fetch_row($res)))
	$patterns[] = "'{$row[0]}'";
mysqli_free_result($res);
$patterns = array_merge($patterns, $defpatterns);

$isNew = $obj->id == 0;

if ($_SERVER["REQUEST_METHOD"]=="POST")
{
	if (empty($_POST["active"]))
        $_POST["active"] = $isNew;

    //Protect unchangable fields of changing
    if (!$isNew)
    {
        unset($_POST["algorithm"]);
        unset($_POST["bits"]);
    }
	ObjectFromArray($obj, $_POST);

    if (DbQuery("SELECT * FROM {$DB_PREFIX}products WHERE name='{$obj->name}' AND id!='{$obj->id}'"))
		die("addError('#name', '" . PR_ENAME_TXT . "');");

	if (!$obj->Save())
		die("alert('" . V_ERROR_TXT . ": " . str_replace("'", "\\'", mysqli_error($mysqli_link)) . "');");

	echo "loadcontent('product');";
	exit;
}

$title = $isNew ? M_NEWPROD_TXT : H_EDITPROD_TXT;
	
?>
<div id="actions">
	<div id="help">
		<?php echo HELP_NEW_PRODUCT; ?>
	</div>
	<h1><?php echo $title; ?></h1>
	<button class="cancelBtn" style="float:right" onclick="$('#help').toggle();"><?php echo HELP_TXT; ?></button>
</div>

<div class="formDiv">
<form id="editForm" action="product_edit.php" method="POST">
	<input type="hidden" id="id" name="id" value="<?php echo $obj->id; ?>" />
	<table class="formTbl">
		<tr><th>
			<label for="name"><?php echo PR_NAME_TXT; ?></label>
		</th><td>
			<input type="text" name="name" id="name" class="required" value="<?php echo htmlspecialchars($obj->name); ?>" />
		</td></tr>
		<tr><th>
			<label for="algorithm"><?php echo PR_ALG_TXT; ?></label>
		</th><td>
			<select name="algorithm" id="algorithm" class="required noedit">
				<option value="RSA">RSA</option>
			</select>
		</td></tr>
		<tr><th>
			<label for="bits"><?php echo PR_BITS_TXT; ?></label>
		</th><td>
			<select name="bits" id="bits" class="required noedit">
				<?php for ($i=1024; $i<=4096; $i+=512 ){?>
				<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
				<?php } ?>
			</select>
		</td></tr>
		<tr><th>
			<label for="uses_act"><?php echo PR_KGMODE_TXT; ?></label>
		</th><td>
			<select name="uses_act" id="uses_act" class="required" onchange="usesactChanged()">
				<option value="0"><?php echo PR_KGSN_TXT; ?></option>
				<option value="1"><?php echo PR_KGAC_TXT; ?></option>
			</select>
		</td></tr>
		<tr class="actrow"><th>
			<label for="act_pattern"><?php echo PR_ACTPAT_TXT; ?></label>
		</th><td>
			<input type="text" name="act_pattern" id="act_pattern" class="required" style="border-top-right-radius: 0; border-bottom-right-radius: 0; width: 300px" value="<?php echo htmlspecialchars($obj->act_pattern); ?>" />
		</td></tr>
		<tr class="actrow"><th>
			<label for="act_extracount"><?php echo PR_ACTEX_TXT; ?></label>
		</th><td>
			<input type="text" name="act_extracount" id="act_extracount" class="required numeric" value="<?php echo htmlspecialchars($obj->act_extracount); ?>" />
		</td></tr>
		<?php if ($obj->id!=0) { ?>
		<tr><th>
			<label for="active"><?php echo PR_ACTIVE_TXT; ?></label>
		</th><td>
			<input type="checkbox" id="active" name="active" class="checkbox" value="1" />
		</td></tr>
		<?php } ?>
		<tr>
			<td></td>
			<td style="padding-top:10px">
                <a class="greenBtn" onclick="return saveForm('script')"><span><?php echo SAVE_TXT; ?></span><em></em></a>
			</td>
		</tr>
	</table>
</form>
</div>

<script>
	var isNew = <?php echo ($isNew ? "true" : "false"); ?>;

	function customValidate(){
		if ($('#act_pattern').is(':visible'))
		{
			var p = $('#act_pattern').val();
			if (!p.match(/^[A-Z0-9-#]*$/))
			{
				addError('#act_pattern_btn', 'Pattern has illegal chars.');
				isValid = false;
			}
			else
			if (p.match(/#/g) == null || p.match(/#/g).length < 10){
				addError('#act_pattern_btn', 'Pattern must have at least 10 random chars.');
				isValid = false;
			}
		}
	}

	function usesactChanged(){
		if ($('#uses_act').val() == 0)
			$('.actrow').hide();
		else
			$('.actrow').show();
	}

	function initContent(){
		if (!isNew){
			$('.noedit').attr('disabled', 'disabled');
			$('#active').prop('checked', <?php echo ($obj->active ? "true" : "false"); ?>);
		}
		$('#algorithm').val('<?php echo $obj->algorithm; ?>');
		$('#bits').val('<?php echo $obj->bits; ?>');
		$('#uses_act').val('<?php echo $obj->uses_act; ?>');
		$('#act_pattern').autocomplete({minLength: 0, source: [<?php echo implode(",", $patterns); ?>]});
		var input = $('#act_pattern');
		$("<button id='act_pattern_btn' type='button'>&nbsp;</button>")
			.attr( "tabIndex", -1 )
			.attr( "title", "Show All Items" )
			.insertAfter( input )
			.button({
				icons: {
					primary: "ui-icon-triangle-1-s"
				},
				text: false
			})
			.removeClass('ui-corner-all').addClass('ui-button-icon')
			.css('border-top-right-radius', '3px').css('border-bottom-right-radius', '3px')
			.css('width', '20px').css('height', '30px')
			.position({
				my: "left center",
				at: "right center",
				of: input,
				offset: "-1 0"
			})
			.click(function() {
				// close if already visible
				if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
					input.autocomplete( "close" );
					return;
				}
				// pass empty string as value to search for, displaying all results
				input.autocomplete( "search", "" );
				input.focus();
			});
		usesactChanged();
	}
</script>