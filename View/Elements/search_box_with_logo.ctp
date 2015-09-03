<?php


	# filtered by config search and acls
	$thisSearchCategories = array();

	$first = False;

	foreach (Configure::read("SearchCategories") AS $key) {
		$value = AppConfig::$searchCategory[$key];
		if (! $this->OgAcl->$value['acl_action']()) {
			continue;
		}

		$thisSearchCategories[$key] = $value;

		if (! $first) {
			$first = $key;
		}
	}

if ($first) { 
?>
<div class="lb-find">
<?php	echo $this->Form->create('Search', array('url' => '/' . $first, 'class' => 'SearchBoxForm')); ?>
<div class="lb-select" <?php echo count($thisSearchCategories)==1?"style='display:none'":""?>>
	<a class="<?php echo $thisSearchCategories[$first]['css_class'] ?>" href="javascript:void(0);"> <?php echo $thisSearchCategories[$first]['label']; ?> </a>
	<div>
	<?php foreach($thisSearchCategories AS $key => $value) { 
	?>
		<a class="<?php echo $thisSearchCategories[$key]['css_class'] ?>" id="<?php echo $thisSearchCategories[$key]['css_class'] ?>" href="javascript:void(0);" onclick="changeOption('<?php echo $thisSearchCategories[$key]['css_class']; ?>', '<?php echo $key; ?>');"> <?php echo $thisSearchCategories[$key]['label']; ?> </a>
	<?php } ?>
	</div>
</div>
<?php 
echo $this->Form->input('category',array('type' =>'hidden', 'value' => 'user')); ?>
<?php echo $this->Form->input('value', array('label' => false)); ?>
<input type="submit" value="&#xf002;" name="">
<?php echo $this->Form->end(); ?>
</div>
<script>
	$(document).ready(function() {
		$(".lb-select a").mouseover(function(){
			$(".lb-select div").show();
		});
		$(".lb-select").mouseleave(function(){
			$(".lb-select div").hide();
		});
	    <?php 
				if(in_array($this->params['controller'], array("user_searches", "office_searches", "company_searches", "contact_searches", "wildcard_searches"))):?>
	    var tmp = $("#SearchValue").val();
	    $("#SearchValue").focus();
	    $("#SearchValue").val('');
	    $("#SearchValue").val(tmp);
    	<?php endif; ?>
		<?php if (isset($showResults) && ($showResults == 1)):?>
		if ( searchCategory != undefined )
			changeOption(css_class, searchCategory);
		<?php endif; ?>
		$("#SearchAdvancedSearchForm").attr('action', '/'+$("#SearchCategory").val());		
	});
	function changeOption(css_class, option) {
		var current_css_class = $(".lb-select a").attr("class");
		$(".lb-select a:first").removeClass(current_css_class);
		$(".lb-select a:first").addClass(css_class);
		var selected_option = $('#'+css_class).text();
		$(".lb-select a:first").text(selected_option);
		$("#SearchCategory").val(option);
		$("#SearchValue").focus();
		$(".lb-select div").hide();
		$(".lb-select a:first").mouseover(function(){
			$(".lb-select div").show();
		});	
		$(".SearchBoxForm").attr('action', '/'+option); 		
	}
</script>
<?php } ?>
