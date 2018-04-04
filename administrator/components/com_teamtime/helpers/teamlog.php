<?php

class JHTMLTeamlog {

	public function projectList($options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $idtag = false, $translate = false) {
		$acl = new TeamTime_Acl();
		$projectFilter = $acl->filterUserProjectIds();

		$where = array();
		if (sizeof($projectFilter) > 0) {
			$where[] = "a.id in (" . implode(",", $projectFilter) . ")";
		}
		if (sizeof($where) > 0) {
			$where = " where " . implode(" and ", $where);
		}
		else {
			$where = "";
		}

		$query = 'select a.id as value, a.name as text
			from #__teamlog_project AS a
			' . $where . '
			order by a.name';

		return JHTMLTeamlog::queryList($query, $options, $name, $attribs, $key, $text, $selected, $idtag,
						$translate);
	}

	function projectListState0($options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $idtag = false, $translate = false) {
		$query = 'SELECT b.id AS value, b.name AS text'
				. ' FROM #__teamlog_project AS b '
				. ' WHERE state = 0'
				. ' GROUP BY b.id'
				. ' ORDER BY b.name';
		return JHTMLTeamlog::queryList($query, $options, $name, $attribs, $key, $text, $selected, $idtag,
						$translate);
	}

	/*
	  Function: taskList
	  Returns task select list html string.
	 */

