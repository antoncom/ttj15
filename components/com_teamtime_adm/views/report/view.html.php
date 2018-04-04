<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/*
  Class: ReportViewReport
  The View Class for Report
 */

class ReportViewReport extends JView {

  var $format = "";
  var $client_view = false;

  function check_task_item($query, $value) {
    $db = & JFactory::getDBO();
    $db->setQuery($query);
    $result = $db->loadObjectList();
    foreach ($result as $row) {
      if ($row->value == $value)
        return true;
    }
    return false;
  }

  function display($tpl = null) {
    global $mainframe, $option;

    $db = & JFactory::getDBO();
    $user = & JFactory::getUser();
    $config = & JFactory::getConfig();
    $model = & $this->getModel();

    $client = JRequest::getVar("client");

    if (!$this->client_view) {
      // set toolbar items
      JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . JText::_('Reports'), TEAMLOG_ICON);
    }

    // get request vars
    $controller = JRequest::getWord('controller');
    $task = JRequest::getVar('task', '');
    $project_id = JRequest::getVar('project_id', 0);

    $user_id = $mainframe->getUserStateFromRequest(
        $option . '.filter_user_id', 'user_id', 0, 'int');
    $from_period = $mainframe->getUserStateFromRequest(
        $option . '.from_period', 'from_period', '', 'string');
    $until_period = $mainframe->getUserStateFromRequest(
        $option . '.until_period', 'until_period', '', 'string');

    $type_id = JRequest::getVar('type_id');
    $task_id = JRequest::getVar('task_id');

    if ($this->client_view) {
      $report_user_data = &YFactory::getUser();
      if (!$report_user_data->guest) {
        $user_id = $report_user_data->id;
      }
    }

    $filter_from = JRequest::getVar('filter_from', '');
    if ($filter_from != "") {
      $from_period = $filter_from;
    }
    $filter_until = JRequest::getVar('filter_until', '');
    if ($filter_until != "") {
      $until_period = $filter_until;
    }

    // project_id not set
    if ($client && !$project_id) {
      //$project_id = $client[0];
    }
    else {
      $client = array($client[0]);
    }

    //if(JRequest::getVar("callback") == "report-content-area")
    //	var_dump($project_id);

    if (sizeof($client) > 1) {
      $project_filter = " and a.project_id in (" . implode(",", $client) . ") ";
    }
    else {
      $project_filter = $project_id > 0 ?
          " and a.project_id = {$project_id} " : "";
    }

    $query = 'SELECT b.id AS value, b.name AS text
			FROM #__teamlog_log AS a
			LEFT JOIN #__teamlog_type AS b ON a.type_id = b.id where b.id '
        . $project_filter
        . ' GROUP BY b.name ORDER BY b.name';

    //NOTE reset filter if type not found
    if (!$this->client_view) {
      if (!$this->check_task_item($query, $type_id)) {
        $type_id = "";
      }
    }

    $options = JHTML::_('select.option', '', JText::_('All types'));

    $lists['select_type'] = JHTML::_(
            'teamlog.querylist', $query, $options, 'type_id', 'class="inputbox auto-submit"', 'value', 'text', $type_id);

    $type_filter = $type_id != "" ?
        " and b.type_id = {$type_id}" : "";
    $query = 'SELECT b.name AS value, b.name AS text
			FROM #__teamlog_log AS a
			LEFT JOIN #__teamlog_task AS b ON a.task_id = b.id  where b.id '
        . $project_filter
        . $type_filter
        . ' GROUP BY b.name ORDER BY b.name';

    //NOTE reset filter if type not found
    if (!$this->client_view) {
      if (!$this->check_task_item($query, $task_id)) {
        $task_id = "";
      }
    }

    $options = JHTML::_('select.option', '', JText::_('All tasks'));
    $lists['select_task'] = JHTML::_(
            'teamlog.querylist', $query, $options, 'task_id', 'class="inputbox auto-submit"', 'value', 'text', $task_id);

    $model->type_id = $type_id;
    $model->task_id = $task_id;

    // set date presets
    $date = JFactory::getDate();
    $date = $date->toUnix();
    $date = mktime(0, 0, 0, date('n', $date), date('j', $date), date('Y', $date));
    $monday = (date('w', $date) == 1) ? $date : strtotime('last Monday', $date);

