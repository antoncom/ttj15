<?php

class TableTeamtimeTodo extends Core_Joomla_Model {

	public $id = null;
	public $user_id = null;
	public $title = null;
	public $description = null;
	public $hours_plan = null;
	public $hours_fact = null;
	public $task_id = null;
	public $project_id = null;
	public $type_id = null;
	public $created = null;
	public $modified = null;
	public $modified_by = null;
	public $state = null;
	public $selected = null;
	public $color = null;
	public $isalldayevent = null;
	public $hourly_rate = null;
	public $costs = null;
	public $current_repeat_date = null;
	public $is_parent = null;
	public $showskills = null;
	public $is_autotodo = null;

	function __construct(&$db) {
		parent::__construct('#__teamtime_todo', 'id', $db);
	}

	function loadObjects($result) {
		$objects = array();
		if ($result != "") {
			foreach ($result as $row) {
				$object = & new Todo();
				$object->bind($row);

				//set spec fields
				if (is_array($row)) {
					$object->project_name = $row["project_name"];
					$object->is_repeat = $row["todo_id"] ? true : false;
					$object->current_repeat_date = $row["repeat_date"];
				}
				else if (is_object($row)) {
					$object->project_name = $row->project_name;
					$object->is_repeat = $row->todo_id ? true : false;
					$object->current_repeat_date = $row->repeat_date;
				}

				$objects[] = $object;
			}
		}

		return $objects;
	}

