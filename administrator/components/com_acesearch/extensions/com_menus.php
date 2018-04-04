<?php
/*
* @package		AceSearch
* @subpackage	Menus
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		http://www.joomace.net/company/license
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

class AceSearch_com_menus extends AcesearchExtension {

	public function getResults() {
		$where = parent::getSearchFieldsWhere('name');
		if (empty($where)){
			return array();
		}
		
		if ($this->site && $this->AcesearchConfig->access_checker == '1') {
			$where[] = '(access <= '.$this->aid.')';
		}
		
		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where): '');
		$where .= " ORDER BY name";
		
		$identifier = parent::getIdentifier();
		
		return AceDatabase::loadObjectList("SELECT {$identifier}, id, name, link FROM #__menu {$where}", '', 0, parent::getSqlLimit());
	}
	
	public function _getItemURL(&$item) {
		if ($this->site){				
			$item->link = $item->link.'&Itemid='.$item->id;
		}
		else {
			$item->link = $item->link;
		}
    }
}