<?php

class TeamtimeModelTodos extends Core_Joomla_ManagerList {

	public $_table = 'teamtimetodo';
	public $_totalHours = null;

	public function __construct() {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		parent::__construct();

		// get request vars
		$filterUserId = $mainframe->getUserStateFromRequest(
				$option . '.filter_user_id', 'filter_user_id', '', 'int');
		$fromPeriod = $mainframe->getUserStateFromRequest(
				$option . '.from_period', 'from_period', '', 'string');
		$untilPeriod = $mainframe->getUserStateFromRequest(
				$option . '.until_period', 'until_period', '', 'string');
		$filterState = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.filter_state', 'filter_state', '', 'cmd');
		$projectId = $mainframe->getUserStateFromRequest(
				$option . '.filter_project_id', 'project_id', '', 'string');
		$typeId = $mainframe->getUserStateFromRequest(
				$option . '.filter_type_id', 'type_id', '', 'string');
		$taskId = $mainframe->getUserStateFromRequest(
				$option . '.filter_task_id', 'task_id', '', 'string');
		$filterLimit = $mainframe->getUserStateFromRequest(
				$option . '.filter_limit', 'filter_limit', 10, 'int');

		// set model vars
		$this->setState('filter_user_id', $filterUserId);
		$this->setState('filter_state', $filterState);
		$this->setState('project_id', $projectId);
		$this->setState('type_id', $typeId);
		$this->setState('task_id', $taskId);
		$this->setState('levellimit', $filterLimit);
		$this->setState('from_period', $fromPeriod);
		$this->setState('until_period', $untilPeriod);
	}

	public function parentCalcfields($parentId, $srcRow = null, $whereHours = "") {
		$todo = new TeamtimeModelTodo();

		if ($srcRow == null) {
			$result = new stdClass();
			$itemsCount = 0;
		}
		else {
			$result = $srcRow;
			$itemsCount = 1;
		}

		$list = $todo->treeToList($todo->getTree(array(), $parentId));

		//error_log(print_r($list, true));

		$where = $this->_buildContentWhere(array(), false);
		$addSelect = array(
			array(
				"fields" => "rd.repeat_date",
				"join" => "LEFT JOIN #__teamtime_todo_repeatdate AS rd ON a.id = rd.todo_id",
				"where" => $where
			)
		);

		$data = $todo->getDataForTreelist($list, $addSelect);
		foreach ($data as $row) {
			$result->hours_plan += $row->hours_plan;
			$result->hourly_rate += $row->hourly_rate;
			$result->costs += $row->costs;
			$result->hours_plan_costs += $row->hours_plan * $row->hourly_rate;

			$itemsCount++;
		}

		$result->hourly_rate = round($result->hourly_rate / $itemsCount);

		// init real_hours_fact
		$ids = array();
		foreach ($list as $row) {
			$ids[] = $row->id;
		}
		$result->hours_fact += $todo->get_hours_fact($ids, $whereHours);

		return $result;
	}

	public function getData() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {

			$query = $this->_buildQueryForTree();
			$this->_data = $this->_getList($query);

			$levellimit = $this->getState('levellimit');

			// establish the hierarchy of the todos
			$children = array();
			// first pass - collect children
			foreach ($this->_data as $v) {
				$v->name = "";

				$pt = (int) $v->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}

			// second pass - get an indent list of the items
			$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, max(0, $levellimit - 1));
			$list = array_slice($list, $this->getState('limitstart'), $this->getState('limit'));

			$ids = array();
			foreach ($list as $row) {
				$ids[] = $row->id;
			}

			// fix for empty data
			if (sizeof($ids) == 0) {
				$ids[] = 0;
			}

			$query = $this->_buildQuery(array(" a.id in (" . implode(",", $ids) . ") "));
			$this->_data = $this->_getList($query);

			$rows_data = array();
			foreach ($this->_data as $row) {
				$rows_data[$row->id] = $row;
			}
			foreach ($list as $i => $row) {
				$list[$i]->data = $rows_data[$row->id];
			}

			$this->_data = $list;

			// get data for selected todos
			// init stat fields
			$fromPeriod = $this->getState('from_period');
			$untilPeriod = $this->getState('until_period');

			//init hours_fact
			if ($fromPeriod && $untilPeriod) {
				$whereHoursFact = $this->_buildContentWhereForFact();
				$whereRepeatTodo = ' and repeat_date >= ' . $this->_db->Quote(
								$this->_db->getEscaped("$fromPeriod 00:00:00", true), false) .
						' and repeat_date <= ' . $this->_db->Quote(
								$this->_db->getEscaped("$untilPeriod 23:59:59", true), false);
			}
			else {
				$whereHoursFact = "";
				$whereRepeatTodo = "";
			}

