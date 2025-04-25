<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/*
  Class: ReportModelReport
  The Model Class for Report
 */

class ReportModelReport extends JModel {

  var $type_id;
  var $task_id;

  function getTaskFilter($table_task) {
    $db = & JFactory::getDBO();

    $type_id = $this->type_id;
    $task_id = $this->task_id;

    $res = array();
    if ($type_id != "") {
      $res[] = " a.type_id = {$type_id} ";
    }

    if ($task_id != "") {
      $task_id = $db->Quote($task_id);
      $res[] = " {$table_task}.name = {$task_id} ";
    }

    return sizeof($res) > 0 ?
        (" and (" . implode(" and ", $res) . ") ") : "";
  }

  function getPeriod($query, $from, $until) {
    $db = & JFactory::getDBO();
    $period = JRequest::getVar('period', '');

    if ($period == "last30") {
      //get last log
      $db->setQuery($query . " order by a.date desc limit 1");
      $row_last = $db->loadObject();

      if ($row_last) {
        $until = date("Y-m-d", strtotime($row_last->date));
        $from = date("Y-m-d", strtotime($row_last->date . " -30 day"));
      }
    }

    return array($from, $until);
  }

  /**
   * Method to get project report data
   *
   * @access public
   * @return array
   */
  function getProjectReport($project_id, $user_id, $from, $until) {
    if (is_array($project_id)) {
      $project_id = implode(",", $project_id);
    }

    $task_filter = $this->getTaskFilter("b");

    //init vars
    $db = & JFactory::getDBO();
    $report['data'] = array();
    $report['type'] = array();
    $report['user'] = array();
    $report['total'] = 0;
    $report['total_plan'] = 0;

    $query = "SELECT a.id AS id, a.description AS log, a.duration AS duration, a.date AS date, a.project_id,
				b.id AS task_id, b.name AS task_name, c.id AS type_id, c.name AS type_name, d.id AS user_id,
				d.name AS username, a.money, e.title as todo_title, e.hours_fact, e.hours_plan, e.hourly_rate,
        e.description as todo_description, e.costs, e.created as todo_date,
				a.todo_id, f.hour_price, b.rate as task_rate,
        pp.name as project_name, pp.rate as project_price, pp.dynamic_rate as project_dynamic_rate
				FROM #__teamlog_log AS a
				LEFT JOIN #__teamlog_task AS b ON a.task_id = b.id
				LEFT JOIN #__teamlog_type AS c ON a.type_id = c.id
				LEFT JOIN #__users AS d ON a.user_id = d.id
				LEFT JOIN #__teamlog_todo AS e ON a.todo_id = e.id
				LEFT JOIN #__teamlog_userdata AS f ON a.user_id = f.user_id
				LEFT JOIN #__teamlog_project AS pp ON a.project_id = pp.id
				WHERE a.project_id in ($project_id)"
        . ($user_id ? " AND a.user_id = $user_id" : null);

    list($from, $until) = $this->getPeriod($query . $task_filter, $from, $until);

    //. " ORDER BY b.type_id, a.task_id, a.todo_id, a.date");
    $query .= "
			AND a.date >= '$from 00:00:00' AND a.date <= '$until 23:59:59'" . $task_filter . "
			ORDER BY e.created desc, b.type_id, a.task_id, a.todo_id, a.date";

    // create report
    $db->setQuery($query);

    //error_log($db->replacePrefix($query));

    $result = $db->loadObjectList();
    $todo_id = 0;

    $todo_ids = array();
    $total_plan_price = 0;
    $total_fact_price = 0;
    $total_statement_price = 0;
    $total_costs = 0;

    $price_rates = array();

    foreach ($result as $row) {
      if (!isset($report['type'][$row->type_id])) {
        $report['type'][$row->type_id] = array(
            'id' => $row->user_id,
            'name' => $row->type_name,
            'total' => $row->duration,
            'total_money' => $row->money);
      }
      else {
        $report['type'][$row->type_id]['total'] += $row->duration;
        $report['type'][$row->type_id]['total_money'] += $row->money;
      }

      if (!isset($report['type'][$row->type_id]['task'][$row->task_id])) {
        $report['type'][$row->type_id]['task'][$row->task_id] = array(
            'id' => $row->task_id,
            'name' => $row->task_name,
            'log' => array($row->id));
      }
      else {
        $report['type'][$row->type_id]['task'][$row->task_id]['log'][] = $row->id;
      }

      if (!isset($report['user'][$row->user_id])) {
        $report['user'][$row->user_id] = array(
            'id' => $row->user_id,
            'name' => $row->username,
            'total' => $row->duration,
            'total_money' => $row->money
        );
      }
      else {
        $report['user'][$row->user_id]['total'] += $row->duration;
        $report['user'][$row->user_id]['total_money'] += $row->money;
      }

      // get real hours_fact
      if ($row->todo_id) {
        $db->setQuery("select sum(duration)/60 as hours_fact
					from #__teamlog_log where todo_id = {$row->todo_id}
						AND date >= '$from 00:00:00' AND date <= '$until 23:59:59'");
        $hours_fact_res = $db->loadObject();
        $row->hours_fact = $hours_fact_res->hours_fact;
      }

      $todo_price = $row->todo_id ? $row->hourly_rate : $row->hour_price;
      $todo_plan_price = $todo_price * $row->hours_plan;
      $todo_fact_price = $todo_price * $row->duration / 60;

      if (!isset($price_rates[$row->task_id . "_" . $row->user_id])) {
        // calculate dynamic rate
        if ($row->project_dynamic_rate) {
          // check price by dotu
          $tmp_rate = TeamTime::_("Dotu_getTargetPrice", null,
              array(
              "task_id" => $row->task_id
              ), true);
          if ($tmp_rate === null) {
            $tmp_rate = $row->task_rate;
          }
          $tmp_rate = $row->project_price * $tmp_rate;
        }
        // get static rate
        else {
          $tmp_rate = $row->project_price;
        }
        // save to cache
        $price_rates[$row->task_id . "_" . $row->user_id] = $tmp_rate;
      }
      else {
        // get from cache
        $tmp_rate = $price_rates[$row->task_id . "_" . $row->user_id];
      }
      $hours_statement_price = $tmp_rate * $row->hours_plan;

      $total_fact_price += $todo_fact_price;

      if ($row->todo_id && !isset($todo_ids[$row->todo_id])) {
        $todo_ids[$row->todo_id] = 1;
        $total_plan_price += $todo_plan_price;
        $total_costs += $row->costs;
      }

      //if (!$report['data'][$row->task_id][$row->todo_id]) {
      //	$report['data'][$row->task_id][$row->todo_id] = array();
      //}

      if (!$report['data'][$row->todo_id]) {
        $report['data'][$row->todo_id] = array();
      }

      //$report['data'][$row->task_id][$row->todo_id][$row->id] = array(
      $report['data'][$row->todo_id][$row->id] = array(
          'id' => $row->id,
          'log' => $row->log,
          'username' => $row->username,
          'date' => $row->date,
          'duration' => $row->duration,
          'money' => $row->money,
          'todo_title' => $row->todo_title,
          'todo_description' => TeamTime::processRelativeLinks(
							$row->todo_description, JURI::root()),
          "hours_fact" => $row->hours_fact,
          "hours_plan" => $row->hours_plan,
          "todo_id" => $row->todo_id,
          'hourly_rate' => $todo_price,
          'hours_plan_price' => $todo_plan_price,
          'hours_fact_price' => $todo_fact_price,
          'hours_statement_price' => $hours_statement_price,
          'costs' => $row->costs,
          'type_id' => $row->type_id,
          'task_id' => $row->task_id,
          'task_name' => $row->task_name,
          'type_name' => $row->type_name,
          'project_name' => $row->project_name,
          'project_id' => $row->project_id,
          "todo_date" => $row->todo_date
      );

      $report['total'] += $row->duration;
      $report['total_money'] += $row->money;

      // change todo
      if ($todo_id != $row->todo_id) {
        $todo_id = $row->todo_id;
        $report['total_plan'] += $row->hours_plan;

        $total_statement_price += $hours_statement_price;
      }
    }

    $report['total_plan'] = $report['total_plan'] * 60;

    $report['total_plan_price'] = $total_plan_price;
    $report['total_fact_price'] = $total_fact_price;
    $report['total_statement_price'] = $total_statement_price;
    $report['total_costs'] = $total_costs;

    // sort type & user data
    $compare = create_function('$a,$b',
        'return $a["total"] == $b["total"] ? 0 : $a["total"] > $b["total"] ? -1 : 1;');
    usort($report['type'], $compare);
    usort($report['user'], $compare);

    $report['from'] = $from;
    $report['until'] = $until;

    return $report;
  }

  function getTotalAmount($user_id) {
    $db = & JFactory::getDBO();
    $query = "SELECT sum(unix_timestamp(ended) - unix_timestamp(created)) / 60 AS total_amount
			FROM #__teamlog_log
			WHERE user_id = {$user_id} and ended > 0";
    $db->setQuery($query);
    $result = $db->loadObjectList();

    return $result[0]->total_amount;
  }

  /**
   * Method to get user report data
   *
   * @access public
   * @return array
   */
  function getUserReport($user_id, $from, $until) {
    $user = & YFactory::getUser();
    $current_user = $user->id;
    $user = & JFactory::getUser();
    if ($user->usertype == "Super Administrator" || $user->usertype == "Administrator")
      $filter_projects = "";
    else
      $filter_projects = " and (" .
          // projects - enabled for all
          " d.id not in (select project_id from #__teamlog_project_user group by project_id) or " .
          // projects - enabled for current user
          "	d.id in (SELECT project_id FROM #__teamlog_project_user
					WHERE user_id = {$current_user} group by project_id) ) ";

