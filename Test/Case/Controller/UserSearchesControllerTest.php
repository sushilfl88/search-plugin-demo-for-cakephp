<?php
/**
* @author Amey Phadke
*/

App::uses('OgilvyControllerTestCase', 'Vendor/ogilvy/test/');

class UserSearchesControllerTest extends OgilvyControllerTestCase {
	
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
    
	function testSearchUsers(){
    	$data = array(
            'Search' => array (
    			'category' => 'user',
    			'value' => 'test'
    		)
        );
        $result = $this->testAction('search/user_searches/index/',array('data' =>$data,'method' => 'post'));
    }
    
	function testAdvancedSearchUsers(){
    	$data = array(
            'Form' => array (
    			'givenname' => 'test',
    			'sn' => 'test'
    		)
        );
        $result = $this->testAction('search/advanced/user',array('data' =>$data,'method' => 'post'));
    }
	
}