<?php

class TeamtimeModelLog extends Core_Joomla_Manager {

	public $_table = 'log';

	public function store($data) {
		$config = & JFactory::getConfig();
		$user = & JFactory::getUser();
		$details = JRequest::getVar('details', null, 'post', 'array');

		$row = & $this->getTable($this->_table);

		// bind the detail fields
		if (!$row->bind($details)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// bind the form fields
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// sanitise id field
		$row->id = (int) $row->id;

		// Are we saving from an item edit?
		if ($row->id) {
			$datenow = & JFactory::getDate();
			$row->modified = $datenow->toMySQL();
		}

		$row->user_id = $row->user_id ? $row->user_id : $user->get('id');

		if ($row->created && strlen(trim($row->created)) <= 10) {
			$row->created .= ' 00:00:00';
		}


		$tzoffset = $config->getValue('config.offset');
		$date = & JFactory::getDate($row->created, $tzoffset);
		$row->created = $date->toMySQL();


		if ($row->date && strlen(trim($row->date)) <= 10) {
			$row->date .= ' 00:00:00';
		}

		$tzoffset = $config->getValue('config.offset');
		$date = & JFactory::getDate($row->date, $tzoffset);
		$row->date = $date->toMySQL();

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

		return true;
	}

	public function storeState($id, $state) {
		$row = & $this->getTable($this->_table);

		// store state to database
		if (!$row->setLogState($id, $state)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	public function delete($cid = array()) {
		$table = & $this->getTable($this->_table);
		if (count($cid)) {
			foreach ($cid as $logId) {
				TeamTime::trigger()->onDeleteLog($logId);
			}

			JArrayHelper::toInteger($cid);
			$cids = implode(',', $cid);
			$query = 'DELETE FROM ' . $table->getTableName()
					. ' WHERE id IN (' . $cids . ')';
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return true;
	}
	public function getLogs($filter = array(), $weeks = 1) {
		// Для формирования отчёта по конкретным датам укажите эти перемнные:
		$sDate = "2018-07-01 00:00:00";
		$eDate = "2018-07-22 23:59:59";

		$table = & $this->getTable($this->_table);
		$result = array();
		$where = array();
		$weekDay = Date('w', strtotime('now'));

		// Если день недели пятница, суббота или воскресенье, то делаем выборку за текущую и прошлые $weeks-1 недель
		if($weekDay == 0 || ($weekDay >= 5 && $weekDay <= 6)) {

			// start week = current week - $weeks
			$sWeek = ($sDate === "") ? strtotime("Monday - " . ($weeks-1) . ' week') : strtotime($sDate);

			// end week = current week
			$eWeek = ($eDate === "") ? strtotime("Sunday this week 23:59") : strtotime($eDate);

			// Если день недели Понедельник..четверг, значит выборка за прошлые $weeks + 1 недель
		} else if ($weekDay >= 1 && $weekDay <= 4) {

			// start week = current week - $weeks + 1
			$sWeek = strtotime("Monday - " . ($weeks + 1) . ' week');

			// end week = current week - 1
			$eWeek = strtotime("Sunday last week 23:59");
		}

		// error_log('==== eWeek: ' . $eWeek, 3, "/home/mediapub/teamlog.teamtime.info/docs/logs/my-errors.log");
		// $date = JFactory::getDate();
		// $date = $date->toUnix();

		$and = " AND date BETWEEN '" . date('Y-m-d H:i:s', $sWeek) . "' AND  '" . date('Y-m-d H:i:s', $eWeek) . "'";


		if (isset($filter["todo_id"])) {
			$where[] = "todo_id = " . (int) $filter["todo_id"];
		}

		if (sizeof($where) > 0) {
			$where = " where " . implode(" and ", $where);
		}
		else {
			$where = "";
		}

		$query = " select * from " . $table->getTableName() .
			$where .
			$and .
			" order by date asc";
		// error_log('====QUERY 555: ' . $query, 3, "/home/mediapub/teamlog.teamtime.info/docs/logs/my-errors.log");
		$this->_db->setQuery($query);

		$rows = $this->_db->loadObjectList();
		if ($rows) {
			$result = $rows;
		}

		return $result;
	}

	public function filterWithAllowedProjects($ids, $acl) {
		$result = array();

		foreach ($ids as $id) {
			$log = $this->getById($id);
			if (sizeof($acl->filterUserProjectIds(array($log->project_id))) > 0) {
				$result[] = $id;
			}
		}

		return $result;
	}

	public function getUncompletedLog($userId, $limit = 0) {
		$table = & $this->getTable($this->_table);

		$query = "select * from " . $table->getTableName() . "
			where user_id = " . $userId . " and
				duration = 0 and ended = '0000-00-00 00:00:00'";
		$this->_db->setQuery($query);

		$result = $this->_db->loadObjectList();
		//$result = $this->loadObjects($result);

		return $result;
	}

	public function setPause($logs) {
		$table = & $this->getTable($this->_table);
		$date = & JFactory::getDate();
		$sdate = $date->toMySQL();

		$query = "update " . $table->getTableName() . "
			set datepause=" . $this->_db->Quote($sdate) . "
			where id = " . $logs[0]->id;

		$this->_db->Execute($query);
	}

	public function checkPause($logs) {
		$table = & $this->getTable($this->_table);

		if (isset($logs[0]) && $logs[0]->id) {
			$query = "select datepause > 0 from " . $table->getTableName() . "
				where id = " . $logs[0]->id;

			$res = $this->_db->GetCol($query);

			return $res[0];
		}

		return false;
	}

	public function resetPause($logs) {
		$table = & $this->getTable($this->_table);
		$date = & JFactory::getDate();
		$now = $this->_db->Quote($date->toMySQL());

		$query = "update " . $table->getTableName() . "
			set sumpause = sumpause +
					unix_timestamp(str_to_date($now, '%Y-%m-%d %H:%i:%s')) - unix_timestamp(datepause),
				created = addtime(created,
					timediff(str_to_date($now, '%Y-%m-%d %H:%i:%s'), datepause)),
				date = created,
				modified = created,
				datepause = 0
			where id = " . $logs[0]->id;

		$this->_db->Execute($query);
	}

}