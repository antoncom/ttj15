<?php

class TeamtimeModelTypes extends Core_Joomla_ManagerList {

	public $_table = 'type';

	public function __construct() {
		$mainframe =& JFactory::getApplication();
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

		$query = 'select a.* from ' . $table->getTableName() . ' as a
			left join #__teamtime_log AS b on a.id = b.type_id
			' . $where . '
			group by a.id
			' . $orderby;

		//var_dump($query);

		return $query;
	}

	protected function _buildContentWhere() {
		$search = $this->getState('search');

		$where = array();

		if ($search) {
			$where[] = 'LOWER(a.name) LIKE ' . $this->_db->Quote(
							'%' . $this->_db->getEscaped($search, true) . '%', false);
		}

		$acl = new TeamTime_Acl();
		$projectId = $acl->filterUserProjectIds();
		if ($projectId !== null) {
			$where[] = 'b.project_id in (' . implode(",", $projectId) . ")";
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

}