    $task_filter = $this->getTaskFilter("b");

    // init vars
    $db = & JFactory::getDBO();
    $config = & JFactory::getConfig();
    $tzoffset = $config->getValue('config.offset');
    $report['data'] = array();
    $report['project'] = array();
    $report['total'] = 0;

    // create report
    $query = "SELECT a.id AS id, a.description AS log, a.duration AS duration,
			a.date AS date, b.id AS task_id, b.name AS task_name, c.id AS type_id, c.name AS type_name,
			d.id AS project_id, d.name AS project_name, a.money,
			e.title as todo_title, e.hourly_rate, e.hours_plan, f.hour_price, a.todo_id, e.costs,
      e.description as todo_description "
        . " FROM #__teamlog_log AS a "
        . " LEFT JOIN #__teamlog_task AS b ON a.task_id = b.id"
        . " LEFT JOIN #__teamlog_type AS c ON b.type_id = c.id"
        . " LEFT JOIN #__teamlog_project AS d ON a.project_id = d.id"
        . " LEFT JOIN #__teamlog_todo AS e ON a.todo_id = e.id"
        . " LEFT JOIN #__teamlog_userdata AS f ON a.user_id = f.user_id "
        . $filter_projects
        . " WHERE a.user_id = $user_id";

    list($from, $until) = $this->getPeriod($query . $task_filter, $from, $until);

