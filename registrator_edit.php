<?php
require_once "include/login.inc.php";
require_once "include/registrator.inc.php";
RequireAdmin();

if (isset($_REQUEST["p"]))
	$obj = Registrator::FromDb($_REQUEST["p"]);
else 
if (isset($_REQUEST["id"]))
	$obj = Registrator::FromDb($_REQUEST["id"]);

if (!isset($obj) || $obj===FALSE)
	$obj = new Registrator();
	
//POST
if ($_SERVER["REQUEST_METHOD"]=="POST")
{
	if (empty($_POST["active"]))
        $_POST["active"] = $obj->id == 0;
    ObjectFromArray($obj, $_POST);

    if ($obj->authmode == 0)
    {
        $obj->login = "";
        $obj->password = "";
    }
    else
    {
        $obj->ipranges = "";
    }

	if (!$obj->Save())
		die("alert('" . V_ERROR_TXT . ": " . str_replace("'", "\\'", mysqli_error($mysqli_link)) . "');");
	
	echo "loadcontent('registrator');";
	exit;
}

//GET
$title = ($obj->id==0 ? M_NEWREG_TXT : H_EDITREG_TXT);

?>

<div id="actions">
	<div id="help">
		<?php echo HELP_NEW_REGISTRATOR; ?>
	</div>
	<h1><?php echo $title; ?></h1>
	<button class="cancelBtn" style="float:right" onclick="$('#help').toggle();"><?php echo HELP_TXT; ?></button>
</div>

<div class="formDiv">
<form id="editForm" action="registrator_edit.php" method="POST">
	<input type="hidden" id="id" name="id" value="<?php echo $obj->id; ?>" />
	<table class="formTbl">
		<tr><th>
			<label for="name"><?php echo R_NAME_TXT; ?></label>
		</th><td>
			<input type="text" name="name" id="name" class="required" value="<?php echo htmlspecialchars($obj->name); ?>" />
		</td></tr>
        <tr><th>
            <label for="name"><?php echo R_AUTHMODE_TXT; ?></label>
        </th><td>
            <select name="authmode" id="authmode" onchange="authmodeChanged()">
                <option value="0"><?php echo R_AUTHMODE_IP_TXT; ?></option>
                <option value="1"><?php echo R_AUTHMODE_LOGIN_TXT; ?></option>
            </select>
        </td></tr>
		<tr class="authmode0"><td colspan="2" style="padding: 0">
		<table>
			<tr><th style="width:223px">
				<label for="ip1"><?php echo R_START_TXT; ?></label>
			</th><td style="width:230px">
				<input type="hidden" name="startip" id="startip" value="<?php echo property_exists($obj, 'startip') ? $obj->startip : ''; ?>" />
				<div id="startipHolder" class="ipeditor">
					<input type="text" id="startip1" size="3" maxlength="3" /> . 
					<input type="text" id="startip2" size="3" maxlength="3" /> . 
					<input type="text" id="startip3" size="3" maxlength="3" /> . 
					<input type="text" id="startip4" size="3" maxlength="3" />
				</div>
			</td>
			<td rowspan="2" style="width:auto;padding:0;text-align: left">
                <a class="greenBtn" onclick="return newIpRange()"><span><?php echo R_ADD_TXT; ?></span><em></em></a>
			</td>
			</tr>
			<tr><th style="width:223px">
				<label for="ip1"><?php echo R_END_TXT; ?></label>
			</th><td style="width:230px">
				<input type="hidden" name="endip" id="endip" value="<?php echo property_exists($obj, 'endip') ? $obj->endip : ''; ?>" />
				<div id="endipHolder" class="ipeditor">
					<input type="text" id="endip1" size="3" maxlength="3" /> . 
					<input type="text" id="endip2" size="3" maxlength="3" /> . 
					<input type="text" id="endip3" size="3" maxlength="3" /> . 
					<input type="text" id="endip4" size="3" maxlength="3" />
				</div>
			</td>
			</tr>
		</table></td></tr>
		<tr class="authmode0"><th>
		</th><td>
			<input type="hidden" name="ipranges" id="ipranges" value="<?php echo htmlspecialchars($obj->ipranges); ?>" />
			<table id="ipRangesContainer"></table>
		</td></tr>
        <tr class="authmode1"><th>
            <label for="name"><?php echo R_LOGIN_TXT; ?></label>
        </th><td>
            <input type="text" name="login" id="login" class="required" value="<?php echo htmlspecialchars($obj->login); ?>" />
        </td></tr>
        <tr class="authmode1"><th>
            <label for="name"><?php echo R_PASSWORD_TXT; ?></label>
        </th><td>
            <input type="text" name="password" id="password" class="required" value="<?php echo htmlspecialchars($obj->password); ?>" />
        </td></tr>
		<?php if ($obj->id!=0) { ?>
		<tr><th>
			<label for="active"><?php echo R_ACTIVE_TXT; ?></label>
		</th><td>
			<input type="checkbox" id="active" name="active" class="checkbox" value="1" />
		</td></tr>
		<?php } ?>
		<tr>
			<td></td>
			<td style="padding-top:10px">
                <a class="greenBtn" onclick="return saveRegistrator()"><span><?php echo SAVE_TXT; ?></span><em></em></a>
			</td>	
		</tr>
	</table>
