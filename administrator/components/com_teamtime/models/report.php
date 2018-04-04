<?php

class TeamtimeModelReport extends Core_Joomla_Manager {

	public $type_id;
	public $task_id;

	private function getTaskFilter($tableTask) {
		$type_id = $this->type_id;
		$task_id = $this->task_id;

		$res = array();
		if ($type_id != "") {
			$res[] = " a.type_id = {$type_id} ";
		}

		if ($task_id != "") {
			$task_id = $this->_db->Quote($task_id);
			$res[] = " " . $tableTask . ".name = {$task_id} ";
		}

		if (sizeof($res) > 0) {
			return "(" . implode(" and ", $res) . ")";
		}
		else {
			return "";
		}
	}

	private function getLastPeriod($query, $from, $until) {
		$period = JRequest::getVar('period', '');

		if ($period == "last30") {
			// get last log
			$this->_db->setQuery($query . " order by a.date desc limit 1");
			$rowLast = $this->_db->loadObject();
			if ($rowLast) {
				$until = date("Y-m-d", strtotime($rowLast->date));
				$from = date("Y-m-d", strtotime($rowLast->date . " -30 day"));
			}
		}

		return array($from, $until);
	}

	public function getTotalAmount($userId) {
		$query = "SELECT sum(unix_timestamp(ended) - unix_timestamp(created)) / 60 AS total_amount
			FROM #__teamtime_log
			WHERE user_id = {$userId} and ended > 0";
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();

		return $result[0]->total_amount;
	}

