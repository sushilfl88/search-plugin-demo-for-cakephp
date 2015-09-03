<?php
/**
* @author Amey Phadke
*/

App::uses('OgilvyControllerTestCase', 'Vendor/ogilvy/test/');

class OfficeSearchesControllerTest extends OgilvyControllerTestCase {
	
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
    
	function testSearchOffices(){
    	$data = array(
            'Search' => array (
    			'category' => 'office',
    			'value' => 'mumbai'
    		)
        );
        $result = $this->testAction('search/office_searches/index/',array('data' =>$data,'method' => 'post'));
    }
    
	function testAdvancedSearchOffices(){
    	$data = array(
            'Form' => array (
    			'officeName' => 'test',
    			'discipline' => 3036,
    			'disciplineid' => 3036,
    			'disciplinelabel' => 'Ogilvy & Mather',
    			'omgpractice' => array('practiceid' => 822220),
    			'practiceid' => 822220,
    			'practicelabel' => 'Corporate'
    		)
        );
        $result = $this->testAction('search/advanced/office',array('data' =>$data,'method' => 'post'));
    }
	
}