<div class="navPanel" id="navPanelTop">
	<div><span class="totalCountTxt"></span> <?php echo ITEMS_TXT; ?></div>
	<div class="navPanelControls">
		<button class="firstBtn" onclick="updatelist(1);return false;"><span></span></button>
		<button class="prevBtn" onclick="updatelist(cur-1);return false;"><span></span></button>
		<div>
			Page <input class="curTxt" style="width:20px;text-align:center" type="text" onchange="updatelist(this.value);"/> of <span class="maxTxt"></span>
		</div>
		<button class="nextBtn" onclick="updatelist(cur+1);return false;"><span></span></button>
		<button class="lastBtn" onclick="updatelist(max);return false;"><span></span></button>
	</div>
</div>

<div id="outer_content">
	<div id="inner_content"></div>
	
	<div class="navPanel" id="navPanelBottom">
		<div><span class="totalCountTxt"></span> <?php echo ITEMS_TXT; ?></div>
		<div class="navPanelControls">
			<button class="firstBtn" onclick="updatelist(1);return false;"><span></span></button>
			<button class="prevBtn" onclick="updatelist(cur-1);return false;"><span></span></button>
			<div>
				Page <input class="curTxt" style="width:20px;text-align:center" type="text" onchange="updatelist(this.value);"/> of <span class="maxTxt"></span>
			</div>
			<button class="nextBtn" onclick="updatelist(cur+1);return false;"><span></span></button>
			<button class="lastBtn" onclick="updatelist(max);return false;"><span></span></button>
		</div>
	</div>
</div>

<script>
	var count = <?php echo $count; ?>;
	var showby = 10, cur = 0, max = 0;
	var self_hash = '<?php echo $hash; ?>';
	var full_hash = '';
	var cur_url = self_hash + '_list.php?';
	
	function updatelist(page)
	{
		if (page == undefined)
			page = cur;
		var a = parseInt(page);
		if (a > 0 && a <= max)
		{
			cur = a;
			var url = cur_url + 'page=' + cur + '&showby=' + showby;
			$('#inner_content').load(url, function(){
				if ($('#totalCount').val() != undefined)
				{
					count = parseInt($('#totalCount').val());
					max = Math.ceil(count/showby);
					if (max == 0) max = 1;
					$('.maxTxt').text(max);
					$('.totalCountTxt').text(count);
				}

				//Update nav buttons
				$('.navPanelControls').toggle(max > 1);
				
				$('.navPanel button').removeAttr('disabled');
				if (cur == 1) {
					$( ".prevBtn" ).attr("disabled", "disabled");
					$( ".firstBtn" ).attr("disabled", "disabled");
				}
				if (cur == max){
					$( ".nextBtn" ).attr("disabled", "disabled");
					$( ".lastBtn" ).attr("disabled", "disabled");
				}
			});
		}
		if (full_hash.indexOf('?') != -1)
			location.hash = full_hash + '&page=' + cur;
		else
			location.hash = full_hash + '/?page=' + cur;
		$('.curTxt').val(cur);
	}
	
	function initContent(){
		if (full_hash == '')
		{
			full_hash = location.hash;
			full_hash = full_hash.substring(0, full_hash.indexOf(self_hash) + self_hash.length);
		}
		var paramsString = location.hash.substring(location.hash.indexOf(full_hash) + full_hash.length);
		var page = paramsString.match(/page=(\d+)/);
		
		//Select rows count to fit page height
		$('#outer_content').height($('#contentDiv').height()-95-$('#footerDiv').height());
		var ic = Math.floor(($('#outer_content').height()-80) / 55);
		if (ic == 0) ic = 1;
		showby = ic;
		
		if (page)
		{
			max = Math.ceil(count/showby);
			if (max == 0) max = 1;
			$('.maxTxt').text(max);
			cur = 1;
			updatelist(page[1]);
		}
		else
		{
			max = Math.ceil(count/showby);
			if (max == 0) max = 1;
			$('.maxTxt').text(max);
			updatelist(1);	
		}
	}
</script>