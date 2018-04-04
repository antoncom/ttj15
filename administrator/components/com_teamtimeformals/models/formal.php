<?php

class TeamtimeformalsModelFormal extends Core_Joomla_Manager {

	public $_table = 'formal';

	public function getDataForProject($params, $variables) {
		$fromPeriod = $params["from_period"];
		$untilPeriod = $params["until_period"];
		$projectId = (int) $params["project_id"];

		$acl = new TeamTime_Acl();
		$projectId = array($projectId);
		$projectId = $acl->filterUserProjectIds($projectId);
		if ($projectId !== null) {
			$projectId = $projectId[0];
		}
		else {
			$projectId = 0;
		}

		$where = array();
		// todos list
		$query = "select a.*, b.name as type_name, c.name as project_name,
				c.rate as project_hourly_rate, c.dynamic_rate as project_dynamic_rate,
				tt.rate as task_rate,
				d.mark_expenses, d.mark_hours_plan, rd.repeat_date,
				rf.parent_id as parent_id,
				b.id as check_type_id, tt.id as check_task_id
			from #__teamtime_todo as a
			LEFT JOIN #__teamtime_todo_repeatparams AS p ON a.id = p.todo_id
			LEFT JOIN #__teamtime_todo_repeatdate AS rd ON a.id = rd.todo_id
			left join #__teamtime_type as b on a.type_id = b.id
			left join #__teamtime_project as c on a.project_id = c.id
			left join #__teamtimeformals_todo as d on a.id = d.todo_id
			left join #__teamtime_todo_ref as rf on a.id = rf.todo_id
			left join #__teamtime_task as tt on a.task_id = tt.id
		";
		$sqlPart = TeamTime::helper()->getBpmn()->getFormalsSqlPart($params);
		if (!$sqlPart instanceof TeamTime_Undefined) {
			$query .= $sqlPart["join"];
			$where = $sqlPart["where"];
		}

		if ($params["filter"]) {
			if (isset($params["filter"]["todo_id"])) {
				$where[] = "a.id = " . (int) $params["filter"]["todo_id"];
			}
		}
		else {
			$where[] = "a.project_id = " . $projectId;
			$where[] = "if (rd.todo_id is null,
				a.created >= " . $this->_db->Quote(
							$this->_db->getEscaped("$fromPeriod 00:00:00", true), false)
					. " and a.created <= " . $this->_db->Quote(
							$this->_db->getEscaped("$untilPeriod 23:59:59", true), false)
					. ", rd.repeat_date >= " . $this->_db->Quote(
							$this->_db->getEscaped("$fromPeriod 00:00:00", true), false)
					. " and rd.repeat_date <= " . $this->_db->Quote(
							$this->_db->getEscaped("$untilPeriod 23:59:59", true), false) . ")";
			$where[] = "(d.mark_expenses = 1 or d.mark_hours_plan = 1)";
			$where[] = "rf.parent_id is null";
		}

		if (sizeof($where) > 0) {
			$where = " where " . implode(" and ", $where);
		}
		$query .= $where . " order by a.created";

		//error_log($query);

		$this->_db->setQuery($query);
		$rowsTodos = $this->_db->loadObjectList();

		$mTodo = new TeamtimeModelTodo();
		$data = array();
		foreach ($rowsTodos as $i => $row) {
			if ($row->mark_hours_plan == 0) {
				$row->project_hourly_rate = 0;
			}

			if ($row->mark_expenses == 0) {
				$row->costs = 0;
			}

			// process $row for group data
			$rowsTodos[$i] = $mTodo->calcfields_for_parent_todo($row->id, $row);

			// set current repeat date if exists
			if ($row->repeat_date) {
				$rowsTodos[$i]->created = $row->repeat_date;
			}

			if (!isset($data[$row->type_id])) {
				$data[$row->type_id] = array();
			}

			$data[$row->type_id][] = $row;
		}

