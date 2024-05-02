<?php

class TeamtimeModelUser extends Core_Joomla_Manager {

	public $_table = 'teamtimeuser';

	public function &getData() {
		if (empty($this->_data)) {
			$row = new stdClass();
			if ($this->_id) {
				$query = "select * from #__users
					left join #__teamtime_userdata on #__users.id = #__teamtime_userdata.user_id
					where #__users.id = " . (int) $this->_id;

				$this->_db->setQuery($query);
				$row = $this->_db->loadObject();
			}

			// set defaults, if new
			if ($row->id == 0) {
				
			}

			$this->_data = & $row;
		}

		return $this->_data;
	}

	public function store($data) {
		if (!isset($data['send_msg'])) {
			$data['send_msg'] = 0;
		}

		if (!isset($data['hideforother'])) {
			$data['hideforother'] = 0;
		}

		$query = "
			insert into #__teamtime_userdata (
				user_id,
				send_msg,
				hour_price,
				hideforother,
				salary
			)
			values(
				{$data['id']},
				{$data['send_msg']},
				'{$data['hour_price']}',
				'{$data['hideforother']}',
				'{$data['salary']}'
			)
			ON DUPLICATE KEY UPDATE
				send_msg={$data['send_msg']},
				hour_price='{$data['hour_price']}',
				hideforother='{$data['hideforother']}',
				salary='{$data['salary']}'";

		$res = $this->_db->Execute($query);

		if ($res) {
			if (isset($data["projects"])) {
				$this->setProjectsIds($data['id'], $data["projects"]);
			}
			TeamTime::trigger()->onSaveUserParams($data);
		}
		else {
			$this->setError($this->_db->getErrorMsg());
		}

		return $res;
	}

	private function getHoursFact($userId, $whereStr = "") {
		$query = "select sum(duration)/60 as sfact
			from #__teamtime_log
			where user_id = " . (int) $userId . $whereStr;

		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();

		return $row->sfact;
	}

	private function getHoursPlan($userId, $whereStr = "") {
		$query = "select sum(hours_plan) as splan
			from #__teamtime_todo
			where user_id = " . (int) $userId . $whereStr;

		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();

		return $row->splan;
	}

	public function initHoursForUsers($items, $fromPeriod, $untilPeriod) {
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

	public function initDotu($items, $hasDotuPrices) {
		/*
		  foreach ($items as $i => $item) {
		  $tmp = TeamTime::helper()->getDotu()->getMaxPrice($item->id);
		  if ($tmp !== null) {
		  $hasDotuPrices = true;
		  }

		  $items[$i]->dotu_price = $tmp;
		  }
		 */

		return array($items, $hasDotuPrices);
	}

	public function removeProjectsIds($userId) {
		$query = "delete from #__teamtime_project_user
			where user_id = " . (int) $userId;
		$this->_db->Execute($query);
	}

	public function setProjectsIds($userId, $projectIds) {
		if (!is_array($projectIds) || sizeof($projectIds) == 0 || $projectIds[0] == 0) {
			return;
		}

		$this->removeProjectsIds($userId);

		foreach ($projectIds as $id) {
			$query = "insert into #__teamtime_project_user (user_id, project_id)
					values(" . (int) $userId . ", " . (int) $id . ")";
			$this->_db->Execute($query);
		}
	}

	public function getProjectsIds($userId) {
		$result = array();
		$query = "select * from #__teamtime_project_user
			where user_id = " . (int) $userId;

		$this->_db->setQuery($query);
		foreach ($this->_db->loadObjectList() as $row) {
			$result[] = $row->project_id;
		}

		return $result;
	}

	public function filterWithAllowedProjects($ids, $acl) {
		$table = & $this->getTable($this->_table);
		$result = array();

		$where = array();

		$acl = new TeamTime_Acl();
		$projectId = $acl->filterUserProjectIds();
		if ($projectId !== null) {
			$where[] = 'b.project_id in (' . implode(",", $projectId) . ")";
			$where[] = 'a.id in (' . implode(",", $ids) . ")";
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		$query = 'select a.* from #__users as a
			left join #__teamtime_project_user AS b on a.id = b.user_id
			' . $where . '
			group by a.id';

		//error_log($query);

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		foreach ($rows as $row) {
			$result[] = $row->id;
		}

		return $result;
	}

	public function getUsers($filter = array()) {
		$result = array();
		$where = array();

		$where[] = "a.block = 0";

		//...

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		$query = "select a.* from #__users as a
			" . $where . "
			order by a.name";
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		if ($rows) {
			$result = $rows;
		}

		return $result;
	}

	public function setPause() {
		$user = & JFactory::getUser();
		$mLog = new TeamtimeModelLog();

		$unclog = $mLog->getUncompletedLog($user->id);
		$mLog->setPause($unclog);
	}

	public function checkPause() {
		$user = & JFactory::getUser();
		$mLog = new TeamtimeModelLog();

		$unclog = $mLog->getUncompletedLog($user->id);

		return $mLog->checkPause($unclog);
	}

	public function resetPause() {
		$user = & JFactory::getUser();
		$mLog = new TeamtimeModelLog();

		$unclog = $mLog->getUncompletedLog($user->id);
		$mLog->resetPause($unclog);
	}

}