	function taskList($project_id, $options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $idtag = false, $translate = false) {

		if (is_array($options)) {
			reset($options);
		}
		else {
			$options = array($options);
		}

		$project = new Project($project_id);
		if ($project) {
			foreach ($project->getTaskTypeArray() as $typename => $tasks) {
				if (count($tasks)) {
					$options[] = JHTML::_('select.option', '', $typename);
					foreach ($tasks as $task) {
						$options[] = JHTML::_('select.option', $task->id, '- ' . $task->name);
					}
				}
			}
		}

		return JHTML::_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag,
						$translate);
	}

	function typeList($options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $idtag = false, $translate = false) {
		$query = 'SELECT a.id AS value, a.name AS text'
				. ' FROM #__teamlog_type AS a'
				. ' ORDER BY a.name';
		return JHTMLTeamlog::queryList($query, $options, $name, $attribs, $key, $text, $selected, $idtag,
						$translate);
	}

	function userList($options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $idtag = false, $translate = false) {
		$query = 'SELECT a.id AS value, a.name AS text'
				. ' FROM #__users AS a'
				. ' where a.block = 0'
				. ' ORDER BY name';

		return JHTMLTeamlog::queryList($query, $options, $name, $attribs, $key, $text, $selected, $idtag,
						$translate);
	}

	/*
	  Function: projectStateList
	  Returns project state select list html string.
	 */

	function projectStateList($options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $idtag = false, $translate = false) {
		return JHTMLTeamlog::arrayList(Project::getStates(), $options, $name, $attribs, $key, $text,
						$selected, $idtag, $translate);
	}

	/*
	  Function: taskStateList
	  Returns task state select list html string.
	 */

	function taskStateList($options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $idtag = false, $translate = false) {
		return JHTMLTeamlog::arrayList(Task::getStates(), $options, $name, $attribs, $key, $text,
						$selected, $idtag, $translate);
	}

	/*
	  Function: todoStateList
	  Returns todo state select list html string.
	 */

	function todoStateList($options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $idtag = false, $translate = false) {
		return JHTMLTeamlog::arrayList(Todo::getStates(), $options, $name, $attribs, $key, $text,
						$selected, $idtag, $translate);
	}

	function todoStateList2($options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $idtag = false, $translate = false) {
		$states = array(
			'' => JText::_('All Todos'),
			TODO_STATE_OPEN => JText::_('Open'),
			//TODO_STATE_DONE => JText::_('Done'),
			TODO_STATE_CLOSED => JText::_('Closed'));

		return JHTMLTeamlog::arrayList($states, $options, $name, $attribs, $key, $text, $selected, $idtag,
						$translate);
	}

	function todoPeriodList($options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $idtag = false, $translate = false) {
		$states = array(
			'' => JText::_('All periods'),
			'urgent' => JText::_('Urgent'),
			'week' => JText::_('Weekly'),
			'month' => JText::_('Monthly'),
			'year' => JText::_('Yearly'));

		//init todos counts for each period
		$db = & JFactory::getDBO();
		$table_todo = new TableTeamtimeTodo($db);
		$user = & YFactory::getUser();
		$user_id = $user->id;

		$params = array();
		if (JRequest::getVar("reset_filter") == 1) {
			$params = TeamTime::helper()->getBase()->getDefaultFilter();
		}

		$params["only_count"] = true;
		foreach ($states as $period => $v) {
			$params["filter_period"] = $period;
			$states[$period] .= ": " . $table_todo->getUserTodos($user_id, null, $params);
		}

		return JHTMLTeamlog::arrayList($states, $options, $name, $attribs, $key, $text, $selected, $idtag,
						$translate);
	}

	function todoPeriodListAdmin($options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $idtag = false, $translate = false) {
		$states = array(
			'' => JText::_('All periods'),
			'urgent' => JText::_('Urgent'),
			'week' => JText::_('Weekly'),
			'month' => JText::_('Monthly'),
			'year' => JText::_('Yearly'));

		return JHTMLTeamlog::arrayList($states, $options, $name, $attribs, $key, $text, $selected, $idtag,
						$translate);
	}

	/*
	  Function: queryList
	  Returns select list html string.
	 */

	function queryList($query, $options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $idtag = false, $translate = false) {

		if (is_array($options)) {
			reset($options);
		}
		else {
			$options = array($options);
		}

		$db = & JFactory::getDBO();
		$db->setQuery($query);
		$list = $db->loadObjectList();

		if ($db->getErrorMsg()) {
			echo $db->stderr(true);
		}

		// remove empty
		if ($list[0]->text == "" && $list[0]->value == "") {
			unset($list[0]);
		}

		$options = array_merge($options, $list);

		return JHTML::_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag,
						$translate);
	}

	/*
	  Function: arrayList
	  Returns select list html string.
	 */

	function arrayList($array, $options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $idtag = false, $translate = false) {

		if (is_array($options)) {
			reset($options);
		}
		else {
			$options = array($options);
		}

		$options = array_merge($options, JHTMLTeamlog::listOptions($array));
		return JHTML::_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag,
						$translate);
	}

	/*
	  Function: selectOptions
	  Returns select option as JHTML compatible array.
	 */

	function listOptions($array, $value = 'value', $text = 'text') {

		$options = array();

		if (is_array($array)) {
			foreach ($array as $val => $txt) {
				$options[] = JHTML::_('select.option', strval($val), $txt, $value, $text);
			}
		}

		return $options;
	}

	function todoList($projectId, $currentTodoId, $options, $name, $attribs = null, $key = 'value',
			$text = 'text', $selected = null, $idtag = false, $translate = false) {
		if (is_array($options)) {
			reset($options);
		}
		else {
			$options = array($options);
		}

		$model = new TeamtimeModelTodo();
		//$parent_id = $model->getParentTodo($current_todo_id);
		$params = array(
			"project_id" => $projectId,
			"state" => array(TODO_STATE_CLOSED, " != "));

		// hide current todo subtree
		$cmd = array(
			"hide_node" => array("node_id" => $currentTodoId)
		);

		//error_log(print_r($cmd, true));

		$tree = $model->getTree($params, 0, $cmd); //$parent_id
		$items = $model->treeToList($tree);

		foreach ($items as $row) {
			$s = str_repeat("&nbsp;", $row->level) . ($row->level > 0 ? "-" : "");
			$options[] = JHTML::_('select.option', $row->id, $s . $row->title, 'value', 'text',
							$row->id == $currentTodoId);
		}

		return JHTML::_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag,
						$translate);
	}

	function dateSelector2($options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $idtag = false, $translate = false) {
		$config = & JFactory::getConfig();

		// set date presets
		$date = JFactory::getDate();
		$date = $date->toUnix();
		$date = mktime(0, 0, 0, date('n', $date), date('j', $date), date('Y', $date));
		$monday = (date('w', $date) == 1) ? $date : strtotime('last Monday', $date);

		$date_presets['today'] = array(
			'name' => 'Today',
			'from' => date('Y-m-d'),
			'until' => date('Y-m-d'));

		$date_presets['week'] = array(
			'name' => 'This week',
			'from' => date('Y-m-d', $monday),
			'until' => date('Y-m-d', strtotime('+6 day', $monday)));

		$date_presets['month'] = array(
			'name' => 'This month',
			'from' => date('Y-m-d', mktime(0, 0, 0, date('n', $date), 1, date('Y', $date))),
			'until' => date('Y-m-d', mktime(0, 0, 0, date('n', $date) + 1, 0, date('Y', $date))));

		$date_presets['year'] = array(
			'name' => 'This year',
			'from' => date('Y-m-d', mktime(0, 0, 0, 1, 1, date('Y', $date))),
			'until' => date('Y-m-d', mktime(0, 0, 0, 12, 31, date('Y', $date))));

		$date_presets['last_week'] = array(
			'name' => 'Last week',
			'from' => date('Y-m-d', strtotime('-7 day', $monday)),
			'until' => date('Y-m-d', strtotime('-1 day', $monday)));

		$date_presets['last_month'] = array(
			'name' => 'Last month',
			'from' => date('Y-m-d', mktime(0, 0, 0, date('n', $date) - 1, 1, date('Y', $date))),
			'until' => date('Y-m-d', mktime(0, 0, 0, date('n', $date), 0, date('Y', $date))));

		$date_presets['last_year'] = array(
			'name' => 'Last year',
			'from' => date('Y-m-d', mktime(0, 0, 0, 1, 1, date('Y', $date) - 1)),
			'until' => date('Y-m-d', mktime(0, 0, 0, 12, 31, date('Y', $date) - 1)));

		// simpledate select
		$select = '';
		foreach ($date_presets as $n => $v) {
			$options[] = JHTML::_('select.option', $n, JText::_($v['name']), 'value', 'text');
			if ($n == $selected) {
				$from_period = $v['from'];
				$until_period = $v['until'];
			}
		}

		// set period
		//$tzoffset = $config->getValue('config.offset');
		//$from = JFactory::getDate($from_period, $tzoffset);
		//$until = JFactory::getDate($until_period, $tzoffset);

		$select_date = JHTML::_(
						'select.genericlist', $options, $name, $attribs, $key, $text, $selected);

		return array(
			"from_period" => $from_period,
			"until_period" => $until_period,
			"date_presets" => $date_presets,
			"selected" => $select,
			"select_date" => $select_date
		);
	}

	function getPieChartTable($elementId, $data_table, $total_value) {
		$result = array();

		if (sizeof($data_table) == 0) {
			$data_table = array();
		}

		//JHTML::script('raphael.js', "administrator/components/com_teamtime/assets/js/");
		//JHTML::script('raphael-piechart.js', "administrator/components/com_teamtime/assets/js/");

		$result[] = "<table id='{$elementId}' class='raphael-piechart'>";

		$perc1 = $total_value / 100;

		foreach ($data_table as $row) {
			$perc = (int) ($row->value / $perc1);

			$result[] = '
        <tr>
          <th>' . $row->name . '</th>
          <td>' . $perc . '%</td>
          <td>' . $row->label . '</td>
          <td>' . $row->color . '</td>
        </tr>';
		}

		$result[] = "</table>
      <div id='holder-{$elementId}'></div>
      <script>
        jQuery(function ($) {
          Raphael_PieChart('{$elementId}', 250, 150, 100);
        });
      </script>";

		return implode("", $result);
	}

}