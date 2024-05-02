<?php
/*
* @package		AceSearch
* @subpackage	Components
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		http://www.joomace.net/company/license
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

class AceSearch_com_components extends AcesearchExtension {
	
	public function getResults() {
		$where = parent::getSearchFieldsWhere('name');
		if (empty($where)){
			return array();
		}
		
		$where[] = "`parent` = '0'";
		$where[] = "`option` <> ''";
		
		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where): '');
		$where .= " ORDER BY name";
		
		$identifier = parent::getIdentifier();
		
		return AceDatabase::loadObjectList("SELECT {$identifier}, name, admin_menu_link FROM #__components {$where}", '', 0, parent::getSqlLimit());
	}
	
	public function _getItemURL(&$item) {			
		$item->link = 'index.php?'.$item->admin_menu_link;
    }
}

