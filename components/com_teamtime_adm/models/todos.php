<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/*
  Class: TodoModelTodos
  The Model Class for Todos
 */

class TodoModelTodos extends JModel {

	/**
	 * Table
	 *
	 * @var array
	 */
	var $_table = 'todo';

	/**
	 * Data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Total
	 *
	 * @var integer
	 */
	var $_total = null;
	var $_total_hours = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Constructor
	 *
	 */
	function __construct() {
		parent::__construct();

		global $mainframe, $option;


		// get request vars
		$filter_order = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.filter_order', 'filter_order', 'a.id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$filter_user_id = $mainframe->getUserStateFromRequest(
				$option . '.filter_user_id', 'filter_user_id', '', 'int');
		$from_period = $mainframe->getUserStateFromRequest(
				$option . '.from_period', 'from_period', '', 'string');
		$until_period = $mainframe->getUserStateFromRequest(
				$option . '.until_period', 'until_period', '', 'string');

		$filter_state = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.filter_state', 'filter_state', '', 'cmd');
		$limit = $mainframe->getUserStateFromRequest(
				'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.limitstart', 'limitstart', 0, 'int');
		$search = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.search', 'search', '', 'string');

		$project_id = $mainframe->getUserStateFromRequest(
				$option . '.filter_project_id', 'project_id', '', 'string');
		$type_id = $mainframe->getUserStateFromRequest(
				$option . '.filter_type_id', 'type_id', '', 'string');
		$task_id = $mainframe->getUserStateFromRequest(
				$option . '.filter_task_id', 'task_id', '', 'string');

		$filter_limit = $mainframe->getUserStateFromRequest(
				$option . '.filter_limit', 'filter_limit', 10, 'int');

		// convert search to lower case
		$search = JString::strtolower($search);

		// in case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		// set model vars
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
		$this->setState('filter_user_id', $filter_user_id);
		$this->setState('filter_state', $filter_state);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		$this->setState('search', $search);

		$this->setState('project_id', $project_id);
		$this->setState('type_id', $type_id);
		$this->setState('task_id', $task_id);

		$this->setState('levellimit', $filter_limit);

		$this->setState('from_period', $from_period);
		$this->setState('until_period', $until_period);
	}

	function parentCalcfields($parent_id, $src_row = null, $where_hours = "") {
		$todo = new TodoModelTodo();

		if ($src_row == null) {
			$result = new stdClass();
			$items_count = 0;
		}
		else {
			$result = $src_row;
			$items_count = 1;
		}

		$list = $todo->treeToList($todo->getTree(array(), $parent_id));

		//error_log(print_r($list, true));

		$where = $this->_buildContentWhere(array(), false);
		$add_select = array(
				array(
						"fields" => "rd.repeat_date",
						"join" => "LEFT JOIN #__teamtime_todo_repeatdate AS rd ON a.id = rd.todo_id",
						"where" => $where
				)
		);

		$data = $todo->getDataForTreelist($list, $add_select);

		foreach ($data as $row) {
			$result->hours_plan += $row->hours_plan;
			$result->hourly_rate += $row->hourly_rate;
			$result->costs += $row->costs;
			$result->hours_plan_costs += $row->hours_plan * $row->hourly_rate;

			$items_count++;
		}

		$result->hourly_rate = round($result->hourly_rate / $items_count);

		// init real_hours_fact
		$ids = array();
		foreach ($list as $row) {
			$ids[] = $row->id;
		}
		$result->hours_fact += $todo->get_hours_fact($ids, $where_hours);

		return $result;
	}

