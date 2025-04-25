<?php
/*
* @package		AceSearch
* @subpackage	Modules
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		http://www.joomace.net/company/license
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

class AceSearch_com_modules extends AcesearchExtension {

	public function getResults() {
		$where = parent::getSearchFieldsWhere('title:name');
		if (empty($where)){
			return array();
		}
		
		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where): '');
		$where .= " ORDER BY title";
		
		$identifier = parent::getIdentifier();
		
		return AceDatabase::loadObjectList("SELECT {$identifier}, id, title as name, client_id FROM #__modules {$where}", '', 0, parent::getSqlLimit());
	}
	
	public function _getItemURL(&$item) {			
		$item->link = 'index.php?option=com_modules&client='.$item->client_id.'&task=edit&cid[]='.$item->id;
    }
}