</form>
</div>

<script>
	var isNew = <?php echo ($obj->id==0?"true":"false"); ?>;
	var isValidIp = true;
	
	function validateIpEditor(id, req){
		var ip1 = parseInt($('#' + id + '1').val());
		var ip2 = parseInt($('#' + id + '2').val());
		var ip3 = parseInt($('#' + id + '3').val());
		var ip4 = parseInt($('#' + id + '4').val());
		
		var allNaN = isNaN(ip1) && isNaN(ip2) && isNaN(ip3) && isNaN(ip4);
		var someNaN = isNaN(ip1) || isNaN(ip2) || isNaN(ip3) || isNaN(ip4);
		
		if (req && allNaN)
		{
			$('#' + id + 'Holder').after('<label class="error"><?php echo V_REQ_TXT; ?></label>');
			isValidIp = false;
		}
		else
		if (!allNaN && someNaN)
		{
			$('#' + id + 'Holder').after('<label class="error"><?php echo R_EIP_TXT; ?></label>');
			isValidIp = false;
		}
		else
		if (ip1 > 255 || ip2 > 255 || ip3 > 255 || ip4 > 255)
		{
			$('#' + id + 'Holder').after('<label class="error"><?php echo R_EOCTET_TXT; ?></label>');
			isValidIp = false;
		}
	}
	
	function validateIpEditors(){
		isValidIp = true;
		$('form input').removeClass('errorField');
		$('label.error').remove();
		
		validateIpEditor('startip', true);
		validateIpEditor('endip', false);
		
		//check that end ip is greater than start ip
		if (isValidIp && $('#endip1').val() != "")
		{
			if (parseInt($('#startip1').val()) > parseInt($('#endip1').val()))
				isValidIp = false;
			else if (parseInt($('#startip2').val()) > parseInt($('#endip2').val()))
				isValidIp = false;
			else if (parseInt($('#startip3').val()) > parseInt($('#endip3').val()))
				isValidIp = false;
			else if (parseInt($('#startip4').val()) > parseInt($('#endip4').val()))
				isValidIp = false;
				
			if (!isValidIp)
				$('#endipHolder').after('<label class="error"><?php echo R_EENDIP_TXT; ?></label>');
		}
		return isValidIp;
	}
	
	function newIpRange(){
		if (validateIpEditors())
		{
			joinIp('startip');
			joinIp('endip');
			var value = $('#startip').val();
			if ($('#endip').val() != '')
				value += '-' + $('#endip').val();
			addIpRange(value);
		}
		return false;
	}
	
	function customValidate(){
        if ($('#authmode').val() == 0 && !$('.iprange').length)
		{
			isValid = false;
			alert('<?php echo R_EIPS_TXT; ?>');
		}
	}
	
	function saveRegistrator(){
		if (validateForm())
		{
			joinIpRanges();
			saveForm('script');
		}
		return false;
	}
	
	function joinIpRanges(){
		var ipranges = '';
		var values = $('.iprange');
		if (values.length)
			for (i = 0; i < values.length; i++)
			{
				if (ipranges != '')
					ipranges += ',';
				ipranges += $(values[i]).html();		
			}
		$('#ipranges').val(ipranges);
	}
	
	function splitIpRanges(){
		var ranges = $('#ipranges').val().split(',');
		for (i=0; i<ranges.length; i++)
			if (ranges[i] != '')
				addIpRange(ranges[i]);
	}
	
	function joinIp(id){
		if ($('#' + id + '1').val() != "")
		{
			var newip = $('#' + id + '1').val() + '.' +
						$('#' + id + '2').val() + '.' +
						$('#' + id + '3').val() + '.' +
						$('#' + id + '4').val();
			$('#' + id).val(newip);
		}
		else
			$('#' + id).val('');
	}
	
	function addIpRange(val){
		var html = '<tr><td class="iprange">' + val + '</td><td style="text-align:left"><button class="deleteBtn" onclick="$(this).parent().parent().remove()"><?php echo DELETE_TXT; ?></button></td></tr>';
		$('#ipRangesContainer').append(html);
		
		//Clear all
		$('.ipeditor > input').val('');
		return false;
	}

    function authmodeChanged()
    {
        if ($('#authmode').val() == 0)
        {
            $('.authmode1 input').attr('disabled', 'disabled');
            $('.authmode1').hide();
            $('.authmode0').show();
        }
        else
        {
            $('.authmode0').hide();
            $('.authmode1 input').removeAttr('disabled');
            $('.authmode1').show();
        }
    }
	
	function initContent(){
        $('#authmode').val('<?php echo $obj->authmode; ?>');
        authmodeChanged();
		splitIpRanges();
		$('#active').prop('checked', <?php echo ($obj->active ? "true" : "false"); ?>);
		$('.ipeditor > input').keyup(function(event){
            // Every IP-address octet typed in different input fields, each of them allow enter digits only.
            this.value = this.value.replace(/[^0-9]/g,'');
			if (parseInt(this.value) > 25)
			{
				var m = this.id.match(/(.*)(\d)/);
				$('#' + m[1] + (parseInt(m[2])+1)).focus();
			}
		});
	}
</script>
