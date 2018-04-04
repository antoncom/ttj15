<?php

require_once(dirname(dirname(__FILE__)) . DS . 'tables' . DS . 'teamtimetodo.php');

class Todo extends YObject {

	public $id = null;
	public $user_id = null;
	public $title = null;
	public $description = null;
	public $hours_plan = null;
	public $hours_fact = null;
	public $task_id = null;
	public $project_id = null;
	public $type_id = null;
	public $state = null;
	public $selected = null;
	public $created = null;
	public $modified = null;
	public $modified_by = null;
	public $color = null;
	public $isalldayevent = null;
	public $hourly_rate = null;
	public $costs = null;
	public $current_repeat_date = null;
	public $is_parent = null;
	public $showskills = null;
	public $is_autotodo = null;

	public function __construct($id = 0) {
		// load task if it exists
		if (!empty($id)) {
			$table = & JTable::getInstance('teamtimetodo', 'Table');
			if ($table->load($id)) {
				$this->bind($table);
			}
		}
	}

	public function setState($val) {
		$this->state = $val;
	}

	public function save() {
		// load table object
		$table = & JTable::getInstance('teamtimetodo', 'Table');
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

	public function getLogsSumm() {
		$db = & JFactory::getDBO();

		$result = $db->GetOne("select sum(duration)/60 from #__teamtime_log
			where todo_id = " . (int) $this->id);

		return (float) $result;
	}

	public function getStates() {
		$states = array(
			TODO_STATE_OPEN => JText::_('Open'),
			TODO_STATE_DONE => JText::_('Done'),
			TODO_STATE_CLOSED => JText::_('Closed'),
			TODO_STATE_PROJECT => JText::_('Project'),
		);

		return $states;
	}

	public function getHourlyRate($todo_id) {
		$db = & JFactory::getDBO();

		if ($todo_id == 0) {
			return 0;
		}

		$query = "select a.hourly_rate as todo_rate, b.rate as task_rate,
				c.rate as type_rate, d.rate as project_rate
			from #__teamtime_todo as a
			left join #__teamtime_task as b on a.task_id = b.id
			left join #__teamtime_type as c on a.type_id = c.id
			left join #__teamtime_project as d on a.project_id = d.id
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

	public function getHourlyRateByParams($params, $default_rate = 0) {
		$db = & JFactory::getDBO();

		$result = TeamTime::helper()->getDotu()->getPrice($params);
		if ($result !== null && !($result instanceof TeamTime_Undefined)) {
			return $result;
		}

		if (isset($params["user_id"])) {
			$query = "SELECT * FROM #__teamtime_userdata
				where user_id = " . (int) $params["user_id"];
			$db->setQuery($query);
			$row = $db->loadObject();
			if ($row->hour_price > 0) {
				return $row->hour_price;
			}
		}

		if (isset($params["task_id"])) {
			$query = "SELECT * FROM #__teamtime_task
				where id = " . (int) $params["task_id"];
			$db->setQuery($query);
			$row = $db->loadObject();
			if ($row->rate > 0) {
				return $row->rate;
			}
		}

		if (isset($params["type_id"])) {
			$query = "SELECT * FROM #__teamtime_type
				where id = " . (int) $params["type_id"];
			$db->setQuery($query);
			$row = $db->loadObject();
			if ($row->rate > 0) {
				return $row->rate;
			}
		}
		else if (isset($params["task_id"])) {
			$query = "SELECT b.rate FROM #__teamtime_task as a
				left join #__teamtime_type as b on a.type_id = b.id
				where a.id = " . (int) $params["task_id"];
			$db->setQuery($query);
			$row = $db->loadObject();
			if ($row->rate > 0) {
				return $row->rate;
			}
		}

		if (isset($params["project_id"])) {
			$query = "SELECT * FROM #__teamtime_project
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