			$todo = new TeamtimeModelTodo();
			foreach ($this->_data as $i => $r) {
				$item = $r->data;
				$item->is_repeated = $item->todo_id ? true : false;

				if ($item->is_repeated) {
					//init repeat string
					$item->repeat_params_str = $todo->params_to_str($item, strtotime($item->created));

					//get events count for repeated
					$item->events_count = $todo->get_events_count($item->id, $whereRepeatTodo);
					$item->hours_plan = $item->hours_plan * $item->events_count;
					$item->costs = $item->costs * $item->events_count;
				}

				$item->hours_plan_costs = $item->hours_plan * $item->hourly_rate;
				$item->hours_fact = round($todo->get_hours_fact(array($item->id), $whereHoursFact), 2);

				if ($item->is_parent) {
					$item->todo_hours_plan = $item->hours_plan;
					$item->todo_costs = $item->costs;
					$item->todo_hours_plan_costs = $item->hours_plan_costs;
					$item->todo_hours_fact = $item->hours_fact;
					$item = $this->parentCalcfields($item->id, $item, $whereHoursFact);
				}

				$item->hours_fact = round($item->hours_fact, 2);

				$this->_data[$i]->data = $item;
			}
		}

		if ($this->_db->getErrorMsg()) {
			JError::raiseWarning(500, $this->_db->getErrorMsg());
		}

		return $this->_data;
	}

	public function getTotalHours() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_totalHours)) {
			$search = $this->getState('search');
			$userId = $this->getState('filter_user_id');
			$state = $this->getState('filter_state');
			$fromPeriod = $this->getState('from_period');
			$untilPeriod = $this->getState('until_period');

			//plan stat
			$query = 'select sum(hours_plan) as splan,
					sum(hours_plan * t.hourly_rate) as sprice,
					sum(t.costs) as scosts
				from (' . $this->_buildQuery() . ') as t
				left join #__teamtime_todo_repeatdate as rds on t.id = rds.todo_id
				where if(rds.todo_id is null, true,
					rds.repeat_date >= ' . $this->_db->Quote($this->_db->getEscaped("$fromPeriod 00:00:00",
									true), false) . '
					and rds.repeat_date <= ' . $this->_db->Quote($this->_db->getEscaped("$untilPeriod 23:59:59",
									true), false) .
					')';

			//error_log("Todos list");
			//error_log($this->_db->replacePrefix($query));

			$this->_db->setQuery($query);
			$row = $this->_db->loadObject();
			$totalPlan = $row->splan;
			$totalPrice = $row->sprice;
			$totalCosts = $row->scosts;

			$query = 'select sum(tlog.duration)/60 as sfact from (' . $this->_buildQuery() . ') as t
				left join #__teamtime_log as tlog on t.id = tlog.todo_id
				where tlog.id and (
					tlog.created >= ' . $this->_db->Quote($this->_db->getEscaped("$fromPeriod 00:00:00",
									true), false) . '
					and tlog.created <= ' . $this->_db->Quote($this->_db->getEscaped("$untilPeriod 23:59:59",
									true), false) .
					')';
			$this->_db->setQuery($query);
			$row = $this->_db->loadObject();
			$totalFact = $row->sfact;

			$this->_totalHours = array($totalPlan, $totalFact, $totalPrice, $totalCosts);
		}

		return $this->_totalHours;
	}

	protected function _buildQuery($add_filter = array()) {
		$table = & $this->getTable($this->_table);
		$where = $this->_buildContentWhere($add_filter);
		$orderby = $this->_buildContentOrderBy();

		$query = 'SELECT a.*, b.name AS username, p.*, rd.repeat_date
			FROM ' . $table->getTableName() . ' AS a
			LEFT JOIN #__users AS b ON b.id = a.user_id
			LEFT JOIN #__teamtime_todo_repeatparams AS p ON a.id = p.todo_id
			LEFT JOIN #__teamtime_todo_repeatdate AS rd ON a.id = rd.todo_id'
				. $where
				. " group by a.id "
				. $orderby;

		return $query;
	}

	protected function _buildQueryForTree() {
		$table = & $this->getTable($this->_table);
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();

		$query = 'SELECT a.id, ref.parent_id as parent
			FROM ' . $table->getTableName() . ' AS a
			LEFT JOIN #__users AS b ON b.id = a.user_id
			LEFT JOIN #__teamtime_todo_repeatparams AS p ON a.id = p.todo_id
			LEFT JOIN #__teamtime_todo_repeatdate AS rd ON a.id = rd.todo_id
			left join jos_teamtime_todo_ref as ref on a.id = ref.todo_id'
				. $where
				. " group by a.id "
				. $orderby;

		return $query;
	}

	protected function _buildContentWhere($addFilter = array(), $addWhere = true) {
		$search = $this->getState('search');
		$userId = $this->getState('filter_user_id');
		$state = $this->getState('filter_state');

		$fromPeriod = $this->getState('from_period');
		$untilPeriod = $this->getState('until_period');

		$projectId = $this->getState('project_id');
		$typeId = $this->getState('type_id');
		$taskId = $this->getState('task_id');

		$where = array();
		// search filter
		if ($search) {
			$where[] = 'LOWER(a.title) LIKE ' . $this->_db->Quote(
							'%' . $this->_db->getEscaped($search, true) . '%', false);
		}

		// date filter
		if ($fromPeriod && $untilPeriod) {
			$where[] = ' if(rd.todo_id is null,
				a.created >= ' . $this->_db->Quote(
							$this->_db->getEscaped("$fromPeriod 00:00:00", true), false) . '
					and a.created <= ' . $this->_db->Quote(
							$this->_db->getEscaped("$untilPeriod 23:59:59", true), false) . ',
				rd.repeat_date >= ' . $this->_db->Quote(
							$this->_db->getEscaped("$fromPeriod 00:00:00", true), false) . '
					and rd.repeat_date <= ' . $this->_db->Quote(
							$this->_db->getEscaped("$untilPeriod 23:59:59", true), false) . ') ';
		}

		// user filter
		if ($userId > 0) {
			$where[] = 'a.user_id = ' . intval($userId);
		}

		// state filter
		if ($state !== '') {
			$where[] = 'a.state = ' . intval($state);
		}

		// project filter
		if ($projectId != '') {
			$projectId = array($projectId);
		}
		else {
			$projectId = null;
		}
		$acl = new TeamTime_Acl();
		$projectId = $acl->filterUserProjectIds($projectId);
		if ($projectId !== null) {
			$where[] = 'a.project_id in (' . implode(",", $projectId) . ")";
		}

		// type filter
		if ($typeId != '') {
			$where[] = 'a.type_id = ' . intval($typeId);
		}

		// task filter
		if ($taskId != '') {
			$where[] = 'a.task_id = ' . intval($taskId);
		}

		foreach ($addFilter as $v) {
			$where[] = $v;
		}

		$where = (count($where) ? ($addWhere ? ' WHERE ' : '') . implode(' AND ', $where) : '');

		return $where;
	}

	protected function _buildContentWhereForFact($addFilter = array()) {
		$search = $this->getState('search');
		$userId = $this->getState('filter_user_id');
		$state = $this->getState('filter_state');

		$fromPeriod = $this->getState('from_period');
		$untilPeriod = $this->getState('until_period');

		$projectId = $this->getState('project_id');
		$typeId = $this->getState('type_id');
		$taskId = $this->getState('task_id');

		$where = array();

		// search filter
		if ($search) {
			$where[] = 'LOWER(description) LIKE ' .
					$this->_db->Quote('%' . $this->_db->getEscaped($search, true) . '%', false);
		}

		// date filter
		if ($fromPeriod && $untilPeriod) {
			$where[] = ' date >= ' . $this->_db->Quote(
							$this->_db->getEscaped("$fromPeriod 00:00:00", true), false) .
					' and date <= ' . $this->_db->Quote(
							$this->_db->getEscaped("$untilPeriod 23:59:59", true), false);
		}

		// user filter
		if ($userId > 0) {
			$where[] = 'user_id = ' . intval($userId);
		}

		// state filter
		//if ($state !== '') {
		//	$where[] = 'a.state = ' . intval($state);
		//}
		// project filter
		if ($projectId != '') {
			$where[] = 'project_id = ' . intval($projectId);
		}

		// type filter
		if ($typeId != '') {
			$where[] = 'type_id = ' . intval($typeId);
		}

		// task filter
		if ($taskId != '') {
			$where[] = 'task_id = ' . intval($taskId);
		}

		foreach ($addFilter as $v) {
			$where[] = $v;
		}

		$where = (count($where) ? ' and ' . implode(' AND ', $where) : '');

		return $where;
	}

	protected function _buildContentOrderBy() {
		$colName = $this->getState('filter_order');
		if ($colName == "a.name") {
			$colName = "a.title";
		}

		$orderby = ' ORDER BY ' . $colName
				. ' ' . $this->getState('filter_order_Dir');

		return $orderby;
	}

}