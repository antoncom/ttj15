<?php

class TeamtimeViewReport extends JView {

	public $format = "";
	public $client_view = false;
	private $model;

	private function getDateSelect($fromPeriod, $untilPeriod, $filterFrom, $filterUntil) {
		$config = & JFactory::getConfig();
		$date = JFactory::getDate();
		$date = $date->toUnix();
		$date = mktime(0, 0, 0, date('n', $date), date('j', $date), date('Y', $date));
		$monday = (date('w', $date) == 1) ? $date : strtotime('last Monday', $date);

		$datePresets['last_month'] = array(
			'name' => JText::_('Last Month'),
			'from' => date('Y-m-d', mktime(0, 0, 0, date('n', $date) - 1, 1, date('Y', $date))),
			'until' => date('Y-m-d', mktime(0, 0, 0, date('n', $date), 0, date('Y', $date))));
		/* $date_presets['last30'] = array(
		  'name'  => JText::_('Last 30 days'),
		  'from'  => date('Y-m-d', strtotime('-29 day', $date)),
		  'until' => date('Y-m-d', $date)); */
		$datePresets['last30'] = array(
			'name' => JText::_('Last 30 days'),
			'from' => "",
			'until' => "");
		$datePresets['last_week'] = array(
			'name' => JText::_('Last Week'),
			'from' => date('Y-m-d', strtotime('-7 day', $monday)),
			'until' => date('Y-m-d', strtotime('-1 day', $monday)));
		$datePresets['today'] = array(
			'name' => JText::_('Today'),
			'from' => date('Y-m-d', $date),
			'until' => date('Y-m-d', $date));
		$datePresets['week'] = array(
			'name' => JText::_('This Week'),
			'from' => date('Y-m-d', $monday),
			'until' => date('Y-m-d', strtotime('+6 day', $monday)));
		$datePresets['month'] = array(
			'name' => JText::_('This Month'),
			'from' => date('Y-m-d', mktime(0, 0, 0, date('n', $date), 1, date('Y', $date))),
			'until' => date('Y-m-d', mktime(0, 0, 0, date('n', $date) + 1, 0, date('Y', $date))));
		$datePresets['year'] = array(
			'name' => JText::_('This Year'),
			'from' => date('Y-m-d', mktime(0, 0, 0, 1, 1, date('Y', $date))),
			'until' => date('Y-m-d', mktime(0, 0, 0, 12, 31, date('Y', $date))));
		$datePresets['next_week'] = array(
			'name' => JText::_('Next Week'),
			'from' => date('Y-m-d', strtotime('+7 day', $monday)),
			'until' => date('Y-m-d', strtotime('+13 day', $monday)));
		$datePresets['next_month'] = array(
			'name' => JText::_('Next Month'),
			'from' => date('Y-m-d', mktime(0, 0, 0, date('n', $date) + 1, 1, date('Y', $date))),
			'until' => date('Y-m-d', mktime(0, 0, 0, date('n', $date) + 2, 0, date('Y', $date))));

		// set period
		$tzoffset = $config->getValue('config.offset');
		$from = JFactory::getDate($fromPeriod, $tzoffset);
		$until = JFactory::getDate($untilPeriod, $tzoffset);

		// check period - set to defaults if no value is set or dates cannot be parsed
		if ($from->_date === false || $until->_date === false) {
			if ($fromPeriod != '?' && $untilPeriod != '?') {
				JError::raiseNotice(500, JText::_('Please enter a valid date format (YYYY-MM-DD)'));
			}
			$fromPeriod = $datePresets['last30']['from'];
			$untilPeriod = $datePresets['last30']['until'];
			$from = JFactory::getDate($fromPeriod, $tzoffset);
			$until = JFactory::getDate($untilPeriod, $tzoffset);
		}
		else {
			if ($from->toUnix() > $until->toUnix()) {
				list($fromPeriod, $untilPeriod) = array($untilPeriod, $fromPeriod);
				list($from, $until) = array($until, $from);
			}
		}

		// simpledate select
		$select = '';
		$selectedDate = array();
		$options = array(JHTML::_(
					'select.option', '', '- ' . JText::_('Select Period') . ' -', 'text', 'value'));
		foreach ($datePresets as $name => $value) {
			$options[] = JHTML::_('select.option', $name, JText::_($value['name']), 'text', 'value');
			if ($value['from'] == $fromPeriod && $value['until'] == $untilPeriod) {
				$select = $name;
				$selectedDate = $value;
			}
		}

		if ($filterUntil != "" || $filterFrom != "") {
			$select = "";
		}
		$result = JHTML::_(
						'select.genericlist', $options, 'period', 'class="inputbox" size="1"', 'text', 'value',
						$select);

		return array($result, $selectedDate, $datePresets);
	}

