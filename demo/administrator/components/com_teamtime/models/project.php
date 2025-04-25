<?php

class TeamtimeModelProject extends Core_Joomla_Manager {

	public $_table = 'project';

	public function __construct() {
		parent::__construct();

		$array = JRequest::getVar('cid', array(0), '', 'array');
		$report = JRequest::getVar('report', false);
		if ($report) {
			$this->setId((int) $array[0]);
		}
	}

	public function store($data) {
		$row = & $this->getTable($this->_table);

		// bind the form fields
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// check if model item data is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// store model item to the database
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$this->_data = $row;

		if (isset($data["users"])) {
			$this->setUsersIds($this->_data->id, $data["users"]);
		}
		TeamTime::trigger()->onSaveProjectParams($this, $data);

		return true;
	}

	public function storeState($id, $state) {
		$row = & $this->getTable($this->_table);

		// store state to database
		if (!$row->setProjectState($id, $state)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	public function delete($cid = array()) {
		$table = & $this->getTable($this->_table);
		if (count($cid)) {
			JArrayHelper::toInteger($cid);
			$cids = implode(',', $cid);
			$query = 'DELETE FROM ' . $table->getTableName()
					. ' WHERE id IN (' . $cids . ')';
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			else {
				foreach ($cid as $id) {
					$this->removeUsersIds($id);
				}
			}
		}

		return true;
	}

	public function getMaxRate($projectId, $isDynamicRate = 0) {
		$result = 0;

		// calculate price for dynamic rate
		if ($isDynamicRate) {
			$query = "select * from #__teamtime_task as a
        where a.project_id = " . (int) $projectId;

			$this->_db->setQuery($query);
			$rows = $this->_db->loadObjectList();

			$values = array();
			foreach ($rows as $row) {
				// check price by dotu
				$tmp_rate = TeamTime::helper()->getDotu()->getTargetPrice(array(
					"task_id" => $row->id
						), true);
				if ($tmp_rate === null || ($tmp_rate instanceof TeamTime_Undefined)) {
					$tmp_rate = $row->rate;
				}

				$values[] = $tmp_rate;
			}

			$result = max($values);
		}

		// calculate price for static rate
		else {
			$query = "select max(a.rate) as task_rate, max(b.rate) as type_rate
        from #__teamtime_task as a
        left join #__teamtime_type as b on a.type_id = b.id
        where a.project_id = " . (int) $projectId;

			$this->_db->setQuery($query);
			$row = $this->_db->loadObject();

			$result = max($row->task_rate, $row->type_rate);
		}

		return $result;
	}

	public function getMinRate($projectId, $isDynamicRate = 0) {
		$result = 0;

		// calculate price for dynamic rate
		if ($isDynamicRate) {
			$query = "select * from #__teamtime_task as a
        where a.project_id = " . (int) $projectId;

			$this->_db->setQuery($query);
			$rows = $this->_db->loadObjectList();

			$values = array();
			foreach ($rows as $row) {
				// check price by dotu
				$tmp_rate = TeamTime::helper()->getDotu()->getTargetPrice(array(
					"task_id" => $row->id
						), true);
				if ($tmp_rate === null || ($tmp_rate instanceof TeamTime_Undefined)) {
					$tmp_rate = $row->rate;
				}

				$values[] = $tmp_rate;
			}

			$result = min($values);
		}

		// calculate price for static rate
		else {
			$query = "select min(a.rate) as task_rate, min(b.rate) as type_rate
        from #__teamtime_task as a
        left join #__teamtime_type as b on a.type_id = b.id
        where a.project_id = " . (int) $projectId;

			$this->_db->setQuery($query);
			$row = $this->_db->loadObject();

			$result = min($row->task_rate, $row->type_rate);
		}

		return $result;
	}

	/**
	 * sql filter for projects enabled for current user
	 * @param type $userId
	 */
	public function getProjectsForUserSqlFilter($userId, $colName = "project_id") {
		$table = & $this->getTable($this->_table);

		/*
		  $allSelectedProjects = array();
		  $query = "select project_id from #__teamtime_project_user
		  group by project_id";
		  $this->_db->setQuery($query);
		  $rows = $this->_db->loadObjectList();
		  foreach ($rows as $row) {
		  $allSelectedProjects[] = $row->project_id;
		  }
		 */

		$userProjects = array();
		$query = "select project_id from #__teamtime_project_user
			where user_id = " . (int) $userId . "
			group by project_id";
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		foreach ($rows as $row) {
			$userProjects[] = $row->project_id;
		}

		$filter = array();
		//if (sizeof($allSelectedProjects) > 0) {
		//	$filter[] = $colName . " not in (" . implode(",", $allSelectedProjects) . ")";
		//}
		if (sizeof($userProjects) > 0) {
			$filter[] = $colName . " in (" . implode(",", $userProjects) . ")";
		}

		if (sizeof($filter) > 0) {
			$result = "(" . implode(" or ", $filter) . ")";
		}
		else {
			$result = "1";
		}

		return $result;
	}

	public function getActiveProjects($filterUserProjects = false) {
		$table = & $this->getTable($this->_table);

		$user = & JFactory::getUser();
		$currentUserId = $user->id;

		$where = array();
		$where[] = "state = " . PROJECT_STATE_OPEN;

		if ($filterUserProjects) {
			$where[] = $this->getProjectsForUserSqlFilter($currentUserId, "id");
		}
		else {
			// $user->usertype == "Super Administrator" || $user->usertype == "Administrator"
		}

		if (sizeof($where) > 0) {
			$where = " where " . implode(" and ", $where);
		}
		else {
			$where = "";
		}

		$query = " select * from " . $table->getTableName() .
				$where .
				" order by name";
		$this->_db->setQuery($query);

		$result = $this->_db->loadAssocList();
		$result = $table->loadObjects($result);

		return $result;
	}

	public function getActiveProjectsIds($filterUserProjects = false) {
		$result = array();
		$projects = $this->getActiveProjects($filterUserProjects);
		foreach ($projects as $project) {
			$result[] = $project->id;
		}

		return $result;
	}

	public function projectIsAllowed($id) {
		$allowedProjects = $this->getActiveProjectsIds(true);

		return in_array($id, $allowedProjects);
	}

	public function showProjectDescription() {
		$table = & $this->getTable($this->_table);

		$id = (int) JRequest::getVar('todo_id');
		if ($id > 0) {
			$this->_db->setQuery("select * from #__teamtime_todo
				where id = " . $id);
			$result = $this->_db->loadObject();
			$id = $result->project_id;
		}
		else {
			$id = (int) JRequest::getVar('project_id');
		}

		if (!$this->projectIsAllowed($id)) {
			return;
		}

		$this->_db->setQuery("select * from " . $table->getTableName() . "
				where id = " . $id);
		$result = $this->_db->loadObject();

		if (trim($result->description) != "") {
			print "<h3>" . $result->name . "</h3>";
			//print TeamTime::helper()->getBase()->convertTextLinks($result->description);
			print $result->description;
		}
	}

	private function getHoursFact($projectId, $whereStr = "") {
		$query = "select sum(duration)/60 as sfact
			from #__teamtime_log
			where project_id = " . (int) $projectId . $whereStr;

		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();

		return $row->sfact;
	}

	private function getHoursPlan($projectId, $whereStr = "") {
		$query = "select sum(hours_plan) as splan
			from #__teamtime_todo
			where project_id = " . (int) $projectId . $whereStr;

		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();

		return $row->splan;
	}

	public function initHoursForProjects($items, $fromPeriod, $untilPeriod) {
		if ($fromPeriod && $untilPeriod) {
			$whereHoursFact = ' and date >= ' . $this->_db->Quote(
							$this->_db->getEscaped("$fromPeriod 00:00:00", true), false) .
					' and date <= ' . $this->_db->Quote(
							$this->_db->getEscaped("$untilPeriod 23:59:59", true), false);
		}
		else {
			$whereHoursFact = "";
		}

		if ($fromPeriod && $untilPeriod) {
			$whereHoursPlan = ' and created >= ' . $this->_db->Quote(
							$this->_db->getEscaped("$fromPeriod 00:00:00", true), false) .
					' and created <= ' . $this->_db->Quote(
							$this->_db->getEscaped("$untilPeriod 23:59:59", true), false);
		}
		else {
			$whereHoursPlan = "";
		}

		foreach ($items as $i => $item) {
			$items[$i]->sfact = $this->getHoursFact($item->id, $whereHoursFact);
			$items[$i]->splan = $this->getHoursPlan($item->id, $whereHoursPlan);
		}

		return $items;
	}

	public function removeUsersIds($projectId) {
		$query = "delete from #__teamtime_project_user
			where project_id = " . (int) $projectId;
		$this->_db->Execute($query);
	}

	public function setUsersIds($projectId, $userIds) {
		if (!is_array($userIds) || sizeof($userIds) == 0 || $userIds[0] == 0) {
			return;
		}

		$this->removeUsersIds($projectId);

		foreach ($userIds as $id) {
			$query = "insert into #__teamtime_project_user
					values(" . (int) $projectId . ", " . (int) $id . ")";
			$this->_db->Execute($query);
		}
	}

	public function getUsersIds($projectId) {
		$result = array();

		if (is_array($projectId)) {
			$where = 'project_id in (' . implode(",", $projectId) . ")";
		}
		else {
			$where = "project_id = " . (int) $projectId;
		}
		$where = " where " . $where;

		$query = "select * from #__teamtime_project_user
			" . $where;

		//error_log($query);

		$this->_db->setQuery($query);
		foreach ($this->_db->loadObjectList() as $row) {
			$result[] = $row->user_id;
		}

		return $result;
	}

	public function filterWithAllowedProjects($ids, $acl) {
		return $acl->filterUserProjectIds($ids);
	}

}