    $query .= " AND a.date >= '$from 00:00:00' AND a.date <= '$until 23:59:59'"
        . $task_filter
        . " ORDER BY a.date, a.project_id";
    $db->setQuery($query);
    $result = $db->loadObjectList();

    //error_log("Report todos");
    //error_log($db->replacePrefix($query));

    $todo_ids = array();
    $total_plan_price = 0;
    $total_fact_price = 0;
    $total_costs = 0;

    foreach ($result as $row) {

      $date = JFactory::getDate($row->date, $tzoffset);
      $week = date('Y-W', $date->toUnix());

      if (!isset($report['project'][$row->project_id])) {
        $report['project'][$row->project_id] = array(
            'id' => $row->project_id,
            'name' => $row->project_name,
            'total' => $row->duration,
            'total_money' => $row->money
        );
      }
      else {
        $report['project'][$row->project_id]['total'] += $row->duration;
        $report['project'][$row->project_id]['total_money'] += $row->money;
      }

      if (!isset($report['data'][$week]['total'])) {
        $report['data'][$week]['title'] = JText::_(date('F', $date->toUnix())) . ' ' . date('Y',
                $date->toUnix()) . ' - ' . JText::_('Week') . ' ' . date('W', $date->toUnix());
        $report['data'][$week]['total'] = $row->duration;
        $report['data'][$week]['total_money'] = $row->money;
      }
      else {
        $report['data'][$week]['total'] += $row->duration;
        $report['data'][$week]['total_money'] += $row->money;
      }

      $todo_price = $row->todo_id ? $row->hourly_rate : $row->hour_price;
      $todo_plan_price = $todo_price * $row->hours_plan;
      $todo_fact_price = $todo_price * $row->duration / 60;

      $total_fact_price += $todo_fact_price;

      if ($row->todo_id && !isset($todo_ids[$row->todo_id])) {
        $todo_ids[$row->todo_id] = 1;
        $total_plan_price += $todo_plan_price;
        $total_costs += $row->costs;
      }

      $report['data'][$week]['logs'][] = array(
          'id' => $row->id,
          'project_name' => $row->project_name,
          'project_id' => $row->project_id,
          'type_name' => $row->type_name,
          'task_name' => $row->task_name,
          'log' => $row->log,
          'date' => $row->date,
          'duration' => $row->duration,
          'money' => $row->money,
          "todo_id" => $row->todo_id,
          'todo_title' => $row->todo_title,
          'todo_description' => TeamTime::processRelativeLinks(
							$row->todo_description, JURI::root()),          
          'hourly_rate' => $todo_price,
          'hours_plan_price' => $todo_plan_price,
          'hours_fact_price' => $todo_fact_price,
          'costs' => $row->costs,
      );

      $report['total'] += $row->duration;
      $report['total_money'] += $row->money;
    }

