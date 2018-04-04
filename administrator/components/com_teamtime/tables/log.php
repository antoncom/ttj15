<?php

class TableLog extends Core_Joomla_Model {

	public $id = null;
	public $user_id = null;
	public $type_id = null;
	public $project_id = null;
	public $task_id = null;
	public $description = null;
	public $duration = null;
	public $date = null;
	public $created = null;
	public $ended = null;
	public $modified = null;
	public $datepause = null;
	public $sumpause = null;
	public $money = null;
	public $todo_id = null;

	public function __construct(&$db) {
		parent::__construct('#__teamtime_log', 'id', $db);
	}

	public function loadObjects($result) {
		$objects = array();
		foreach ($result as $row) {
			$object = & new Log();
			$object->bind($row);
			$objects[] = $object;
		}

		return $objects;
	}

	// TODO move to model
	public function getUserLogs($userId, $limit = 0, $projectId = -1) {
		$mProject = new TeamtimeModelProject();
		$user = & JFactory::getUser();
		$currentUserId = $user->id;

		$where = array();
		$where[] = "user_id = " . $userId;
		if ($projectId > 0) {
			$where[] = "project_id = " . $projectId;
		}
		$where[] = $mProject->getProjectsForUserSqlFilter($currentUserId);

		if (sizeof($where) > 0) {
			$where = " where " . implode(" and ", $where);
		}
		else {
			$where = "";
		}

		$query = " select * from " . $this->_tbl .
				$where .
				" order by date desc";

		if ($limit) {
			$query .= " LIMIT 0," . $limit;
		}
		$this->_db->setQuery($query);

		$result = $this->_db->loadAssocList();
		$res = $this->loadObjects($result);

		return $res;
	}

	// TODO move to model
	public function getUserWeekLogs($user_id) {
		$date = JFactory::getDate();
		$date = $date->toUnix();
		$date = mktime(0, 0, 0, date('n', $date), date('j', $date) - 7, date('Y', $date));
		$query = " SELECT * "
				. " FROM " . $this->_tbl
				. " WHERE user_id=" . $user_id
				. " AND date > '" . date('Y-m-d H:i:s', $date) . "'"
				. " ORDER BY date DESC";
		$this->_db->setQuery($query);

		$result = $this->_db->loadAssocList();
		$return = $this->loadObjects($result);

		return $return;
	}

	// TODO move to model
	public function getProjectLogs($project_id) {
		$query = " SELECT * "
				. " FROM " . $this->_tbl
				. " WHERE project_id=" . $project_id
				. " ORDER BY task_id DESC";
		$this->_db->setQuery($query);

		$result = $this->_db->loadAssocList();
		$return = $this->loadObjects($result);

		return $return;
	}

}