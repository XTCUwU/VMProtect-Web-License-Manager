<?php
require_once "include/login.inc.php";
require_once "include/license.inc.php";

if (isset($_REQUEST["p"]))
	$obj = License::FromDb($_REQUEST["p"]);
else 
if (isset($_REQUEST["id"]))
	$obj = License::FromDb($_REQUEST["id"]);
	
$sn = wordwrap($obj->sn, 70, "\n", TRUE);
	
?>

<h1><?php echo LIC_COPYSN_TXT; ?></h1>
<div class="formTbl">
	<textarea id="snText" readonly="readonly" style="width:auto" cols="70" rows="5"><?php echo $sn; ?></textarea>
	<br/><br/>
	<div id="zeroHolder">
        <a id="copyBtn" class="greenBtn"><span><?php echo COPYCPB_TXT; ?></span><em></em></a>
	</div>
	<button style="margin-left: 5px" class="cancelBtn" onclick="closeTableContent()"><?php echo CANCEL_TXT; ?></button>
</div>

<script type="text/javascript" src="js/ZeroClipboard.js"></script>
<script>
	function initTableContent(){
		if (window.clipboardData == undefined)
		{
			ZeroClipboard.setMoviePath('js/ZeroClipboard.swf');
			var clip = new ZeroClipboard.Client();
			clip.setHandCursor(false);
			clip.addEventListener( 'mouseDown', function(client){
				client.setText($('#snText').text().replace(/\n/, ''));
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
				window.clipboardData.setData('Text', $('#snText').text().replace(/\n/, ''));
				closeTableContent();
			});
		}
	}
</script>
