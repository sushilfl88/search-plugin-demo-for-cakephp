<?php
App::uses('SearchAppController', 'Search.Controller');
/**
 * @author Santanu Barman <santanu_barman@fulcrumww.com>
 * @since 4.0
 */
class UserSearchesController extends SearchAppController {
	/**
	 * Controller name
	 */
	public $name = 'UserSearches';
	/**
	 * Search type
	 */
	protected $_searchType = 'user';
	
	/**
	 * Option to include advanced search parameters in search view. 
	 */
	public $showAdvancedSearchValue = false;
	
	/**
     * Structure of array : array('className'=>array('pluginsName','seachingField','allPossibleFields'))
     * 
     * className     : main index("user") of returning array (e.g: array(user=>array(0=>array(cn,uid))))
     * pluginsName   : Name of the related plugin and it's model(e.g: OgUser.OgilvyUser)
     * seachingField : searching values on the basis of this field (e.g: cn/uid)
     * fields        : list of all fileds , on the basis of this we will manupulate the display of datatable(need to modify this section)
	 */
	protected $searchCategory =array(
			'user'=>array(
					'plugins'=>'OgUser.OgilvyUser',
					'searchField'=>array('cn','uid','title'),
					'fields'=>array(
							'cn'                => 'Name',
							# 'givenName'         => 'Given Name',
							#'sn'                => '',
							'uid'               => 'Email',
							'title'             => 'Title',
							'department'        => array('Dept','departmentlabel'),//array(label,mappingField)
							'startdate'         => 'Start Date',
							'primaryoffice'     => 'Office',
							'telephonenumber'   => 'Telephone',
							'mobile'            => 'Mobile',
							'l'                 => 'City',
							'country'           => 'Country',
							'region'            => 'Region',
							#'notes'				=> 'Notes',
							#'omgStartDate'      => 'Omg Start Date',
							#'zone'              => 'Zone',
							#'primaryOfficeid'   => 'Primaryofficeid',
							'omghrmanager'      => 'HR Manager',
							'omghiringmanager'  => 'Hiring Manager',
							'omusertype'        => array('User Type', 'usertypelabel'),
							'omusersubtype'     => array('User Subtype', 'usersubtypelabel'),
					),
					
			)
	);
	
	/**
	 * @see SearchAppController::beforeFilter()
	 */
	public function beforeFilter() {
		$this->set("canExportCSV", $this->OgAcl->canExportUserSearchResults());
		parent::beforeFilter();
	}

	/**
	 * @see SearchAppController::index()
	 */
	public function index() {
		if($this->request->isPost()) {
			$this->_setSearchFields();		
			if ($this->OgAcl->canSearchForSuspendedUsers()) {
				$this->_searchParameters['includesuspendedusers'] = 1;
			}//for searching suspended users
			if ($this->OgAcl->canViewHrNote()) {
				$this->_searchParameters['omghrnote'] = '';
				$this->searchCategory[$this->_searchType]['fields']['omghrnote'] = 'HR Notes';
			}// adding HR notes as searchable field
		}
		
		parent::index();
	} // index
			