	/**
	 * Method to get item data
	 *
	 * @access public
	 * @return array
	 */
	function getData() {
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

			//get data for selected todos
			//init stat fields
			$from_period = $this->getState('from_period');
			$until_period = $this->getState('until_period');

			//init hours_fact
			if ($from_period && $until_period) {
				$where_hours_fact = $this->_buildContentWhereForFact();
				$where_repeat_todo = ' and repeat_date >= ' . $this->_db->Quote(
								$this->_db->getEscaped("$from_period 00:00:00", true), false) .
						' and repeat_date <= ' . $this->_db->Quote(
								$this->_db->getEscaped("$until_period 23:59:59", true), false);
			}
			else {
				$where_hours_fact = "";
				$where_repeat_todo = "";
			}

			$todo = new TodoModelTodo();
			foreach ($this->_data as $i => $r) {
				$item = $r->data;
				$item->is_repeated = $item->todo_id ? true : false;

				if ($item->is_repeated) {
					//init repeat string
					$item->repeat_params_str = $todo->params_to_str($item, strtotime($item->created));

					//get events count for repeated
					$item->events_count = $todo->get_events_count($item->id, $where_repeat_todo);
					$item->hours_plan = $item->hours_plan * $item->events_count;
					$item->costs = $item->costs * $item->events_count;
				}

				$item->hours_plan_costs = $item->hours_plan * $item->hourly_rate;
				$item->hours_fact = round($todo->get_hours_fact(array($item->id), $where_hours_fact), 2);

				if ($item->is_parent) {
					$item->todo_hours_plan = $item->hours_plan;
					$item->todo_costs = $item->costs;
					$item->todo_hours_plan_costs = $item->hours_plan_costs;
					$item->todo_hours_fact = $item->hours_fact;
					$item = $this->parentCalcfields($item->id, $item, $where_hours_fact);
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

	/**
	 * Method to get the total number of items
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_total)) {
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	function getTotalHours() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_total_hours)) {
			$search = $this->getState('search');
			$user_id = $this->getState('filter_user_id');
			$state = $this->getState('filter_state');
			$from_period = $this->getState('from_period');
			$until_period = $this->getState('until_period');

			//plan stat
			$query = 'select sum(hours_plan) as splan,
					sum(hours_plan * t.hourly_rate) as sprice,
					sum(t.costs) as scosts
				from (' . $this->_buildQuery() . ') as t
				left join #__teamtime_todo_repeatdate as rds on t.id = rds.todo_id
				where if(rds.todo_id is null, true,
					rds.repeat_date >= ' . $this->_db->Quote($this->_db->getEscaped("$from_period 00:00:00",
									true), false) . '
					and rds.repeat_date <= ' . $this->_db->Quote($this->_db->getEscaped("$until_period 23:59:59",
									true), false) .
					')';

			//error_log("Todos list");
			//error_log($this->_db->replacePrefix($query));

			$this->_db->setQuery($query);
			$row = $this->_db->loadObject();
			$total_plan = $row->splan;
			$total_price = $row->sprice;
			$total_costs = $row->scosts;

			$query = 'select sum(tlog.duration)/60 as sfact from (' . $this->_buildQuery() . ') as t
				left join #__teamlog_log as tlog on t.id = tlog.todo_id
				where tlog.id and (
					tlog.created >= ' . $this->_db->Quote($this->_db->getEscaped("$from_period 00:00:00",
									true), false) . '
					and tlog.created <= ' . $this->_db->Quote($this->_db->getEscaped("$until_period 23:59:59",
									true), false) .
					')';
			$this->_db->setQuery($query);
			$row = $this->_db->loadObject();
			$total_fact = $row->sfact;

			$this->_total_hours = array($total_plan, $total_fact, $total_price, $total_costs);
		}

		return $this->_total_hours;
	}

	/**
	 * Method to get a pagination object
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	function _buildQuery($add_filter = array()) {
		$db = & JFactory::getDBO();

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

	function _buildQueryForTree() {
		$db = & JFactory::getDBO();

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

	function _buildContentWhere($add_filter = array(), $add_where = true) {
		global $mainframe, $option;

		$db = & JFactory::getDBO();
		$search = $this->getState('search');
		$user_id = $this->getState('filter_user_id');
		$state = $this->getState('filter_state');

		$from_period = $this->getState('from_period');
		$until_period = $this->getState('until_period');

		$project_id = $this->getState('project_id');
		$type_id = $this->getState('type_id');
		$task_id = $this->getState('task_id');

		$where = array();

		// search filter
		if ($search) {
			$where[] = 'LOWER(a.title) LIKE ' . $db->Quote('%' . $db->getEscaped($search, true) . '%', false);
		}

		// date filter
		if ($from_period && $until_period) {
			$where[] = ' if(rd.todo_id is null,
				a.created >= ' . $db->Quote($db->getEscaped("$from_period 00:00:00",
									true), false) . '
					and a.created <= ' . $db->Quote($db->getEscaped("$until_period 23:59:59",
									true), false) . ',
				rd.repeat_date >= ' . $db->Quote($db->getEscaped("$from_period 00:00:00",
									true), false) . '
					and rd.repeat_date <= ' . $db->Quote($db->getEscaped("$until_period 23:59:59",
									true), false) . ') ';
		}

		// user filter
		if ($user_id > 0) {
			$where[] = 'a.user_id = ' . intval($user_id);
		}

		// state filter
		if ($state !== '') {
			$where[] = 'a.state = ' . intval($state);
		}

		// project filter
		if ($project_id != '') {
			$where[] = 'a.project_id = ' . intval($project_id);
		}

		// type filter
		if ($type_id != '') {
			$where[] = 'a.type_id = ' . intval($type_id);
		}

		// task filter
		if ($task_id != '') {
			$where[] = 'a.task_id = ' . intval($task_id);
		}

		foreach ($add_filter as $v) {
			$where[] = $v;
		}

		$where = (count($where) ?
						($add_where ? ' WHERE ' : '') .
						implode(' AND ', $where) : '');

		return $where;
	}

	function _buildContentWhereForFact($add_filter = array()) {
		global $mainframe, $option;

		$db = & JFactory::getDBO();
		$search = $this->getState('search');
		$user_id = $this->getState('filter_user_id');
		$state = $this->getState('filter_state');

		$from_period = $this->getState('from_period');
		$until_period = $this->getState('until_period');

		$project_id = $this->getState('project_id');
		$type_id = $this->getState('type_id');
		$task_id = $this->getState('task_id');

		$where = array();

		// search filter
		if ($search) {
			$where[] = 'LOWER(description) LIKE ' .
					$db->Quote('%' . $db->getEscaped($search, true) . '%', false);
		}

		// date filter
		if ($from_period && $until_period) {
			$where[] = ' date >= ' . $this->_db->Quote(
							$this->_db->getEscaped("$from_period 00:00:00", true), false) .
					' and date <= ' . $this->_db->Quote(
							$this->_db->getEscaped("$until_period 23:59:59", true), false);
		}

		// user filter
		if ($user_id > 0) {
			$where[] = 'user_id = ' . intval($user_id);
		}

		// state filter
		//if ($state !== '') {
		//	$where[] = 'a.state = ' . intval($state);
		//}
		// project filter
		if ($project_id != '') {
			$where[] = 'project_id = ' . intval($project_id);
		}

		// type filter
		if ($type_id != '') {
			$where[] = 'type_id = ' . intval($type_id);
		}

		// task filter
		if ($task_id != '') {
			$where[] = 'task_id = ' . intval($task_id);
		}

		foreach ($add_filter as $v) {
			$where[] = $v;
		}

		$where = (count($where) ? ' and ' . implode(' AND ', $where) : '');

		return $where;
	}

	function _buildContentOrderBy() {
		global $mainframe, $option;

		$orderby = ' ORDER BY ' . $this->getState('filter_order')
				. ' ' . $this->getState('filter_order_Dir');

		return $orderby;
	}

}