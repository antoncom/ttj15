<?php

jimport('joomla.application.component.model');

class Core_ListModel extends JModel {

	/**
	 * Table
	 *
	 * @var array
	 */
	var $_table = '';

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
		$filter_order = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.filter_order', 'filter_order', 'a.name',
				'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.filter_order_Dir', 'filter_order_Dir', '',
				'word');

		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit',
				$mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.limitstart', 'limitstart', 0, 'int');
		// in case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$search = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.search', 'search', '', 'string');
		$search = JString::strtolower($search);
		//...
		//
		// set model vars
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		$this->setState('search', $search);
		//...
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
			$this->_data = $this->_getList($query, $this->getState('limitstart'),
					$this->getState('limit'));
		}
		if ($this->_db->getErrorMsg()) {
			JError::raiseWarning(500, $this->_db->getErrorMsg());
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
			$this->_pagination = new JPagination($this->getTotal(),
							$this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	function _buildQuery() {
		$table = & $this->getTable($this->_table);
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();

		$query = "select * from " . $table->getTableName() . " as a " .
				$where . " " . $orderby;

		return $query;
	}

	function _buildContentWhere() {
		$search = $this->getState('search');

		$where = array();

		if ($search) {
			$where[] = 'lower(a.name) like ' .
					$this->_db->Quote('%' . $this->_db->getEscaped($search, true) . '%', false);
		}

		$where = sizeof($where) ? ' where ' . implode(' and ', $where) : '';

		return $where;
	}

	function _buildContentOrderBy() {
		$order = $this->getState('filter_order');
		if ($order == "") {
			return "";
		}

		$orderby = ' order by ' . $order .
				' ' . $this->getState('filter_order_Dir');

		return $orderby;
	}

}