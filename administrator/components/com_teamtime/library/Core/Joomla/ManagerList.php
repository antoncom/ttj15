<?php

class Core_Joomla_ManagerList extends JModel {

	public $_table = '';
	public $_data = null;
	public $_total = null;
	public $_pagination = null;

	public function __construct() {
		$mainframe = & JFactory::getApplication();
		$option = JRequest::getCmd('option');

		parent::__construct();

		// get request vars
		$filterOrder = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.filter_order', 'filter_order', 'a.id', 'cmd');
		$filterOrderDir = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit',
				$mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.limitstart', 'limitstart', 0, 'int');
		// in case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$search = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.search', 'search', '', 'string');
		$search = JString::strtolower($search);

		// set model vars
		$this->setState('filter_order', $filterOrder);
		$this->setState('filter_order_Dir', $filterOrderDir);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		$this->setState('search', $search);
	}

	public function getData() {
		if (empty($this->_data)) {
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

			if ($this->_db->getErrorMsg()) {
				JError::raiseWarning(500, $this->_db->getErrorMsg());
			}
		}

		return $this->_data;
	}

	public function getTotal() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_total)) {
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	public function getPagination() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(),
							$this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	protected function _buildQuery() {
		$table = & $this->getTable($this->_table);
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();

		$query = "select * from " . $table->getTableName() . " as a " .
				$where . " " . $orderby;

		return $query;
	}

	protected function _buildContentWhere() {
		$search = $this->getState('search');

		$where = array();

		if ($search) {
			$where[] = 'lower(a.name) like ' .
					$this->_db->Quote('%' . $this->_db->getEscaped($search, true) . '%', false);
		}

		$where = sizeof($where) ? ' where ' . implode(' and ', $where) : '';

		return $where;
	}

	protected function _buildContentOrderBy() {
		$order = $this->getState('filter_order');
		if ($order == "") {
			return "";
		}

		$orderby = ' order by ' . $order .
				' ' . $this->getState('filter_order_Dir');

		return $orderby;
	}

}