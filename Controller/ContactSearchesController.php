<?php
App::uses('SearchAppController', 'Search.Controller');
/**
 * @author Santanu Barman <santanu_barman@fulcrumww.com>
 * @since 4.0
 */
class ContactSearchesController extends SearchAppController {
	/**
	 * Controller name
	 */
	public $name = 'ContactSearches';
	/**
	 * Search type
	 */
	protected $_searchType = 'company_contact';
	/**
	 * Load all the components
	 */
	public $components = array('Company.CompanyData');
	/**
	 * Structure of array : array('className'=>array('pluginsName','seachingField','allPossibleFields'))
	 *
	 * className     : main index("user") of returning array (e.g: array(user=>array(0=>array(cn,uid))))
	 * pluginsName   : Name of the related plugin and it's model(e.g: OgUser.OgilvyUser)
	 * seachingField : searching values on the basis of this field (e.g: cn/uid)
	 * fields        : list of all fileds , on the basis of this we will manupulate the display of datatable(need to modify this section)
	 */
	protected $searchCategory =array(
			'company_contact'=>array(
								'plugins'=>'Company.CompanyContact',
								'searchField'=>array('firstName', "lastName"),
								'fields'=>array(
									"companyContactId" => array("Id"),
									"companyId" => "Company Id",
									"companyName" => "Company Name",
									"firstName" => "First Name",
									"lastName"  => "Last Name",
									"name"  => "Name",
									"emailAddress"     => "Email",
									"title"     => "Title",
									"level"     => array("Level", "levelLabel"),
									"businessFunction"     => array("Business Function", "clientBizFunctionLabel"),
									"region"    => array("Region", "regionLabel"),
									"country"   => array("Country", "countryLabel"),
									"city"      => array("City", "cityLabel"),
									"companyAddress" => array("Company Address", "value"),
									"address" => array("Secondary Address", "value"),
									"discipline"      => array("Discipline", "disciplineLabel"),
									"receivePublishedThoughtLeadership" => array("Receive O&M published thought leadership"),
									"publishingType" => array("Publishing Type", "clientPublishingLabel"),
									"audienceType" => array("Audience Type", "audienceTypeLabel"),
									"primaryRelationshipOwnerUid" => array("Primary O&M Relationship Owner", "description"),
									"secondaryRelationshipOwnerUid" => array("Secondary O&M Relationship Owner", "description"),									
								)
			)
	);	
	
	/**
	 * @see SearchAppController::beforeFilter()
	 */
	public function beforeFilter() {
		$companyIds = $this->Company->findAll();
		$companyIds = Hash::combine($companyIds, "{n}.identifier", "{n}.label");
		$this->set("canExportCSV", $this->OgAcl->canExportContactsSearchResults());
		$this->set("searchTitle",  "O&M Client / Company Contacts");		
		$this->set("companyIds",  $companyIds); // FormHelper reads this variable and sets the options
		parent::beforeFilter();
	}

	/**
	 * @see SearchAppController::advanced_search()
	 */
	public function advanced_search() {
		if ($this->request->isPost()) {
			$this->_setSearchFields();
			
			// No longer needed since the view is passing an empty string if it wasn't selected
			// if (Hash::check($this->request->data, "Form.receivePublishedThoughtLeadership") && $this->request->data['Form']['receivePublishedThoughtLeadership'] ===  ''){
				// unset($this->_searchParameters['receivePublishedThoughtLeadership']);
			// }
			//specified region, country, l, uid with the post data to search user cause these are not available in post data			
		}	
		parent::advanced_search();
	} // advanced_search($type)
	
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
	
		if(preg_match('/^(@ogil|ogilvy|ogilv|ogil|gilv|gilvy|ilvy|\.com|y\.com|vy\.com|lvy\.com|ilvy\.com|gilvy\.com|ogilvy\.com|@ogilvy\.com)$/', $this->request->data['Search']['value'])) {
			$errorMgs = 'Please refine your search.';
		}//email domains can not be enter as search string
	
		if(strlen($errorMgs)>0)	{
			$this->Session->setFlash($errorMgs, 'default', array('class'=>'page-message warning-msg'));
			$this->redirect($this->referer());
		}//redirect
	}//_validateSimpleSearch	

	/**
	 *
	 * Call CompanDataComponent's injectCompanyData method to insert the necessary company data into the $result array
	 * Method takes array by reference to inject data into the original array.
	 * 
	 * @see SearchAppController::_processSearchResults()
	 */
	protected function _processSearchResults($result) {
		$result = $this->CompanyData->injectCompanyData($result);
		
		if (count($result['CompanyContact']) > 0) {
			foreach ($result['CompanyContact'] AS $key => $value) {
				$result['CompanyContact'][$key]['name'] = $value['firstName'] . ' ' . $value['lastName'];
				if ( isset($result['CompanyContact'][$key]['receivePublishedThoughtLeadership'])) {
					$result['CompanyContact'][$key]['receivePublishedThoughtLeadership'] = ($result['CompanyContact'][$key]['receivePublishedThoughtLeadership'] === true )?"Yes":"No";
				}
			}
		}
		return $this->_createJsonData($result);
	}
}
?>