	public function getProjectReport($projectId, $userId, $from, $until) {
		$helperBase = TeamTime::helper()->getBase();

		if (!is_array($projectId)) {
			$projectId = array($projectId);
		}

		$acl = new TeamTime_Acl();
		$projectId = $acl->filterUserProjectIds($projectId);

		// init vars
		$report['data'] = array();
		$report['type'] = array();
		$report['user'] = array();
		$report['total'] = 0;
		$report['total_plan'] = 0;
		$report['total_money'] = 0;

		$where = array();
		$where[] = "a.project_id in (" . implode(",", $projectId) . ")";
		if ($userId) {
			$where[] = "a.user_id = " . (int) $userId;
		}
		$taskFilter = $this->getTaskFilter("b");
		if ($taskFilter != "") {
			$where[] = $taskFilter;
		}
		if (sizeof($where) > 0) {
			$whereStr = " where " . implode(" and ", $where);
		}
		else {
			$whereStr = "";
		}

		$query = "SELECT a.id AS id, a.description AS log, a.duration AS duration, a.date AS date, a.project_id,
			b.id AS task_id, b.name AS task_name, c.id AS type_id, c.name AS type_name, d.id AS user_id,
			d.name AS username, a.money, e.title as todo_title, e.hours_fact, e.hours_plan, e.hourly_rate,
			e.description as todo_description, e.costs, e.created as todo_date,
			a.todo_id, f.hour_price, b.rate as task_rate,
			pp.name as project_name, pp.rate as project_price, pp.dynamic_rate as project_dynamic_rate
			FROM #__teamtime_log AS a
			LEFT JOIN #__teamtime_task AS b ON a.task_id = b.id
			LEFT JOIN #__teamtime_type AS c ON a.type_id = c.id
			LEFT JOIN #__users AS d ON a.user_id = d.id
			LEFT JOIN #__teamtime_todo AS e ON a.todo_id = e.id
			LEFT JOIN #__teamtime_userdata AS f ON a.user_id = f.user_id
			LEFT JOIN #__teamtime_project AS pp ON a.project_id = pp.id
		";


		list($from, $until) = $this->getLastPeriod($query . $whereStr, $from, $until);
		$where[] = "a.date >= '{$from} 00:00:00' AND a.date <= '{$until} 23:59:59'";

		if (sizeof($where) > 0) {
			$whereStr = " where " . implode(" and ", $where);
		}
		else {
			$whereStr = "";
		}

		$query .= $whereStr . "
			order by e.created desc, b.type_id, a.task_id, a.todo_id, a.date";

		// create report
		$this->_db->setQuery($query);

		$result = $this->_db->loadObjectList();
		$todoId = 0;
		$todoIds = array();
		$totalPlanPrice = 0;
		$totalFactPrice = 0;
		$totalStatementPrice = 0;
		$totalCosts = 0;
		$priceRates = array();

		foreach ($result as $row) {
			if (!isset($report['type'][$row->type_id])) {
				$report['type'][$row->type_id] = array(
					'id' => $row->user_id,
					'name' => $row->type_name,
					'total' => $row->duration,
					'total_money' => $row->money);
			}
			else {
				$report['type'][$row->type87777_id]['total'] += $row->duration;
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
				$this->_db->setQuery("select sum(duration)/60 as hours_fact
					from #__teamtime_log where todo_id = {$row->todo_id}
						AND date >= '$from 00:00:00' AND date <= '$until 23:59:59'");
				$resHoursFact = $this->_db->loadObject();
				$row->hours_fact = $resHoursFact->hours_fact;
			}

			$todoPrice = $row->todo_id ? $row->hourly_rate : $row->hour_price;
			$todoPlanPrice = $todoPrice * $row->hours_plan;
			$todoFactPrice = $todoPrice * $row->duration / 60;

			if (!isset($priceRates[$row->task_id . "_" . $row->user_id])) {
				// calculate dynamic rate
				if ($row->project_dynamic_rate) {
					// check price by dotu
					$tmpRate = TeamTime::helper()->getDotu()->getTargetPrice(array(
						"task_id" => $row->task_id
							), true);
					if ($tmpRate === null || ($tmpRate instanceof TeamTime_Undefined)) {
						$tmpRate = $row->task_rate;
					}
					$tmpRate = $row->project_price * $tmpRate;
				}
				// get static rate
				else {
					$tmpRate = $row->project_price;
				}
				// save to cache
				$priceRates[$row->task_id . "_" . $row->user_id] = $tmpRate;
			}
			else {
				// get from cache
				$tmpRate = $priceRates[$row->task_id . "_" . $row->user_id];
			}
			$hoursStatementPrice = $tmpRate * $row->hours_plan;

			$totalFactPrice += $todoFactPrice;

			if ($row->todo_id && !isset($todoIds[$row->todo_id])) {
				$todoIds[$row->todo_id] = 1;
				$totalPlanPrice += $todoPlanPrice;
				$totalCosts += $row->costs;
			}

			//if (!$report['data'][$row->task_id][$row->todo_id]) {
			//	$report['data'][$row->task_id][$row->todo_id] = array();
			//}

			if (!isset($report['data'][$row->todo_id])) {
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
				'todo_description' => $helperBase->processRelativeLinks(
						$row->todo_description, JURI::root()),
				"hours_fact" => $row->hours_fact,
				"hours_plan" => $row->hours_plan,
				"todo_id" => $row->todo_id,
				'hourly_rate' => $todoPrice,
				'hours_plan_price' => $todoPlanPrice,
				'hours_fact_price' => $todoFactPrice,
				'hours_statement_price' => $hoursStatementPrice,
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
			if ($todoId != $row->todo_id) {
				$todoId = $row->todo_id;
				$report['total_plan'] += $row->hours_plan;

				$totalStatementPrice += $hoursStatementPrice;
			}
		}

		$report['total_plan'] = $report['total_plan'] * 60;

		$report['total_plan_price'] = $totalPlanPrice;
		$report['total_fact_price'] = $totalFactPrice;
		$report['total_statement_price'] = $totalStatementPrice;
		$report['total_costs'] = $totalCosts;

		// sort type & user data
		$compare = create_function('$a,$b',
				'return $a["total"] == $b["total"] ? 0 : $a["total"] > $b["total"] ? -1 : 1;');
		usort($report['type'], $compare);
		usort($report['user'], $compare);

		$report['from'] = $from;
		$report['until'] = $until;

		return $report;
	}

	public function getUserReport($userId, $from, $until) {
		$helperBase = TeamTime::helper()->getBase();
		$config = & JFactory::getConfig();
		$tzoffset = $config->getValue('config.offset');

		$acl = new TeamTime_Acl();
		$projectId = $acl->filterUserProjectIds();

		// init vars
		$report['data'] = array();
		$report['project'] = array();
		$report['total'] = 0;
		$report['total_money'] = 0;

		$where = array();
		if ($projectId !== null) {
			$where[] = "a.project_id in (" . implode(",", $projectId) . ")";
		}
		if ($userId) {
			$where[] = "a.user_id = " . (int) $userId;
		}
		$taskFilter = $this->getTaskFilter("b");
		if ($taskFilter != "") {
			$where[] = $taskFilter;
		}
		if (sizeof($where) > 0) {
			$whereStr = " where " . implode(" and ", $where);
		}
		else {
			$whereStr = "";
		}

		$query = "SELECT a.id AS id, a.description AS log, a.duration AS duration,
			a.date AS date, b.id AS task_id, b.name AS task_name, c.id AS type_id, c.name AS type_name,
			d.id AS project_id, d.name AS project_name, a.money,
			e.title as todo_title, e.hourly_rate, e.hours_plan, f.hour_price, a.todo_id, e.costs,
      e.description as todo_description
			FROM #__teamtime_log AS a
			LEFT JOIN #__teamtime_task AS b ON a.task_id = b.id
			LEFT JOIN #__teamtime_type AS c ON b.type_id = c.id
			LEFT JOIN #__teamtime_project AS d ON a.project_id = d.id
			LEFT JOIN #__teamtime_todo AS e ON a.todo_id = e.id
			LEFT JOIN #__teamtime_userdata AS f ON a.user_id = f.user_id
		";

		list($from, $until) = $this->getLastPeriod($query . $whereStr, $from, $until);
		$where[] = "a.date >= '{$from} 00:00:00' AND a.date <= '{$until} 23:59:59'";

		if (sizeof($where) > 0) {
			$whereStr = " where " . implode(" and ", $where);
		}
		else {
			$whereStr = "";
		}

		$query .= $whereStr . "
			order by a.date, a.project_id";

		// create report
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();

		$todoIds = array();
		$totalPlanPrice = 0;
		$totalFactPrice = 0;
		$totalCosts = 0;

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

			$todoPrice = $row->todo_id ? $row->hourly_rate : $row->hour_price;
			$todoPlanPrice = $todoPrice * $row->hours_plan;
			$todoFactPrice = $todoPrice * $row->duration / 60;

			$totalFactPrice += $todoFactPrice;

			if ($row->todo_id && !isset($todoIds[$row->todo_id])) {
				$todoIds[$row->todo_id] = 1;
				$totalPlanPrice += $todoPlanPrice;
				$totalCosts += $row->costs;
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
				'todo_description' => $helperBase->processRelativeLinks(
						$row->todo_description, JURI::root()),
				'hourly_rate' => $todoPrice,
				'hours_plan_price' => $todoPlanPrice,
				'hours_fact_price' => $todoFactPrice,
				'costs' => $row->costs,
			);

			$report['total'] += $row->duration;
			$report['total_money'] += $row->money;
		}

		$report['total_plan_price'] = $totalPlanPrice;
		$report['total_fact_price'] = $totalFactPrice;
		$report['total_costs'] = $totalCosts;

		// sort project data
		$compare = create_function('$a,$b',
				'return $a["total"] == $b["total"] ? 0 : $a["total"] > $b["total"] ? -1 : 1;');
		usort($report['project'], $compare);

		$report['from'] = $from;
		$report['until'] = $until;

		return $report;
	}

	public function getPeriodReport($from, $until) {
		$acl = new TeamTime_Acl();
		$projectId = $acl->filterUserProjectIds();

		// init vars
		$report['data'] = array();
		$report['total'] = 0;

		$where = array();
		if ($projectId) {
			$where[] = "a.project_id in (" . implode(",", $projectId) . ")";
		}
		$taskFilter = $this->getTaskFilter("c");
		if ($taskFilter != "") {
			$where[] = $taskFilter;
		}
		if (sizeof($where) > 0) {
			$whereStr = " where " . implode(" and ", $where);
		}
		else {
			$whereStr = "";
		}

		$query = "SELECT b.id AS id, b.name AS name, a.duration AS duration, a.money as smoney, a.date as date
			FROM #__teamtime_log AS a
			LEFT JOIN #__teamtime_project AS b ON a.project_id = b.id
			LEFT JOIN #__teamtime_task AS c ON a.task_id = c.id
		";

		list($from, $until) = $this->getLastPeriod($query . $whereStr, $from, $until);
		$where[] = "a.date >= '{$from} 00:00:00' AND a.date <= '{$until} 23:59:59'";

		if (sizeof($where) > 0) {
			$whereStr = " where " . implode(" and ", $where);
		}
		else {
			$whereStr = "";
		}

		$query = "SELECT b.id AS id, b.name AS name, SUM(a.duration) AS duration, SUM(a.money) as smoney
			FROM #__teamtime_log AS a
			LEFT JOIN #__teamtime_project AS b ON a.project_id = b.id
			LEFT JOIN #__teamtime_task AS c ON a.task_id = c.id
			" . $whereStr . "
			group by id
			order by duration DESC";

		// create report
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();

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

			$splannedCost = $this->getSumPlannedCosts($row->id,
					" and a.created >= '$from 00:00:00' AND a.created <= '$until 23:59:59'");

			$sfactCost = $this->getSumFactCosts($row->id,
					" and a.date >= '$from 00:00:00' AND a.date <= '$until 23:59:59'");

			$report['data'][] = array(
				'id' => $row->id,
				'name' => $row->name,
				'duration' => $row->duration,
				'smoney' => $row->smoney,
				'splan' => $splan,
				'scosts' => $scosts,
				'splanned_cost' => $splannedCost,
				'sfact_cost' => $sfactCost
			);
			$report['total'] += $row->duration;
			$report['total_money'] += $row->smoney;
			$report['total_plan'] += $splan;
			$report['total_costs'] += $scosts;
			$report['total_planned_cost'] += $splannedCost;
			$report['total_fact_cost'] += $sfactCost;
		}

		$report['total_plan'] = $report['total_plan'] * 60;
		$report['from'] = $from;
		$report['until'] = $until;

		return $report;
	}

