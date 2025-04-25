<?php

class TeamtimeModelUsers extends Core_Joomla_ManagerList {

	public $_table = 'teamtimeuser';

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
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();

		$query = 'select a.*, ud.send_msg, ud.hour_price
			from #__users as a
			left join #__teamtime_project_user AS b on a.id = b.user_id
			left join #__teamtime_userdata as ud on a.id = ud.user_id			
			' . $where . '
			group by a.id
			' . $orderby;
		
		//error_log($query);

		return $query;
	}

	protected function _buildContentWhere() {
		$search = $this->getState('search');

		$where = array();

		$where[] = 'a.block = 0';

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