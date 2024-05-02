<?php

class TeamlogModelLog extends JModel {

	var $_user = null;
	var $_tasks = null;
	var $_task_types = null;
	var $_task_type_array = null;
	var $_projects = null;
	var $_user_logs = null;
	var $_other_logs = null;
	var $_user_todos = null;

	function __construct() {
		parent::__construct();

		$currentUser = & JFactory::getUser();
		$this->_user = new YUser($currentUser->id);
	}

	function loadProject() {
		$project_id = JRequest::getInt('project_id');

		if (empty($project_id)) {
			JError::raiseWarning(0, 'Project id empty!');
			return false;
		}

		$project = new Project($project_id);
		return $project;
	}

	function loadTodo() {
		$todo_id = JRequest::getInt('todo_id');
		$todo = new Todo($todo_id);
		return $todo;
	}

	function getProject() {
		return $this->loadProject();
	}

	function getTasks() {
		if (empty($this->_tasks)) {
			$project = $this->loadProject();
			$this->_tasks = $project->getTasks();
		}

		return $this->_tasks;
	}

	function getTaskTypes() {
		if (empty($this->_task_types)) {
			$project = $this->loadProject();
			$this->task_types = $project->getTaskTypes();
		}

		return $this->task_types;
	}

	function getTaskTypeArray() {
		if (empty($this->_task_type_array)) {
			$project = $this->loadProject();
			if ($project) {
				$this->_task_type_array = $project->getTaskTypeArray();
			}
			else {
				$this->_task_type_array = array();
			}
		}

		return $this->_task_type_array;
	}

	function getProjects($filterUserProjects = null) {
		if (empty($this->_projects)) {
			if ($filterUserProjects === null) {
				$filterUserProjects = JRequest::getVar("client") ? false : true;
			}
			$mProject = new TeamtimeModelProject();
			$this->_projects = $mProject->getActiveProjects($filterUserProjects);
		}

		return $this->_projects;
	}

	function getTypes() {
		if (empty($this->_types)) {
			$table = & JTable::getInstance('type', 'Table');
			$this->_types = $table->getTypes();
		}

		return $this->_types;
	}

	function getUserLogs() {
		if (empty($this->_user_logs)) {
			$logs = $this->_user->getWeekLogs();
			$this->_user_logs = array();

			foreach ($logs as $log) {
				$date = $log->date;
				$date = DateHelper::formatDate($date, $this->_user->getParam('timezone'));
				$this->_user_logs[$date][] = $log;
			}
		}

		return $this->_user_logs;
	}

	function getOtherLogs() {
		if (empty($this->_other_logs)) {
			$this->_other_logs = array();

			$date = JFactory::getDate();
			$date = $date->toUnix();
			$date = mktime(0, 0, 0, date('n', $date), date('j', $date) - 10, date('Y', $date));

			$query = "SELECT b.id AS user_id"
					. " FROM #__teamtime_userdata AS a "
					. " LEFT JOIN #__users AS b ON a.user_id = b.id"
					. " WHERE a.state_modified > '" . date('Y-m-d H:i:s', $date) .
					"' and a.hideforother = 0 "
					. " ORDER BY b.name";

			$result = $this->_getList($query);
			foreach ($result as $row) {
				if ($row->user_id != $this->_user->id) {
					$user = new YUser($row->user_id);
					$line = array();
					$line['user'] = $user;

					if (JRequest::getVar("client") != "") {
						$line['logs'] = $user->getLogs(4, JRequest::getVar("client"));
					}
					else {
						$line['logs'] = $user->getLogs(4);
					}
					$this->_other_logs[] = $line;
				}
			}
		}

		return $this->_other_logs;
	}

	function getUserTodos() {
		if (empty($this->_user_todos)) {

			$params = array();
			if (JRequest::getVar("reset_filter") == 1) {
				$params = TeamTime::helper()->getBase()->getDefaultFilter();
			}

			$this->_user_todos = $this->_user->getTodos($params);
		}

		return $this->_user_todos;
	}

	/**
	 * Method to remove a model item
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function delete($cid = null) {
		$table = & $this->getTable($this->_table);
		$user = & JFactory::getUser();

		if (!is_null($cid)) {

			$query = 'SELECT user_id, date'
					. ' FROM ' . $table->getTableName()
					. ' WHERE id=' . $cid;
			$this->_db->setQuery($query);
			if (!$log = $this->_db->loadAssoc()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			if ($log['user_id'] != $user->id) {
				$this->setError(JText::_('You have to be the owner of the logentry to delete it.'));
				return false;
			}

			if (!DateHelper::isToday($log['date'])) {
				$this->setError(JText::_('Log has to be created today to be deleted.'));
				return false;
			}

			$query = 'UPDATE ' . $table->getTableName()
					. ' SET state = 0'
					. ' WHERE id = ' . $cid;
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return true;
		}

		return false;
	}

}