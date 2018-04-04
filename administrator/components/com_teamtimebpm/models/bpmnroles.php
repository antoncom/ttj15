<?php

class BpmnRoleModelBpmnRoles extends Core_Joomla_ManagerList {

	public $_table = 'bpmnrole';

	public function __construct() {
		$mainframe = & JFactory::getApplication();
		$option = JRequest::getCmd('option');

		parent::__construct();

		// get request vars
		$fromPeriod = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.from_period', 'from_period', '', 'string');
		$untilPeriod = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.until_period', 'until_period', '', 'string');

		// set model vars
		$this->setState('from_period', $fromPeriod);
		$this->setState('until_period', $untilPeriod);
	}

	protected function _buildQuery() {
		$table = & $this->getTable($this->_table);
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();

		$fromPeriod = $this->getState('from_period');
		$untilPeriod = $this->getState('until_period');

		$fields = array();
		$joins = array();

		if (TeamTime::addonExists("com_teamtimecareer")) {
			$fields[] = "tv.title as target_name";
			$joins[] = " left join #__teamtimecareer_targetvector as tv on a.target_id = tv.id";
		}

		$fields[] = "u.name as user_name";
		$joins[] = " left join #__users as u on a.user_id = u.id";

		if (sizeof($fields) > 0) {
			$fields = ", " . implode(", ", $fields);
		}
		else {
			$fields = "";
		}

		if (sizeof($joins) > 0) {
			$joins = implode("\n", $joins);
		}
		else {
			$joins = "";
		}

		$query = "select a.* {$fields}
			from " . $table->getTableName() . " as a
			{$joins} " . $where . " " . $orderby;

		return $query;
	}

	protected function _buildContentWhere() {
		$search = $this->getState('search');

		$fromPeriod = $this->getState('from_period');
		$untilPeriod = $this->getState('until_period');

		$where = array();

		if ($search) {
			$where[] = 'LOWER(a.name) LIKE ' . $this->_db->Quote(
							'%' . $this->_db->getEscaped($search, true) . '%', false);
		}

		$where = sizeof($where) ? ' WHERE ' . implode(' AND ', $where) : '';

		return $where;
	}

	protected function _buildContentOrderBy() {
		$orderby = ' ORDER BY ' . $this->getState('filter_order') .
				' ' . $this->getState('filter_order_Dir');

		return $orderby;
	}

}