	private function getColorscheme($color, $count) {
		preg_match("/^([\da-f]{2})([\da-f]{2})([\da-f]{2})$/i", preg_replace(
						"/^#/", "", $color), $matches);
		$color_hex = array(strtolower($color));
		$color_rgb = array_slice($matches, 1);

		for ($i = 0; $i < 3; $i++) {
			$color_rgb[$i] = hexdec($color_rgb[$i]);
		}

		for ($i = 1; $i < $count; $i++) {
			$color_hex[$i] = "#";
			for ($j = 0; $j < 3; $j++) {
				$color_rgb[$j] += 15;
				if ($color_rgb[$j] < 0)
					$color_rgb[$j] = 0;
				if ($color_rgb[$j] > 255)
					$color_rgb[$j] = 255;
				$value_hex = dechex($color_rgb[$j]);
				$color_hex[$i] .= strlen($value_hex) < 2 ? "0" . $value_hex : $value_hex;
			}
		}

		return $color_hex;
	}

	private function initProjectReport($projectId, $client, $userId, $fromPeriod, $untilPeriod,
			$chartOptions) {
		if (sizeof($client) > 1) {
			$report = $this->model->getProjectReport($client, $userId, $fromPeriod, $untilPeriod);
		}
		else {
			$report = $this->model->getProjectReport($projectId, $userId, $fromPeriod, $untilPeriod);
		}
		$contentLayout = 'report_project';

		// create chart
		$chart_t = new FusionCharts('Pie2D', '400', '300');
		$chart_t->setSWFPath($chartOptions["swfpath"]);
		$chart_t->setChartParams(implode(';',
						array_merge(
								$chartOptions["params"], array('caption=Type stats', 'xAxisName=Types', 'yAxisName=Minutes'))));
		$colors = $this->getColorscheme('#224565', count($report['type']));

		$dataTable = array();
		foreach ($report['type'] as $type) {
			$perc = $type['total'] / $report['total'];
			$nextColor = array_shift($colors);

			// data for flash chart
			$param = $perc >= 0.05 ? 'name=' . $type['name'] : 'name=';
			$param .= ';hoverText=' . $type['name'] . ' ('
					. DateHelper::formatTimespan($type['total'], 'hr mi')
					. ')' . ';color=' . $nextColor;
			$chart_t->addChartData($type['total'], $param);

			// data for raphael piechart
			$tmp = new stdClass();
			$tmp->name = $perc >= 0.05 ? $type['name'] : "";
			$tmp->value = $type['total'];
			$tmp->label = $type['name'] . ' ('
					. DateHelper::formatTimespan($type['total'], 'hr mi')
					. '), ' . round(100 * $perc) . "%";
			$tmp->color = $nextColor;
			$dataTable[] = $tmp;
		}

		// set raphael chart data
		$typeChart = JHTML::_(
						"teamtime.getPieChartTable", "typeChart", $dataTable, $report['total']);
		$this->assignRef('typeChart', $typeChart);

		$chart_u = new FusionCharts('Pie2D', '400', '300');
		$chart_u->setSWFPath($chartOptions["swfpath"]);
		$chart_u->setChartParams(implode(';',
						array_merge(
								$chartOptions["params"], array('caption=User stats', 'xAxisName=Users', 'yAxisName=Minutes'))));
		$colors = $this->getColorscheme('#21561f', count($report['user']));

		$dataTable = array();
		foreach ($report['user'] as $usr) {
			$perc = $usr['total'] / $report['total'];
			$nextColor = array_shift($colors);

			// data for flash chart
			$param = $perc >= 0.05 ? 'name=' . $usr['name'] : 'name=';
			$param .= ';hoverText=' . $usr['name'] . ' ('
					. DateHelper::formatTimespan($usr['total'], 'hr mi')
					. ')' . ';color=' . $nextColor;
			$chart_u->addChartData($usr['total'], $param);

			// data for raphael piechart
			$tmp = new stdClass();
			$tmp->name = $perc >= 0.05 ? $usr['name'] : "";
			$tmp->value = $usr['total'];
			$tmp->label = $usr['name'] . ' ('
					. DateHelper::formatTimespan($usr['total'], 'hr mi')
					. '), ' . round(100 * $perc) . "%";
			$tmp->color = $nextColor;
			$dataTable[] = $tmp;
		}

		// set raphael chart data
		$userChart = JHTML::_(
						"teamtime.getPieChartTable", "userChart", $dataTable, $report['total']);
		$this->assignRef('userChart', $userChart);

		// set template vars
		$this->assignRef('type_chart', $chart_t);
		$this->assignRef('user_chart', $chart_u);

		return array($report, $contentLayout);
	}

