<?php
/*
* @package		AceSearch
* @subpackage	Banners
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		http://www.joomace.net/company/license
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

class AceSearch_com_banners extends AcesearchExtension {
	
	public function getResults() {
		$cats = $items = array();
		$items = self::_getItems();
	
		$ext = parent::getCmd('ext');
		$cat = parent::getInt('category');
		if (!empty($ext) && (!empty($cat) || $this->params->get('search_categories', '1') == '1') && $this->admin) {
			$cats = parent::_getCategories('com_banner');
		}
		
		$results = array_merge($items, $cats);
		
		return $results;
	}
	
	protected function _getItems() {
		$where = parent::getSearchFieldsWhere('b.name:name, b.description:description');
		if (empty($where)){
			return array();
		}
		
		if ($this->site) {
			$where[] = '(b.showBanner = 1)';
		}
		
		$catid = parent::getInt('cat');
		if (!empty($catid)) {
			$where[] = "b.cid = ".$catid;
		}
		
		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where): '');
		
		$order = parent::getWord('order');
		$orderdir = parent::getWord('orderdir');
		if (!empty($order) && !empty($orderdir)) {
			$where .= " ORDER BY b.{$order} {$orderdir}"; 
		} else {
			$where .= " ORDER BY b.date";
		}
		
		$identifier = parent::getIdentifier();
		
		$query = "SELECT  {$identifier} , b.bid AS id , b.name, b.description, b.date, b.clicks AS hits, c.title AS category FROM #__banner AS b LEFT JOIN #__categories AS c ON b.cid = c.id {$where}";
		
		return AceDatabase::loadObjectList($query, '', 0, parent::getSqlLimit());
	}
	
	public function _getItemURL(&$item) {
		if ($this->site){				
			$item->link = 'index.php?option=com_banners&task=click&bid='.$item->id;
		}
		else {
			$item->link = 'index.php?option=com_banners&task=edit&cid[]='.$item->id;
		}
    }
	
	public function _getCategoryURL(&$cat) {
		if($this->admin) {
			$cat->link = 'index.php?option=com_categories&section=com_banner&task=edit&cid[]='.$cat->id.'&type=other';
		}
	}
	
	public function getCategoryList($filter = '0') {
		return parent::_getCategoryList($this->extension->extension,$filter);
	}
}