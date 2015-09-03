<?php 

	if ($showResults == 1) {
		echo $this->Html->script("jquery/jquery.dateFormat");
		echo $this->Html->script("soap_dataTable"); 
?>
<div class="head-bar hd-bar clearfix">
	<div class="pg-title-bar adm-field-hd">
		<span class="searchlabel">Search <?php echo $search_label; ?>:</span>
		<span class="searchcontent"><?php echo $searchValue; ?></span>
		<span class="searchcount"></span>
	</div>
</div>
<div class="search-index">
	<div class='sectionhd'></div>
</div>
<?php } ?>

<!-- need to put this section of code in a js file -->
<script>
var fields=<?php echo (isset($fields))?$fields:"''";?>;
var value=<?php echo (isset($searchData))?$searchData:"''";?>;
var searchCategory = '<?php echo $search_category; ?>';
var css_class = '<?php echo $css_class; ?>';
var search_label = '<?php echo $search_label; ?>';
var search_title = "<?php echo (isset($searchTitle)&&$searchTitle!='')?$searchTitle:"";?>";
var search_value = '<?php echo (isset($searchValue)&&$searchValue!='')?$searchValue:'';?>';
var canExportCSV = '<?php echo (isset($canExportCSV))?$canExportCSV:'';?>';
</script>