	private function initUserReport(/* $projectId, $client, */ $userId, $fromPeriod, $untilPeriod,
			$chartOptions) {
		$report = $this->model->getUserReport($userId, $fromPeriod, $untilPeriod);
		$contentLayout = 'report_user';

		// create chart
		$chart = new FusionCharts('Pie2D', '700', '300');
		$chart->setSWFPath($chartOptions["swfpath"]);
		$chart->setChartParams(implode(';',
						array_merge(
								$chartOptions["params"],
								array('caption=Project stats', 'xAxisName=Types', 'yAxisName=Minutes'))));
		$colors = $this->getColorscheme('#FC7000', count($report['project']));

		$dataTable = array();
		foreach ($report['project'] as $project) {
			$perc = $project['total'] / $report['total'];
			$nextColor = array_shift($colors);

			// data for flash chart
			$param = $perc >= 0.05 ? 'name=' . $project['name'] : 'name=';
			$param .= ';hoverText=' . $project['name'] . ' ('
					. DateHelper::formatTimespan($project['total'], 'hr mi')
					. ')' . ';color=' . $nextColor;
			$chart->addChartData($project['total'], $param);

			// data for raphael piechart
			$tmp = new stdClass();
			$tmp->name = $perc >= 0.05 ? $project['name'] : "";
			$tmp->value = $project['total'];
			$tmp->label = $project['name'] . ' ('
					. DateHelper::formatTimespan($project['total'], 'hr mi')
					. '), ' . round(100 * $perc) . "%";
			$tmp->color = $nextColor;
			$dataTable[] = $tmp;
		}

		// set raphael chart data
		$projectsChart = JHTML::_(
						"teamtime.getPieChartTable", "projectsChart", $dataTable, $report['total']);
		$this->assignRef('projectsChart', $projectsChart);

		// set template vars
		$this->assignRef('proj_chart', $chart);

		return array($report, $contentLayout);
	}

