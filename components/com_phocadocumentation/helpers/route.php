<?php
/**
 * @version		$Id: route.php 11190 2008-10-20 00:49:55Z ian $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Component Helper
jimport('joomla.application.component.helper');

/**
 * Content Component Route Helper
 *
 * @static
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class PhocaDocumentationHelperRoute
{
	
	function getArticleRoute($id, $alias, $catid, $catalias, $itemid = 0) {
	
		$link = 'index.php?option=com_content&view=article&catid='.$catid.':'.$catalias.'&id='.$id.':'.$alias;

		
		if ((int)$itemid > 0) {
			$link .= '&Itemid='.(int)$itemid;
		} else {
			//$link .= '&Itemid='.JRequest::getVar('Itemid', 0, '', 'int');
		}
		
		return $link;
	}
	
	function getCategoryRoute($catid, $catidAlias = '', $sectionid)
	{
		$needles = array(
			'category' => (int) $catid,
			'section'  => (int) $sectionid,
			'sections' => ''
		);
		
		if ($catidAlias != '') {
			$catid = $catid . ':' . $catidAlias;
		}

		//Create the link
		$link = 'index.php?option=com_phocadocumentation&view=category&id='.$catid;

		if($item = PhocaDocumentationHelperRoute::_findItem($needles)) {
			if(isset($item->query['layout'])) {
				$link .= '&layout='.$item->query['layout'];
			}
			$link .= '&Itemid='.$item->id;
		};

		return $link;
	}
	
	function getSectionRoute($sectionid, $sectionidAlias = '')
	{
		$needles = array(
			'section' => (int) $sectionid,
			'sections' => ''
		);
		
		if ($sectionidAlias != '') {
			$sectionid = $sectionid . ':' . $sectionidAlias;
		}

		//Create the link
		$link = 'index.php?option=com_phocadocumentation&view=section&id='.$sectionid;

		if($item = PhocaDocumentationHelperRoute::_findItem($needles)) {
			if(isset($item->query['layout'])) {
				$link .= '&layout='.$item->query['layout'];
			}
			$link .= '&Itemid='.$item->id;
		};

		return $link;
	}
	
	function getSectionsRoute()
	{
		$needles = array(
			'sections' => ''
		);
		
		//Create the link
		$link = 'index.php?option=com_phocadocumentation&view=sections';

		if($item = PhocaDocumentationHelperRoute::_findItem($needles)) {
			if(isset($item->query['layout'])) {
				$link .= '&layout='.$item->query['layout'];
			}
			$link .= '&Itemid='.$item->id;
		};

		return $link;
	}

	function _findItem($needles, $notCheckId = 0)
	{
		$component =& JComponentHelper::getComponent('com_phocadocumentation');

		$menus	= &JApplication::getMenu('site', array());
		$items	= $menus->getItems('componentid', $component->id);

		if(!$items) {
			return JRequest::getVar('Itemid', 0, '', 'int');
			//return null;
		}
		
		$match = null;
		

		foreach($needles as $needle => $id)
		{
			
			if ($notCheckId == 0) {
				foreach($items as $item) {
					if ((@$item->query['view'] == $needle) && (@$item->query['id'] == $id)) {
						$match = $item;
						break;
					}
				}
			} else {
				foreach($items as $item) {
					if (@$item->query['view'] == $needle) {
						$match = $item;
						break;
					}
				}
			}

			if(isset($match)) {
				break;
			}
		}

		return $match;
	}
}
?>
