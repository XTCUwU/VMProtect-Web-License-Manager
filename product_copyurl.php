<?php
require_once "include/login.inc.php";
require_once "include/product.inc.php";
require_once "include/keygen_keys.inc.php";
RequireAdmin();

if (isset($_REQUEST["p"]))
	$obj = Product::FromDb($_REQUEST["p"]);
else 
if (isset($_REQUEST["id"]))
	$obj = Product::FromDb($_REQUEST["id"]);
	
$base_url = "http://" . $_SERVER["SERVER_NAME"] . substr($_SERVER["REQUEST_URI"], 0, strrpos($_SERVER["REQUEST_URI"], "/"));

?>

<h1><?php echo PR_KEYGEN_TXT; ?></h1>
<div class="formTbl">
	<?php echo PR_REG_TXT; ?>: <select id="registratorCombo">
	<?php
	foreach ($KEYGEN_KEYS as $reg => $keys)
	{
		echo "<option value=\"{$reg}\">{$reg}</option>";
		$urls[$reg] = "{$base_url}/keygen.php?productid={$obj->id}";
		foreach ($keys as $k => $v)
			$urls[$reg] .= "&{$k}={$v}";
	}
	?>
	</select>
	<br /><br />
	<input type="text" id="urlText" readonly="readonly" style="width:99%" />
	<br /><br />
	<div id="zeroHolder">
        <a id="copyBtn" class="greenBtn"><span><?php echo COPYCPB_TXT; ?></span><em></em></a>
	</div>
	<button class="cancelBtn" style="margin-left: 5px" onclick="closeTableContent()"><?php echo CANCEL_TXT; ?></button>
</div>
<script type="text/javascript" src="js/ZeroClipboard.js"></script>
<script>
	var urls = new Object();
	<?php
		foreach ($urls as $reg => $url)
		echo "urls['{$reg}'] = '{$url}';";
	?>

	function initTableContent(){
		if (window.clipboardData == undefined)
		{
			ZeroClipboard.setMoviePath('js/ZeroClipboard.swf');
			var clip = new ZeroClipboard.Client();
			clip.setHandCursor(false);
			clip.addEventListener( 'mouseDown', function(client){
				client.setText($('#urlText').val());
			});
			clip.addEventListener( 'mouseUp', function(client) {
				closeTableContent();
			});
			clip.glue('copyBtn', 'zeroHolder');
		}
		else
		{
			//for Internet Explorer
			$('#copyBtn').click(function(){
				window.clipboardData.setData('Text', $('#urlText').val());
				closeTableContent();
			});
		}
		
		$('#registratorCombo').change(function(){
			$('#urlText').val(urls[this.value]);
		});
		$('#registratorCombo').change();
	}
</script>