	function _getWhereDate($filter_state, $from_period, $until_period) {
		$from_period = $this->_db->Quote($from_period);
		$until_period = $this->_db->Quote($until_period);

		if ($filter_state === TODO_STATE_OPEN) {
			$result = "if(rd.todo_id is null,
        (a.created <= {$until_period}),
        (rd.repeat_date <= {$until_period}))";
		}
		else {
			$result = "if(rd.todo_id is null,
        (a.created >= {$from_period} and a.created <= {$until_period}),
        (rd.repeat_date >= {$from_period} and rd.repeat_date <= {$until_period}))";
		}

		return $result;
	}

	function getUserTodos($user_id, $limit_end_date = null, $params = array()) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$config = TeamTime::getConfig();

		if ($limit_end_date == null) {
			//get current monday date
			$limit_end_date = time();
		}
		else {
			$limit_end_date = strtotime($limit_end_date);
		}
		$w = date("w", $limit_end_date) - 1;
		if ($w < 0) {
			$w = 6;
		}

		$periods_str_filters = array();

		//month period str
		$week_end = $this->_db->Quote(
				date("Y-m-d H:i:s",
						mktime(23, 59, 59, date("n", $limit_end_date), date("t", $limit_end_date),
								date("Y", $limit_end_date))));
		$week_start = $this->_db->Quote(
				date("Y-m-d H:i:s", mktime(0, 0, 0, date("n", $limit_end_date), 1, date("Y", $limit_end_date))));
		$periods_str_filters["month"] = "if(rd.todo_id is null,
			(a.created >= {$week_start} and a.created <= {$week_end}),
			(rd.repeat_date >= {$week_start} and rd.repeat_date <= {$week_end}))";

		$week_end = $this->_db->Quote(
				date("Y-m-d H:i:s", mktime(23, 59, 59, 12, 31, date("Y", $limit_end_date))));
		$week_start = $this->_db->Quote(
				date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 1, date("Y", $limit_end_date))));
		$periods_str_filters["year"] = "if(rd.todo_id is null,
			(a.created >= {$week_start} and a.created <= {$week_end}),
			(rd.repeat_date >= {$week_start} and rd.repeat_date <= {$week_end}))";

		//week period str
		$week_end = $this->_db->Quote(
				date("Y-m-d H:i:s",
						mktime(23, 59, 59, date("n", $limit_end_date), date("j", $limit_end_date) - $w + 6,
								date("Y", $limit_end_date))));
		$week_start = $this->_db->Quote(
				date("Y-m-d H:i:s",
						mktime(0, 0, 0, date("n", $limit_end_date), date("j", $limit_end_date) - $w,
								date("Y", $limit_end_date))));
		$periods_str_filters["week"] = "if(rd.todo_id is null,
			(a.created >= {$week_start} and a.created <= {$week_end}),
			(rd.repeat_date >= {$week_start} and rd.repeat_date <= {$week_end}))";

		$where = array();
		$where[] = "a.user_id = " . $user_id;

		// filter state
		if (isset($params["filter_state"])) {
			$filter_state = $params["filter_state"];
		}
		else {
			$filter_state = $mainframe->getUserState(
					$option . '.filter_state', TODO_STATE_OPEN);
		}

		$closed_todos = false;

		if ($filter_state === "") {
			
		}
		else if ($filter_state == TODO_STATE_OPEN) {
			$where[] = "(a.state = " . TODO_STATE_OPEN . " or a.state = " . TODO_STATE_DONE . ")";
		}
		else if ($filter_state == TODO_STATE_CLOSED) {
			$where[] = "a.state = " . TODO_STATE_CLOSED;

			$closed_todos = true;
		}

		// filter period
		if (isset($params["filter_period"])) {
			$filter_period = $params["filter_period"];
		}
		else {
			$filter_period = $mainframe->getUserState(
					$option . '.filter_period', '');
		}

		// filter date and todo/projects
		$where_filter_date = null;
		if ($config->show_todos_datefilter) {
			if (isset($params["filter_date"])) {
				$filter_date = $params["filter_date"];
			}
			else {
				$filter_date = $mainframe->getUserState(
						$option . '.filter_date', 'today');
			}

			if ($filter_date != "") {
				$options = array(
					JHTML::_('select.option', '', '- ' . JText::_('All dates') . ' -', 'value', 'text')
				);
				$selector_data = JHTML::_(
								'teamtime.dateselector2', $options, 'filter_date',
								'class="inputbox" size="1" onchange="set_filter_date();"', 'value', 'text', $filter_date);

				if (in_array($filter_date, array("week", "month", "year"))) {
					// until_period = end this week
					$selector_data["until_period"] = date("Y-m-d H:i:s",
							mktime(23, 59, 59, date(
											"n", $limit_end_date), date("j", $limit_end_date) - $w + 6,
									date(
											"Y", $limit_end_date)));
				}
				else {
					$selector_data["until_period"] .= " 23:59:59";
				}
				$where_filter_date = $this->_getWhereDate(
						$filter_state, $selector_data["from_period"], $selector_data["until_period"]);
			}
			else {
				$where_filter_date = 1;
			}

			// filter todo/projects
			if (isset($params["filter_stodo"])) {
				$filter_stodo = $params["filter_stodo"];
			}
			else {
				$filter_stodo = $mainframe->getUserState(
						$option . '.filter_stodo', '');
			}
			if ($filter_stodo != "") {
				$where[] = '(LOWER(a.title) LIKE ' . $this->_db->Quote(
								'%' . $this->_db->getEscaped($filter_stodo, true) . '%', false) .
						' or LOWER(a.description) LIKE ' . $this->_db->Quote(
								'%' . $this->_db->getEscaped($filter_stodo, true) . '%', false) . ')';
			}

			if (isset($params["filter_sproject"])) {
				$filter_sproject = $params["filter_sproject"];
			}
			else {
				$filter_sproject = $mainframe->getUserState(
						$option . '.filter_sproject', '');
			}
			if ($filter_sproject != "") {
				$where[] = 'LOWER(proj.name) LIKE ' . $this->_db->Quote(
								'%' . $this->_db->getEscaped($filter_sproject, true) . '%', false);
			}
		}

		if ($filter_period == "week") {
			$where[] = "(p.repeat_mode = 'weekly' or rr.repeating_history = 'weekly')";
			// date filter for closed
			if ($closed_todos) {
				$where[] = $where_filter_date !== null ?
						$where_filter_date : $periods_str_filters["week"];
			}
		}
		else if ($filter_period == "month") {
			$where[] = "(p.repeat_mode = 'monthly' or rr.repeating_history = 'monthly')";
			// date filter for closed
			if ($closed_todos) {
				$where[] = $where_filter_date !== null ?
						$where_filter_date : $periods_str_filters["month"];
			}
		}
		else if ($filter_period == "year") {
			$where[] = "(p.repeat_mode = 'yearly' or rr.repeating_history = 'yearly')";
			// date filter for closed
			if ($closed_todos) {
				$where[] = $where_filter_date !== null ?
						$where_filter_date : $periods_str_filters["year"];
			}
		}
		else if ($filter_period == "urgent") {
			$where[] = "(p.todo_id is null and rr.todo_id is null)";
			// date filter for closed
			if ($closed_todos) {
				$where[] = $where_filter_date !== null ?
						$where_filter_date : $periods_str_filters["week"];
			}
		}
		else {
			// date filter for closed
			if ($closed_todos) {
				if ($where_filter_date !== null) {
					$where[] = $where_filter_date;
				}
				else {
					$where[] = "if((p.repeat_mode = 'weekly' or rr.repeating_history = 'weekly'),
            {$periods_str_filters['week']},
            if((p.repeat_mode = 'monthly' or rr.repeating_history = 'monthly'),
              {$periods_str_filters['month']},
              if((p.repeat_mode = 'yearly' or rr.repeating_history = 'yearly'),
                {$periods_str_filters['year']},
                {$periods_str_filters['week']}
              )
            )
          )";
				}
			}
		}

		// date filter for opened or all periods
		if (!$closed_todos) {
			if ($where_filter_date !== null) {
				$where[] = $where_filter_date;
			}
			else {
				$where[] = "if(rd.todo_id is null,
          a.created <= {$week_end}, rd.repeat_date <= {$week_end})";
			}
		}

		if (sizeof($where) > 0) {
			$where = implode(" and ", $where);
		}

		// select todos (usual and repeated)
		$query = "select a.*, p.*, rd.*, proj.name as project_name from " . $this->_tbl . " as a
			left join #__teamtime_project as proj on a.project_id = proj.id

			left join #__teamtime_repeat_todo_ref as rr on a.id = rr.todo_id

			left join #__teamtime_todo_repeatparams as p on a.id = p.todo_id
			left join #__teamtime_todo_repeatdate as rd on a.id = rd.todo_id

			where {$where}
			order by if(rd.todo_id is null, a.created, rd.repeat_date)";

		//if ($closed_todos)
		//	error_log("$filter_period: " . $this->_db->replacePrefix($query) . "\n\n");
		//error_log($query);

		$this->_db->setQuery($query);
		$result = $this->_db->loadAssocList();

		if (isset($params["only_count"])) {
			return sizeof($result);
		}
		else {
			//print $this->_db->replacePrefix($query);
		}

		$return = $this->loadObjects($result);

		return $return;
	}

	function getUserTodo($user_id, $todo_id) {
		$query = " SELECT * "
				. " FROM " . $this->_tbl
				. " WHERE user_id=" . $user_id
				. " AND (state=" . TODO_STATE_OPEN . " OR state=" . TODO_STATE_DONE . ")"
				. " AND (id=" . $todo_id . ")"
				. " ORDER BY created DESC";
		$this->_db->setQuery($query);

		$result = $this->_db->loadAssocList();
		$return = $this->loadObjects($result);

		return $return;
	}

}