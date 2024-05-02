<?php

class TeamtimeModelTodo extends Core_Joomla_Manager {

	public $_table = 'teamtimetodo';

	public function store($data) {
		$row = & $this->getTable($this->_table);

		// bind the form fields
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$user = & JFactory::getUser();

		// sanitise id field
		$row->id = (int) $row->id;

		// Are we saving from an item edit?
		if ($row->id) {
			$datenow = & JFactory::getDate();
			$row->modified = $datenow->toMySQL();
		}
		else {
			if (!$row->modified_by) {
				$row->modified_by = $user->get("id");
			}
		}

		//$row->user_id = $row->user_id ? $row->user_id : $user->get('id');
		//error_log("1 Created = " . $row->created);

		if ($row->created && strlen(trim($row->created)) <= 10) {
			$row->created .= ' 00:00:00';
		}

		//error_log("2 Created = " . $row->created);

		if (is_array($data) && !isset($data['ignore_offset'])) {
			$config = & JFactory::getConfig();
			$tzoffset = $config->getValue('config.offset');
			$date = & JFactory::getDate($row->created, $tzoffset);

			//error_log("fixed by tzoffset: " . $row->created);

			$row->created = $date->toMySQL();
		}

		//error_log("3 Created = " . $row->created);
		//NOTE fix date for new todo???
		//if ($row->id == 0)
		//	$row->created = $this->fixDate($row);
		//error_log("4 Created = " . $row->created);
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

		// on save todo handlers
		if (is_array($data)) {
			TeamTime::trigger()->onSaveTodo($row, $data);

			if (!$row->is_parent) {
				// delete current id ref from children
				$sql = "delete from #__teamtime_todo_ref where parent_id = {$row->id}";
				$res = $this->_db->Execute($sql);
			}

			if (isset($data["curtodoid"]) && $data["curtodoid"] != "") {
				$this->setParentTodo($row->id, $data["curtodoid"]);
			}
			else {
				$this->deleteParentTodo($row->id);
			}

			TeamTime::helper()->getBase()->saveTodoRepeatParams($row, $data);
		}

		$this->_data = $row;

		return true;
	}

	public function setParentTodo($id, $parentId) {
		$id = (int) $id;
		$parentId = (int) $parentId;

		$query = "insert into #__teamtime_todo_ref
					(todo_id, parent_id) values(" . $id . ", " . $parentId . ")
					ON DUPLICATE KEY UPDATE parent_id = " . $parentId;
		$res = $this->_db->Execute($query);

		$query = "update #__teamtime_todo
					set is_parent = 1
					where id = " . $parentId;
		$res = $this->_db->Execute($query);
	}

	public function getParentTodo($id) {
		$id = (int) $id;

		$query = "select parent_id from #__teamtime_todo_ref
				where todo_id = {$id}";

		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();

		return $row ? $row->parent_id : null;
	}

	public function deleteParentTodo($id) {
		$id = (int) $id;

		// get link row by id
		$query = "select * from #__teamtime_todo_ref
				where todo_id = " . $id;
		$res = $this->_db->setQuery($query);
		$row = $this->_db->loadObject();
		if (!$row) {
			return;
		}

		$query = "delete from #__teamtime_todo_ref
				where todo_id = " . $id;
		$res = $this->_db->Execute($query);

		// get rest links count for parent todo
		$query = "select count(*) as num from #__teamtime_todo_ref
				where parent_id = " . $row->parent_id;
		$res = $this->_db->setQuery($query);
		$count_row = $this->_db->loadObject();

		if ($count_row->num == 0) {
			// reset is_parent for parent_id
			$query = "update #__teamtime_todo
					set is_parent = 0
					where id = " . $row->parent_id;
			$res = $this->_db->Execute($query);
		}
	}

	public function fixDate($row) {
		$db = & JFactory::getDBO();

		$day_date = array_shift(explode(" ", $row->created));
		$res = $db->setQuery("select id, created from {$row->_tbl}
			where created >= " . $db->Quote($day_date . " 00:00:00")
				. " and created <= " . $db->Quote($day_date . " 23:59:59") .
				" order by created desc limit 1");
		$last = $db->loadObject();
		if ($last) {
			$time = strtotime($last->created) + 15 * 60; //add 15 min interval
			if (date("H", $time) * 60 + date("i", $time) > 23 * 60 + 59) {
				// check first
				$res = $db->setQuery("select id, created from {$row->_tbl}
					where created >= " . $db->Quote($day_date . " 00:00:00")
						. " and created <= " . $db->Quote($day_date . " 23:59:59") .
						" order by created limit 1");
				$first = $db->loadObject();
				$time = strtotime($first->created) - 15 * 60; //sub 15 min interval
			}
			$time = date("Y-m-d H:i:s", $time);
		}
		else
			$time = $row->created;

		return $time;
	}