    $date_presets['last_month'] = array(
        'name' => JText::_('Last Month'),
        'from' => date('Y-m-d', mktime(0, 0, 0, date('n', $date) - 1, 1, date('Y', $date))),
        'until' => date('Y-m-d', mktime(0, 0, 0, date('n', $date), 0, date('Y', $date))));

    /* $date_presets['last30'] = array(
      'name'  => JText::_('Last 30 days'),
      'from'  => date('Y-m-d', strtotime('-29 day', $date)),
      'until' => date('Y-m-d', $date)); */
    $date_presets['last30'] = array(
        'name' => JText::_('Last 30 days'),
        'from' => "",
        'until' => "");

    $date_presets['last_week'] = array(
        'name' => JText::_('Last Week'),
        'from' => date('Y-m-d', strtotime('-7 day', $monday)),
        'until' => date('Y-m-d', strtotime('-1 day', $monday)));

    $date_presets['today'] = array(
        'name' => JText::_('Today'),
        'from' => date('Y-m-d', $date),
        'until' => date('Y-m-d', $date));
    $date_presets['week'] = array(
        'name' => JText::_('This Week'),
        'from' => date('Y-m-d', $monday),
        'until' => date('Y-m-d', strtotime('+6 day', $monday)));
    $date_presets['month'] = array(
        'name' => JText::_('This Month'),
        'from' => date('Y-m-d', mktime(0, 0, 0, date('n', $date), 1, date('Y', $date))),
        'until' => date('Y-m-d', mktime(0, 0, 0, date('n', $date) + 1, 0, date('Y', $date))));
    $date_presets['year'] = array(
        'name' => JText::_('This Year'),
        'from' => date('Y-m-d', mktime(0, 0, 0, 1, 1, date('Y', $date))),
        'until' => date('Y-m-d', mktime(0, 0, 0, 12, 31, date('Y', $date))));

    $date_presets['next_week'] = array(
        'name' => JText::_('Next Week'),
        'from' => date('Y-m-d', strtotime('+7 day', $monday)),
        'until' => date('Y-m-d', strtotime('+13 day', $monday)));
    $date_presets['next_month'] = array(
        'name' => JText::_('Next Month'),
        'from' => date('Y-m-d', mktime(0, 0, 0, date('n', $date) + 1, 1, date('Y', $date))),
        'until' => date('Y-m-d', mktime(0, 0, 0, date('n', $date) + 2, 0, date('Y', $date))));

    // set period
    $tzoffset = $config->getValue('config.offset');
    $from = JFactory::getDate($from_period, $tzoffset);
    $until = JFactory::getDate($until_period, $tzoffset);

    $period = JRequest::getVar('period', '');

    // check period - set to defaults if no value is set or dates cannot be parsed
    if ($from->_date === false || $until->_date === false) {
      if ($from_period != '?' && $until_period != '?') {
        JError::raiseNotice(500, JText::_('Please enter a valid date format (YYYY-MM-DD)'));
      }
      $from_period = $date_presets['last30']['from'];
      $until_period = $date_presets['last30']['until'];
      $from = JFactory::getDate($from_period, $tzoffset);
      $until = JFactory::getDate($until_period, $tzoffset);
    }
    else {
      if ($from->toUnix() > $until->toUnix()) {
        list($from_period, $until_period) = array($until_period, $from_period);
        list($from, $until) = array($until, $from);
      }
    }

    // simpledate select
    $select = '';
    $date_select = array();
    $options = array(JHTML::_(
            'select.option', '', '- ' . JText::_('Select Period') . ' -', 'text', 'value'));
    foreach ($date_presets as $name => $value) {
      $options[] = JHTML::_('select.option', $name, JText::_($value['name']), 'text', 'value');
      if ($value['from'] == $from_period && $value['until'] == $until_period) {
        $select = $name;
        $date_select = $value;
      }
    }

    if ($filter_until != "" || $filter_from != "") {
      $select = "";
    }

    $lists['select_date'] = JHTML::_(
            'select.genericlist', $options, 'period', 'class="inputbox" size="1"', 'text', 'value', $select);