	private function initPeriodReport(/* $projectId, $client, $userId, */ $fromPeriod, $untilPeriod,
			$chartOptions) {
		$report = $this->model->getPeriodReport($fromPeriod, $untilPeriod);
		$contentLayout = 'report_period';

		// create chart
		$chart = new FusionCharts('Pie2D', '700', '300');
		$chart->setSWFPath($chartOptions["swfpath"]);
		$chart->setChartParams(implode(';',
						array_merge(
								$chartOptions["params"],
								array('caption=Project stats', 'xAxisName=Types', 'yAxisName=Minutes'))));
		$colors = $this->getColorscheme('#6B007F', count($report['data']));

		$dataTable = array();
		foreach ($report['data'] as $project) {
			$perc = $project['duration'] / $report['total'];
			$nextColor = array_shift($colors);

			// data for flash chart
			$param = $perc >= 0.05 ? 'name=' . $project['name'] : 'name=';
			$param .= ';hoverText=' . $project['name'] . ' ('
					. DateHelper::formatTimespan($project['duration'], 'hr mi')
					. ')' . ';color=' . $nextColor;
			$chart->addChartData($project['duration'], $param);

			// data for raphael piechart
			$tmp = new stdClass();
			$tmp->name = $perc >= 0.05 ? $project['name'] : "";
			$tmp->value = $project['duration'];
			$tmp->label = $project['name'] . ' ('
					. DateHelper::formatTimespan($project['duration'], 'hr mi')
					. '), ' . round(100 * $perc) . "%";
			$tmp->color = $nextColor;
			$dataTable[] = $tmp;
		}

		// set raphael chart data
		$projectsChart = JHTML::_(
						"teamtime.getPieChartTable", "projectsChart", $dataTable, $report['total']);
		$this->assignRef('projectsChart', $projectsChart);

		// set template vars
		$this->assignRef('proj_chart', $chart);

		return array($report, $contentLayout);
	}

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		
		$helperBase = TeamTime::helper()->getBase();
		$user = & JFactory::getUser();
		$this->model = & $this->getModel();

		$client = JRequest::getVar("client");