	public function storeState($id, $state) {

		$todo = new Todo($id);
		$todo->setState($state);

		if (!$todo->save()) {
			$this->setError($todo->getError());
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

			foreach ($cid as $todo_id) {
				// delete repeat params
				$this->deleteRepeatParams($todo_id);
				$this->deleteParentTodo($todo_id);

				TeamTime::trigger()->onDeleteTodo($todo_id);
			}

			// delete current id ref from children
			$query = 'delete from #__teamtime_todo_ref ' .
					' WHERE parent_id IN (' . $cids . ')';
			$res = $this->_db->Execute($query);
		}

		return true;
	}

	public function deleteRepeatParams($todo_id) {
		$db = & JFactory::getDBO();

		$query = "delete from #__teamtime_todo_repeatparams
				where todo_id = " . $todo_id;
		$db->Execute($query);

		$query = "delete from #__teamtime_todo_repeatdate
				where todo_id = " . $todo_id;
		$db->Execute($query);
	}

	public function set_repeat_dates($todo_id, $created, $row) {
		// update repeat dates
		$db = & JFactory::getDBO();

		$result = 0;

		$query = "delete from #__teamtime_todo_repeatdate
				where todo_id = " . $todo_id;
		$db->Execute($query);

		$row->created = $created;
		$event = new TeamTime_Calendar_Event($row);
		$events_dates = $event->generate_dates();

		//error_log("Set dates for $todo_id, created: $created, [{$row->start_date}..{$row->end_date}], dates:");
		//error_log(print_r($events_dates, true));

		$result = sizeof($events_dates);
		if ($result == 0)
			return $result;

		$query = "insert into #__teamtime_todo_repeatdate (todo_id, repeat_date) values ";
		foreach ($events_dates as $i => $v) {
			if ($i > 0)
				$query .= ",\n";

			$v = $db->Quote($v);
			$query .= "($todo_id, $v)";
		}
		$db->Execute($query);

		return $result;
	}

	public function setRepeatParams($todo_id, $post) {
		$db = & JFactory::getDBO();

		if (is_array($post)) {
			// +3 years for uncompleted events
			if ((int) $post["end_date_type"] == 0)
				$post["end_date"] = date("Y-m-d H:i:s", time() + 24 * 60 * 60 * 365 * 3);

			//modify start_date for day start
			$start_date = strtotime($post["start_date"]);
			$post["start_date"] = date("Y", $start_date) . "-" .
					date("m", $start_date) . "-" . date("d", $start_date) . " 00:00:00";

			//modify end_date for day end
			$end_date = strtotime($post["end_date"]);
			$post["end_date"] = date("Y", $end_date) . "-" . date("m", $end_date) . "-" .
					date("d", $end_date) . " 23:59:59";

			$row = new stdClass();
			$row->todo_id = $todo_id;
			$row->repeat_mode = $post["repeating"];
			$row->repeat_mon = isset($post["mon"]) ? 1 : 0;
			$row->repeat_tue = isset($post["tue"]) ? 1 : 0;
			$row->repeat_wed = isset($post["wed"]) ? 1 : 0;
			$row->repeat_thu = isset($post["thu"]) ? 1 : 0;
			$row->repeat_fri = isset($post["fri"]) ? 1 : 0;
			$row->repeat_sat = isset($post["sat"]) ? 1 : 0;
			$row->repeat_sun = isset($post["sun"]) ? 1 : 0;
			$row->repeat_interval = $post["repeat_interval"];
			$row->start_date = $post["start_date"];
			$row->end_date = $post["end_date"];

			$created = $post["created"];
		}
		else {
			$row = $post;
			$row->todo_id = $todo_id;
			$created = $row->created;
			unset($row->created);
		}

		//update or insert repeat params
		$fields = array();
		$values = array();
		$update_values = array();
		foreach (get_object_vars($row) as $k => $v) {
			$fields[] = "`" . $k . "`";
			$values[] = $db->Quote($v);

			if ($k != "todo_id")
				$update_values[] = "`" . $k . "` = " . $db->Quote($v);
		}
		$query = "insert into #__teamtime_todo_repeatparams
					(" . implode(",", $fields) . " ) values (" . implode(",",
						$values) . ")
				on duplicate KEY UPDATE " . implode(",", $update_values);
		$db->Execute($query);

		//error_log("Query: $query");

		$result = $this->set_repeat_dates($todo_id, $created, $row);
		if ($result == 0) {
			$this->delete(array($todo_id));
		}
	}

	public function get_repeat_params($todo_id) {
		$db = & JFactory::getDBO();

		$query = "select * from #__teamtime_todo_repeatparams
				where todo_id = " . $todo_id;
		$db->setQuery($query);
		$row = $db->loadObject();

		return $row;
	}