    // user select
    $query = 'SELECT b.id AS value, b.name AS text'
        . ' FROM #__teamlog_log AS a '
        . ' LEFT JOIN #__users AS b ON a.user_id = b.id'
        . ($this->client_view ? '' : ' where b.block = 0')
        . ' GROUP BY b.id'
        . ' ORDER BY b.name';
    $options = JHTML::_('select.option', '', '- ' . JText::_('Select User') . ' -');
    $lists['select_user'] = JHTML::_(
            'teamlog.querylist', $query, $options, 'user_id', 'class="inputbox auto-submit"', 'value', 'text', $user_id);

    // project select
    $query = 'SELECT b.id AS value, b.name AS text'
        . ' FROM #__teamlog_log AS a '
        . ' LEFT JOIN #__teamlog_project AS b ON a.project_id = b.id'
        . ' GROUP BY b.id'
        . ' ORDER BY b.name';
    $options = JHTML::_('select.option', '', '- ' . JText::_('All Projects') . ' -');
    $lists['select_project'] = JHTML::_(
            'teamlog.querylist', $query, $options, 'project_id', 'class="inputbox auto-submit" size="1"', 'value', 'text', $project_id);

    // load chart library
    include(dirname(dirname(dirname(__FILE__)))
        . '/libraries/fusioncharts/class/FusionCharts_Gen.php');

    if ($this->client_view) {
      $swfpath = JURI::base()
          . 'administrator/components/com_teamtime/libraries/fusioncharts/charts/';
    }
    else {
      $swfpath = JURI::base()
          . 'components/com_teamtime/libraries/fusioncharts/charts/';
    }

    $params = array(
        'numberPrefix=', 'decimalPrecision=0',
        'formatNumberScale=1', 'showNames=1',
        'showValues=0', 'pieBorderAlpha=100',
        'shadowXShift=4', 'shadowYShift=4',
        'shadowAlpha=60', 'pieBorderColor=f1f1f1');

    // create report_project
    if ($project_id || sizeof($client) > 1) {
      if (sizeof($client) > 1) {
        $report = $model->getProjectReport($client, $user_id, $from_period, $until_period);
      }
      else {
        $report = $model->getProjectReport($project_id, $user_id, $from_period, $until_period);
      }
      $contentLayout = 'report_project';

      // create chart
      $chart_t = new FusionCharts('Pie2D', '400', '300');
      $chart_t->setSWFPath($swfpath);
      $chart_t->setChartParams(implode(';', array_merge(
                  $params, array('caption=Type stats', 'xAxisName=Types', 'yAxisName=Minutes'))));
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
              "teamlog.getPieChartTable", "typeChart", $dataTable, $report['total']);
      $this->assignRef('typeChart', $typeChart);