		return array(
			"rows_todos" => $rowsTodos,
			"data" => $data,
			"variables" => $variables
		);
	}

	public function getDataForUser($params, $variables) {
		$fromPeriod = $params["from_period"];
		$untilPeriod = $params["until_period"];
		$userId = (int) $params["project_id"];

		$mProject = new TeamtimeModelProject();

		$where = array();
		$query = "select a.*, b.name as type_name, c.name as project_name,
				b.id as check_type_id, 1 as check_task_id
			from #__teamtime_todo as a
			left join #__teamtime_type as b on a.type_id = b.id
			left join #__teamtime_project as c on a.project_id = c.id
			LEFT JOIN #__teamtime_todo_repeatparams AS p ON a.id = p.todo_id
			LEFT JOIN #__teamtime_todo_repeatdate AS rd ON a.id = rd.todo_id
		";
		$sqlPart = TeamTime::helper()->getBpmn()->getFormalsSqlPart($params);
		if (!$sqlPart instanceof TeamTime_Undefined) {
			$query .= $sqlPart["join"];
			$where = $sqlPart["where"];
		}

		$where[] = "a.user_id = " . $userId;

		$acl = new TeamTime_Acl();
		$projectId = $acl->filterUserProjectIds();
		if ($projectId !== null) {
			$where[] = "a.project_id in (" . implode(",", $projectId) . ")";
		}

		$where[] = "if (rd.todo_id is null,
			a.created >= " . $this->_db->Quote(
						$this->_db->getEscaped("$fromPeriod 00:00:00", true), false) .
				" and a.created <= " . $this->_db->Quote(
						$this->_db->getEscaped("$untilPeriod 23:59:59", true), false) .
				", rd.repeat_date >= " . $this->_db->Quote(
						$this->_db->getEscaped("$fromPeriod 00:00:00", true), false) .
				" and rd.repeat_date <= " . $this->_db->Quote(
						$this->_db->getEscaped("$untilPeriod 23:59:59", true), false) . ")";

		if (sizeof($where) > 0) {
			$where = " where " . implode(" and ", $where);
		}
		$query .= $where . " order by a.created";

		//error_log($query);

		$this->_db->setQuery($query);
		$rowsTodos = $this->_db->loadObjectList();

		$yearFromPeriod = date("Y-01-01", strtotime($fromPeriod));
		$yearUntilPeriod = date("Y-12-31", strtotime($untilPeriod));
		$yearLimitPeriod = date("Y-m-31"); // limit for current year-month
		$queryYear = "select a.*, b.name as type_name, c.name as project_name
        from #__teamtime_todo as a
        left join #__teamtime_type as b on a.type_id = b.id
        left join #__teamtime_project as c on a.project_id = c.id
        where a.user_id = {$userId}" .
				" and a.created >= " . $this->_db->Quote(
						$this->_db->getEscaped("$yearFromPeriod 00:00:00", true), false) .
				//" and a.created <= " . $this->_db->Quote($this->_db->getEscaped("$year_until_period 23:59:59", true), false) .
				" and a.created <= " . $this->_db->Quote(
						$this->_db->getEscaped("$yearLimitPeriod 23:59:59", true), false) .
				" order by a.created";
		$this->_db->setQuery($queryYear);
		$rowsTodosYear = $this->_db->loadObjectList();

		$queryYearData = "select distinct date_format(a.created, '%Y') as tmp_year,
          date_format(a.created, '%m') as tmp_m
        from #__teamtime_todo as a
        left join #__teamtime_type as b on a.type_id = b.id
        left join #__teamtime_project as c on a.project_id = c.id
        where a.user_id = {$userId}
          and a.created <= " . $this->_db->Quote(
						$this->_db->getEscaped("$yearLimitPeriod 23:59:59", true), false) .
				" order by a.created";
		$this->_db->setQuery($queryYearData);
		$tmpRows = $this->_db->loadObjectList();
		$rowsYearData = array();
		foreach ($tmpRows as $row) {
			if ($row->tmp_year == '0000') {
				continue;
			}

			if (!isset($rowsYearData[$row->tmp_year])) {
				$rowsYearData[$row->tmp_year] = array();
			}
			$rowsYearData[$row->tmp_year][] = $row->tmp_m;
		}

		$data = array();
		$totalSumPrice = 0;
		foreach ($rowsTodos as $row) {
			$k = $row->project_name . '-' . $row->type_name;
			if (!isset($data[$k])) {
				$data[$k] = array();
			}
			$data[$k][] = $row;
			$totalSumPrice += $row->hours_plan * $row->hourly_rate;
		}
		ksort($data); // sort by project/type

		$variables["total_user_sum"] = $totalSumPrice;
		$priceStr = TeamTime::helper()->getFormals()->num2curr($totalSumPrice);
		$variables["total_user_sum_string"] = $priceStr[1];
		$variables["total_user_sum_string_rest"] = $priceStr[2];

		$query = "select * from #__users where id = {$userId}";
		$this->_db->setQuery($query);
		$tmpRow = $this->_db->loadObject();
		$variables["for_user_name"] = $tmpRow->name;

		return array(
			"rows_todos" => $rowsTodos,
			"data" => $data,
			"variables" => $variables,
			"total_sum_price" => $totalSumPrice,
			"rows_todos_year" => $rowsTodosYear,
			"rows_year_data" => $rowsYearData
		);
	}

	public function getTemplateData($templateId) {
		$query = "select b.name as template_name, b.description as template_content,
				c.name as doctype_name, c.generator
				from #__teamtimeformals_template as b
				left join #__teamtimeformals_type as c on b.type = c.id
				where b.id = {$templateId}";
		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();

		return $row;
	}

	public function initVariables($params, $usingIn) {
		$fromPeriod = $params["from_period"];
		$untilPeriod = $params["until_period"];
		$projectId = (int) $params["project_id"];

		// select and set main variables
		if ($usingIn == "project") {
			$query = "select b.tagname, b.name, b.defaultval, a.content, a.mdate
          from #__teamtimeformals_variable as b
          left join #__teamtimeformals_formaldata as a on b.id = a.variable_id
          where a.project_id = {$projectId} or a.project_id is null
          order by a.mdate desc";
		}
		else if ($usingIn == "user") {
			$query = "select b.tagname, b.name, b.defaultval, a.content, a.mdate
          from #__teamtimeformals_variable as b
          left join #__teamtimeformals_formaldata as a on b.id = a.variable_id
          where a.user_id = {$projectId} or a.user_id is null
          order by a.mdate desc";
		}
		else {
			$query = "";
		}

		$this->_db->setQuery($query);
		$rowsVariables = $this->_db->loadObjectList();
		$variables = array();
		$variablesNames = array();
		foreach ($rowsVariables as $row) {
			// store as array with key by date
			if (substr($row->tagname, 0, 9) == "user_tax_") {
				if (trim($row->content) != "") {
					$v = $row->content;
				}
				else {
					$v = $row->defaultval;
				}

				if (!isset($variables[$row->tagname])) {
					$variables[$row->tagname] = array();
				}

				if ($v != "") {
					$variables[$row->tagname][$row->mdate] = $v;
				}
			}
			else {
				// use only last variable value
				if (isset($variables[$row->tagname])) {
					continue;
				}

				if (trim($row->content) != "") {
					$variables[$row->tagname] = $row->content;
				}
				else {
					$variables[$row->tagname] = $row->defaultval;
				}
			}

			$variablesNames[$row->tagname] = $row->name;
		}

		// process tax variables
		foreach (range(1, 10) as $v) {
			if (!isset($variables["user_tax_" . $v])
					|| sizeof($variables["user_tax_" . $v]) == 0) {
				unset($variables["user_tax_" . $v]);
			}
			else {
				$current_date_tax = strtotime(date("Y-m-01", strtotime($fromPeriod)));
				// set tax N for selected date
				foreach ($variables["user_tax_" . $v] as $d => $tax) {
					if (strtotime($d) <= $current_date_tax) {
						$variables["current_user_tax_" . $v] = $tax;
						break;
					}
				}
			}
		}

		return array($variablesNames, $variables);
	}

	private function getDocsCount($templateId) {
		$this->_db->setQuery("select count(*) as num from #__teamtimeformals_formal
				where doctype_id = " . (int) $templateId);
		$row = $this->_db->loadObject();

		return $row ? $row->num : 0;
	}

	public function initMainVariables($params, $variables = array()) {
		$fromPeriod = $params["from_period"];
		$untilPeriod = $params["until_period"];

		$variables["start_date_short"] = JHTML::_('date', $fromPeriod, "%d%m%y");
		$variables["end_date_short"] = JHTML::_('date', $untilPeriod, "%d%m%y");

		$variables["start_date_middle"] = JHTML::_('date', $fromPeriod, "%d%m%Y");
		$variables["end_date_middle"] = JHTML::_('date', $untilPeriod, "%d%m%Y");

		$dateStr = explode(" ", JHTML::_('date', $fromPeriod, "%d %m %Y"));
		$dateStr[1] = JText::_("STR_MONTH" . (int) $dateStr[1]);
		$dateStr = implode(" ", $dateStr);
		$variables["start_date_long"] = $dateStr;

		$dateStr = explode(" ", JHTML::_('date', $untilPeriod, "%d %m %Y"));
		$dateStr[1] = JText::_("STR_MONTH" . (int) $dateStr[1]);
		$dateStr = implode(" ", $dateStr);
		$variables["end_date_long"] = $dateStr;

		$now = date("Y-m-d");
		$variables["todate_short"] = JHTML::_('date', $now, "%d%m%y");
		$variables["todate_middle"] = JHTML::_('date', $now, "%d%m%Y");

		$variables["todate_day"] = JHTML::_('date', $now, "%d");
		$variables["todate_month"] = JHTML::_('date', $now, "%m");
		$variables["todate_year"] = JHTML::_('date', $now, "%Y");
		$variables["todate_of_month"] = JText::_("STR_MONTH" . (int) JHTML::_('date', $now, "%m"));

		$nowStr = explode(" ", JHTML::_('date', $now, "%d %m %Y"));
		$nowStr[1] = JText::_("STR_MONTH" . (int) $nowStr[1]);
		$nowStr = implode(" ", $nowStr);
		$variables["todate_long"] = $nowStr;

		$variables["start_date_short_pointed"] = JHTML::_('date', $fromPeriod, "%d.%m.%y");
		$variables["end_date_short_pointed"] = JHTML::_('date', $untilPeriod, "%d.%m.%y");

		$variables["start_date_middle_pointed"] = JHTML::_('date', $fromPeriod, "%d.%m.%Y");
		$variables["end_date_middle_pointed"] = JHTML::_('date', $untilPeriod, "%d.%m.%Y");

		$variables["current_month_YYYY"] = JHTML::_('date', $now, "%B %Y");
		$next_month = date("Y-m-d", mktime(0, 0, 0, date("n") + 1, date("j"), date("Y")));
		$variables["next_month_YYYY"] = JHTML::_('date', $next_month, "%B %Y");

		$variables["doc_counter"] = $this->getDocsCount($params["template_id"]) + 1;

		return $variables;
	}

	private function prepareVariables($content, $variables) {
		$search = array();
		$replace = array();
		foreach ($variables as $k => $v) {
			if (is_array($v)) {
				continue;
			}
			$search[] = '{' . $k . '}';
			$replace[] = '[' . $k . ']';
		}

		return str_replace($search, $replace, $content);
	}

	private function processVariables($content, $variables) {
		$search = array();
		$replace = array();
		foreach ($variables as $k => $v) {
			if (is_array($v)) {
				continue;
			}
			$search[] = '[' . $k . ']';
			$replace[] = $v;
		}

		return str_replace($search, $replace, $content);
	}

	public function generateContent($params, $using_in = "project") {
		// NOTE dont' change variable names (used in generators)
		$from_period = $params["from_period"];
		$until_period = $params["until_period"];
		$project_id = (int) $params["project_id"];

		$template_id = (int) $params["doctype_id"];
		$params["template_id"] = $template_id;
		
		//error_log(print_r($params, true));

		// get template data
		$rowTemplate = $this->getTemplateData($template_id);
		$total_sum_price = 0;
		$result_content = "";
		$generatorPath = $path = JPATH_ROOT .
				"/administrator/components/com_teamtimeformals/assets/generators/" .
				$rowTemplate->generator . ".php";
		if (!file_exists($generatorPath)) {
			return array("File $generatorPath not found");
		}

		// get variables and data
		list($variables_names, $variables) = $this->initVariables($params, $using_in);
		$variables = $this->initMainVariables($params, $variables);
		$data = array();
		$rows_todos = array();
		if ($using_in == "project") {
			$tmp_data = $this->getDataForProject($params, $variables);
			extract($tmp_data);
		}
		else if ($using_in == "user") {
			$tmp_data = $this->getDataForUser($params, $variables);
			extract($tmp_data);
		}

		// check errors
		$todoErrors = array();
		foreach ($rows_todos as $row) {
			if (empty($row->check_type_id) || empty($row->check_task_id)) {
				$todoErrors[$row->id] = $row->created . " / " .
						$row->title . " / " . $row->project_name;
			}
		}

		// generate formal
		$rowTemplate->template_content = $this->prepareVariables(
				$rowTemplate->template_content, $variables);
		$tpl = new HTML_Template_IT("");
		$tpl->setTemplate($rowTemplate->template_content, true, true);
		include($generatorPath);
		$result_content = $this->processVariables($result_content, $variables);

		if (sizeof($todoErrors) > 0) {
			$result_content = JText::_("Errors in todos") . "<br>" .
					JText::_("Type or task not defined in todo") . "<br>" .
					implode("<br>", array_values($todoErrors)) . "<br><hr><br>" . $result_content;
		}

		return array($result_content, $total_sum_price, $todoErrors);
	}

	public function getTemplateIdByGenerator($generator) {
		$generator = $this->_db->Quote($generator);
		$this->_db->setQuery("select b.* from #__teamtimeformals_type as a
				left join #__teamtimeformals_template as b on a.id = b.type
				where a.generator = $generator
				limit 1");
		$row = $this->_db->loadObject();

		return $row->id;
	}

	public function getDoctype($templateId) {
		$query = "select a.* from #__teamtimeformals_template as b
			left join #__teamtimeformals_type as a on b.type = a.id
			where b.id = " . $templateId;
		$this->_db->setQuery($query);
		$res = $this->_db->loadObject();

		return $res;
	}

}
