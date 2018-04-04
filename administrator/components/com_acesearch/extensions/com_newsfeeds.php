<?php
/*
* @package		AceSearch
* @subpackage	News Feeds
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		http://www.joomace.net/company/license
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

class Acesearch_com_newsfeeds extends AcesearchExtension {

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
		$where = parent::getSearchFieldsWhere('n.name:name');
		if (empty($where)){
			return array();
		}
		
		if ($this->site) {
			$where[] = '(n.published = 1)';
		}
		
		$catid = parent::getInt('category');
		if (!empty($catid)) {
			$where[] = 'n.catid = '.$catid;
		}
		
		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where): '');
		$where .= " ORDER BY n.name";
		
		$identifier = parent::getIdentifier();
		
		$query = "SELECT {$identifier}, n.id, n.name AS name, c.title AS category, CONCAT_WS(':', n.id, n.alias) AS itemslug, CONCAT_WS(':', c.id, c.alias) AS catslug FROM #__newsfeeds AS n LEFT JOIN #__categories AS c ON n.catid = c.id {$where}";		
		
		return AceDatabase::loadObjectList($query, '', 0, parent::getSqlLimit());
	}
	
	public function _getItemURL(&$item) {
		if ($this->site){				
			$item->link = 'index.php?option=com_newsfeeds&view=newsfeed&catid='.$item->catslug.'&id='.$item->itemslug . parent::getItemid(array('view' => 'newsfeed'));
		}
		else {
			$item->link = 'index.php?option=com_newsfeeds&task=edit&cid[]='.$item->id;
		}
    }
	
	public function _getCategoryURL(&$cat) {
		if ($this->site) {
			$cat->link = 'index.php?option=com_newsfeeds&view=category&id='.$cat->id.$cat->alias. parent::getItemid(array('view' => 'category'));
		}
		else {
			$cat->link = 'index.php?option=com_categories&section=com_newsfeeds&task=edit&cid[]='.$cat->id.'&type=other';
		}
	}
	
	public function getCategoryList($filter = '0') {
		return parent::_getCategoryList($this->extension->extension ,$filter);
	}
}