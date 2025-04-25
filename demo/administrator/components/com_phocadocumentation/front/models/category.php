<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');


class PhocaDocumentationModelCategory extends JModel
{
	var $_document 			= null;
	var $_category 			= null;
	var $_section			= null;

	function __construct() {
		parent::__construct();
	}

	function getDocumentList($categoryId, $params) {
		$user	=& JFactory::getUser();
		$aid 	= $user->get('aid', 0);	
		
		if (empty($this->_document)) {	
			global $mainframe;
			$user 			= &JFactory::getUser();
			$aid 			= $user->get('aid', 0);			
			$query			= $this->_getDocumentListQuery( $categoryId, $aid, $params );
			$this->_document= $this->_getList( $query );
		}
		return $this->_document;
	}
	
	function _getDocumentListQuery( $categoryId, $aid, $params ) {
		
		$wheres[]	= " c.catid= ".(int)$categoryId;
		$wheres[]	= " c.catid= cc.id";
		if ($aid !== null) {
			$wheres[] = "c.access <= " . (int) $aid;
			$wheres[] = "cc.access <= " . (int) $aid;
		}
	//	$wheres[] = " c.state = 1";
		$wheres[] = " cc.published = 1";
		
		// Archive, State
		$jnow		=& JFactory::getDate();
		$now		= $jnow->toMySQL();
		$nullDate	= $this->_db->getNullDate();
		$wheres[] = ' ( '
		//.= ' ( a.created_by = ' . (int) $user->id . ' ) ';
		//.= '   OR ';
		.' ( c.state = 1'
		.' AND ( c.publish_up = '.$this->_db->Quote($nullDate).' OR c.publish_up <= '.$this->_db->Quote($now).' )'
		.' AND ( c.publish_down = '.$this->_db->Quote($nullDate).' OR c.publish_down >= '.$this->_db->Quote($now).' )'
		.'   ) '
		.'   OR '
		.' ( c.state = -1 ) '
		.' ) ';
		
		
		$query = " SELECT c.id, c.title, c.alias, cc.id AS categoryid, cc.title AS categorytitle, cc.alias AS categoryalias "
				." FROM #__content AS c, #__categories AS cc"
				. " WHERE " . implode( " AND ", $wheres )
				. " ORDER BY c.ordering";
				
		return $query;
	}
	
	function getCategory($categoryId, $params) {
		$user	=& JFactory::getUser();
		$aid 	= $user->get('aid', 0);	
		//$wheres[] = " cc.state = 1 ";	
		if (empty($this->_category)) {	
			global $mainframe;
			$user 			= &JFactory::getUser();
			$aid 			= $user->get('aid', 0);			
			$query			= $this->_getCategoryQuery( $categoryId, $aid, $params );
			$this->_category= $this->_getList( $query, 0, 1 );
		}
		return $this->_category;
	}
	
	function _getCategoryQuery( $categoryId, $aid, $params ) {
		
		$wheres[]	= " cc.id= ".(int)$categoryId;
		if ($aid !== null) {
			$wheres[] = "cc.access <= " . (int) $aid;
		}
		$wheres[] = " cc.published = 1";
		
		$query = " SELECT cc.id, cc.title, cc.alias, cc.image, cc.image_position, cc.description"
				. " FROM #__categories AS cc"
				. " WHERE " . implode( " AND ", $wheres )
				. " ORDER BY cc.ordering";
		return $query;
	}
	
	function getSection($categoryId, $params) {
		$user	=& JFactory::getUser();
		$aid 	= $user->get('aid', 0);	
		if (empty($this->_category)) {	
			global $mainframe;
			$user 			= &JFactory::getUser();
			$aid 			= $user->get('aid', 0);			
			$query			= $this->_getSectionQuery( $categoryId, $aid, $params );
			$this->_section= $this->_getList( $query, 0, 1 );
		}
		return $this->_section;
	}
	
	function _getSectionQuery( $categoryId, $aid, $params ) {
		
		$wheres[]	= " cc.id= ".(int)$categoryId;
		$wheres[]	= " cc.section=s.id";
		if ($aid !== null) {
			$wheres[] = "cc.access <= " . (int) $aid;
			$wheres[] = "s.access <= " . (int) $aid;
		}
		$wheres[] = " cc.published = 1";
		
		$query = " SELECT s.id, s.title, s.alias"
				. " FROM #__sections AS s"
				. " ,#__categories AS cc"
				. " WHERE " . implode( " AND ", $wheres )
				. " ORDER BY cc.ordering";
		return $query;
	}
	
}
?>