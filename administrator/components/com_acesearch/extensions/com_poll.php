<?php
/*
* @package		AceSearch
* @subpackage	Polls
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		http://www.joomace.net/company/license
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

class Acesearch_com_poll extends AcesearchExtension {

	public function getResults() {
		$where = parent::getSearchFieldsWhere('title:name');
		if (empty($where)){
			return array();
		}
		
		if ($this->site){
			$where[] = "(published = 1)";
			
			if ($this->AcesearchConfig->access_checker == '1') {
				$where[] = '(access <= '.$this->aid.')';
			}
		}
		
		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where): '');
		
		$identifier = parent::getIdentifier();
		
		return AceDatabase::loadObjectList("SELECT {$identifier}, id, title AS name, alias, voters AS hits FROM #__polls {$where} ORDER BY name", '', 0, parent::getSqlLimit());
	}
	
	public function _getItemURL(&$item) {
		if ($this->site){				
			$item->link = 'index.php?option=com_poll&view=poll&id='.$item->id.':'.$item->alias . parent::getItemid();
		}
		else {
			$item->link = 'index.php?option=com_poll&view=poll&task=edit&cid[]='.$item->id;
		}
    }
}