<?php
App::uses('AppController', 'Controller');
App::uses("FieldCountryAclUtils", "Lib/Utils");
class SearchAppController extends AppController {
	/**
	 * Load all the models
	 */
	public $uses = array("FormBuilder.Form", "FormBuilder.OcdPropertyDef", "Company.Company");
	/**
	 * Load all the helpers
	 */
	public $helpers = array("FormBuilder.FormBuilder");
	/**
	 * or/and to join fields. 
	 * Musr be lowercase!
	 * 
	 * @var string
	 */
	protected $_operator = 'or';

	/**
	 * Fields used for LDAP search
	 * 
	 * @var array
	 */
	protected $_searchParameters = array();
	
	/**
	 * @var array
	 */
	protected $_advancedSearchOperator = array('user' => 'and', 'office' => 'AND', 'company_contact' => 'AND','company'=>'AND');	
	/**
	 * Option to include advanced search parameters in search view.
	 */
	public $showAdvancedSearchValue = true;	
	/**
	 * @see Controller::beforeFilter()
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		
		$this->set('title_for_layout', 'Search');
	}
	
	/**
	 * This method serves simple search requests.
	 */
	public function index() {
		
		$this->layout = APPNAME.DS."home";	
		$this->_setViewParams();
		if($this->request->isPost()) {
			$this->_validateSimpleSearch();
			$value = '';
			$searchData = array();
			if ( isset($this->request->data['Search']['value'])) {
				$value = $this->request->data['Search']['value'];
			}
			if(!empty($value)) {
				$this->_setSearchFields($value);
				$searchCondition=array('conditions' => $this->_searchParameters,'operator'=>$this->_operator);//prepearing search condtion array
				$searchData=$this->_search($this->searchCategory[$this->_searchType]['plugins'], $searchCondition);
				$searchData = $this->_processSearchResults($searchData);
			}//check if the value is not empty
		
			$this->set('searchData',$searchData);
			$this->_setDisplayFields();
		}
		$this->render("Searches/index");
	} //index

	
	/**
	 * Methos serves advanced search requests.
	 * Additional search parametes may be set in $_searchParameters
	 */
	public function advanced_search() {
		$this->_setViewParams();
		if($this->request->isPost()) {
			$this->_validateAdvancedSearch();//make sure the post data is not empty
			$searchCondition = array('conditions' => $this->_searchParameters, 'operator'=>$this->_advancedSearchOperator[$this->_searchType]);//prepeare search condition array
			//To set the search results
			$searchData=$this->_search($this->searchCategory[$this->_searchType]['plugins'],$searchCondition);
			$searchData = $this->_processSearchResults($searchData);
			$this->set('searchData',$searchData);
			$this->_setDisplayFields();		
		}		
		$this->render("Searches/advanced_search");
	} //advanced_search
	
	/**
	 * 
	 * @since 4.0
	 * @param array $searchData
	 * @return String
	 */
	protected function _processSearchResults($searchData) {
		return $this->_createJsonData($searchData);
	} //_processSearchResults
	
	/**
	 * Sets view variables requered to render search form/result table
	 *
	 * @since 4.0
	 */
	protected function _setViewParams(){
		$this->set('showResults',0);
		$this->set('search_type',$this->_searchType);	
		$searchCategory = array();
		$searchCategoryCssClass = array();


		foreach (Configure::read("SearchCategories") AS $key) {
			$value = AppConfig::$searchCategory[$key];
		
			$searchCategory[$key] = $value['label'];
			$searchCategoryCssClass[$key] = $value['css_class'];
		}


		$this->set('search_category',$this->_searchType);
		//To set css class of option in search dropdown
		$this->set('css_class',$searchCategoryCssClass[$this->_searchType]);
		//To set search label on top let of results page
		$this->set('search_label',$searchCategory[$this->_searchType]);
		if($this->request->isPost()) {
			$this->layout = APPNAME.DS."default";			
			$this->set('showResults',1);
			$searchValue = '';
			if (isset($this->request->data['Search']['value']) && !empty($this->request->data['Search']['value'])) {
				$searchValue = $this->request->data['Search']['value'];
			}
			if ( isset($this->request->data['Form']) && is_array($this->request->data['Form']) && $this->showAdvancedSearchValue === true )
			{
				$searchValue = $this->_extractSearchParameters();				
			}
			$this->set('searchValue',$searchValue);
		}		
	} //_setViewParams
	
