<?php

class DoctypeModelDoctypes extends Core_Joomla_ManagerList {

	public $_table = 'doctype';

	public function __construct() {
		$mainframe = & JFactory::getApplication();
		$option = JRequest::getCmd('option');

		parent::__construct();

		$filterType = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.filter_type', 'filter_type', '', 'cmd');

		// set model vars
		$this->setState('filter_type', $filterType);
	}

	protected function _buildQuery() {
		$table = & $this->getTable($this->_table);
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();

		$query = 'select a.* from ' . $table->getTableName() . ' as a
			' . $where . '
			' . $orderby;

		return $query;
	}

	protected function _buildContentWhere() {
		$search = $this->getState('search');
		$type = $this->getState('filter_type');

		$where = array();

		if ($search) {
			$where[] = 'LOWER(a.name) LIKE ' . $this->_db->Quote(
							'%' . $this->_db->getEscaped($search, true) . '%', false);
		}

		if ($type !== '') {
			$where[] = 'a.generator = ' . $this->_db->Quote($type);
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

}