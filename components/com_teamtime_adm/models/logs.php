<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/*
  Class: LogModelLogs
  The Model Class for Logs
 */

class LogModelLogs extends JModel {

	/**
	 * Table
	 *
	 * @var array
	 */
	var $_table = 'log';
	/**
	 * Data array
	 *
	 * @var array
	 */
	var $_data = null;
	/**
	 * Total
	 *
	 * @var integer
	 */
	var $_total = null;
	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Constructor
	 *
	 */
	function __construct() {
		parent::__construct();

		global $mainframe, $option;

		// get request vars
		$filter_order = $mainframe->getUserStateFromRequest($option . $this->getName() . '.filter_order', 'filter_order', 'a.id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option . $this->getName() . '.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$filter_user_id = $mainframe->getUserStateFromRequest($option . '.filter_user_id', 'filter_user_id', '', 'int');
		
		$filter_project_id = $mainframe->getUserStateFromRequest($option . $this->getName() . '.filter_project_id', 'filter_project_id', '', 'int');
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option . $this->getName() . '.limitstart', 'limitstart', 0, 'int');
		$search = $mainframe->getUserStateFromRequest($option . $this->getName() . '.search', 'search', '', 'string');

		// convert search to lower case
		$search = JString::strtolower($search);

		// in case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		// set model vars
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
		$this->setState('filter_user_id', $filter_user_id);
		$this->setState('filter_project_id', $filter_project_id);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		$this->setState('search', $search);
	}

	/**
	 * Method to get item data
	 *
	 * @access public
	 * @return array
	 */
	function getData() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	/**
	 * Method to get the total number of items
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_total)) {
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	function _buildQuery() {
		$table = & $this->getTable($this->_table);
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();

		$query = ' SELECT a.*, b.name AS project_name, c.name AS task_name, d.name AS user_name'
			. ' FROM ' . $table->getTableName() . ' AS a '
			. ' LEFT JOIN #__teamlog_project AS b ON b.id = a.project_id'
			. ' LEFT JOIN #__teamlog_task AS c ON c.id = a.task_id'
			. ' LEFT JOIN #__users AS d ON d.id = a.user_id'
			. $where
			. $orderby
		;

		return $query;
	}

	function _buildContentWhere() {
		global $mainframe, $option;

		$db = & JFactory::getDBO();
		$search = $this->getState('search');
		$user_id = $this->getState('filter_user_id');
		$project_id = $this->getState('filter_project_id');
		$where = array();

		// search filter
		if ($search) {
			$where[] = 'LOWER(a.description) LIKE ' . $db->Quote('%' . $db->getEscaped($search, true) . '%', false);
		}

		// user filter
		if ($user_id > 0) {
			$where[] = 'a.user_id = ' . (int) $user_id;
		}

		// project filter
		if ($project_id > 0) {
			$where[] = 'a.project_id = ' . (int) $project_id;
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

	function _buildContentOrderBy() {
		global $mainframe, $option;

		$orderby = ' ORDER BY ' . $this->getState('filter_order') . ' ' . $this->getState('filter_order_Dir');

		return $orderby;
	}

}