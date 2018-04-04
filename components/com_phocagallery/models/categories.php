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
defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class PhocagalleryModelCategories extends JModel
{
	var $_data 					= null;
	var $_total 				= null;
	var $_context 				= 'com_phocagallery.categories';

	function __construct() {
		
		global $mainframe;
		parent::__construct();

		$config 			= JFactory::getConfig();		
		$paramsC 			= JComponentHelper::getParams('com_phocagallery') ;
		$default_pagination	= $paramsC->get( 'default_pagination_categories', '0' );
		$context			= $this->_context.'.';
	
		// Get the pagination request variables
		$this->setState('limit', $mainframe->getUserStateFromRequest($context .'limit', 'limit', $default_pagination, 'int'));
		$this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));
		// In case limit has been changed, adjust limitstart accordingly
		$this->setState('limitstart', ($this->getState('limit') != 0 ? (floor($this->getState('limitstart') / $this->getState('limit')) * $this->getState('limit')) : 0));
		// Get the filter request variables
		$this->setState('filter_order', JRequest::getCmd('filter_order', 'ordering'));
		$this->setState('filter_order_dir', JRequest::getCmd('filter_order_Dir', 'ASC'));
	}

	function getData() {
		global $mainframe;
		if (empty($this->_data)) {
			$query = $this->_buildQuery();
			$this->_data = $this->_getList( $query );// We need all data because of tree

			// Order Categories to tree
			$text = ''; // test is tree name e.g. Category >> Subcategory
			$tree = array();
			
			$this->_data = $this->_categoryTree($this->_data, $tree, 0, $text, -1);
			return $this->_data;
		}
	}

	/*
	* Is called after setTotal from the view
	*/
	function getTotal() {
		return $this->_total;
	}
	
	function setTotal($total) {
		$this->_total = (int)$total;
	}

	/*
	 * Is called after setTotal from the view
	 */
	function getPagination() {
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new PhocaGalleryPaginationCategories( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
	
	function _buildQuery() {
		
		global $mainframe;
		
		$user	= &JFactory::getUser();
		$gid	= $user->get('aid', 0);
		
		// Params
		$params	= &$mainframe->getParams();
		$display_subcategories	= $params->get( 'display_subcategories', 1 );
		//$show_empty_categories= $params->get( 'display_empty_categories', 0 );
		//$hide_categories 		= $params->get( 'hide_categories', '' );
		$category_ordering		= $params->get( 'category_ordering', 1 );
		
		// Display or hide subcategories in CATEGORIES VIEW
		$hideSubCatSql = '';
		if ((int)$display_subcategories != 1) {
			$hideSubCatSql = ' AND cc.parent_id = 0';
		}
		
		// Get all categories which should be hidden
		/*$hideCatArray	= explode( ';', trim( $hide_categories ) );
		$hideCatSql		= '';
		if (is_array($hideCatArray)) {
			foreach ($hideCatArray as $value) {
				$hideCatSql .= ' AND cc.id != '. (int) trim($value) .' ';
			}
		}*/
		
		//Display or hide empty categories
		/*	$emptyCat = '';
		if ($show_empty_categories != 1) {
			$emptyCat = ' AND a.published = 1';
		}*/
		phocagalleryimport('phocagallery.ordering.ordering');
		$categoryOrdering = PhocaGalleryOrdering::getOrderingString($category_ordering);
		
		   
		$votes	= ' ORDER BY cc.';
		switch ($categoryOrdering) {
		  case 'count ASC':
			$votes	= ' ORDER BY r.';
			break;
		  case 'count DESC':
			$votes	= ' ORDER BY r.';
			break;
		  case 'average ASC':
			$votes	= ' ORDER BY r.';
			break;
		  case 'average DESC':
			$votes	= ' ORDER BY r.';
			break;
		  default:
			$votes	= ' ORDER BY cc.';
		}
		
		$query = 'SELECT cc.*, a.catid, COUNT(a.id) AS numlinks, u.username AS username, r.count AS ratingcount, r.average AS ratingaverage, uc.avatar AS avatar, uc.approved AS avatarapproved, uc.published AS avatarpublished, a.filename, a.exts, a.extm, a.extw, a.exth,'
		. ' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(\':\', cc.id, cc.alias) ELSE cc.id END as slug'
		. ' FROM #__phocagallery_categories AS cc'
		//. ' LEFT JOIN #__phocagallery AS a ON a.catid = cc.id'
		. ' LEFT JOIN #__phocagallery AS a ON a.catid = cc.id and a.published = 1'
		. ' LEFT JOIN #__phocagallery_user AS uc ON uc.userid = cc.owner_id'
		. ' LEFT JOIN #__users AS u ON u.id = cc.owner_id'
		. ' LEFT JOIN #__phocagallery_votes_statistics AS r ON r.catid = cc.id'
		. ' WHERE cc.published = 1'
		. ' AND cc.approved = 1'
		//. ' AND (a.published = 1 OR a.id is null)'
		//. $emptyCat - need to be set in tree
		. $hideSubCatSql
		//. $hideCatSql - need to be set in tree
		. ' GROUP BY cc.id'
		//. ' ORDER BY cc.'.$categoryOrdering;
		. $votes . $categoryOrdering;
		return $query;
	}
	
	/*
	 * Create category tree
	 */
	function _categoryTree( $data, $tree, $id = 0, $text='', $currentId) {		

		foreach ($data as $key) {	
			$show_text =  $text . $key->title;
			
			static $iCT = 0;// All displayed items
	
			if ($key->parent_id == $id && $currentId != $id && $currentId != $key->id ) {	

				$tree[$iCT] 					= new JObject();
				$tree[$iCT]->id 				= $key->id;
				$tree[$iCT]->title 				= $show_text;
				$tree[$iCT]->title_self 		= $key->title;
				$tree[$iCT]->parent_id			= $key->parent_id;
				$tree[$iCT]->name				= $key->name;
				$tree[$iCT]->alias				= $key->alias;
				$tree[$iCT]->image				= $key->image;
				$tree[$iCT]->section			= $key->section;
				$tree[$iCT]->image_position		= $key->image_position;
				$tree[$iCT]->description		= $key->description;
				$tree[$iCT]->published			= $key->published;
				$tree[$iCT]->editor				= $key->editor;
				$tree[$iCT]->ordering			= $key->ordering;
				$tree[$iCT]->access				= $key->access;
				$tree[$iCT]->count				= $key->count;
				$tree[$iCT]->params				= $key->params;
				$tree[$iCT]->catid				= $key->catid;
				$tree[$iCT]->numlinks			= $key->numlinks;
				$tree[$iCT]->slug				= $key->slug;
				$tree[$iCT]->hits				= $key->hits;
				$tree[$iCT]->username			= $key->username;
				$tree[$iCT]->ratingaverage		= $key->ratingaverage;
				$tree[$iCT]->ratingcount		= $key->ratingcount;
				$tree[$iCT]->accessuserid		= $key->accessuserid;
				$tree[$iCT]->uploaduserid		= $key->uploaduserid;
				$tree[$iCT]->deleteuserid		= $key->deleteuserid;
				$tree[$iCT]->userfolder			= $key->userfolder;
				$tree[$iCT]->latitude			= $key->latitude;
				$tree[$iCT]->longitude			= $key->longitude;
				$tree[$iCT]->zoom				= $key->zoom;
				$tree[$iCT]->geotitle			= $key->geotitle;
				$tree[$iCT]->avatar				= $key->avatar;
				$tree[$iCT]->avatarapproved		= $key->avatarapproved;
				$tree[$iCT]->avatarpublished	= $key->avatarpublished;
				$tree[$iCT]->link				= '';
				$tree[$iCT]->filename			= '';// Will be added in View (after items will be reduced)
				$tree[$iCT]->extid				= $key->extid;// Picasa Album
				// info about one image (not using recursive function)
				$tree[$iCT]->filename			= $key->filename;
				$tree[$iCT]->extm				= $key->extm;
				$tree[$iCT]->exts				= $key->exts;
				$tree[$iCT]->extw				= $key->extw;
				$tree[$iCT]->exth				= $key->exth;
				
				$tree[$iCT]->linkthumbnailpath	= '';
				$iCT++;
				
				$tree = $this->_categoryTree($data, $tree, $key->id, $show_text . " &raquo; ", $currentId );	
			}	
		}
		return($tree);
	}
}
?>