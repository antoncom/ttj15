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


class PhocaDocumentationModelSection extends JModel
{
	var $_category 			= null;
	var $_section 			= null;
	var $_most_viewed_docs	= null;

	function __construct() {
		parent::__construct();
	}

	function getCategoryList($sectionId, $params) {
		$user	=& JFactory::getUser();
		$aid 	= $user->get('aid', 0);	
		
		if (empty($this->_category)) {	
			global $mainframe;
			$user 			= &JFactory::getUser();
			$aid 			= $user->get('aid', 0);			
			$query			= $this->_getCategoryListQuery( $sectionId, $aid, $params );
			$this->_category= $this->_getList( $query );
		}
		return $this->_category;
	}
	
	function _getCategoryListQuery( $sectionId, $aid, $params ) {
		
		$wheres[]	= " cc.section= ".(int)$sectionId;
		if ($aid !== null) {
			$wheres[] = "cc.access <= " . (int) $aid;
			//$wheres[] = "c.access <= " . (int) $aid;
		}
		$wheres[] = " cc.published = 1";
		//$wheres[] = " c.state = 1";
		//$wheres[] = " c.catid = cc.id";
		
		$query = " SELECT  cc.id, cc.title, cc.alias, COUNT(c.id) AS numdoc"
				. " FROM #__categories AS cc"
				. " LEFT JOIN #__content AS c ON c.catid = cc.id AND c.state = 1"
				. " WHERE " . implode( " AND ", $wheres )
				. " GROUP BY cc.id"
				. " ORDER BY cc.ordering";
				
		return $query;
	}
	
	function getSection($sectionId, $params) {
		$user	=& JFactory::getUser();
		$aid 	= $user->get('aid', 0);	
		//$wheres[] = " cc.state = 1 ";	
		if (empty($this->_category)) {	
			global $mainframe;
			$user 			= &JFactory::getUser();
			$aid 			= $user->get('aid', 0);			
			$query			= $this->_getSectionQuery( $sectionId, $aid, $params );
			$this->_section= $this->_getList( $query, 0, 1 );
		}
		return $this->_section;
	}
	
	function _getSectionQuery( $sectionId, $aid, $params ) {
		
		$wheres[]	= " s.id= ".(int)$sectionId;
		if ($aid !== null) {
			$wheres[] = "s.access <= " . (int) $aid;
		}
		$wheres[] = " s.published = 1";
		
		$query = " SELECT s.id, s.title, s.alias, s.image, s.image_position, s.description"
				. " FROM #__sections AS s"
				. " WHERE " . implode( " AND ", $wheres )
				. " ORDER BY s.ordering";
		return $query;
	}
	
	
	function getMostViewedDocsList($sectionId, $params) {
		$user	=& JFactory::getUser();
		$aid 	= $user->get('aid', 0);	
		
		if (empty($this->_most_viewed_docs)) {	
			global $mainframe;
			$user 						= &JFactory::getUser();
			$aid 						= $user->get('aid', 0);			
			$query						= $this->_getMostViewedDocsListQuery( $sectionId, $aid, $params );
			$this->_most_viewed_docs 	= $this->_getList( $query );
		}
		return $this->_most_viewed_docs;
	}
	
	function _getMostViewedDocsListQuery( $sectionId, $aid, $params ) {
		
		// PARAMS
		$most_viewed_docs_num = $params->get( 'most_viewed_docs_num', 5 );
		/*
		$display_sections = $params->get('display_sections', '');
		if ( $display_sections != '' ) {
			$section_ids_where = " AND s.id IN (".$display_sections.")";
		} else {
			$section_ids_where = '';
		}
		
		$hide_sections = $params->get('hide_sections', '');
		if ( $hide_sections != '' ) {
			$section_ids_not_where = " AND s.id NOT IN (".$hide_sections.")";
		} else {
			$section_ids_not_where = '';
		}*/
		
		$displaySections = $params->get('display_sections', '');
		if (count($displaySections) > 1) {
			JArrayHelper::toInteger($displaySections);
			$displaySectionsString	= implode(',', $displaySections);
			$wheres[]	= ' s.id IN ( '.$displaySectionsString.' ) ';
		} else if ((int)$displaySections > 0) {
			$wheres[]	= ' s.id IN ( '.$displaySections.' ) ';
		}
		
		$hideSections = $params->get('hide_sections', '');
		if (count($hideSections) > 1) {
			JArrayHelper::toInteger($hideSections);
			$hideSectionsString	= implode(',', $hideSections);
			$wheres[]	= ' s.id NOT IN ( '.$hideSectionsString.' ) ';
		} else if ((int)$hideSections > 0) {
			$wheres[]	= ' s.id NOT IN ( '.$hideSections.' ) ';
		}
		
		$wheres[]	= " c.sectionid= s.id";
		$wheres[]	= " c.catid= cc.id";
	//	$wheres[]	= " c.state= 1";
		if ($aid !== null) {
			$wheres[] = "c.access <= " . (int) $aid;
			$wheres[] = "s.access <= " . (int) $aid;
			$wheres[] = "cc.access <= " . (int) $aid;
		}
		$wheres[]	= " s.id= ".(int)$sectionId;
		
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
		
		$query = " SELECT c.id, c.title, c.alias, s.title AS sectiontitle, cc.id AS categoryid, cc.title AS categorytitle, cc.alias AS categoryalias "
				." FROM #__content AS c, #__sections AS s, #__categories AS cc"
				. " WHERE " . implode( " AND ", $wheres )
				//. $section_ids_where
				//. $section_ids_not_where
				. " ORDER BY c.hits DESC"
				. " LIMIT ".(int)$most_viewed_docs_num;
		return $query;
	}
}
?>