	public function get_events_count($todo_id, $where = "") {
		$db = & JFactory::getDBO();

		$sql = "select count(*) as events_count from #__teamtime_todo_repeatdate
				where todo_id = {$todo_id} {$where}";
		$db->setQuery($sql);
		$row = $db->loadObject();

		return $row->events_count;
	}

	public function get_repeat_num($todo_id, $start_range, $end_range) {
		$db = & JFactory::getDBO();

		$todo_id = (int) $todo_id;
		$start_range = $db->Quote($start_range);
		$end_range = $db->Quote($end_range);

		$query = "select count(*) as num from #__teamtime_todo_repeatdate
				where todo_id = {$todo_id} and repeat_date between {$start_range} and {$end_range}";

		//error_log($query);

		$db->setQuery($query);
		$row = $db->loadObject();

		return $row->num;
	}

	public function trunc_repeat_dates($todo_id, $range = array()) {
		if (!isset($range["end_date"]) && !isset($range["start_date"]))
			return;

		$db = & JFactory::getDBO();

		$todo = new TeamtimeModelTodo();
		$todo->setId($todo_id);
		$data = $todo->getData();

		$row = $this->get_repeat_params($todo_id);

		$update_values = array();

		if (isset($range["start_date"])) {

			//modify start_date for day start
			$start_date = strtotime($range["start_date"]);
			$range["start_date"] = date("Y", $start_date) . "-" .
					date("m", $start_date) . "-" . date("d", $start_date) . " 00:00:00";

			$row->start_date = $range["start_date"];
			$update_values[] = "start_date = " . $db->Quote($row->start_date);
		}

		if (isset($range["end_date"])) {

			//modify end_date for day end
			$end_date = strtotime($range["end_date"]);
			$range["end_date"] = date("Y", $end_date) . "-" .
					date("m", $end_date) . "-" . date("d", $end_date) . " 23:59:59";

			$row->end_date = $range["end_date"];
			$update_values[] = "end_date = " . $db->Quote($row->end_date);
		}

		$query = "update #__teamtime_todo_repeatparams
				set " . implode(", ", $update_values) . "
				where todo_id = " . $todo_id;

		$db->Execute($query);

		$result = $this->set_repeat_dates($todo_id, $data->created, $row);
		if ($result == 0) {
			$this->delete(array($todo_id));
		}

		return true;
	}

	public function create_repeat_copy($src_id, $range = array()) {
		$db = & JFactory::getDBO();

		//copy todo data
		$query = "select * from #__teamtime_todo where id = " . $src_id;
		$db->setQuery($query);
		$src_data = $db->loadAssoc($query);
		$src_data["ignore_offset"] = true;
		unset($src_data["id"]);

		$new_todo = new TeamtimeModelTodo();
		if ($new_todo->store($src_data)) {
			$new_todo_id = $new_todo->_data->id;

			//copy params data
			$params = $this->get_repeat_params($src_id);
			$params->created = $src_data["created"];

			if (isset($range["start_date"])) {

				//modify start_date for day start
				$start_date = strtotime($range["start_date"]);
				$range["start_date"] = date("Y", $start_date) . "-" .
						date("m", $start_date) . "-" . date("d", $start_date) . " 00:00:00";

				$params->start_date = $range["start_date"];
			}

			if (isset($range["end_date"])) {

				//modify end_date for day end
				$end_date = strtotime($range["end_date"]);
				$range["end_date"] = date("Y", $end_date) . "-" .
						date("m", $end_date) . "-" . date("d", $end_date) . " 23:59:59";

				$params->end_date = $range["end_date"];
			}

			$this->setRepeatParams($new_todo_id, $params);
			TeamTime::helper()->getFormals()->onCopyTodo($src_id, $new_todo_id);

			return $new_todo_id;
		}

		return null;
	}

	public function create_copy($src_id) {
		$db = & JFactory::getDBO();

		//copy todo data
		$query = "select * from #__teamtime_todo where id = " . $src_id;
		$db->setQuery($query);
		$src_data = $db->loadAssoc($query);
		$src_data["ignore_offset"] = true;
		unset($src_data["id"]);

		$new_todo = new TeamtimeModelTodo();
		if ($new_todo->store($src_data)) {
			$new_todo_id = $new_todo->_data->id;

			TeamTime::helper()->getFormals()->onCopyTodo($src_id, $new_todo_id);

			return $new_todo_id;
		}

		return null;
	}

