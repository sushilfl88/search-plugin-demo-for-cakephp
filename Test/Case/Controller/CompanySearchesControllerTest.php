<?php
/**
* @author Amey Phadke
*/

App::uses('OgilvyControllerTestCase', 'Vendor/ogilvy/test/');

class CompanySearchesControllerTest extends OgilvyControllerTestCase {
	
	function startCase(){
        echo "<h1>Starting SearchesController Test Case</h1>";

    }
 
    function endCase(){
        echo "<h1>Ending SearchesController Test Case</h1>";
    }
 
    function startTest($method){
        echo '<h3>Starting method '.$method.'</h3>';
    }
 
    function endTest($method){
        echo '<hr/>';
    }
    
	function testSearchCompanies(){
    	$data = array(
            'Search' => array (
    			'category' => 'company',
    			'value' => 'test'
    		)
        );
        $result = $this->testAction('search/company_searches/index/',array('data' =>$data,'method' => 'post'));
    }
    
	function testAdvancedSearchCompanies(){
    	$data = array(
            'Form' => array (
    			'companyName' => 'test',
    			'region' => 2013,
    			'regionLabel' => 'North America',
    			'country' => 2014,
    			'countryLabel' => 'United States',
    			'city' => 2015,
    			'cityLabel' => 'New York'
    		)
        );
        $result = $this->testAction('search/advanced/company',array('data' =>$data,'method' => 'post'));
    }
	
}