	public function getSumPlan($projectId, $filter = "") {
		$sql = "SELECT SUM(a.hours_plan) AS splan
			from #__teamtime_todo as a
			where project_id = " . (int) $projectId . " " . $filter;
		$this->_db->setQuery($sql);
		$result = $this->_db->loadObjectList();

		return $result[0]->splan;
	}

	public function getSumCosts($projectId, $filter = "") {
		$sql = "SELECT SUM(a.costs) AS scosts
			from #__teamtime_todo as a
			where project_id = " . (int) $projectId . " " . $filter;
		$this->_db->setQuery($sql);
		$result = $this->_db->loadObjectList();

		return $result[0]->scosts;
	}

	public function getSumPlannedCosts($projectId, $filter = "") {
		$sql = "SELECT a.hourly_rate, a.hours_plan, b.hour_price
			from #__teamtime_todo a
			left join #__teamtime_userdata b on a.user_id = b.user_id
			where a.project_id = " . (int) $projectId . " " . $filter;
		$this->_db->setQuery($sql);
		$result = $this->_db->loadObjectList();

		$sumRes = 0;
		foreach ($result as $row) {
			$price = $row->hourly_rate == 0 ? $row->hour_price : $row->hourly_rate;
			$sumRes += $price * $row->hours_plan;
		}

		return $sumRes;
	}