	public function exclude_repeated_todo($id, $old_date, $new_date) {
		$db = & JFactory::getDBO();

		$repeat_data = $this->get_repeat_params($id);
		if (!$repeat_data) {
			return;
		}

		// make copy of repeated todo
		$new_todo_id = $this->create_copy($id);
		if (!$new_todo_id) {
			return;
		}

		// update created date for copy
		$query = "update #__teamtime_todo
				set created = " . $db->Quote($new_date) . ", hours_fact = 0
				where id = " . (int) $new_todo_id;
		$res = $db->Execute($query);

		// add ref for source repeated todo
		//$query = "insert ignore into #__teamtime_repeat_todo_ref
		//(todo_id, repeating_history) values(" .
		//    (int) $new_todo_id . ", " . $db->Quote($repeat_data->repeat_mode) . ")";
		//$res = $db->Execute($query);

		$this->create_repeat_copy($id,
				array(
			"start_date" => date("Y-m-d H:i:s", strtotime($old_date . " +1 day"))));

		$res = $this->trunc_repeat_dates($id,
				array(
			"end_date" => date("Y-m-d H:i:s", strtotime($old_date . " -1 day"))));
	}

	public function has_week_days($params) {
		return $params->repeat_mon ||
				$params->repeat_tue ||
				$params->repeat_wed ||
				$params->repeat_thu ||
				$params->repeat_fri ||
				$params->repeat_sat ||
				$params->repeat_sun;
	}

	public function params_to_str($params, $created) {
		$s = "";

		$repeating = "";
		switch ($params->repeat_mode) {
			case 'weekly':
				$repeating = JText::_('Every Week');
				break;

			case 'monthly':
				$repeating = JText::_('Every month');
				break;

			case 'yearly':
				$repeating = JText::_('Every year');
				break;

			default:
				$repeating = "";
		}

		if ($params->repeat_interval != 1) {
			$tmp = explode(" ", $repeating);
			$repeating = $tmp[0] . " " . $params->repeat_interval . " " . $tmp[1];
		}
		$s .= $repeating;

		$month_strs = array(
			"",
			JText::_("JANUARY"),
			JText::_("FEBRUARY"),
			JText::_("MARCH"),
			JText::_("APRIL"),
			JText::_("MAY"),
			JText::_("JUNE"),
			JText::_("JULY"),
			JText::_("AUGUST"),
			JText::_("SEPTEMBER"),
			JText::_("OCTOBER"),
			JText::_("NOVEMBER"),
			JText::_("DECEMBER")
		);

		$s .= ", " . $month_strs[(int) date("m", $created)];

		$week_days = array();
		if ($params->repeat_mon)
			$week_days[] = JText::_('MON');
		if ($params->repeat_tue)
			$week_days[] = JText::_('tue');
		if ($params->repeat_wed)
			$week_days[] = JText::_('wed');
		if ($params->repeat_thu)
			$week_days[] = JText::_('thu');
		if ($params->repeat_fri)
			$week_days[] = JText::_('fri');
		if ($params->repeat_sat)
			$week_days[] = JText::_('sat');
		if ($params->repeat_sun)
			$week_days[] = JText::_('sun');

		if (sizeof($week_days) > 0) {
			$s .= ", " . implode(", ", $week_days) . ", " . JText::_("REPEATING NEAR DAY");
		}
		else {
			$s .= ", ";
		}

		$day_value = (int) date("d", $created);
		if (sizeof($week_days) == 0 && $params->repeat_mode == "monthly") {
			if (date("t", $created) == (int) date("d", $created))
				$day_value = JText::_('REPEATING LAST');
		}

		$s .= " " . $day_value . " " . JText::_("REPEATING DAY");

		if ($params->end_date != "0000-00-00 00:00:00")
			$s .= " - " . JText::_('REPEATING END UNTILL') . " " . array_shift(explode(" ", $params->end_date));

		return $s;
	}

