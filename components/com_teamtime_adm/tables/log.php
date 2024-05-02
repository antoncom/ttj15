<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
  Class: TableLog
  The Table Class for Log. Manages the database operations.
 */

class TableLog extends JTable {

	// int primary key
	var $id = null;
	// int
	var $user_id = null;
	// int
	var $type_id = null;
	// int
	var $project_id = null;
	// int
	var $task_id = null;
	// string
	var $description = null;
	// int
	var $duration = null;
	// datetime
	var $date = null;
	// datetime
	var $created = null;
	// datetime
	var $ended = null;
	// datetime
	var $modified = null;
	var $datepause = null;
	var $sumpause = null;
	var $money = null;
	var $todo_id = null; // see also classes/log.php

	function __construct(&$db) {
		parent::__construct('#__teamlog_log', 'id', $db);
	}

	function loadObjects($result) {
		$objects = array();
		foreach ($result as $row) {
			$object = & new Log();
			$object->bind($row);
			$objects[] = $object;
		}

		return $objects;
	}

	function getUserLogs($user_id, $limit = 0, $project_id = -1) {
		$user = & YFactory::getUser();
		$current_user = $user->id;

		$query = " SELECT * "
				. " FROM " . $this->_tbl
				. " WHERE user_id=" . $user_id
				. ($project_id > 0 ? " and project_id={$project_id}" : "")
				. " and (" .
				// projects - enabled for all
				" project_id not in (select project_id from #__teamlog_project_user group by project_id) or " .
				// projects - enabled for current user
				"	project_id in (SELECT project_id FROM #__teamlog_project_user
					WHERE user_id = {$current_user} group by project_id) )"
				. " ORDER BY date DESC"
				. ($limit ? " LIMIT 0," . $limit : "");
		$this->_db->setQuery($query);

		$result = $this->_db->loadAssocList();
		$return = $this->loadObjects($result);
		return $return;
	}

	function getUncompletedLog($user_id, $limit = 0) {
		$query = " SELECT * "
				. " FROM " . $this->_tbl
				. " WHERE user_id=" . $user_id
				. " AND duration=0 AND ended='0000-00-00 00:00:00'";
		$this->_db->setQuery($query);

		$result = $this->_db->loadAssocList();
		$return = $this->loadObjects($result);
		return $return;
	}

	function getUserWeekLogs($user_id) {
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

	function getProjectLogs($project_id) {
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