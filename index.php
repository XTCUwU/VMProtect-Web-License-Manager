<?php
require_once "include/login.inc.php";
require_once "include/lang.inc.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>VMProtect Web License Manager</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" type="text/css"/>
	<link rel="stylesheet" href="css/jquery-ui.css" type="text/css"/>
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/jquery-ui.js"></script>
	<script type="text/javascript" src="js/jquery.form.js"></script>
	<script type="text/javascript" src="js/validation.php"></script>
	<script type="text/javascript" src="js/jquery.ba-hashchange.js"></script>
	<script type="text/javascript">
		var lastHash = '';
	
		function hashtourl(hash){
			if (hash[0] == '#')
				hash = hash.substring(1);
			var urlparts = hash.split('/');
				
			//Possible routes:
			//#license
			//#license/edit
			//#license/edit/10
			//#license/?page=5
			
			if (urlparts.length == 1)
				url = urlparts[0] + '.php';
			else
			if (urlparts.length == 2)
			{
				if (urlparts[1][0] != '?')
					url = urlparts[0] + '_' + urlparts[1] + '.php';
				else
					url = urlparts[0] + '.php' + urlparts[1];
			}
			else
			if (urlparts.length == 3)
			{
				url = urlparts[0] + '_' + urlparts[1] + '.php';
				if (urlparts[2][0] == '?')
					url += urlparts[2];
				else
					url += '?p=' + urlparts[2];
			}
			return url;
		}
	
		function hashToHelpTopic(hash)
		{
			helpTopic = 'quick_start_guide.htm';
			if(hash.match(/^license(\/\?|$)/)) helpTopic = 'licenses.htm';
			if(hash.match(/^license\/edit(\/\?|$)/)) helpTopic = 'adding_a_license.htm';
			if(hash.match(/^license\/edit\/\d+(\/\?|$)/)) helpTopic = 'editing_a_license.htm';
			if(hash.match(/^license\/import(\/\?|$)/)) helpTopic = 'importing_licenses.htm';
			if(hash.match(/^activations(\/\?|$)/)) helpTopic = 'activation_codes.htm';
			if(hash.match(/^activations\/edit(\/\?|$)/)) helpTopic = 'adding_new_activation_code.htm';
			if(hash.match(/^activations\/edit\/\d+(\/\?|$)/)) helpTopic = 'editing_a_code.htm';
			if(hash.match(/^activations\/import(\/\?|$)/)) helpTopic = 'importing_codes.htm';
			if(hash.match(/^user(\/\?|$)/)) helpTopic = 'users.htm';
			if(hash.match(/^user\/edit(\/\?|$)/)) helpTopic = 'adding_a_new_user.htm';
			if(hash.match(/^user\/edit\/\d+(\/\?|$)/)) helpTopic = 'users.htm';
			if(hash.match(/^registrator(\/\?|$)/)) helpTopic = 'agents.htm';
			if(hash.match(/^registrator\/edit(\/\?|$)/)) helpTopic = 'adding_a_new_agent.htm';
			if(hash.match(/^registrator\/edit\/\d+(\/\?|$)/)) helpTopic = 'agents.htm';
			if(hash.match(/^product(\/\?|$)/)) helpTopic = 'products.htm';
			if(hash.match(/^product\/import(\/\?|$)/)) helpTopic = 'importingexporting_a_product.htm';
			if(hash.match(/^product\/edit(\/\?|$)/)) helpTopic = 'adding_a_product.htm';
			if(hash.match(/^product\/edit\/\d+(\/\?|$)/)) helpTopic = 'editingdeactivating_a_product.htm';
			if(hash.match(/^product\/mode(\/\?|$)/)) helpTopic = 'adding_a_product_mode.htm';
			if(hash.match(/^product\/mode\/\d+(\/\?|$)/)) helpTopic = 'adding_a_product_mode.htm';
			return helpTopic;
		}
		function loadcontent(hash, target, changehash)
		{
			if (hash == undefined)
				hash = location.hash;
			if (changehash == undefined)
				changehash = true;
			if (hash[0] == '#')
				hash = hash.substring(1);
			if (target == undefined)
			{
				target = '#contentDiv';
				$('a#helpLnk').attr('href', 'help/index.htm?' + hashToHelpTopic(hash));
			}
			if (hash != '')
			{
				url = hashtourl(hash);
				
				//check hash safety
				if (!url.match(/^[a-z0-9_-]*\.php(\?.*)?$/)){
					location.hash = '';
					location.reload();
					return;
				}

				if (location.hash.match(/^#[^\/]+$/) || location.hash.match(/^#[^\/]+\/\?/))
					lastHash = location.hash;
				
				$(target).empty();
				//Delete init functions to allow Ajax create new ones
				initContent = undefined;
				initTableContent = undefined;
				customValidate = undefined;
				$(target).load(url, function(response, status, xhr) {
					if (status == 'success')
					{
						if (response.indexOf('initContent()') != -1 && typeof(initContent)=='function')
							initContent();
							
						if (target == '#inTableContent'){
							$('#inTableContentRow').show();
							if (response.indexOf('initTableContent()') != -1 && typeof(initTableContent)=='function')
								initTableContent();
						}
					}
				});
			}
			else
				$(target).text('Not implemented');
			if (changehash)
				location.hash = '#' + hash;

			//Update menu styles
			var top_hash = location.hash;
			if (top_hash.indexOf('/') != -1)
				top_hash = top_hash.substr(0, location.hash.indexOf('/'));
			$('.menuItem1 a').removeClass('active');
			$('.menuItem1 a[href="' + top_hash + '"]').addClass('active');

			return false;
		}
		
		function loadTableContent(){
			//delete another row if exists
			$('#inTableContentRow').remove();
			var row = $(this).parents('tr');
			row.after('<tr id="inTableContentRow" style="display:none;"><td colspan="100"><a id="closeInTableContent" href="#" onclick="return closeTableContent()"/><div id="inTableContent"></div><div id="bottom" /></td></tr>');
			loadcontent($(this).attr('href'), '#inTableContent', false);
			return false;
		}
		
		function closeTableContent(){
			$('#inTableContentRow').remove();
			return false;
		}
		
		function loadScript(){
			var url = hashtourl($(this).attr('href'));
			$.getScript(url);
			return false;
		}
		
		function loadlastcontent(){
			if (lastHash != '')
				loadcontent(lastHash);
			else
			{
				var m = location.hash.match(/^(#[^/]*)/);
				loadcontent(m[1]);
			}
			return false;
		}
		
		function lockButtons(lock){
			if (lock)
				$('button').attr('disabled', 'disabled');
			else
				$('button').removeAttr('disabled');
		}
		
		function saveForm(type){
			if (type == undefined)
				type = 'html';
			if (validateForm())
			{
				$('form').ajaxSubmit({
					type: 'POST',
					dataType: type,
					success: function(data){
						lockButtons(false);
						if (type == 'html'){
							if (data != '')
								alert(data);
							else
								loadlastcontent();
						}
					},
					error: function(xhr, ajaxOptions, thrownError){
						lockButtons(false);
						alert(xhr.responseText);
					}
				});
				lockButtons(true);
			}
			return false;
		}
	
		$(function() {
			$('.menu a').click(function () {
				$('.menu a.active').removeClass('active');
				$(this).addClass('active');
				if ($(this).parent().hasClass('menuItem2'))
				{
					//Activate top menu item
					var idx = $(this).parent().index();
					$(this).parent().parent().find('li:lt(' + idx + ')').filter('.menuItem1:last').children('a').addClass('active');
				}
				loadcontent(this.hash);
			});
			
			//Attach global Ajax events
			$('#log').ajaxStart(function() {
				$(this).show();
			});
			$('#log').ajaxStop(function() {
				$(this).hide();
			});
			
			if (location.hash == '' || location.hash == '#')
				location.hash = '#license';
			
			$(window).hashchange(function() {
				//eat page->nopage history item
				var hash = location.hash;
				if(lastHash.indexOf(hash + '/?page=') != -1)
				{
					history.go(-1);
					return;
				}
			//Restore menu item selection
				var hash = location.hash;
				if (hash.indexOf('/?') != -1)
					hash = hash.substr(0, location.hash.indexOf('/?'));
				var link = $('.menu a[href="'+ hash +'"]');
				if (link.length == 1) {
					$('.menu a.active').removeClass('active');
					link.addClass('active');
				}
				loadcontent();
			})
			// Since the event is only triggered when the hash changes, we need to trigger
			// the event now, to handle the hash the page may have loaded with.
			$(window).hashchange();
		});
	</script>
</head>
<body>
	<div id="menuDiv">
		<a href="http://www.vmpsoft.com/" target="_blank" title="Visit site"><img id="logo" src="images/logo.png" /></a>
		<ul class="menu">
			<li class="menuItem1"><a href="#license"><?php echo M_LICS_TXT; ?></a></li>
			<li class="menuItem2"><a href="#license/edit"><?php echo M_NEWLIC_TXT; ?></a></li>
			<li class="menuItem2"><a href="#license/import"><?php echo M_IMPORTLIC_TXT; ?></a></li>
			<li class="menuItem1"><a href="#activations"><?php echo M_ACTS_TXT; ?></a></li>
			<li class="menuItem2"><a href="#activations/edit"><?php echo M_NEWACT_TXT; ?></a></li>
			<li class="menuItem2"><a href="#activations/import"><?php echo M_IMPORTACT_TXT; ?></a></li>
			<?php if (isset($_SESSION["cur_user"]) && $_SESSION["cur_user"]->isadmin) { ?>
			<li class="menuItem1"><a href="#product"><?php echo M_PRODS_TXT; ?></a></li>
			<li class="menuItem2"><a href="#product/edit"><?php echo M_NEWPROD_TXT; ?></a></li>
			<li class="menuItem2"><a href="#product/mode"><?php echo M_NEWMODE_TXT; ?></a></li>
			<li class="menuItem2"><a href="#product/import"><?php echo M_IMPORTPROD_TXT; ?></a></li>
			<li class="menuItem1"><a href="#registrator"><?php echo M_REGS_TXT; ?></a></li>
			<li class="menuItem2"><a href="#registrator/edit"><?php echo M_NEWREG_TXT; ?></a></li>
			<li class="menuItem1"><a href="#user"><?php echo M_USERS_TXT; ?></a></li>
			<li class="menuItem2"><a href="#user/edit"><?php echo M_NEWUSER_TXT; ?></a></li>
			<?php } ?>
		</ul>
	</div>
	<div id="mainDiv">
		<div id="headerDiv"><?php include "header.inc.php"; ?></div>
		<div id="contentDiv"></div>
		<div id="footerDiv"><?php include "footer.inc.php"; ?></div>
	</div>
	<div id="log">Loading</div>
</body>
</html>
