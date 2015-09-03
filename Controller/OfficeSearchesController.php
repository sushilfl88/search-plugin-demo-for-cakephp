<?php
App::uses('SearchAppController', 'Search.Controller');
/**
 * @author Santanu Barman <santanu_barman@fulcrumww.com>
 * @since 4.0
 */
class OfficeSearchesController extends SearchAppController {
	/**
	 * Controller name
	 */
	public $name = 'OfficeSearches';
	/**
	 * Search type
	 */
	protected $_searchType = 'office';
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
			'office'=>array(
					'plugins'=>'Office.Office',
					'searchField'=>array('officeName', 'cityLabel', 'countryLabel'),
					'fields'=>array(
							'identifier'=>'ID',
							'label'=>'Name',
							'city'=>'City',
							'country'=>'Country',
							'region'=>'Region',
							'headsOfOffice'=>array('Head of Office', 'label'),
							'officeDomainName'=>'Mail Domain',
							'discipline'=>array('Discipline', 'label'),
							'percentageOwned'=>'Percentage Owned',
							'publishStatus'=>array('Publish Status', 'label')
					)
			),
	);
	
	/**
	 * @see SearchAppController::beforeFilter()
	 */
	public function beforeFilter() {
		$this->set("canExportCSV", $this->OgAcl->canExportOfficeSearchResults());
		parent::beforeFilter();
	}
	/**
	 * @see SearchAppController::index()
	 */
	public function index() {
		if($this->request->isPost()) {
			$this->_setSearchFields();		
			$this->_searchParameters['canViewDraftOffices'] = 0;
			if ($this->OgAcl->canViewDraftOffices()) {
				$this->_searchParameters['canViewDraftOffices'] = 1;
			}
		}
		parent::index();	
	} // index
		
	/**
	 * @see SearchAppController::advanced_search()
	 */
	public function advanced_search() {
		if($this->request->isPost()) {
			$this->_setSearchFields();
			$this->_searchParameters['canViewDraftOffices'] = 0;
			if ($this->OgAcl->canViewDraftOffices()) {
				$this->_searchParameters['canViewDraftOffices'] = 1;
			}			
		}
		parent::advanced_search();
	} // advanced_search($type)
}
?>