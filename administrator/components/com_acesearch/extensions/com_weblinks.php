<?php
/*
* @package		AceSearch
* @subpackage	Weblinks
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		http://www.joomace.net/company/license
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

class Acesearch_com_weblinks extends AcesearchExtension {
	
	public function getResults() {
		$cats = $items = array();
		$items = self::_getItems();
		
		$ext = parent::getCmd('ext');
		$cat = parent::getInt('category');
		if (!empty($ext) && (!empty($cat) || $this->params->get('search_categories', '1') == '1')) {
			$cats = parent::_getCategories($this->extension->extension);
		}
		
		$results = array_merge($items, $cats);
		
		return $results;
	}
	
	protected function _getItems() {
		$where = parent::getSearchFieldsWhere('w.title:name, w.description:description');
		if (empty($where)){
			return array();
		}
		
		if ($this->site){
			$where[] = '(w.published = 1)';
		}
		
		$catid = parent::getInt('cat');
		if (!empty($catid)) {
			$where[] = 'w.catid = '.$catid;
		}
		
		if ($this->search_fields === true) {
			if ($this->params->get('days', '1') == '1' || $this->params->get('daterange', '1') == '1') {
				$date = parent::getDateFieldsWhere('w.date');
				
				if (!empty($date)) {
					$where[] = $date;
				}
			}
		}
		
		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where): '');
		
		$order = parent::getWord('order');
		$orderdir = parent::getWord('orderdir');
		if (!empty($order) && !empty($orderdir)) {
			$where .= " ORDER BY w.{$order} {$orderdir}";
		} else {
			$where .= " ORDER BY w.date";
		}
		
		$identifier = parent::getIdentifier();
		
		$query = "SELECT {$identifier}, w.id, w.title AS name, w.description, w.date, c.title AS category, CONCAT_WS(':', w.id, w.alias) AS itemslug, CONCAT_WS(':', c.id, c.alias) AS catslug FROM #__weblinks AS w LEFT JOIN #__categories AS c ON w.catid = c.id {$where}";
		
		return AceDatabase::loadObjectList($query, '', 0,parent::getSqlLimit());
	}
	
	public function _getItemURL(&$item) {
		if ($this->site) {
			$item->link = 'index.php?option=com_weblinks&view=weblink&catid='.$item->catslug.'&id='.$item->itemslug . parent::getItemid(array('view' => 'weblink'));
		}
		else {
			$item->link = 'index.php?option=com_weblinks&view=weblink&task=edit&cid[]='.$item->id;
		}
    }
	
	public function _getCategoryURL(&$cat) {
		if ($this->site) {
			$cat->link = 'index.php?option=com_weblinks&view=category&id='.$cat->id.$cat->alias. parent::getItemid(array('view' => 'category'));
		}
		else {
			$cat->link = 'index.php?option=com_categories&section=com_newsfeeds&task=edit&cid[]='.$cat->id.'&type=other';
		}
	}
	
	public function getCategoryList($filter = '0') {
		return parent::_getCategoryList($this->extension->extension , $filter);
	}
}