		if (!$this->client_view) {
			// set toolbar items
			JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . JText::_('Reports'), TEAMLOG_ICON);
		}

		// get request vars
		$controller = JRequest::getWord('controller');
		$task = JRequest::getVar('task', '');
		$projectId = JRequest::getVar('project_id', 0);

		$userId = $mainframe->getUserStateFromRequest(
				$option . '.filter_user_id', 'user_id', 0, 'int');
		if ($this->client_view) {
			if (!$user->guest && !$userId) {
				$userId = $user->id;
			}
		}

		$fromPeriod = $mainframe->getUserStateFromRequest(
				$option . '.from_period', 'from_period', '', 'string');
		$untilPeriod = $mainframe->getUserStateFromRequest(
				$option . '.until_period', 'until_period', '', 'string');

		$typeId = JRequest::getVar('type_id');
		$taskId = JRequest::getVar('task_id');

		$filterFrom = JRequest::getVar('filter_from', '');
		if ($filterFrom != "") {
			$fromPeriod = $filterFrom;
		}
		$filterUntil = JRequest::getVar('filter_until', '');
		if ($filterUntil != "") {
			$untilPeriod = $filterUntil;
		}

		// projectId not set
		if ($client && !$projectId) {
			//$projectId = $client[0];
		}
		else {
			$client = array($client[0]);
		}

		//if(JRequest::getVar("callback") == "report-content-area")
		//	var_dump($project_id);

		$projectFilter = $helperBase->getProjectFilter($projectId, $client);
		list($lists['select_type'], $typeId) = $helperBase->getTypesSelect($typeId, $projectFilter,
				array("clientView" => $this->client_view));
		list($lists['select_task'], $taskId) = $helperBase->getTasksSelect($typeId, $taskId,
				$projectFilter, array("clientView" => $this->client_view));

		$this->model->type_id = $typeId;
		$this->model->task_id = $taskId;

		$period = JRequest::getVar('period', '');
		list($lists['select_date'], $dateSelected, $datePresets) = $this->getDateSelect(
				$fromPeriod, $untilPeriod, $filterFrom, $filterUntil);

		$lists['select_user'] = $helperBase->getUserSelect($userId,
				array("clientView" => $this->client_view));
		$lists['select_project'] = $helperBase->getProjectSelect($projectId);

		// load chart library
		$chartOptions = array();
		include(dirname(dirname(dirname(__FILE__)))
				. '/library/fusioncharts/class/FusionCharts_Gen.php');
		if ($this->client_view) {
			$chartOptions["swfpath"] = JURI::base()
					. 'administrator/components/com_teamtime/library/fusioncharts/charts/';
		}
		else {
			$chartOptions["swfpath"] = JURI::base()
					. 'components/com_teamtime/library/fusioncharts/charts/';
		}
		$chartOptions["params"] = array(
			'numberPrefix=', 'decimalPrecision=0',
			'formatNumberScale=1', 'showNames=1',
			'showValues=0', 'pieBorderAlpha=100',
			'shadowXShift=4', 'shadowYShift=4',
			'shadowAlpha=60', 'pieBorderColor=f1f1f1');

		if ($projectId || sizeof($client) > 1) {
			// create report_project
			list($report, $contentLayout) = $this->initProjectReport(
					$projectId, $client, $userId, $fromPeriod, $untilPeriod, $chartOptions);
		}
		else if ($userId) {
			// create report_user
			list($report, $contentLayout) = $this->initUserReport(
					/* $project_id, $client, */ $userId, $fromPeriod, $untilPeriod, $chartOptions);
		}
		else {
			// create report_period
			list($report, $contentLayout) = $this->initPeriodReport(
					/* $project_id, $client, $user_id, */ $fromPeriod, $untilPeriod, $chartOptions);
		}

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);
		$this->assignRef('project_id', $projectId);
		$this->assignRef('user_id', $userId);

		//$this->assignRef('from_period', $from_period);
		//$this->assignRef('until_period', $until_period);
		$this->assignRef('from_period', $report["from"]);
		$this->assignRef('until_period', $report["until"]);
		$this->assignRef('date_select', $dateSelected);
		$this->assignRef('period', $period);

		$this->assignRef('report', $report);
		$this->assignRef('contentLayout', $contentLayout);
		$this->assignRef('date_presets', $datePresets);

		$this->assignRef('type_id', $typeId);
		$this->assignRef('task_id', $taskId);

		$this->assignRef('client_view', $this->client_view);

		$report_format = JRequest::getVar('format');
		if ($report_format == "xls" || $report_format == "htm") {
			$this->displayFormat($report_format, $contentLayout);
		}
		else {
			parent::display($tpl);
		}
	}

	private function displayFormat($format, $contentLayout) {
		if ($format == "xls") {
			$format = "xml";
		}
		$fbasename = $contentLayout . "." . $format;
		$fname = JPATH_COMPONENT_ADMINISTRATOR . "/assets/xlstemplates/" . $fbasename;

		header("Content-type: application/{$format}");
		header("Content-Disposition: attachment; filename=" . $fbasename);

		$fn = "generate_" . $contentLayout;
		$this->$fn($fname);
	}

	private function generate_report_period($fname) {
		$tpl = new HTML_Template_IT("");
		$tpl->loadTemplatefile($fname, true, true);

		foreach ($this->report['data'] as $i => $project) {
			$block = $i % 2 == 0 ? "row1" : "row2";

			$tpl->setCurrentBlock($block);
			$tpl->setVariable("name", $project['name']);
			$tpl->setVariable("splan", round($project['splan'], 2));
			$tpl->setVariable("duration", DateHelper::formatTimespan($project['duration'], 'h:m'));
			$tpl->setVariable("splanned_cost", round($project["splanned_cost"], 2));
			$tpl->setVariable("sfact_cost", round($project["sfact_cost"], 2));
			$tpl->setVariable("scosts", (int) $project['scosts']);
			$tpl->setVariable("smoney", (float) $project['smoney']);
			$tpl->parseCurrentBlock($block);

			$tpl->parse("rows");
		}

		$tpl->setCurrentBlock();

		$tpl->setVariable("s_name", JText::_('Project'));
		$tpl->setVariable("s_hours_plan", JText::_('Planned Hours'));
		$tpl->setVariable("s_duration", JText::_('Duration'));
		$tpl->setVariable("s_planned_cost", JText::_('Planned cost'));
		$tpl->setVariable("s_actual_cost", JText::_('Actual cost'));
		$tpl->setVariable("s_overhead_expenses", JText::_('Overhead expenses'));

		$tpl->setVariable("total_plan", DateHelper::formatTimespan($this->report['total_plan'], 'hr mi'));
		$tpl->setVariable("total", DateHelper::formatTimespan($this->report['total'], 'hr mi'));
		$tpl->setVariable("total_planned_cost", round($this->report["total_planned_cost"], 2));
		$tpl->setVariable("total_fact_cost", round($this->report["total_fact_cost"], 2));
		$tpl->setVariable("total_costs", (int) $this->report['total_costs']);
		$tpl->setVariable("total_money", (float) round($this->report['total_money'], 2));

		$tpl->show();
	}

	private function makeURLtoLinks($log_text) {
		return (str_replace('<a href="/images', '<a href="https://teamtime.rosze.ru/images', $log_text));
	}

	private function generate_report_user($fname) {
		$format = JText::_('DATE_FORMAT_LC1');

		$mainframe =& JFactory::getApplication();
		$userId = $mainframe->getUserStateFromRequest(
				$option . '.filter_user_id', 'user_id', 0, 'int');

		$tpl = new HTML_Template_IT("");
		$tpl->loadTemplatefile($fname, true, true);

		$user = & JFactory::getUser($userId);
		

		$mvar = TeamTime::helper()->getFormals()->getUserVariables($userId, Array("user_full_name"));
		$tpl->setCurrentBlock("user_info");
		$tpl->setVariable("username", $mvar[1]["user_full_name"]);
		$tpl->setVariable("from_period", $this->from_period);
		$tpl->setVariable("until_period", $this->until_period);
		
		$tpl->parseCurrentBlock("user_info");

		foreach ($this->report['data'] as $i => $week_logs) {
			$tpl->setCurrentBlock("week");

			$tpl->setVariable("s_project", JText::_('Project'));
			$tpl->setVariable("s_task", JText::_('Task'));
			$tpl->setVariable("s_todo", JText::_('Todo'));
			$tpl->setVariable("s_log", JText::_('Log'));
			$tpl->setVariable("s_date", JText::_('Date'));
			$tpl->setVariable("s_duration", JText::_('Duration'));
			$tpl->setVariable("s_hourly_rate", JText::_('Hourly rate'));
			$tpl->setVariable("s_planned_cost", JText::_('Planned cost'));
			$tpl->setVariable("s_actual_cost", JText::_('Actual cost'));
			$tpl->setVariable("s_overhead_expenses", JText::_('Overhead expenses'));


			$first_row_for_salary = 1;
			$week_earnings = 0;
			foreach ($week_logs['logs'] as $j => $log) {
				$block = $j % 2 == 0 ? "row1" : "row2";

				if ($first_row_for_salary == 1) {
					$tpl->setVariable("salary", $this->report['salary']);
				} else {
					$tpl->setVariable("salary", "");
				}
				$first_row_for_salary++;

				$tpl->setCurrentBlock($block);
				$tpl->setVariable("project", $log['project_name']);
				$tpl->setVariable("task", $log['task_name']);
				$tpl->setVariable("todo", $log['todo_title']);
				//$tpl->setVariable("log", strip_tags($log['log']));
				$tpl->setVariable("log", $this->makeURLtoLinks($log['log']));
				$tpl->setVariable("date", JHTML::_('date', $log['date'], $format));
				$tpl->setVariable("duration", DateHelper::formatTimespan($log['duration'], 'h:m'));
				$tpl->setVariable("hourly_rate", $log['hourly_rate']);
				$tpl->setVariable("hours_plan_price", round($log['hours_plan_price'], 2));
				$tpl->setVariable("hours_fact_price", round($log['hours_fact_price'], 2));
				$tpl->setVariable("costs", (int) $log['costs']);
				$tpl->setVariable("money", (float) $log['money']);
				$week_earnings += round($log['hours_fact_price'], 2);

				$tpl->parseCurrentBlock($block);
				$tpl->parse("rows");
			}

			$tpl->setCurrentBlock("week");
			$tpl->setVariable("week", $week_logs['title']);
			$tpl->setVariable("total", round($week_logs['total'] / 60, 2));
			
			$tpl->setVariable("week_earnings", $week_earnings);
			$tpl->setVariable("totalsum", $week_logs['total_money'] != 0 ? $week_logs['total_money'] : 0);
			$tpl->parseCurrentBlock("week");
		}

		$tpl->setCurrentBlock();

		$tpl->setVariable("total_amount", round($this->report['total'] / 60, 2));

		$tpl->setVariable("total_plan_price", number_format($this->report['total_plan_price'],2));
		$tpl->setVariable("total_fact_price", number_format($this->report['total_fact_price'],2));
		$tpl->setVariable("total_costs", number_format($this->report['total_costs'], 2));
		$tpl->setVariable("total_salary", number_format($this->report['salary'], 2));
		$tpl->setVariable("total_money", number_format($this->report['total_money'], 2));
		$sum = ceil($this->report['total_fact_price'] + $this->report['salary'] + $this->report['total_money']);
		$tpl->setVariable("total_sum", number_format($sum, 2));

		$tpl->show();
	}

	private function generate_report_project($fname) {
		$format = JText::_('DATE_FORMAT_LC1');

		$tpl = new HTML_Template_IT("");
		$tpl->loadTemplatefile($fname, true, true);

		foreach ($this->report['type'] as $type) {
			$show_type = true;
			foreach ($type['task'] as $task) {
				$show_task = true;
				foreach ($task['log'] as $i => $log_id) {
					$log = $this->report['data'][$log_id];

					$block = $i % 2 == 0 ? "row1" : "row2";

					$tpl->setCurrentBlock($block);

					$tpl->setVariable("task", $show_task ? $task['name'] : null);
					$show_task = false;

					$tpl->setVariable("type", $show_type ? $type['name'] : null);
					$show_type = false;

					$tpl->setVariable("todo", $log['todo_title']);
					$tpl->setVariable("log", strip_tags($log['log']));
					$tpl->setVariable("user", $log['username']);
					$tpl->setVariable("date", JHTML::_('date', $log['date'], $format));
					$tpl->setVariable("duration", DateHelper::formatTimespan($log['duration'], 'h:m'));

					$tpl->setVariable("hours_fact", round((float) $log["hours_fact"], 2));
					$tpl->setVariable("hours_plan", round((float) $log["hours_plan"], 2));
					$tpl->setVariable("hourly_rate", $log["hourly_rate"]);
					$tpl->setVariable("hours_plan_price", round($log['hours_plan_price'], 2));
					$tpl->setVariable("hours_fact_price", round($log['hours_fact_price'], 2));
					$tpl->setVariable("costs", (float) $log['costs']);
					$tpl->setVariable("money", (float) $log['money']);
					$tpl->parseCurrentBlock($block);

					$tpl->parse("rows");
				}
			}
		}

		$tpl->setCurrentBlock();

		$tpl->setVariable("s_type", JText::_('Type'));
		$tpl->setVariable("s_task", JText::_('Task'));
		$tpl->setVariable("s_todo", JText::_('Todo'));
		$tpl->setVariable("s_log", JText::_('Log'));
		$tpl->setVariable("s_user", JText::_('User'));
		$tpl->setVariable("s_date", JText::_('Date'));
		$tpl->setVariable("s_hours_plan", JText::_('PLANNED_ACTUAL_HOURS_OF_TODO'));
		$tpl->setVariable("s_duration", JText::_('Duration'));
		$tpl->setVariable("s_hourly_rate", JText::_('Hourly rate'));
		$tpl->setVariable("s_planned_cost", JText::_('Planned cost'));
		$tpl->setVariable("s_actual_cost", JText::_('Actual cost'));
		$tpl->setVariable("s_overhead_expenses", JText::_('Overhead expenses'));

		$tpl->setVariable("total", round($this->report['total'] / 60, 2));
		$tpl->setVariable("total_plan", round($this->report['total_plan'] / 60, 2));
		$tpl->setVariable("total_plan_price", round($this->report['total_plan_price'], 2));
		$tpl->setVariable("total_fact_price", round($this->report['total_fact_price'], 2));
		$tpl->setVariable("total_costs", (int) $this->report['total_costs']);
		$tpl->setVariable("total_money", (float) round($this->report['total_money'], 2));

		$tpl->show();
	}

}