	public function create_copy_forlog($id) {
		$db = & JFactory::getDBO();

		//get selected todo date
		$this->setId((int) $id);
		$todo_data = $this->getData();

		$current_date = $todo_data->current_repeat_date;
		$current_date = date("Y-m-d", strtotime($current_date)) . " " .
				date("H:i:00", strtotime($todo_data->created));

		$result = null;

		$repeat_data = $this->get_repeat_params($id);
		if (!$repeat_data)
			return $result;

		//make copy of repeated todo
		$new_todo_id = $this->create_copy($id);
		if (!$new_todo_id)
			return $result;

		$result = $new_todo_id;

		//reset state for src todo
		$query = "update #__teamtime_todo
				set state = 0
				where id = " . (int) $id;
		$res = $db->Execute($query);

		//update created date for copy
		$query = "update #__teamtime_todo
				set created = " . $db->Quote($current_date) . ", hours_fact = 0
				where id = " . (int) $new_todo_id;
		$res = $db->Execute($query);

		$this->create_repeat_copy($id,
				array(
			"start_date" => date("Y-m-d H:i:s", strtotime($current_date . " +1 day"))));

		$res = $this->trunc_repeat_dates($id,
				array(
			"end_date" => date("Y-m-d H:i:s", strtotime($current_date . " -1 day"))));

		//add ref for source repeated todo
		$query = "insert ignore into #__teamtime_repeat_todo_ref
				(todo_id, repeating_history) values(" .
				(int) $new_todo_id . ", '" . $repeat_data->repeat_mode . "')";
		$res = $db->Execute($query);

		return $result;
	}

	/* function getTodoState($ids, $todo) {
	  //normal todo
	  if (isset($ids[$todo->id . "_"]))
	  return $ids[$todo->id . "_"] == TODO_STATE_DONE ? TODO_STATE_DONE : TODO_STATE_OPEN;

	  //repeated todo
	  if (isset($ids[$todo->id . "_" . $todo->current_repeat_date]))
	  return $ids[$todo->id . "_" . $todo->current_repeat_date] == TODO_STATE_DONE ?
	  TODO_STATE_DONE : TODO_STATE_OPEN;

	  return TODO_STATE_OPEN;
	  } */

	/* function isTodoSelected($ids, $todo) { //repeated todo has any selected item
	  foreach ($ids as $k => $r) {
	  list($id, $t) = explode("_", $k);
	  if ($id == $todo->id && $r == TODO_STATE_DONE)
	  return TODO_STATE_DONE;
	  }
	  return TODO_STATE_OPEN;
	  } */

	public function clear_selected_todos($user_id) {
		$db = & JFactory::getDBO();

		$query = "update #__teamtime_todo
				set selected = 0
				where user_id = " . (int) $user_id;
		$res = $db->Execute($query);

		return $res;
	}

	public function set_selected_todos($user_id, $todo_ids) {
		$db = & JFactory::getDBO();

		$this->clear_selected_todos($user_id);

		$id = null;
		$curr_date = null;
		//find selected todo id
		foreach ($todo_ids as $k => $r) {
			list($id, $t) = explode("_", $k);
			if ($r) {
				$curr_date = $db->Quote($t);
				break;
			}
		}
		if ($id) {
			$query = "update #__teamtime_todo
					set selected = 1, current_repeat_date = {$curr_date}
					where id = " . (int) $id;
			$res = $db->Execute($query);
		}

		return $res;
	}

	public function process_todos($rows) {
		$fhours = 0;
		$phours = 0;

		foreach ($rows as $row) {
			$tmp = $row->is_repeat;

			if ($tmp && $row->current_repeat_date != "0000-00-00 00:00:00") {
				$row->tmp_repeat_date = $row->current_repeat_date;
				$this->setId($row->id);
				$data = $this->getData();

				$row->tmp_checked = ($row->selected &&
						$data->current_repeat_date == $row->current_repeat_date) ? 1 : 0;
			}
			else {
				$row->tmp_repeat_date = "";
				$row->tmp_checked = $row->selected ? 1 : 0;
			}

			//calc sum hours
			$fhours += $row->hours_fact;
			$phours += $row->hours_plan;
		}

		return array($fhours, $phours, $rows);
	}

	public function get_hours_fact($ids, $where = "") {
		$db = & JFactory::getDBO();

		if (sizeof($ids) == 0)
			return 0;

		$ids = implode(", ", $ids);
		$sql = "select sum(duration)/60 as sfact from #__teamtime_log
				where todo_id in ({$ids}) {$where}";
		$db->setQuery($sql);
		$row = $db->loadObject();

		//error_log($db->replacePrefix($sql));

		return $row->sfact;
	}

	public function getTree($params = array(), $parent_id = 0, $cmd = array()) {
		$filter = array();

		if ($parent_id == 0) {
			$filter[] = "b.parent_id is null ";
		}
		else {
			$filter[] = "b.parent_id = " . $parent_id;

			if (isset($cmd["unsetChildrenFilter"])) {
				foreach ($cmd["unsetChildrenFilter"] as $v) {
					unset($params[$v]);
				}
			}
		}

		foreach ($params as $k => $v) {
			if (is_array($v)) {
				if ($v[1] == "in") {
					if (sizeof($v[0]) > 0) {
						$filter[] = "a." . $k . " in (" . implode(",", $v[0]) . ")";
					}
				}
				else {
					$filter[] = "a." . $k . $v[1] . $this->_db->Quote($v[0]);
				}
			}
			else {
				$filter[] = "a." . $k . " = " . $this->_db->Quote($v);
			}
		}

		if (sizeof($filter) > 0) {
			$filter = " where " . implode(" and ", $filter);
		}

		$query = "select a.id, a.title from #__teamtime_todo as a
				left join #__teamtime_todo_ref as b on a.id = b.todo_id " . $filter .
				" order by a.title";

		$this->_db->setQuery($query);

		$rows = $this->_db->loadObjectList();
		foreach ($rows as $i => $row) {
			// hide current node
			if (isset($cmd["hide_node"]) && $cmd["hide_node"]["node_id"] == $row->id) {
				//$rows[$i]->children = array();
				unset($rows[$i]);
			}
			else {
				$rows[$i]->children = $this->getTree($params, $row->id, $cmd);
			}
		}

		return $rows;
	}

	public function treeToList($tree, $level = 0) {
		$result = array();

		foreach ($tree as $row) {
			$r = new stdClass();
			$r->level = $level;
			$r->id = $row->id;
			$r->title = $row->title;
			$result[] = $r;

			if (sizeof($row->children) > 0) {
				$result = array_merge($result, $this->treeToList($row->children, $level + 1));
			}
		}

		return $result;
	}

	public function getDataForTreelist($list, $add_select = array()) {
		$ids = array();
		foreach ($list as $row) {
			$ids[] = $row->id;
		}
		if (sizeof($ids) == 0) {
			return array();
		}

		$db = & JFactory::getDBO();

		$add_fields = "";
		$add_join = "";
		$add_where = "";
		if (sizeof($add_select) > 0) {
			foreach ($add_select as $r) {
				$add_fields .= ", " . $r["fields"];
				$add_join .= " " . $r["join"];

				if (isset($r["where"])) {
					$add_where .= $r["where"] != "" ? (" and " . $r["where"]) : "";
				}
			}
		}

		$ids = implode(", ", $ids);
		$sql = "select a.* {$add_fields} from #__teamtime_todo as a
				{$add_join}
				where a.id in ({$ids}) {$add_where}";

		//error_log($sql);

		$db->setQuery($sql);
		$rows = $db->loadObjectList();

		$result = array();
		foreach ($rows as $row) {
			$result[$row->id] = $row;
		}

		return $result;
	}

	public function calcfields_for_parent_todo($parent_id, $src_row = null) {
		if ($src_row == null) {
			$result = new stdClass();
			$items_count = 0;
		}
		else {
			$result = $src_row;
			$items_count = 1;
		}

		$list = $this->treeToList($this->getTree(array(), $parent_id));
		$add_select = array(
			array(
				"fields" => "c.rate as project_hourly_rate, d.mark_expenses, d.mark_hours_plan",
				"join" => "left join #__teamtime_project as c on a.project_id = c.id
						left join #__teamtimeformals_todo as d on a.id = d.todo_id"
			)
		);
		$data = $this->getDataForTreelist($list, $add_select);

		//$items_count += sizeof($data);
		foreach ($data as $row) {

			/* error_log(print_r(array(
			  $row->mark_expenses,
			  $row->costs,
			  $result->costs
			  ), true)); */

			if ($row->mark_expenses || $row->mark_hours_plan) {
				$result->hours_plan += $row->hours_plan;
				$result->project_hourly_rate += $row->project_hourly_rate;
				$result->costs += $row->costs;

				$items_count++;
				//TODO Add other fields if need
			}
		}

		$result->project_hourly_rate = $result->project_hourly_rate / $items_count;

		return $result;
	}

	public function getUser($id) {
		$query = "SELECT * FROM #__users
			where id = " . (int) $id;
		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();

		return $row;
	}

	public function getDefaultDescription($initialText = "", $needsText = "", $instrText = "") {
		if ($initialText == "") {
			$initialText = "_";
		}

		if ($needsText == "") {
			$needsText = "_";
		}

		if ($instrText == "") {
			$instrText = "_";
		}

		$result = '<h3 class="todo_content">' . JText::_("TODO INITIAL DATA") . '</h3>
<ol class="todo_content">
<li>' . $initialText . '</li>
</ol>
<h3 class="todo_content">' . JText::_("TODO NEEDS") . '</h3>
<ol class="todo_content">
<li>' . $needsText . '</li>
</ol>
<h3 class="todo_content">' . JText::_("TODO INSTRUCTIONS") . '</h3>
<ol class="todo_content">
<li>' . $instrText . '</li>
</ol>';

		return $result;
	}

	public function isEditedDescription($descr) {
		$lenDefault = mb_strlen($this->getDefaultDescription());
		$lnDescr = mb_strlen(trim(str_replace(array("&nbsp;"), array(" "), $descr)));

		return ($lnDescr - $lenDefault) > 30;
	}

	public function createAutoTodoForLog($log, $post) {
		$data = array();

		//$config = & JFactory::getConfig();
		//$tzoffset = $config->getValue('config.offset');
		//$date = & JFactory::getDate($log->created);
		$dateTitle = date("Y-m-d H:i:s");

		$mType = new Type($log->type_id);
		$typeName = $mType->name;

		$mTask = new Task($log->task_id);
		$taskName = $mTask->name;

		$data["ignore_offset"] = true;
		$data["title"] = $typeName . " / " . $taskName . " # " . $dateTitle;
		$data["description"] = $this->getDefaultDescription($post["autotodo_text"], $data["title"]);
		//$data["created"] = $date->toMySQL();
		$data["created"] = $log->created;
		$data["is_autotodo"] = 1;

		// selected parent todo
		$data["curtodoid"] = $post["curtodoid"];

		//error_log($data["created"]);

		$data["user_id"] = $log->user_id;
		$data["project_id"] = $log->project_id;
		$data["type_id"] = $log->type_id;
		$data["task_id"] = $log->task_id;

		$params = get_object_vars($log);

		// set target by task
		$mTargetvector = new TeamtimecareerModelTargetvector();
		$data["target_id"] = $params["target_id"] =
				$mTargetvector->getTargetIdByTaskId($params["task_id"]);

		$todo = new Todo();
		$data["hourly_rate"] = round($todo->getHourlyRateByParams($params), 2);

		//error_log(print_r($params, true));

		$res = $this->store($data);

		//error_log(print_r($this->_data, true));

		return $res ? $this->_data : null;
	}

	public function sortTodoByCreated($a, $b) {
		$ta = strtotime($a);
		$tb = strtotime($b);

		if ($ta > $tb) {
			return 1;
		}
		else if ($ta < $tb) {
			return -1;
		}

		return 0;
	}

	public function getTodosQuery($filter = array(), $sqlFilter = array()) {
		$table = & $this->getTable($this->_table);

		$fields = "a.*, p.*, rd.*,
				proj.name as project_name, usr.name as user_name";
		$join = "
			left join #__teamtime_task as b on a.task_id = b.id
			left join #__teamtime_project as proj on a.project_id = proj.id
			left join #__users as usr on a.user_id = usr.id

			left join #__teamtime_repeat_todo_ref as rr on a.id = rr.todo_id

			left join #__teamtime_todo_repeatparams as p on a.id = p.todo_id
			left join #__teamtime_todo_repeatdate as rd on a.id = rd.todo_id

			left join #__teamtime_todo_ref as tp on a.id = tp.todo_id
		";

		$where = array();
		if (sizeof($sqlFilter) > 0) {
			$where = $sqlFilter;
		}

		// filter by date
		if (isset($filter["start_date"]) && isset($filter["end_date"])) {
			$startDate = $this->_db->Quote($filter["start_date"]);
			$endDate = $this->_db->Quote($filter["end_date"]);
			$where[] = "if(rd.todo_id is null,
				a.created between " . $startDate . " and " . $endDate . ",
				rd.repeat_date between " . $startDate . " and " . $endDate . "
			 )";
		}

		// filter by archive status
		if (isset($filter["archived"])) {
			$sqlPart = TeamTime::helper()->getCalendar()->getTodosQueryPart($filter);
			if (!$sqlPart instanceof TeamTime_Undefined) {
				$fields .= $sqlPart["fields"];
				$join .= $sqlPart["join"];
				if ($sqlPart["where"] != "") {
					$where[] = $sqlPart["where"];
				}
			}
		}

		$result = "select " . $fields . "
			from " . $table->getTableName() . " as a " .
				$join;
		if (sizeof($where) > 0) {
			$result .= " where " . implode(" and ", $where);
		}

		$result .= "
			order by if(rd.todo_id is null, a.created, rd.repeat_date)
		";

		//error_log($this->_db->replacePrefix($result));

		return $result;
	}