	/**
	 * Extracts search parameters and forms searchValue.
	 */
	protected function _extractSearchParameters() {
		$res = array();
		foreach ($this->request->data['Form'] as $formKey => $value) {
			if ( !empty($value) )
			{
				if ( array_key_exists($formKey, $this->searchCategory[$this->_searchType]['fields'])){
						
					$labelField = $this->searchCategory[$this->_searchType]['fields'][$formKey];
					if ( is_array($labelField)) {
						if (array_key_exists(1, $labelField)) {
							if ( array_key_exists($labelField[1], $this->request->data['Form'])) {
								$res[$labelField[0]] = $this->request->data['Form'][$labelField[1]];
							}
							else {
								$res[array_shift($this->searchCategory[$this->_searchType]['fields'][$formKey])] = $value;
							}
						}
					}
					else if ( array_key_exists($formKey, $this->searchCategory[$this->_searchType]['fields']))
						$res[$this->searchCategory[$this->_searchType]['fields'][$formKey]] = $value;
				}
			}
		}
		return json_encode($res);
	}

	
	/**
	 * Sets display fields for datatables component. 
	 *
	 * @since 4.0
	 */
	protected function _setDisplayFields(){
		//To setup columns in search results
		$fields=json_encode(FieldCountryAclUtils::getSearchableFields($this->_searchType, $this->searchCategory[$this->_searchType]['fields']));
		$this->set('fields',$fields);		
	} //_setDisplayFields
	
	/**
	 * Sets fields used for LDAP search in $_searchParameters
	 * 
	 * @since 4.0
	 * @param String $value
	 */
	protected function _setSearchFields($value = null){
		#need to create a function buildcondition and manupulate the condition for advance search
 		$this->_searchParameters=array_merge($this->_searchParameters, array_fill_keys(array_keys(FieldCountryAclUtils::getSearchableFields($this->_searchType, $this->searchCategory[$this->_searchType]['fields'])),""));
		foreach ($this->searchCategory[$this->_searchType]['searchField'] AS $searchField) {
			$this->_searchParameters[$searchField] = ($value!=null)?$value:'';
		}
		if ( $this->request->isPost() && isset($this->request->data['Form']) ) {
			foreach ($this->request->data['Form'] AS $key => $value) {
				if ($value !== "")
					$this->_searchParameters[$key] = $value;
			}//prepearing the condtions array for search from post data
		}
	} //_setSearchFields


	/**
	 * call the WSDL services 
	 * 
	 * @since 4.0
	 * @param string $plugins
	 * @param array $condition
	 * @return Ambigous <string, boolean>
	 */
	protected function _search($plugins, $condition) {	
		if(!empty($plugins) && !empty($condition)) {
			$result = ClassRegistry::init($plugins)->find('all',$condition);
 			return $result;
		} else {
			$this->Session->setFlash('No data available in table','default', array('class' => 'error'));
		}
	} // _search	
	
	/**
	 * Encode the return array
	 * 
	 * @since 4.0
	 * @param array $results
	 * @return string|boolean
	 */
	protected function _createJsonData($results) {
		$modelNameKey = key($results);
		if(isset($results[$modelNameKey]) && !empty($results[$modelNameKey])){
			return json_encode($results[$modelNameKey]);
		}else{
			return true;
		}
	} // _createJsonData	
	
	/**
	 * make sure that search string longer than 4 characters
	 * and isn't a part of email domain name
	 * 
	 * if the post data is empty redirect to the referer
	 * 
	 * @since 4.0
	 */
	protected function _validateSimpleSearch() {
		$errorMgs = '';
		if(4 > strlen($this->request->data['Search']['value'])) {
			$errorMgs = 'The search string must be 4 characters or more.';
			$return=true;
		}//for string length validation
		
		if(preg_match('/^(@ogil|ogilvy|ogilv|ogil|gilv|gilvy|ilvy|\.com|y\.com|vy\.com|lvy\.com|ilvy\.com|gilvy\.com|ogilvy\.com|@ogilvy\.com|gmail|john|mike|michael|yahoo)$/', $this->request->data['Search']['value'])) {
			$errorMgs = 'Please refine your search.';
		}//email domains can not be enter as search string
		
		if(strlen($errorMgs)>0)	{
			$this->Session->setFlash($errorMgs, 'default', array('class'=>'page-message warning-msg'));
			$this->redirect($this->referer());
		}//redirect		
	}//_validateSimpleSearch

	
	/**
	 * make sure the post data is not empty
	 * if the post data is empty redirect to the referer
	 * 
	 * @since 4.0
	 */
	protected function _validateAdvancedSearch()
	{
		$return=true;
		$searchValues=array_values($this->request->data['Form']);
		if(isset($this->request->data['Form']))	{
			foreach($this->request->data['Form'] as $key=>$value) {
				if(is_array($value)) {
					foreach($value as $ikey=>$ivalue) {
						if(isset($ivalue) && $ivalue != "") {
							$return=false;
						}
					}
				}
				else if(isset($value) && $value != "") {
					$return=false;
				}
			}//checking if the post data is not empty
		}
		if($return)	{
			$this->Session->setFlash('All fields were left empty. Please enter data in at least one field in order to execute search.', 'default');
			$this->redirect($this->referer());
		}//redirect
	}//_validateAdvancedSearch
}

