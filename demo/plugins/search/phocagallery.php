<?php
/**
 * @version		$Id: contacts.php 9975 2008-01-30 17:02:11Z ircmaxell $
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$mainframe->registerEvent( 'onSearch', 'plgSearchPhocagallery' );
$mainframe->registerEvent( 'onSearchAreas', 'plgSearchPhocagalleryAreas' );

JPlugin::loadLanguage( 'plg_search_phocagallery' );

/**
 * @return array An array of search areas
 */
function &plgSearchPhocagalleryAreas()
{
	static $areas = array(
		'phocagallery' => 'Phocagallery'
	);
	return $areas;
}

/**
* Contacts Search method
*
* The sql must return the following fields that are used in a common display
* routine: href, title, section, created, text, browsernav
* @param string Target search string
* @param string mathcing option, exact|any|all
* @param string ordering option, newest|oldest|popular|alpha|category
*/
function plgSearchPhocagallery( $text, $phrase='', $ordering='', $areas=null )
{
	$db		=& JFactory::getDBO();
	$user	=& JFactory::getUser();

	if (is_array( $areas )) {
		if (!array_intersect( $areas, array_keys( plgSearchPhocagalleryAreas() ) )) {
			return array();
		}
	}

	// load plugin params info
 	$plugin =& JPluginHelper::getPlugin('search', 'phocagallery');
 	$pluginParams = new JParameter( $plugin->params );

	$limit = $pluginParams->def( 'search_limit', 50 );

	$text = trim( $text );
	if ($text == '') {
		return array();
	}

	$section = JText::_( 'Phocagallery');

	switch ( $ordering ) {
		case 'alpha':
			$order = 'a.title ASC';
			break;

		case 'category':
			$order = 'a.id ASC';
			break;

		case 'popular':
		case 'newest':
		case 'oldest':
		default:
			$order = 'a.title ASC';
	}

$text	= $db->Quote( '%'.$db->getEscaped( $text, true ).'%', false );


	$rows = array();
	
	// search picture categories
			$query = 'SELECT id, a.title AS title, "" AS created,'
		. ' a.description AS text,'
		. ' CONCAT_WS( " / ", '.$db->Quote($section).', a.title ) AS section,'
		. ' "2" AS browsernav'
		. ' FROM #__phocagallery_categories AS a'
		. ' WHERE ( a.title LIKE '.$text
		. ' OR a.name LIKE '.$text
		. ' OR a.description LIKE '.$text.' )'
		. ' AND a.access <= '.(int) $user->get( 'aid' )
		. ' GROUP BY a.id'
		. ' ORDER BY '. $order
		;
		$db->setQuery( $query, 0, $limit );
		$list2 = $db->loadObjectList();
		$limit -= count($list2);

		if(isset($list2))
		{
			foreach($list2 as $key => $item)
			{
				$list2[$key]->href = JRoute::_('index.php?option=com_phocagallery&view=category&id='. $item->id );
			}
		}

		$rows[] = $list2;




	// search pictures
	if ( $limit > 0 )
	{
		switch ( $ordering ) {
		case 'alpha':
			$order = 'a.title ASC';
			break;
		case 'category':
			$order = 'b.title ASC, a.title ASC';
			break;
		case 'popular':
		case 'newest':
			$order = 'a.date ASC';
			break;
		case 'oldest':
			$order = 'a.date DESC';
			break;
		default:
			$order = 'a.title ASC';
		}
	
	$query	= 'SELECT '
	. ' CASE WHEN CHAR_LENGTH(a.title) THEN CONCAT_WS(\': \', b.title, a.title)
ELSE b.title END AS title, '
	. ' CASE WHEN CHAR_LENGTH(a.description) THEN CONCAT_WS(\': \', a.title,
a.description) ELSE a.title END AS text, '
	. ' a.date AS created, '
	. ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug, '
	. ' CASE WHEN CHAR_LENGTH(b.alias) THEN CONCAT_WS(\':\', b.id, b.alias) ELSE b.id END AS catslug, '
	. ' CONCAT_WS( " / ", '.$db->Quote($section).', a.title ) AS section,'
	. ' "2" AS browsernav'
	. ' FROM #__phocagallery AS a'
	. ' LEFT JOIN #__phocagallery_categories AS b ON b.id = a.catid'
	. ' WHERE ( a.title LIKE '.$text
	. ' OR a.filename LIKE '.$text
	. ' OR a.description LIKE '.$text.' )'
	. ' AND a.published = 1'
	. ' AND b.published = 1'
	. ' AND b.access <= '.(int) $user->get( 'aid' )
	. ' ORDER BY '. $order
	;

	$db->setQuery( $query, 0, $limit );
	$list1 = $db->loadObjectList();

		if(isset($list1))
		{
			foreach($list1 as $key => $item) {
					$list1[$key]->href = JRoute::_('index.php?option=com_phocagallery&view=category&id='.$item->catslug );
			}
		}
		$rows[] = $list1;
	}
	

	$results = array();
	if(count($rows))
	{
		foreach($rows as $row)
		{
			$results = array_merge($results, (array) $row);
		}
	}
	

return $results;
}
