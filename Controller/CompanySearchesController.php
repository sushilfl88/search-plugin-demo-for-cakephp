<?php
App::uses('SearchAppController', 'Search.Controller');
/**
 * @author Santanu Barman <santanu_barman@fulcrumww.com>
 * @since 4.0
 */
class CompanySearchesController extends SearchAppController {
	/**
	 * Controller name
	 */
	public $name = 'CompanySearches';
	/**
	 * Search type
	 */
	protected $_searchType = 'company';
	/**
     * Structure of array : array('className'=>array('pluginsName','seachingField','allPossibleFields'))
     * 
     * className     : main index("user") of returning array (e.g: array(user=>array(0=>array(cn,uid))))
     * pluginsName   : Name of the related plugin and it's model(e.g: OgUser.OgilvyUser)
     * seachingField : searching values on the basis of this field (e.g: cn/uid)
     * fields        : list of all fileds , on the basis of this we will manupulate the display of datatable(need to modify this section)
	 */
	protected $searchCategory =array(
			'company'=>array(
					'plugins'=>'Company.Company',
					'searchField'=> array('companyName'),
					'fields'=>array(
							"companyId" => array("Id"),
							"companyName" => array("Company Name", "label", "search-companyname"),
							"address" => array("Address", "value"),
							"city" => array("City", "cityLabel"),
							"country" => array("Country", "countryLabel"),
							"region" => array("Region", "regionLabel"),
							"audienceType" => array("Audience Type", "audienceTypeLabel"),
							"clientType" => array("Client Type", "clientTypeLabel"),
							"clientStatus" => array("Status", "clientStatusLabel"),
							"relationshipScope" => array("Relationship Scope", "relationshipScopeLabel"),
							"industry" => array("Industry", "industryLabel"),
							"practice" => array("Practice", "practiceLabel"),
							"outcome" => array("Outcome", "clientOutcomeLabel"),
							"dateOfEngagement" => array("Engagement Date", "description"),
							"relationshipSince" => array("Relationship Since Date", "description"),
					)
					
			)
	);

	/**
	 * @see SearchAppController::beforeFilter()
	 */
	public function beforeFilter() {
		$this->set("canExportCSV", $this->OgAcl->canExportCompanySearchResults());
		$this->set("searchTitle",  'O&M Client / Company Roster');
		parent::beforeFilter();
	}
	/**
	 * @see SearchAppController::index()
	 */
	public function index() {
		
		if($this->request->isPost()) {
			$this->_setSearchFields();
			if (!$this->OgAcl->canViewInactiveCompanies()) {
				$this->_operator = 'and';
				$this->_searchParameters['clientStatus'] = 837750;
			}//for searching inactive companies
	
			if (!$this->OgAcl->canViewCompanyPitchConsultants() && !$this->OgAcl->canViewCompanyProspects() && !$this->OgAcl->canViewCompanyInfluencers()) {
				$this->_operator = 'and';
				$this->_searchParameters['audienceType'] = 837767;
			}//		
			
		}
		parent::index();
	} // index

	/**
	 * @see SearchAppController::advanced_search()
	 */
	public function advanced_search() {
		if ($this->request->isPost()) {
			$this->_setSearchFields();
			if (!$this->OgAcl->canViewInactiveCompanies()) {
				$this->_searchParameters['clientStatus'] = 837750;
			}//for searching inactive companies

			if (!$this->OgAcl->canViewCompanyPitchConsultants() && !$this->OgAcl->canViewCompanyProspects() && !$this->OgAcl->canViewCompanyInfluencers()) {
				$this->_searchParameters['audienceType'] = 837767;
			}

			if (isset($this->_searchParameters['brandId'])) {
				$this->_searchParameters['brandInfo'] = array($this->_searchParameters['brandLabel']);
				if (isset($this->_searchParameters['subbrand']) && $this->_searchParameters['brandId']) {
					$this->_searchParameters['brandInfo'] = $this->_searchParameters['brandId'] . $this->_searchParameters['subbrand'];
				}
			}

			unset($this->_searchParameters['companyBrand']);
			unset($this->_searchParameters['companybrandLabel']);
			unset($this->_searchParameters['brandId']);
			unset($this->_searchParameters['brandLabel']);
			unset($this->_searchParameters['subbrand']);
			unset($this->_searchParameters['subbrandLabel']);
			//specified region, country, l, uid with the post data to search user cause these are not available in post data
		}
		parent::advanced_search();
	} // advanced_search($type)
	
	/**
	 *
	 * Turns dateOfEngagement and relationshipSince fields from ISO 8601 format
	 * to Day-Month-Year (e.g. 12-May-2013) format 
	 *
	 * @see SearchAppController::_processSearchResults()
	 */
	protected function _processSearchResults($result) {
		if (count($result['Company']) > 0) {
			foreach ($result['Company'] AS $key => $value) {
				$dateOfEngagement = new DateTime(Hash::get($result['Company'][$key], "dateOfEngagement"));
				$result['Company'][$key]['dateOfEngagement'] = $dateOfEngagement->format('d-M-Y');
				$relationshipSince = new DateTime(Hash::get($result['Company'][$key], "dateOfEngagement"));
				$result['Company'][$key]['relationshipSince'] = $relationshipSince->format('d-M-Y');
			}
		}
		return $this->_createJsonData($result);
	}
	
	/**
	 * make sure that company name longer than 3 characters
	 * and isn't a part of email domain name
	 *
	 * if the post data is empty redirect to the referer
	 * @see SearchAppController::_validateSimpleSearch()
	 * @since 4.0
	 */
	protected function _validateSimpleSearch() {
		$errorMgs = '';
		if(3 > strlen($this->request->data['Search']['value'])) {
			$errorMgs = 'The search string must be 3 characters or more.';
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
}
?>