      $chart_u = new FusionCharts('Pie2D', '400', '300');
      $chart_u->setSWFPath($swfpath);
      $chart_u->setChartParams(implode(';', array_merge(
                  $params, array('caption=User stats', 'xAxisName=Users', 'yAxisName=Minutes'))));
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
              "teamlog.getPieChartTable", "userChart", $dataTable, $report['total']);
      $this->assignRef('userChart', $userChart);

      // set template vars
      $this->assignRef('type_chart', $chart_t);
      $this->assignRef('user_chart', $chart_u);
    }

    // create report_user
    elseif ($user_id) {
      $report = $model->getUserReport($user_id, $from_period, $until_period);
      $contentLayout = 'report_user';

      // create chart
      $chart = new FusionCharts('Pie2D', '700', '300');
      $chart->setSWFPath($swfpath);
      $chart->setChartParams(implode(';', array_merge(
                  $params, array('caption=Project stats', 'xAxisName=Types', 'yAxisName=Minutes'))));
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
              "teamlog.getPieChartTable", "projectsChart", $dataTable, $report['total']);
      $this->assignRef('projectsChart', $projectsChart);

      // set template vars
      $this->assignRef('proj_chart', $chart);
    }

    // create report_period
    else {
      $report = $model->getPeriodReport($from_period, $until_period);
      $contentLayout = 'report_period';

      // create chart
      $chart = new FusionCharts('Pie2D', '700', '300');
      $chart->setSWFPath($swfpath);
      $chart->setChartParams(implode(';', array_merge(
                  $params, array('caption=Project stats', 'xAxisName=Types', 'yAxisName=Minutes'))));
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
              "teamlog.getPieChartTable", "projectsChart", $dataTable, $report['total']);
      $this->assignRef('projectsChart', $projectsChart);

      // set template vars
      $this->assignRef('proj_chart', $chart);
    }

    // set template vars
    $this->assignRef('user', $user);
    $this->assignRef('option', $option);
    $this->assignRef('controller', $controller);
    $this->assignRef('lists', $lists);
    $this->assignRef('project_id', $project_id);
    $this->assignRef('user_id', $user_id);

    //$this->assignRef('from_period', $from_period);
    //$this->assignRef('until_period', $until_period);
    $this->assignRef('from_period', $report["from"]);
    $this->assignRef('until_period', $report["until"]);

    $this->assignRef('report', $report);
    $this->assignRef('contentLayout', $contentLayout);
    $this->assignRef('date_presets', $date_presets);

    $this->assignRef('type_id', $type_id);
    $this->assignRef('task_id', $task_id);

    $this->assignRef('client_view', $this->client_view);

    $this->assignRef('date_select', $date_select);
    $this->assignRef('period', $period);

    //$this->assignRef('total_amount', $model->get_total_amount($user_id));

    $report_format = JRequest::getVar('format');
    if ($report_format == "xls" || $report_format == "htm") {
      $this->display_format($report_format, $contentLayout);
    }
    else {
      parent::display($tpl);
    }
  }

  function getColorscheme($color, $count) {
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

  function display_format($format, $contentLayout) {
    set_include_path(
        get_include_path() . PATH_SEPARATOR .
        JPATH_ROOT . "/administrator/components/com_teamtime/assets/PEAR");
    require_once("HTML/Template/IT.php");

    if ($format == "xls") {
      $format = "xml";
    }
    $fname = JPATH_COMPONENT_ADMINISTRATOR
        . "/assets/xlstemplates/" . $contentLayout . "." . $format;

    header("Content-type: application/{$format}");
    header("Content-Disposition: attachment; filename={$contentLayout}.{$format}");
    $fn = "generate_{$contentLayout}";
    $this->$fn($fname);
  }

  function generate_report_period($fname) {
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

  function generate_report_user($fname) {
    $format = JText::_('DATE_FORMAT_LC1');

    $tpl = new HTML_Template_IT("");
    $tpl->loadTemplatefile($fname, true, true);

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

      foreach ($week_logs['logs'] as $j => $log) {
        $block = $j % 2 == 0 ? "row1" : "row2";

        $tpl->setCurrentBlock($block);
        $tpl->setVariable("project", $log['project_name']);
        $tpl->setVariable("task", $log['task_name']);
        $tpl->setVariable("todo", $log['todo_title']);
        $tpl->setVariable("log", strip_tags($log['log']));
        $tpl->setVariable("date", JHTML::_('date', $log['date'], $format));
        $tpl->setVariable("duration", DateHelper::formatTimespan($log['duration'], 'h:m'));
        $tpl->setVariable("hourly_rate", $log['hourly_rate']);
        $tpl->setVariable("hours_plan_price", round($log['hours_plan_price'], 2));
        $tpl->setVariable("hours_fact_price", round($log['hours_fact_price'], 2));
        $tpl->setVariable("costs", (int) $log['costs']);
        $tpl->setVariable("money", (float) $log['money']);
        $tpl->parseCurrentBlock($block);

        $tpl->parse("rows");
      }

      $tpl->setCurrentBlock("week");
      $tpl->setVariable("week", $week_logs['title']);
      $tpl->setVariable("total", round($week_logs['total'] / 60, 2));
      $tpl->setVariable("totalsum", $week_logs['total_money'] != 0 ? $week_logs['total_money'] : "");
      $tpl->parseCurrentBlock("week");
    }

    $tpl->setCurrentBlock();

    $tpl->setVariable("total_amount", round($this->report['total'] / 60, 2));

    $tpl->setVariable("total_plan_price", round($this->report['total_plan_price'], 2));
    $tpl->setVariable("total_fact_price", round($this->report['total_fact_price'], 2));
    $tpl->setVariable("total_costs", (int) $this->report['total_costs']);
    $tpl->setVariable("total_money", (float) round($this->report['total_money'], 2));

    $tpl->show();
  }

  function generate_report_project($fname) {
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