	public function getTodos($filter = array(), $orderDir = "") {
		$result = array();
		$where = array();

		$query = "select a.*,
				u.name as user_name, p.name as project_name, tp.name as type_name
				from #__teamtime_todo a
				left join #__users u on a.user_id = u.id
				left join #__teamtime_project p on a.project_id = p.id
				left join #__teamtime_type tp on a.type_id = tp.id
				left join #__teamtime_task ta on a.task_id = ta.id";

		if (isset($filter["ids"])) {
			$where[] = "a.id in (" . implode(",", $filter["ids"]) . ")";
		}

		if (isset($filter["task_ids"])) {
			$where[] = "a.task_id in (" . implode(",", $filter["task_ids"]) . ")";
		}

		if (isset($filter["task_id"])) {
			$where[] = "a.task_id = " . (int) $filter["task_id"];
		}

		if (isset($filter["type_ids"])) {
			$where[] = "a.type_id in (" . implode(",", $filter["type_ids"]) . ")";
		}

		if (isset($filter["type_id"])) {
			$where[] = "a.type_id = " . (int) $filter["type_id"];
		}

		if (sizeof($where) > 0) {
			$query .= " where " . implode(" and ", $where);
		}

		$query .= " order by a.created " . $orderDir;

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		if ($rows) {
			foreach ($rows as $row) {
				$result[] = $row;

				//...
			}
		}

		return $result;
	}

