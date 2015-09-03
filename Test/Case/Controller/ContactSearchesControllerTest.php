<?php
/**
* @author Amey Phadke
*/

App::uses('OgilvyControllerTestCase', 'Vendor/ogilvy/test/');

class ContactSearchesControllerTest extends OgilvyControllerTestCase {
	
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
    
	function testSearchContacts(){
    	$data = array(
            'Search' => array (
    			'category' => 'company_contact',
    			'value' => 'AbcContact'
    		)
        );
        $result = $this->testAction('search/contact_searches/index/',array('data' =>$data,'method' => 'post'));
    }
    
	function testAdvancedSearchContacts(){
    	$data = array(
            'Form' => array (
    			'firstName' => 'Lee',
    			'lastName' => 'Test'
    		)
        );
        $result = $this->testAction('search/advanced/company_contact',array('data' =>$data,'method' => 'post'));
    }
	
}