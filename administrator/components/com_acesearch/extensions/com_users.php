<?php
/*
* @package		AceSearch
* @subpackage	Users
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		http://www.joomace.net/company/license
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

class AceSearch_com_users extends AcesearchExtension {

	public function getResults() {
		$where = parent::getSearchFieldsWhere('name, username, email');
		if (empty($where)){
			return array();
		}
		
		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where): '');
		$where .= " ORDER BY name";
		
		$identifier = parent::getIdentifier();
		
		return AceDatabase::loadObjectList("SELECT {$identifier}, id, name FROM #__users {$where}", '', 0, parent::getSqlLimit());
	}
	
	public function _getItemURL(&$item) {			
		$item->link = 'index.php?option=com_users&view=user&task=edit&cid[]='.$item->id;
    }
}