	/**
	 * @see SearchAppController::advanced_search()
	 */
	public function advanced_search() {
		if($this->request->isPost()) {
			$this->_setSearchFields();
			$this->_searchParameters['primaryofficeid']=(isset($this->_searchParameters['office']) ? $this->_searchParameters['office'] : '');
			$this->_searchParameters['omusertype']['usertypeid']=(isset($this->_searchParameters['usertypelabel']) ? $this->_searchParameters['usertypelabel'] : '');
			$this->_searchParameters['omusersubtype']['usersubtypeid']=(isset($this->_searchParameters['usersubtypelabel']) ? $this->_searchParameters['usersubtypelabel'] : '');
			$this->_searchParameters['industry']=(isset($this->_searchParameters['industrylabel']) ? $this->_searchParameters['industrylabel'] : '');
			$this->_searchParameters['region'] = (isset($this->_searchParameters['regionLabel']) ? $this->_searchParameters['regionLabel'] : '');
			$this->_searchParameters['country'] = (isset($this->_searchParameters['countryLabel']) ? $this->_searchParameters['countryLabel'] : '');
			$this->_searchParameters['l'] = (isset($this->_searchParameters['cityLabel']) ? $this->_searchParameters['cityLabel'] : '');
			$this->_searchParameters['uid'] = (isset($this->_searchParameters['email']) ? $this->_searchParameters['email'] : '');
			$this->_searchParameters['jobskills']['skillid'] = (isset($this->_searchParameters['skill']) ? $this->_searchParameters['skill'] : '');
			$this->_searchParameters['jobskills']['subskillid'] = (isset($this->_searchParameters['subskill']) ? $this->_searchParameters['subskill'] : '');
			$this->_searchParameters['languages']['language'] = (isset($this->_searchParameters['languagelabel']) ? $this->_searchParameters['languagelabel'] : '');
			$this->_searchParameters['languages']['proficiency'] = (isset($this->_searchParameters['languagelevellabel']) ? $this->_searchParameters['languagelevellabel'] : '');
			$this->_searchParameters['clientbrand']['client'] = (isset($this->_searchParameters['clientLabel']) ? $this->_searchParameters['clientLabel'] : '');
			$this->_searchParameters['department']['subdeptid'] = (isset($this->_searchParameters['subdepartment']) ? $this->_searchParameters['subdepartment'] : '');
			
			// Leadership lists	
			$this->_searchParameters['groupLeadership']['grouptypeid'] = (isset($this->_searchParameters['groupLeadership']['grouptype']) ? $this->_searchParameters['groupLeadership']['grouptype'] : '');
			$this->_searchParameters['groupLeadership']['disciplineid'] = (isset($this->_searchParameters['groupLeadership']['discipline']) ? $this->_searchParameters['groupLeadership']['discipline'] : '');
			$this->_searchParameters['groupLeadership']['countryid'] = (isset($this->_searchParameters['groupLeadership']['country']) ? $this->_searchParameters['groupLeadership']['country'] : '');
			$this->_searchParameters['groupLeadership']['regionid'] = (isset($this->_searchParameters['groupLeadership']['region']) ? $this->_searchParameters['groupLeadership']['region'] : '');
			
			/**
			 * omgdiscipline/disciplineid cannot be sent to OCD as an empty field.
			 * If nothing is selected in a form, it should be removed from search params.
			 */
			if ( isset($this->_searchParameters['disciplineid']) ) {
				$this->_searchParameters['omgdiscipline']['disciplineid'] = $this->_searchParameters['disciplineid'];
			} else {
				unset($this->_searchParameters['omgdiscipline']);
			}
			unset($this->_searchParameters['groupLeadership']['leadershiplist']);
			unset($this->_searchParameters['disciplinelabel']);
			unset($this->_searchParameters['disciplineid']);
			unset($this->_searchParameters['discipline']);
			unset($this->_searchParameters['address']);
			unset($this->_searchParameters['office']);
			unset($this->_searchParameters['countryLabel']);
			unset($this->_searchParameters['city']);
			unset($this->_searchParameters['regionLabel']);
			unset($this->_searchParameters['cityLabel']);
			unset($this->_searchParameters['officeLabel']);
			unset($this->_searchParameters['skill']);
			unset($this->_searchParameters['office']);
			unset($this->_searchParameters['groupLeadership']['region']);
			unset($this->_searchParameters['groupLeadership']['country']);
			unset($this->_searchParameters['groupLeadership']['discipline']);
			unset($this->_searchParameters['groupLeadership']['grouptype']);
			
			if ($this->OgAcl->canSearchForSuspendedUsers()) {
				$this->_searchParameters['includesuspendedusers'] = 1;
			}
			if ($this->OgAcl->canViewHrNote()) {
				$this->_searchParameters['omghrnote'] = '';
				$this->searchCategory[$this->_searchType]['fields']['omghrnote'] = 'HR Notes';
			}// adding HR notes as searchable field			
		}
		parent::advanced_search();		
	} // advanced_search()
}
