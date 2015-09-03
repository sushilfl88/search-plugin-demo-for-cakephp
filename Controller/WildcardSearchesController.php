<?php

/**
 * @author Santanu Barman <santanu_barman@fulcrumww.com>
 * @since 4.0
 */
class WildcardSearchesController extends \SearchAppController {
	/**
	 * Controller name
	 */
	public $name = 'WildcardSearches';
	/**
	 * Search type
	 */
	protected $_searchType = 'wildcard';
	
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
			'wildcard'=>array(
					'plugins'=>'Ocd.Ocd',
					'searchField'=>array('request'),
					'fields'=>array(
							'target'                => 'Target',
							'type'                => 'Type',
							'identifier'        => 'Identifier',
							'label'             => 'Label',
					),
						
			)
	);	
	
	
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
			$searchScope = array('USER', 'ROLE', 'CONTACT', 'GROUP');
			$result = ClassRegistry::init($plugins)->wildcardEntitySearch($searchScope,'*'.Hash::get($condition, "conditions.request").'*');
			return $result;
		} else {
			$this->Session->setFlash('No data available in table','default', array('class' => 'error'));
		}
	} // _search	

	protected function _processSearchResults($data) {
		$target_map = array(
			"USER" => "/users/view/",
			"ROLE" => "/roles/view/",		//removed ?role=
			"GROUP" => "/groups/view/",		//removed ?group= because it breaks the soap call -BM
			"CONTACT" => "/group/contacts/index/"
		);

		$rt = array();
		foreach ($data as $row)  {
			$row['target'] = $target_map[$row['type']] . urlencode($row['identifier']);
			array_push($rt, $row);
		}
		return parent::_processSearchResults($rt);
	}

	
	/**
	 * Encode the return array
	 *
	 * @since 4.0
	 * @param array $results
	 * @return string|boolean
	 */
	protected function _createJsonData($results) {
		return json_encode($results);
	} // _createJsonData	
	
}

?>