	public function getTodosByTree($filter = array()) {
		$params = array();
		if (isset($filter["ids"])) {
			$params["id"] = array($filter["ids"], "in");
		}

		$todoTree = $this->getTree($params, 0, array("unsetChildrenFilter" => array("id")));

		$todoList = $this->treeToList($todoTree);

		$add_select = array(
			array(
				"fields" => "
						u.name as user_name,
						p.name as project_name,
						tp.name as type_name",
				"join" => "
						left join #__users u on a.user_id = u.id
						left join #__teamtime_project p on a.project_id = p.id
						left join #__teamtime_type tp on a.type_id = tp.id
						left join #__teamtime_task ta on a.task_id = ta.id"
			)
		);

		$todos = $this->getDataForTreelist($todoList, $add_select);
		$result = array_values($todos);

		return $result;
	}

	public function initTodosPrice($todos) {
		foreach ($todos as $i => $todo) {
			$todos[$i]->price = $todo->hourly_rate * $todo->hours_plan;
		}

		return $todos;
	}

	public function getLogs($filter = array()) {
		$where = array();

		if (isset($filter["count"])) {
			$fields = "count(*) as num";
		}
		else {
			$fields = "a.*";
		}

		$query = "select {$fields} from #__teamtime_log a";

		if (isset($filter["todo_id"])) {
			$where[] = "a.todo_id = " . (int) $filter["todo_id"];
		}

		if (sizeof($where) > 0) {
			$query .= " where " . implode(" and ", $where);
		}

		//error_log($this->_db->replacePrefix($query));
		//$query .= " order by a.created";

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();

		if (isset($filter["count"])) {
			$result = $rows[0]->num;
		}
		else {
			$result = $rows ? $rows : array();
		}

		return $result;
	}