    $report['total_plan_price'] = $total_plan_price;
    $report['total_fact_price'] = $total_fact_price;
    $report['total_costs'] = $total_costs;

    // sort project data
    $compare = create_function('$a,$b',
        'return $a["total"] == $b["total"] ? 0 : $a["total"] > $b["total"] ? -1 : 1;');
    usort($report['project'], $compare);

    $report['from'] = $from;
    $report['until'] = $until;

    return $report;
  }

  /**
   * Method to get period report data
   *
   * @access public
   * @return array
   */
  function getPeriodReport($from, $until) {
    $task_filter = $this->getTaskFilter("c");

    // init vars
    $db = & JFactory::getDBO();
    $report['data'] = array();
    $report['total'] = 0;

    // create report
    $query = "SELECT b.id AS id, b.name AS name, a.duration AS duration, a.money as smoney, a.date as date"
        . " FROM #__teamlog_log AS a "
        . " LEFT JOIN #__teamlog_project AS b ON a.project_id = b.id"
        . " LEFT JOIN #__teamlog_task AS c ON a.task_id = c.id"
        . " WHERE 1 ";
    list($from, $until) = $this->getPeriod($query . $task_filte, $from, $until);

    $query = "SELECT b.id AS id, b.name AS name, SUM(a.duration) AS duration, SUM(a.money) as smoney"
        . " FROM #__teamlog_log AS a "
        . " LEFT JOIN #__teamlog_project AS b ON a.project_id = b.id"
        . " LEFT JOIN #__teamlog_task AS c ON a.task_id = c.id"
        . " WHERE a.date >= '$from 00:00:00' AND a.date <= '$until 23:59:59'"
        . $task_filter
        . " GROUP BY id"
        . " ORDER BY duration DESC";

    $db->setQuery($query);
    $result = $db->loadObjectList();

    $report['total'] = 0;
    $report['total_money'] = 0;
    $report['total_plan'] = 0;
    $report['total_costs'] = 0;
    $report['total_planned_cost'] = 0;
    $report['total_fact_cost'] = 0;

    foreach ($result as $row) {
      $splan = $this->getSumPlan($row->id,
          " and a.created >= '$from 00:00:00' AND a.created <= '$until 23:59:59'");

      $scosts = $this->getSumCosts($row->id,
          " and a.created >= '$from 00:00:00' AND a.created <= '$until 23:59:59'");

      $splanned_cost = $this->getSumPlannedCosts($row->id,
          " and a.created >= '$from 00:00:00' AND a.created <= '$until 23:59:59'");

      $sfact_cost = $this->getSumFactCosts($row->id,
          " and a.date >= '$from 00:00:00' AND a.date <= '$until 23:59:59'");

      $report['data'][] = array(
          'id' => $row->id,
          'name' => $row->name,
          'duration' => $row->duration,
          'smoney' => $row->smoney,
          'splan' => $splan,
          'scosts' => $scosts,
          'splanned_cost' => $splanned_cost,
          'sfact_cost' => $sfact_cost
      );
      $report['total'] += $row->duration;
      $report['total_money'] += $row->smoney;
      $report['total_plan'] += $splan;
      $report['total_costs'] += $scosts;
      $report['total_planned_cost'] += $splanned_cost;
      $report['total_fact_cost'] += $sfact_cost;
    }

    $report['total_plan'] = $report['total_plan'] * 60;

    $report['from'] = $from;
    $report['until'] = $until;

    return $report;
  }

  function getSumPlan($project_id, $filter = "") {
    $db = & JFactory::getDBO();

    $sql = "SELECT SUM(a.hours_plan) AS splan
			from #__teamlog_todo as a
			where project_id = " . (int) $project_id . " " . $filter;
    $db->setQuery($sql);
    $result = $db->loadObjectList();

    return $result[0]->splan;
  }

  function getSumCosts($project_id, $filter = "") {
    $db = & JFactory::getDBO();

    $sql = "SELECT SUM(a.costs) AS scosts
			from #__teamlog_todo as a
			where project_id = " . (int) $project_id . " " . $filter;
    $db->setQuery($sql);
    $result = $db->loadObjectList();

    return $result[0]->scosts;
  }

  function getSumPlannedCosts($project_id, $filter = "") {
    $db = & JFactory::getDBO();
    $sql = "SELECT a.hourly_rate, a.hours_plan, b.hour_price
			from #__teamlog_todo a
			left join #__teamlog_userdata b on a.user_id = b.user_id
			where a.project_id = " . (int) $project_id . " " . $filter;

    //var_dump($db->replacePrefix($sql));

    $db->setQuery($sql);
    $result = $db->loadObjectList();

    $sum_res = 0;
    foreach ($result as $row) {
      $price = $row->hourly_rate == 0 ? $row->hour_price : $row->hourly_rate;
      $sum_res += $price * $row->hours_plan;
    }

    return $sum_res;
  }

  function getSumFactCosts($project_id, $filter = "") {
    $db = & JFactory::getDBO();
    $sql = "SELECT a.duration, c.hourly_rate, b.hour_price
			from #__teamlog_log a
			left join #__teamlog_todo c on a.todo_id = c.id
			left join #__teamlog_userdata b on a.user_id = b.user_id
			where a.project_id = " . (int) $project_id . " " . $filter;

    //var_dump($db->replacePrefix($sql));

    $db->setQuery($sql);
    $result = $db->loadObjectList();

    $sum_res = 0;
    foreach ($result as $row) {
      $price = $row->hourly_rate == 0 ? $row->hour_price : $row->hourly_rate;
      $sum_res += $price * ($row->duration / 60);
    }

    return $sum_res;
  }

}