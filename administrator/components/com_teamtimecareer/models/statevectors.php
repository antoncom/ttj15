<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TeamtimecareerModelStatevectors extends Core_Joomla_ManagerList {

	public $_table = 'statevector';

	function __construct() {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		parent::__construct();

		// get request vars
		$filterUserId = $mainframe->getUserStateFromRequest(
				$option . '.filter_user_id', 'filter_user_id', '', 'int');
		$filterTargetId = $mainframe->getUserStateFromRequest(
				$option . '.filter_target_id', 'filter_target_id', '', 'int');
		$filterType = $mainframe->getUserStateFromRequest(
				$option . '.filter_type', 'filter_type', '', 'string');

		$fromPeriod = $mainframe->getUserStateFromRequest(
				$option . '.from_period', 'from_period', '', 'string');
		$untilPeriod = $mainframe->getUserStateFromRequest(
				$option . '.until_period', 'until_period', '', 'string');

		// set model vars
		$this->setState('filter_user_id', $filterUserId);
		$this->setState('filter_target_id', $filterTargetId);
		$this->setState('filter_type', $filterType);
		$this->setState('from_period', $fromPeriod);
		$this->setState('until_period', $untilPeriod);
	}

	public function getTotalStat() {
		if (empty($this->_totalstat)) {
			$query = $this->_buildQueryStat();

			$this->_db->setQuery($query);
			$row = $this->_db->loadObject();

			$this->_totalstat = array(
				"num" => $row->num
			);
		}

		return $this->_totalstat;
	}

	protected function _buildQuery() {
		$table = & $this->getTable($this->_table);
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();

		$query = 'select a.*, tv.title as goal, u.name as user_name, a.date as date
      from ' . $table->getTableName() . ' as a
      left join #__teamtimecareer_targetvector as tv on a.target_id = tv.id
      left join #__users as u on a.user_id = u.id
			' . $where . '
			' . $orderby;

		//error_log($this->_db->replacePrefix($query));

		return $query;
	}

	protected function _buildQueryStat() {
		$table = & $this->getTable($this->_table);
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();

		$query = 'select sum(a.num) as num
      from ' . $table->getTableName() . ' as a
      left join #__teamtimecareer_targetvector as tv on a.target_id = tv.id
      left join #__users as u on a.user_id = u.id
			' . $where . '
			' . $orderby;

		return $query;
	}

	protected function _buildContentWhere() {
		$search = $this->getState('search');
		$userId = $this->getState('filter_user_id');
		$targetId = $this->getState('filter_target_id');
		$filterType = $this->getState('filter_type');

		$fromPeriod = $this->getState('from_period');
		$untilPeriod = $this->getState('until_period');

		$where = array();

		if ($search) {
			$search = $this->_db->Quote('%' . $this->_db->getEscaped($search, true) . '%', false);
			$where[] = '(LOWER(tv.title) LIKE ' . $search .
					' or LOWER(a.description) LIKE ' . $search . ")";
		}

		$mProject = new TeamtimeModelProject();
		$acl = new TeamTime_Acl();
		$projectId = $acl->filterUserProjectIds();
		if ($projectId !== null) {
			$userIds = $mProject->getUsersIds($projectId);
			if ($userId > 0) {
				$userIds = array_intersect(array($userId), $userIds);
			}
			$where[] = 'a.user_id in (' . implode(",", $userIds) . ")";
		}
		else {
			if ($userId > 0) {
				$where[] = 'a.user_id = ' . intval($userId);
			}
		}

		if ($targetId > 0) {
			$where[] = 'a.target_id = ' . intval($targetId);
		}

		if ($filterType == "logs") {
			$where[] = 'a.log_id > 0';
		}
		else if ($filterType == "indicators") {
			$where[] = '(a.log_id = 0 or a.log_id is null)';
		}

		// date filter
		if ($fromPeriod) {
			$where[] = ' a.date >= ' . $this->_db->Quote(
							$this->_db->getEscaped("$fromPeriod 00:00:00", true), false);
		}
		if ($untilPeriod) {
			$where[] = ' a.date <= ' . $this->_db->Quote(
							$this->_db->getEscaped("$untilPeriod 23:59:59", true), false);
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

}