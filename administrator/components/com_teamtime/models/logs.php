<?php

class TeamtimeModelLogs extends Core_Joomla_ManagerList {

	public $_table = 'log';

	public function __construct() {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		parent::__construct();

		$filterUserId = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.filter_user_id', 'filter_user_id', '', 'int');
		$filterProjectId = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.filter_project_id', 'filter_project_id', '', 'int');

		$this->setState('filter_user_id', $filterUserId);
		$this->setState('filter_project_id', $filterProjectId);
	}

	protected function _buildQuery() {
		$table = & $this->getTable($this->_table);
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();

		$query = ' SELECT a.*, b.name AS project_name, c.name AS task_name, d.name AS user_name
			FROM ' . $table->getTableName() . ' AS a
			LEFT JOIN #__teamtime_project AS b ON b.id = a.project_id
			LEFT JOIN #__teamtime_task AS c ON c.id = a.task_id
			LEFT JOIN #__users AS d ON d.id = a.user_id
			' . $where
				. $orderby;

		return $query;
	}

	protected function _buildContentWhere() {
		$search = $this->getState('search');
		$userId = $this->getState('filter_user_id');
		$projectId = $this->getState('filter_project_id');
		$where = array();

		// search filter
		if ($search) {
			$where[] = 'LOWER(a.description) LIKE ' . $this->_db->Quote(
							'%' . $this->_db->getEscaped($search, true) . '%', false);
		}

		// user filter
		if ($userId > 0) {
			$where[] = 'a.user_id = ' . (int) $userId;
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

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

}