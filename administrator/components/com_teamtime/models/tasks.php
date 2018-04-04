<?php

class TeamtimeModelTasks extends Core_Joomla_ManagerList {

	public $_table = 'task';

	public function __construct() {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		parent::__construct();

		// get request vars
		$filterState = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.filter_state', 'filter_state', '', 'cmd');
		$filterProject = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.filter_project', 'filter_project', '', 'cmd');
		$filterType = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.filter_type', 'filter_type', '', 'cmd');

		// set model vars
		$this->setState('filter_state', $filterState);
		$this->setState('filter_project', $filterProject);
		$this->setState('filter_type', $filterType);

		if (TeamTime::addonExists("com_teamtimecareer")) {
			$filterTargetId = $mainframe->getUserStateFromRequest(
					$option . '.filter_target_id', 'filter_target_id', '', 'int');
			$this->setState('filter_target_id', $filterTargetId);
		}
	}

	protected function _buildQuery() {
		$table = & $this->getTable($this->_table);
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();

		$sqlTeamtimeCareer = TeamTime::helper()->getDotu()->getTasksSqlData();

		$query = ' SELECT a.*, b.name AS project_name, c.name AS type_name';

		if (!$sqlTeamtimeCareer instanceof TeamTime_Undefined) {
			$query .= $sqlTeamtimeCareer["fields"];
		}

		$query .= ' FROM ' . $table->getTableName() . ' AS a
			LEFT JOIN #__teamtime_project AS b ON b.id = a.project_id
			LEFT JOIN #__teamtime_type AS c ON c.id = a.type_id
		';

		if (!$sqlTeamtimeCareer instanceof TeamTime_Undefined) {
			$query .= $sqlTeamtimeCareer["join"];
		}

		$query .= $where . $orderby;

		//error_log($query);

		return $query;
	}

	protected function _buildContentWhere() {
		$search = $this->getState('search');
		$state = $this->getState('filter_state');

		$projectId = $this->getState('filter_project');
		$type = $this->getState('filter_type');
		$targetId = $this->getState('filter_target_id');

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

		// project filter
		if ($projectId != '') {
			$projectId = array($projectId);
		}
		else {
			$projectId = null;
		}
		$acl = new TeamTime_Acl();
		$projectId = $acl->filterUserProjectIds($projectId);
		if ($projectId !== null) {
			$where[] = 'a.project_id in (' . implode(",", $projectId) . ")";
		}

		if ($type !== '') {
			$where[] = 'a.type_id = ' . intval($type);
		}

		if ($targetId > 0) {
			$where[] = 'tv.id = ' . intval($targetId);
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

	function _buildContentOrderBy() {
		$order = $this->getState('filter_order');
		if (TeamTime::addonExists("com_teamtimecareer") && $order == 'a.rate') {
			$orderby = ' ORDER BY if(tp.price, tv.hourprice, a.rate) ' . $order;
		}
		else {
			$orderby = ' ORDER BY ' . $order . ' ' . $this->getState('filter_order_Dir');
		}

		return $orderby;
	}

}