	public function getSumFactCosts($projectId, $filter = "") {
		$sql = "SELECT a.duration, c.hourly_rate, b.hour_price
			from #__teamtime_log a
			left join #__teamtime_todo c on a.todo_id = c.id
			left join #__teamtime_userdata b on a.user_id = b.user_id
			where a.project_id = " . (int) $projectId . " " . $filter;

		$this->_db->setQuery($sql);
		$result = $this->_db->loadObjectList();

		$sumRes = 0;
		foreach ($result as $row) {
			$price = $row->hourly_rate == 0 ? $row->hour_price : $row->hourly_rate;
			$sumRes += $price * ($row->duration / 60);
		}

		return $sumRes;
	}

	public function showReport() {
		$user = & JFactory::getUser();
		$id = JRequest::getVar("id");

		$helperBase = TeamTime::helper()->getBase();

		if ($id) {
			$log = new Log($id);
			$todo = new Todo($log->todo_id);

			$mProject = new TeamtimeModelProject();
			if (!$user->guest) {
				if (!$mProject->projectIsAllowed($log->project_id)) {
					return;
				}
			}

			header("Content-Type: text/html; charset=UTF-8");
			print "<h2>" . $todo->title . "</h2>";
			print $helperBase->processRelativeLinks($log->description, JURI::root());
		}
	}

}