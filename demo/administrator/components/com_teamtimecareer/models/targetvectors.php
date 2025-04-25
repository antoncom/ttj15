<?php

class TeamtimecareerModelTargetvectors extends Core_Joomla_ManagerList {

	public $_table = 'targetvector';

	public function __construct() {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		parent::__construct();

		// get request vars
		$filterLimit = $mainframe->getUserStateFromRequest(
				$option . '.filter_limit', 'filter_limit', 10, 'int');
		$filterGoalsOnly = $mainframe->getUserStateFromRequest(
				$option . '.filter_goalsonly', 'filter_goalsonly', 1, 'int');

		$this->setState('levellimit', $filterLimit);
		$this->setState('goalsonly', $filterGoalsOnly);
	}

	public function getData() {
		if (empty($this->_data)) {
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query);

			$levellimit = $this->getState('levellimit');

			// establish the hierarchy of the items
			$children = array();
			// first pass - collect children
			foreach ($this->_data as $v) {
				$v->name = "";

				$pt = (int) $v->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}

			// second pass - get an indent list of the items
			$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, max(0, $levellimit - 1));
			$list = array_slice($list, $this->getState('limitstart'), $this->getState('limit'));

			$ids = array();
			foreach ($list as $row) {
				$ids[] = $row->id;
			}

			// fix for empty data
			if (sizeof($ids) == 0) {
				$ids[] = 0;
			}

			$query = $this->_buildQuery(array(" a.id in (" . implode(",", $ids) . ") "));
			$this->_data = $this->_getList($query);

			$rowsData = array();
			foreach ($this->_data as $row) {
				$rowsData[$row->id] = $row;
			}
			foreach ($list as $i => $row) {
				$list[$i]->data = $rowsData[$row->id];
			}

			$this->_data = $list;
		}

		if ($this->_db->getErrorMsg()) {
			JError::raiseWarning(500, $this->_db->getErrorMsg());
		}

		return $this->_data;
	}

	protected function _buildQuery() {
		$table = & $this->getTable($this->_table);
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();

		$query = 'select a.*, a.parent_id as parent
			from ' . $table->getTableName() . ' as a
			' . $where . '
			' . $orderby;

		return $query;
	}

	protected function _buildContentWhere() {
		$search = $this->getState('search');
		$goalsonly = $this->getState('goalsonly');

		$where = array();

		if ($search) {
			$search = $this->_db->Quote('%' . $this->_db->getEscaped($search, true) . '%', false);
			$where[] = '(LOWER(a.title) LIKE ' . $search .
					' or LOWER(a.description) LIKE ' . $search . ")";
		}

		if ($goalsonly) {
			$where[] = "a.is_skill = 0";
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

	protected function _buildContentOrderBy() {
		$order = $this->getState('filter_order');
		$orderDir = $this->getState('filter_order_Dir');

		if ($order) {
			$orderby = ' ORDER BY ' . $order . ' ' . $orderDir . ', a.parent_id, a.ordering';
		}
		else {
			$orderby = ' ORDER BY a.parent_id, a.ordering';
		}

		return $orderby;
	}

}