<?php
/*
* @package		AceSearch
* @subpackage	Content
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		http://www.joomace.net/company/license
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE.'/components/com_content/helpers/route.php');

class Acesearch_com_content extends AcesearchExtension {
	
	public function getResults() {
		$cats = $items = array();
		
		$itemsss = self::_getItems();
		if (!empty($itemsss)) {
			$items = $itemsss;
		}
		
		$cat = parent::getInt('category');
		if (empty($cat) && $this->params->get('search_categories', '1') == '1') {
			$cats = self::_getCategories(); 
		}
		
		$results = array_merge($items, $cats);
		
		return $results;
	}
	
	protected function _getItems() {
		$where = parent::getSearchFieldsWhere('a.title:name, a.introtext:description, a.`fulltext`:description');
		if (empty($where)){
			return array();
		}
		
		if ($this->site) {
			$where[] = '(a.state = 1)';
			
			if ($this->AcesearchConfig->access_checker == '1') {
				$where[] = '(a.access <= '.$this->aid.')';
			}
		}
		
		$catid = parent::getInt('category');
		if (!empty($catid)) {
			$where[] = '(a.catid = '.$catid.')';
		}
	
		$user_id = parent::getUserID('user');
		if (!empty($user_id)) {
			$where[] = "a.created_by = '{$user_id}'";
		}
		
		if ($this->search_fields === true) {
			if ($this->params->get('days', '1') == '1' || $this->params->get('daterange', '1') == '1') {
				$date = parent::getDateFieldsWhere('a.created');
				
				if (!empty($date)) {
					$where[] = $date;
				}
			}
		}
		
		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where): '');
		$where .= " GROUP BY a.id ";
		$order = parent::getWord('order');
		$orderdir = parent::getWord('orderdir');
		if (!empty($order) && !empty($orderdir)) {
			$where .= " ORDER BY a.{$order} {$orderdir}";
		} else {
			$where .= " ORDER BY a.hits DESC";
		}
		
		$identifier = parent::getIdentifier();
		
		$query = "SELECT {$identifier}, a.id, a.title AS name,".
		" CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(':', c.id, c.alias) ELSE c.id END AS catslug, ".
		" CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(':', a.id, a.alias) ELSE a.id END AS slug, a.sectionid AS sectionid, ".
		" CONCAT(a.introtext, a.`fulltext`) AS description, a.created AS date, a.hits AS hits, c.title AS category".
		" FROM #__content AS a LEFT JOIN #__categories AS c ON c.id = a.catid {$where} ";
		
		return AceDatabase::loadObjectList($query, '', 0, parent::getSqlLimit());
	}
	
	public function _getItemURL(&$item) {
		if ($this->site){				
			$item->link = ContentHelperRoute::getArticleRoute($item->slug, $item->catslug, $item->sectionid);
			
			unset($item->catslug);
			unset($item->slug);
			unset($item->sectionid);
		}
		else {
			$item->link = 'index.php?option=com_content&sectionid='.$item->sectionid.'&task=edit&cid[]='.$item->id;
		}
    }
	
	public function _getCategories() {
		$where = parent::getSearchFieldsWhere('c.title:name, c.description:description');
		if (empty($where)){
			return array();
		}
		
		$where[] = "s.scope = 'content'";
		
		if ($this->site) {
			$where[] = "c.published = 1";
			
			if ($this->AcesearchConfig->access_checker == '1') {
				$where[] = "c.access <= {$this->aid}";
			}
		}
		
		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where): '');
		
		$identifier = parent::getIdentifier('Category');
		
		return AceDatabase::loadObjectList("SELECT {$identifier}, c.id, c.alias, c.title AS name, c.description, c.count AS hits, c.checked_out_time AS date FROM #__categories AS c LEFT JOIN #__sections AS s ON c.section = s.id {$where}", '', 0, parent::getSqlLimit());
	}
	
	public function _getCategoryURL(&$cat) {
		if ($this->site) {
			$cat->link = ContentHelperRoute::getCategoryRoute($cat->id.':'.$cat->alias,'');
		}
		else {
			$cat->link = 'index.php?option=com_categories&section=com_content&task=edit&cid[]='.$cat->id.'&type=content';
		}
	}
	
	public function getUser($user){
		return '<input type="text" name="author" value="'.JRequest::getString('usr').'" />';
	}
	
	public function getCategoryList($filter = '0') {
		if ($this->site || $filter == '1') {
			$where[] = "c.published = 1";
			$where[] = 'c.access <= '.$this->aid.' AND s.access <= '.$this->aid;
		}
		
		$where[] = "s.scope = 'content' AND c.section = s.id";
		
		$where = (count($where) ? ' WHERE ' . implode(' AND ' , $where): '');
		
		return AceDatabase::loadObjectList("SELECT c.id, CONCAT_WS(' / ', s.title, c.title) AS name FROM #__categories AS c, #__sections AS s {$where}  ORDER BY s.title, c.title");
	}
}