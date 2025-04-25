<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// include table
require_once(dirname(dirname(__FILE__)) . DS . 'tables' . DS . 'project.php');

define('PROJECT_STATE_OPEN', 0);
define('PROJECT_STATE_CLOSED', 1);

/*
  Class: Project
  Project related attributes and functions.
 */

class Project extends YObject {
	/*
	  Variable: id
	  Primary key.
	 */

	var $id = null;

	/*
	  Variable: name
	  Project name.
	 */
	var $name = null;

	/*
	  Variable: description
	  Logs description.
	 */
	var $description = null;

	/*
	  Variable: duration
	  Project state.
	 */
	var $state = null;

	/*
	  Variable: tasks
	  Array with all tasks.
	 */
	var $_tasks = null;

	/*
	  Variable: task_types
	  Array with task type and name.
	 */
	var $_task_types = null;

	/*
	  Variable: logs
	  Array with all logs
	 */
	var $_logs = null;

	/*
	  Variable: rate
	  Rate
	 */
	var $rate = null;
	var $dynamic_rate = null;

	/*
	  Function: __construct
	  Constructor.

	  Parameters:
	  id - Project id.
	 */

	function __construct($id = 0) {
		// load project if it exists
		if (!empty($id)) {
			$table = & JTable::getInstance('project', 'Table');
			if ($table->load($id)) {
				$this->bind($table);
			}
		}
	}

	/*
	  Function: getLogs
	  Get projects logs.

	  Returns:
	  Array.
	 */

	function getLogs() {
		$this->loadLogs();
		return $this->_logs;
	}

	/*
	  Function: getTasks
	  Get projects tasks.

	  Returns:
	  Array.
	 */

	function getTasks() {
		$this->loadTasks();
		return $this->_tasks;
	}

	/*
	  Function: getTaskTypes
	  Get all defined task types.

	  Returns:
	  Array.
	 */

	function getTaskTypes() {
		$this->loadTaskTypes();
		return $this->_task_types;
	}

	/*
	  Function: getTaskTypeArray
	  Get all defined types and their corresponding tasks.

	  Returns:
	  Array. Key Type. Value Tasksarray
	 */

	function getTaskTypeArray() {
		$check_exist_tasks = JRequest::getVar("callback") != "";

		if ($check_exist_tasks) { //filter only exists task and types in logs
			$db = & JFactory::getDBO();
			$type_ids = array();
			$task_ids = array();

			$db->setQuery("select type_id from #__teamlog_log group by type_id");
			$rows = $db->loadObjectList();
			foreach ($rows as $row)
				$type_ids[] = $row->type_id;

			$db->setQuery("select task_id from #__teamlog_log group by task_id");
			$rows = $db->loadObjectList();
			foreach ($rows as $row)
				$task_ids[] = $row->task_id;
		}

		$this->loadTaskTypes();
		$this->loadTasks();
		$taskType = array();
		foreach ($this->_task_types as $type) {
			if ($check_exist_tasks)
				if (!in_array($type->id, $type_ids))
					continue;

			$tasks = array();
			foreach ($this->_tasks as $task) {
				if ($check_exist_tasks)
					if (!in_array($task->id, $task_ids))
						continue;

				if ($task->type_id == $type->id) {
					$tasks[] = $task;
				}
			}
			$taskType[$type->name] = $tasks;
		}
		ksort($taskType);
		return $taskType;
	}

	/*
	  Function: getTaskTypeName
	  Get name of task type.

	  Returns:
	  String.
	 */

	function getTaskTypeName($type) {
		$this->loadTasks();

		if (array_key_exists($type, $this->_task_types)) {
			return $this->_task_types[$type];
		}

		return null;
	}

	/*
	  Function: getTask
	  Get task by task_id.

	  Returns:
	  String.
	 */

	function getTask($task_id) {
		$this->loadTasks();

		foreach ($this->_tasks as $task) {
			if ($task_id == $task->id) {
				return $task;
			}
		}

		return null;
	}

	/*
	  Function: getType
	  Get type by type_id.

	  Returns:
	  String.
	 */

	function getType($type_id) {
		$this->loadTaskTypes();

		foreach ($this->_task_types as $type) {
			if ($type_id == $type->id) {
				return $type;
			}
		}

		return null;
	}

	/*
	  Function: loadTasks
	  Load all project tasks.

	  Returns:
	  Void.
	 */

	function loadTasks() {
		if (empty($this->_tasks)) {

			// get task information
			$table = & JTable::getInstance('task', 'Table');
			$alltasks = $table->getTasks($this->id);
			$this->_tasks = array();
			foreach ($alltasks as $task) {
				$this->_tasks[$task->name] = $task;
			}
			ksort($this->_tasks);
		}
	}

	/*
	  Function: loadTaskTypes
	  Load all project task types.

	  Returns:
	  Void.
	 */

	function loadTaskTypes() {
		if (empty($this->_tasks_types)) {
			$this->loadTasks();

			// get type information
			$table = & JTable::getInstance('type', 'Table');
			$alltypes = $table->getTypes();
			$types = array();
			foreach ($alltypes as $type) {
				foreach ($this->_tasks as $task) {
					if ($task->type_id == $type->id && !array_key_exists($type->id, $types)) {
						$types[$type->id] = $type;
					}
				}
			}

			$this->_task_types = $types;
		}
	}

	/*
	  Function: loadLogs
	  Load all project logs.

	  Returns:
	  Void.
	 */

	function loadLogs() {
		if (empty($this->_logs)) {
			$table = & JTable::getInstance('log', 'Table');
			$this->_logs = $table->getProjectLogs($this->id);
		}
	}

	/*
	  Function: save
	  Save project to database.

	  Returns:
	  Boolean.
	 */

	function save() {
		// load table object
		$table = & JTable::getInstance('project', 'Table');
		$table->bind($this->getProperties());

		// check object
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// store object
		if (!$result = $table->store()) {
			$this->setError($table->getError());
		}

		// set id
		if (empty($this->id)) {
			$this->id = $table->get('id');
		}

		return $result;
	}

	/*
	  Function: getstates
	  Get project states as array.

	  Returns:
	  Array.
	 */

	function getStates() {
		$states = array(
			PROJECT_STATE_OPEN => JText::_('Open'),
			PROJECT_STATE_CLOSED => JText::_('Closed'));

		return $states;
	}

}