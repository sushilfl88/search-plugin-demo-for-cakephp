<?php
// debug($css_class); echo '<hr />'; exit;//TODO delete?>

<?php if ($showResults == 1) { 
		echo $this->Html->script("jquery/jquery.dateFormat");
		echo $this->Html->script("soap_dataTable");
?>
<div class="head-bar hd-bar clearfix">
	<div class="pg-title-bar adm-field-hd">
		<span class="searchlabel">Search <?php echo $search_label; ?>:</span>
		<span class="searchcontent">&nbsp;</span>
		<span class="searchcount">&nbsp;</span>
	</div>
</div>
<div class="search-index">
	<div class='sectionhd'></div>
</div>
<script>
var fields=<?php echo (isset($fields))?$fields:"''";?>;
var value=<?php echo (isset($searchData))?$searchData:"''";?>;
var searchCategory = '<?php echo $search_category; ?>';
var css_class = '<?php echo $css_class; ?>';
var search_label = "<?php echo $search_label; ?>";
var search_title = "<?php echo (isset($searchTitle)&&$searchTitle!='')?$searchTitle:"";?>";
var search_value = <?php echo (isset($searchValue)&&$searchValue!='')?$searchValue:"''";?>;
var canExportCSV = '<?php echo (isset($canExportCSV))?$canExportCSV:'';?>';
	$(document).ready(function() {
	    $.each(search_value, function(i, item) {
    	   $('.searchcontent').append('<span>'+i+': '+item+'</span>');
        }); 
	});	
</script>
<?php } else { ?>
<script>
	$(document).ready(function() {
	
    <?php 
	    switch ($search_type):
			case 'user':
				$bodyClass = "asearch-employee";
				break;
			case 'office':
				$bodyClass = "asearch-office";
				break;
			case 'company':
				$bodyClass = "asearch-company";
				break;
			case 'company_contact':
				$bodyClass = "asearch-contact";
				break;
			
			default:
				$bodyClass = "";
				break;
		endswitch;
    ?>
		$('body').addClass("<?php echo $bodyClass; ?>");
	});
</script>
<div class="ibox ibox-form">
 	<div>
    	<hgroup>
      		<header>Advanced <?php echo $search_label; ?> Search</header>
    	</hgroup>
    	<?php 
    		switch ($search_type) {
    			case 'office':
    				$form = $this->FormBuilder->create(FormNames::P_ADVANCEDOFFICESEARCH, array("view" => "AdvancedOfficeSearchFormsView", "url" => "/search/advanced/office"));
    				break;
    			case 'user':
    				$form = $this->FormBuilder->create(FormNames::P_ADVANCEDUSERSEARCH, array("view" => "AdvancedUserSearchFormsView", "url" => "/search/advanced/user"));
    				break;
    			case 'company':
    				$form = $this->FormBuilder->create(FormNames::P_ADVANCEDCOMPANYSEARCH, array("view" => "AdvancedCompanySearchFormsView", "url" => "/search/advanced/company"));
    				break;
    			case 'company_contact':
    				$form = $this->FormBuilder->create(FormNames::P_ADVANCEDCOMPANYCONTACTSEARCH, array("view" => "AdvancedCompanyContactSearchFormsView", "url" => "/search/advanced/company_contact"));
    				break;
    		}
    		
    		$form->render();
    		//return;
    	?>
  	</div>
</div>
 <?php } 
 if($search_type=="office" || $search_type=="user")
 {

 	?>
 	<script>
 	$(document).ready(function() {
 		$('#FormAddress').attr('readonly',true);
 		});
 	
 	</script>
 	<?php
 }
 ?>