	public function showTodo() {
		$todoId = JRequest::getInt('todo_id');
		$todo = new Todo($todoId);
		$mProject = new TeamtimeModelProject();
		if (!$mProject->projectIsAllowed($todo->project_id)) {
			return;
		}

		$helperBpmn = TeamTime::helper()->getBpmn();
		print "<h3>" . $todo->title . "</h3>";
		print $helperBpmn->getProcessLink($todo->id);
		print '<div class="todo-description">
			' . $todo->description . '
			</div>';
		print $helperBpmn->jsonTodoInfo($todo);
	}

	public function showReportTodo() {
		$user = & JFactory::getUser();
		$projectId = JRequest::getVar("project_id");
		$todoId = JRequest::getInt('todo_id');
		$todo = new Todo($todoId);

		$mProject = new TeamtimeModelProject();
		if (!$user->guest) {
			if (!$mProject->projectIsAllowed($todo->project_id)) {
				return;
			}
		}

		$helperBase = TeamTime::helper()->getBase();
		$helperBpmn = TeamTime::helper()->getBpmn();
		if ($todoId && $projectId == $todo->project_id) {
			header("Content-Type: text/html; charset=UTF-8");
			print "<h2>" . $todo->title . "</h2>";
			print $helperBase->processRelativeLinks($todo->description, JURI::root());
			// todo json info
			print $helperBpmn->jsonTodoInfo($todo);
		}
	}

	public function filterWithAllowedProjects($ids, $acl) {
		$result = array();

		foreach ($ids as $id) {
			$todo = $this->getById($id);
			if (sizeof($tmp = $acl->filterUserProjectIds(array($todo->project_id))) > 0) {
				$result[] = $id;
			}
		}

		return $result;
	}

	public function getRepeatParamsSqlPart($params = array()) {
		$result = array();
		$where = array();

		$join = '
			left join #__teamtime_todo_repeatparams as trp on t.id = trp.todo_id
			left join #__teamtime_todo_repeatdate as trd on t.id = trd.todo_id
		';

		if (!empty($params["from"]) && !empty($params["until"])) {
			$from = $this->_db->Quote($this->_db->getEscaped($params["from"] . " 00:00:00", true), false);
			$until = $this->_db->Quote($this->_db->getEscaped($params["until"] . " 23:59:59", true), false);
			$where[] = "
				if (trd.todo_id is null,
					t.created >= " . $from . " and t.created <= " . $until . ",
					trd.repeat_date >= " . $from . " and trd.repeat_date <= " . $until . "
				)";
		}

		return array(
			"join" => $join,
			"where" => $where
		);
	}

	public function getMeta($todoId, $decode = true) {
		$result = "";

		$query = "select * from #__teamtime_todo_meta
			where id = " . (int) $todoId;
		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();
		if ($row) {
			$result = $row->meta;
		}

		if ($decode) {
			if ($result != "") {
				$result = json_decode($result);
			}
			else {
				$result = array();
			}
		}

		return $result;
	}

	public function setMeta($todoId, $data) {
		$data = json_encode($data);
		$data = $this->_db->Quote($data);

		$query = "insert into #__teamtime_todo_meta
      (id, meta)
      values(" . (int) $todoId . ", " . $data . ")
      on duplicate key update meta = " . $data;

		$this->_db->Execute($query);
	}

}
