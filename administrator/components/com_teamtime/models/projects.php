<?php

class TeamtimeModelProjects extends Core_Joomla_ManagerList {

	public $_table = 'project';

	public function __construct() {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		parent::__construct();

		// get request vars
		$filterState = $mainframe->getUserStateFromRequest($option . $this->getName() . '.filter_state',
				'filter_state', '', 'cmd');
		$fromPeriod = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.from_period', 'from_period', '', 'string');
		$untilPeriod = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.until_period', 'until_period', '', 'string');

		// set model vars
		$this->setState('filter_state', $filterState);
		$this->setState('from_period', $fromPeriod);
		$this->setState('until_period', $untilPeriod);
	}

	private function getCalcFields() {
		$fromPeriod = $this->getState('from_period');
		$untilPeriod = $this->getState('until_period');

		// hours fact date filter
		if ($fromPeriod && $untilPeriod) {
			$whereStr = ' and date >= ' . $this->_db->Quote(
							$this->_db->getEscaped("$fromPeriod 00:00:00", true), false) .
					' and date <= ' . $this->_db->Quote(
							$this->_db->getEscaped("$untilPeriod 23:59:59", true), false);
		}
		else {
			$whereStr = "";
		}

		$result = ", (
			select sum(duration)/60 from #__teamtime_log
				where project_id = a.id
					$whereStr) as sfact,
			(SELECT SUM(t.hours_plan)
				from #__teamtime_todo as t
				where project_id = a.id) as splan ";

		return $result;
	}

	protected function _buildQuery($calcFields = false) {
		$table = & $this->getTable($this->_table);
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();

		if ($calcFields)
			$scalcFields = $this->getCalcFields();
		else {
			$scalcFields = "";
		}

		$query = ' SELECT a.*
			' . $scalcFields . '
			from ' . $table->getTableName() . ' AS a
			' . $where . $orderby;

		//var_dump($query);

		return $query;
	}

	protected function _buildContentWhere() {
		$search = $this->getState('search');
		$state = $this->getState('filter_state');

		$where = array();

		// search filter
		if ($search) {
			$where[] = 'LOWER(a.name) LIKE ' . $this->_db->Quote(
							'%' . $this->_db->getEscaped($search, true) . '%', false);
		}

		// state filter
		if ($state !== '') {
			$where[] = 'a.state = ' . intval($state);
		}

		$acl = new TeamTime_Acl();
		$projectId = $acl->filterUserProjectIds();
		if ($projectId !== null) {
			$where[] = 'a.id in (' . implode(",", $projectId) . ")";
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

}