<?php

class TeamtimeformalsModelFormals extends Core_Joomla_ManagerList {

	public $_table = 'formal';

	public function __construct() {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		parent::__construct();

		// get request vars
		$filterType = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.filter_type', 'filter_type', '', 'cmd');
		$filterProject = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.filter_project', 'filter_project', '', 'cmd');
		$filterUsing = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.filter_using', 'filter_using', '', 'cmd');

		// convert using type
		$doctypeModel = new DoctypeModelDoctype();
		if (in_array($filterUsing, array_keys($doctypeModel->getUsings()))) {
			$filterUsing = $doctypeModel->getUsingIndex($filterUsing);
		}

		// set model vars
		$this->setState('filter_type', $filterType);
		$this->setState('filter_project', $filterProject);
		$this->setState('filter_using', $filterUsing);
	}

	protected function _buildQuery() {
		$table = & $this->getTable($this->_table);
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();

		$using = $this->getState('filter_using');

		if ($using == "") {
			$sfield = " b.name as project_name, u.name as user_name ";
			$s = "
				left join #__teamtime_project as b on a.project_id = b.id
				left join #__users as u on a.project_id = u.id ";
		}
		else if ($using == "0") {
			$sfield = " b.name as project_name ";
			$s = "
				left join #__teamtime_project as b on a.project_id = b.id ";
		}
		else if ($using == "1") {
			$sfield = " u.name as user_name ";
			$s = "
				left join #__users as u on a.project_id = u.id ";
		}

		$query = "select a.*, c.name as template_name, {$sfield}
			from " . $table->getTableName() . " as a
			{$s}
			left join #__teamtimeformals_template as c on a.doctype_id = c.id
			left join #__teamtimeformals_type as t on c.type = t.id
			" . $where . "
			group by a.id
			" . $orderby;

		//error_log($query);

		return $query;
	}

	private function getProjectFilter($projectId) {
		$result = array();

		$result[] = "t.using_in = " . $this->_db->Quote("project");

		if ($projectId !== '') {
			$projectId = array($projectId);
		}
		else {
			$projectId = null;
		}
		$acl = new TeamTime_Acl();
		$projectId = $acl->filterUserProjectIds($projectId);
		if ($projectId !== null) {
			$result[] = 'a.project_id in (' . implode(",", $projectId) . ")";
		}
		else {
			$result[] = "b.id is not null";
		}

		return $result;
	}

	private function getUserFilter($userId) {
		$result = array();

		$result[] = "t.using_in = " . $this->_db->Quote("user");

		if ($userId != '') {
			$result[] = 'a.project_id = ' . (int) $userId;
		}
		else {
			$mProject = new TeamtimeModelProject();
			$acl = new TeamTime_Acl();
			$projectId = $acl->filterUserProjectIds();
			if ($projectId !== null) {
				$result[] = 'a.project_id in (' . implode(",", $mProject->getUsersIds($projectId)) . ")";
			}
			else {
				$result[] = "u.id is not null";
			}
		}

		return $result;
	}

	private function getProjectAndUserFlter($projectId) {
		$result = array();

		$result[] = "(" . implode(" and ", $this->getProjectFilter($projectId)) . ")";
		$result[] = "(" . implode(" and ", $this->getUserFilter($projectId)) . ")";
		$result = "(" . implode(" or ", $result) . ")";

		return $result;
	}

	protected function _buildContentWhere() {
		$search = $this->getState('search');
		$type = $this->getState('filter_type');
		$projectId = $this->getState('filter_project');
		$using = $this->getState('filter_using');

		$where = array();

		if ($search) {
			$where[] = 'LOWER(a.name) LIKE ' .
					$this->_db->Quote('%' . $this->_db->getEscaped($search, true) . '%', false);
		}

		if ($type !== '') {
			$where[] = 'a.doctype_id = ' . intval($type);
		}

		if ($using != "") {
			if ($using == "0") {
				$where = array_merge($where, $this->getProjectFilter($projectId));
			}
			else if ($using == "1") {
				$where = array_merge($where, $this->getUserFilter($projectId));
			}
		}
		else {
			$where[] = $this->getProjectAndUserFlter($projectId);
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

}