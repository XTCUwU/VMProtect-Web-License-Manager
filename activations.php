<?php
require_once "include/login.inc.php";

$hash = "activations";

$count = DbQuery("SELECT COUNT(*) FROM {$DB_PREFIX}activations");

?>

<div id="actions">
	<h1><?php echo M_ACTS_TXT; ?></h1>
	<a href="#activations/edit" class="addLink" onclick="return loadcontent('activations/edit')"><?php echo ADD_TXT; ?></a>
		
	<div id="filterDiv">
		<input type="text" id="filterTxt" />
		<span class="search"></span>
	</div>
</div>

<div id="filters">
	<a href="#" class="active" onclick="return addStateFilter('', this)"><?php echo ALL_TXT; ?></a>
	(<span class="allCountTxt">0</span>)&nbsp;
	<a href="#" onclick="return addStateFilter('blocked', this)"><?php echo BLOCKED_TXT; ?></a>
	(<span class="blockedCountTxt">0</span>)
</div>

<?php
include "default_lister.php";
?>

<script type="text/javascript">
var keyTimer;

$('#filterTxt').keyup(function (){
    clearTimeout(keyTimer);
    keyTimer = setTimeout(runFilter, 500);
    $('#filterClr').css('visibility', $('#filterTxt').val()!=''?'visible':'hidden');
});

function runFilter(){
    if ($('#filterTxt').val()=='')
        clearFilter();
    else
    {
        cur_url = 'activations_list.php?filter=' + escape($('#filterTxt').val()) + '&';
        full_hash = '#activations/?filter=' + escape($('#filterTxt').val());
		resetStateFilters();
        updatelist(1);
    }
}

function clearFilter(){
    $('#filterTxt').val('');
    $('#filterClr').css('visibility', 'hidden');
    cur_url = 'activations_list.php?';
    full_hash = '#activations';
	resetStateFilters();
    updatelist(1);
    return false;
}

function resetStateFilters(){
	$('#filters a.active').removeClass('active');
	$('#filters a:contains("<?php echo ALL_TXT; ?>")').addClass('active');
}

function addStateFilter(sf, sender){
	$('#filters a.active').removeClass('active');
	$(sender).addClass('active');
	
	cur_url = cur_url.replace(/state=[^&\/]*&/i, '');
	full_hash = full_hash.replace(/(\/\?)?(&)?state=[^&\/]*/i, '');
	if (sf != '')
	{
		cur_url += 'state=' + sf + '&';
		if (full_hash.indexOf('/?') != -1)
			full_hash += '&state=' + sf;
		else
			full_hash += '/?' + 'state=' + sf;
	}	
		
	updatelist(1);	
	return false;
}

var m = location.hash.match(/(^#.*filter=([^&\/]*))/);
if (m)
{
    full_hash = m[1];
    cur_url += 'filter=' + m[2] + '&';
    $('#filterTxt').val(unescape(m[2]));
    $('#filterClr').css('visibility', 'visible');
}

m = location.hash.match(/(^#.*state=([^&\/]*))/);
if (m)
{
    full_hash = m[1];
    cur_url += 'state=' + m[2] + '&';
	$('#filters a.active').removeClass('active');
	$('#filters a[onclick*="addStateFilter(\'' + m[2] + '\'"]').addClass('active');
}


</script>