<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// include table
//require_once(dirname(dirname(__FILE__)).DS.'tables'.DS.'task.php');
require_once(dirname(dirname(__FILE__)) . DS . 'tables' . DS . 'todo.php');

/*
  Class: Todo
  Todo related attributes and functions.
 */

class Todo extends YObject {
	/*
	  Variable: id
	  Primary key.
	 */

	var $id = null;

	/*
	  Variable: user id
	  User id.
	 */
	var $user_id = null;

	/*
	  Variable: title
	  Task title.
	 */
	var $title = null;

	/*
	  Variable: description
	  Task description.
	 */
	var $description = null;

	/*
	  Variable: hours_plan
	  Planned hours.
	 */
	var $hours_plan = null;

	/*
	  Variable: hours_fact
	  Hours in fact.
	 */
	var $hours_fact = null;
	var $task_id = null;
	var $project_id = null;
	var $type_id = null;

	/*
	  Variable: duration
	  Todo state.
	 */
	var $state = null;
	var $selected = null;

	/*
	  Variable: created
	  Todos creation date.
	 */
	var $created = null;

	/*
	  Variable: modified
	  Todos last modification date.
	 */
	var $modified = null;
	var $color = null;
	var $isalldayevent = null;
	var $hourly_rate = null;
	var $costs = null;
	var $current_repeat_date = null;
	var $is_parent = null;
	var $showskills = null;
	var $is_autotodo = null;

	/*
	  Function: __construct
	  Constructor.

	  Parameters:
	  id - Todo id.
	 */

	function __construct($id = 0) {
		// load task if it exists
		if (!empty($id)) {
			$table = & JTable::getInstance('todo', 'Table');
			if ($table->load($id)) {
				$this->bind($table);
			}
		}
	}

	/*
	  Function: setstate
	  Set todo state.

	  Returns:
	  Void.
	 */

	function setState($val) {
		$this->state = $val;
	}

	/*
	  Function: save
	  Save todo to database.

	  Returns:
	  Boolean.
	 */

	function save() {
		// load table object
		$table = & JTable::getInstance('todo', 'Table');
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

	function getLogsSumm() {
		$db = & JFactory::getDBO();

		$result = $db->GetOne("select sum(duration)/60 from #__teamlog_log
			where todo_id = " . (int) $this->id);

		return (float) $result;
	}

	/*
	  Function: getstates
	  Get todo states as array.

	  Returns:
	  Array.
	 */

	function getStates() {
		$states = array(
			TODO_STATE_OPEN => JText::_('Open'),
			TODO_STATE_DONE => JText::_('Done'),
			TODO_STATE_CLOSED => JText::_('Closed'),
			TODO_STATE_PROJECT => JText::_('Project'),
		);

		return $states;
	}

	function getHourlyRate($todo_id) {
		$db = & JFactory::getDBO();

		if ($todo_id == 0) {
			return 0;
		}

		$query = "select a.hourly_rate as todo_rate, b.rate as task_rate,
				c.rate as type_rate, d.rate as project_rate
			from #__teamlog_todo as a
			left join #__teamlog_task as b on a.task_id = b.id
			left join #__teamlog_type as c on a.type_id = c.id
			left join #__teamlog_project as d on a.project_id = d.id
			where a.id = " . (int) $todo_id;
		$db->setQuery($query);
		$result = $db->loadObject();

		if (!$result) {
			return 0;
		}

		if ($result->task_rate > 0) {
			return $result->task_rate;
		}

		if ($result->type_rate > 0) {
			return $result->type_rate;
		}

		if ($result->project_rate > 0) {
			return $result->project_rate;
		}

		return 0;
	}

	function getHourlyRateByParams($params, $default_rate = 0) {
		$db = & JFactory::getDBO();

		$result = TeamTime::helper()->getDotu()->getPrice($params);
		if ($result !== null && !($result instanceof TeamTime_Undefined)) {
			return $result;
		}

		if (isset($params["user_id"])) {
			$query = "SELECT * FROM #__teamlog_userdata
				where user_id = " . (int) $params["user_id"];
			$db->setQuery($query);
			$row = $db->loadObject();
			if ($row->hour_price > 0) {
				return $row->hour_price;
			}
		}

		if (isset($params["task_id"])) {
			$query = "SELECT * FROM #__teamlog_task
				where id = " . (int) $params["task_id"];
			$db->setQuery($query);
			$row = $db->loadObject();
			if ($row->rate > 0) {
				return $row->rate;
			}
		}

		if (isset($params["type_id"])) {
			$query = "SELECT * FROM #__teamlog_type
				where id = " . (int) $params["type_id"];
			$db->setQuery($query);
			$row = $db->loadObject();
			if ($row->rate > 0) {
				return $row->rate;
			}
		}
		else if (isset($params["task_id"])) {
			$query = "SELECT b.rate FROM #__teamlog_task as a
				left join #__teamlog_type as b on a.type_id = b.id
				where a.id = " . (int) $params["task_id"];
			$db->setQuery($query);
			$row = $db->loadObject();
			if ($row->rate > 0) {
				return $row->rate;
			}
		}

		if (isset($params["project_id"])) {
			$query = "SELECT * FROM #__teamlog_project
				where id = " . (int) $params["project_id"];
			$db->setQuery($query);
			$row = $db->loadObject();
			if ($row->rate > 0) {
				return $row->rate;
			}
		}

		return $default_rate;
	}

}