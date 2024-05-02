<?php
/**
 * HsConfigs Model for Highslide Configuration Component
 *
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

/**
 * HsConfig Model
 */
class HsConfigsModelHsConfigs extends JModel
{
	/**
	 * HsConfigs data array
	 *
	 * @var array
	 */
	var $_list = null;

	var $_page = null;

	/**
	 * Retrieves the hsconfig data
	 * @return array Array of objects containing the data from the database
	 */
	function getList()
	{
		global $mainframe;

		if (!empty($this->_list))
		{
			return $this->_list;
		}

		// Initialize variables
		$db		=& $this->getDBO();

		// Get some variables from the request
		$limit				= $mainframe->getUserStateFromRequest('global.list.limit',			'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart			= $mainframe->getUserStateFromRequest('articleelement.limitstart',	'limitstart',		0,	'int');

		// Get the total number of records
		$query = ' SELECT count(*) '
			. ' FROM #__hsconfig AS a '
			. ' LEFT JOIN #__content AS b '
			. ' ON a.id = b.id '
		;

		$db->setQuery($query);
		$total = $db->loadResult();

		// Create the pagination object
		jimport('joomla.html.pagination');
		$this->_page = new JPagination($total, $limitstart, $limit);

		$query = ' SELECT a.*, b.title '
			. ' FROM #__hsconfig AS a '
			. ' LEFT JOIN #__content AS b '
			. ' ON a.id = b.id '
			. ' ORDER BY a.id '
		;

		$db->setQuery($query, $this->_page->limitstart, $this->_page->limit);
		$this->_list = $db->loadObjectList();

		for ($i=0, $n=count( $this->_list ); $i < $n; $i++)
		{
			$row = &$this->_list[$i];
			$row->published = HsConfigHelper::isPublished( $row->id);
		}
		return $this->_list;
	}

	function getPagination()
	{
		if (is_null($this->_list) || is_null($this->_page)) {
			$this->getList();
		}
		return $this->_page;
	}
}
