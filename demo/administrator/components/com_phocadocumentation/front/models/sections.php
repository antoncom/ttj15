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


class PhocaDocumentationModelSections extends JModel
{
	var $_section 			= null;
	var $_most_viewed_docs 	= null;

	function __construct() {
		parent::__construct();
	}

	function getSectionList($params) {
		$user	=& JFactory::getUser();
		$aid 	= $user->get('aid', 0);	
		
		if (empty($this->_section)) {	
			global $mainframe;
			$user 			= &JFactory::getUser();
			$aid 			= $user->get('aid', 0);			
			$query			= $this->_getSectionListQuery( $aid, $params );
			$this->_section = $this->_getList( $query );
			
			
			if (!empty($this->_section)) {
				foreach ($this->_section as $key => $value) {
					$query	= $this->_getCategoryListQuery( $value->id, $aid, $params );
					$this->_section[$key]->categories = $this->_getList( $query );
				}
			}
			
		}
		return $this->_section;
	}
	
	
	
	
	function _getSectionListQuery( $aid, $params ) {
		
	/*	$display_sections = $params->get('display_sections', '');
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

		$wheres[] = " s.published = 1";
		$wheres[] = " cc.published = 1";
		$wheres[] = " s.id = cc.section";
			

		if ($aid !== null) {
			$wheres[] = "s.access <= " . (int) $aid;
		}
		

		$query =  " SELECT s.id, s.title, s.alias, COUNT(cc.id) AS numcat, '' AS categories"
				. " FROM #__sections AS s, #__categories AS cc"
				. " WHERE " . implode( " AND ", $wheres )
				//. $section_ids_where
				//. $section_ids_not_where
				. " GROUP BY s.id"
				. " ORDER BY s.ordering";
		return $query;
	}
	
	
	function _getCategoryListQuery( $sectionId, $aid, $params ) {
		
		$wheres[]	= " cc.section= ".(int)$sectionId;
		if ($aid !== null) {
			$wheres[] = "cc.access <= " . (int) $aid;
		}
		$wheres[] = " cc.published = 1";
		
		$query = " SELECT  cc.id, cc.title, cc.alias, COUNT(c.id) AS numdoc"
				. " FROM #__categories AS cc"
				. " LEFT JOIN #__content AS c ON c.catid = cc.id AND c.state = 1"
				. " WHERE " . implode( " AND ", $wheres )
				. " GROUP BY cc.id"
				. " ORDER BY cc.ordering";
				
		return $query;
		
		
	}
	
	function getMostViewedDocsList($params) {
		$user	=& JFactory::getUser();
		$aid 	= $user->get('aid', 0);	
		
		if (empty($this->_most_viewed_docs)) {	
			global $mainframe;
			$user 						= &JFactory::getUser();
			$aid 						= $user->get('aid', 0);			
			$query						= $this->_getMostViewedDocsListQuery( $aid, $params );
			$this->_most_viewed_docs 	= $this->_getList( $query );
		}
		return $this->_most_viewed_docs;
	}
	
	function _getMostViewedDocsListQuery( $aid, $params ) {
		
		$wheres = array();
		// PARAMS
		$most_viewed_docs_num = $params->get( 'most_viewed_docs_num', 5 );
		
		/*$display_sections = $params->get('display_sections', '');
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
		}
		
		$displaySections = $params->get('display_sections', '');
		if (count($displaySections) > 1) {
			JArrayHelper::toInteger($displaySections);
			$displaySectionsString	= implode(',', $displaySections);
			$wheres[]	= ' s.id IN ( '.$displaySectionsString.' ) ';
		} else if ((int)$displaySections > 0) {
			$wheres[]	= ' s.id IN ( '.$displaySections.' ) ';
		}*/
		
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