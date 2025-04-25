<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TeamtimecareerModelTargetvector extends Core_Joomla_Manager {

	public $_table = 'targetvector';
	//
	private $_cache_SvTvBalance = array();
	private $_cache_StateUserData = array();
	private $_cache_TargetUserData = array();
	//
	private $_targetBalance = array();
	private $_filterStateByBalance = false;

	public function &getData() {
		if (empty($this->_data)) {
			$row = & $this->getTable($this->_table);

			// load the row from the db table
			if ($this->_id) {
				$row->load($this->_id);

				$row->num_tree = $row->num;
				$row = $this->calcFieldsForParent($row->id, $row);
			}

			// set defaults, if new
			if ($row->id == 0) {
				//...
			}

			$this->_data = & $row;
		}

		return $this->_data;
	}

	private function applyForChildren($targetId, $data) {
		$table = & $this->getTable($this->_table);

		$rows = $this->flattenTree($this->getTree(array("is_skill" => 0), $targetId));
		$ids = array();
		foreach ($rows as $row) {
			$ids[] = $row->id;
		}

		if (sizeof($ids) == 0) {
			return;
		}

		$ids = implode(",", $ids);
		$query = 'update ' . $table->getTableName() . ' set
			hourprice = ' . $this->_db->Quote($data["hourprice"]) . '
			where id in (' . $ids . ')';

		//error_log($query);

		$this->_db->setQuery($query);
		$this->_db->query();
	}

	private function storeSkills($data) {
		if ($data["is_skill"]) {
			return;
		}

		// update skills
		if (isset($data["skill_title"])) {
			foreach ($data["skill_title"] as $k => $v) {

				// check for delete flag
				if (isset($data["skill_delete"][$k]) && $data["skill_delete"][$k] == "1") {
					$query = "delete from #__teamtimecareer_targetvector
            where id = " . (int) $k;
				}
				else {
					$query = "update #__teamtimecareer_targetvector
              set title = " . $this->_db->Quote($v)
							. ", num = " . $this->_db->Quote($data["skill_num"][$k])
							. " where id = " . (int) $k;
				}

				$this->_db->Execute($query);
			}
		}

		// add new skills
		if (isset($data["newskill_title"])) {
			foreach ($data["newskill_title"] as $k => $v) {
				if (trim($v) == "") {
					continue;
				}

				$query = "insert into #__teamtimecareer_targetvector
            (title, num, parent_id, hourprice, is_skill)
          values(" . $this->_db->Quote($v)
						. ", " . $this->_db->Quote($data["newskill_num"][$k])
						. ", " . $this->_db->Quote($data["id"])
						. ", " . $this->_db->Quote($data["hourprice"])
						. ", 1)";

				$this->_db->Execute($query);
			}
		}
	}

	public function store($data) {
		$row = & $this->getTable($this->_table);

		// bind the form fields
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

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

		// apply properties for for children
		if (isset($data["apply_for_children"])) {
			$this->applyForChildren($row->id, array(
				"hourprice" => $row->hourprice
			));
		}

		$row->reorder('parent = ' . (int) $row->parent_id);

		$this->_data = $row;

		$this->storeSkills($data);

		return true;
	}

	public function getChildren($parentId = 0, $filter = array()) {
		if ($parentId == null) {
			$parentId = $this->_id;
		}

		if ($parentId == null) {
			return array();
		}

		$table = & $this->getTable($this->_table);

		$where = array();
		$where[] = "a.parent_id = " . $parentId;

		if (isset($filter["is_skill"])) {
			$where[] = "a.is_skill = " . $filter["is_skill"];
		}

		$where = " where " . implode(" and ", $where);

		$query = "select a.id, a.title, a.is_skill
			from " . $table->getTableName() . " as a
			" . $where . "
			order by a.parent_id, a.ordering";

		$this->_db->setQuery($query);

		$rows = $this->_db->loadObjectList();
		if (!$rows) {
			return array();
		}

		return $rows;
	}

	public function getParentTargets($targetId = null) {
		$table = & $this->getTable($this->_table);

		$filter = array();
		$filter[] = "a.parent_id = 0 and a.is_skill = 0";

		if ($targetId !== null) {
			$filter[] = "a.id = " . (int) $targetId;
		}

		if (sizeof($filter) > 0) {
			$where = "where " . implode(" and ", $filter);
		}
		else {
			$where = "";
		}

		$query = "select a.id, a.title, a.is_skill
			from " . $table->getTableName() . " as a
			" . $where . "
			order by a.ordering";

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();

		return $rows;
	}

	public function getTree($params = array(), $parentId = 0, $cmd = array()) {
		$table = & $this->getTable($this->_table);

		$filter = array();
		if ($parentId == 0) {
			$filter[] = "a.parent_id = 0";
		}
		else {
			$filter[] = "a.parent_id = " . $parentId;
		}

		foreach ($params as $k => $v) {
			// param as [name] [op] [value]
			if (is_array($v)) {
				$filter[] = "a." . $k . $v[1] . $this->_db->Quote($v[0]);
			}
			// param as [name] = [value]
			else {
				$filter[] = "a." . $k . " = " . $this->_db->Quote($v);
			}
		}
		if (sizeof($filter) > 0) {
			$filter = " where " . implode(" and ", $filter);
		}

		$query = "select a.id, a.title, a.is_skill
        from " . $table->getTableName() . " as a " .
				$filter . " order by a.parent_id, a.ordering";

		$this->_db->setQuery($query);

		$rows = $this->_db->loadObjectList();
		foreach ($rows as $i => $row) {
			//hide current node
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

	public function flattenTree($tree, $level = 0, $parentId = null) {
		$result = array();

		foreach ($tree as $row) {
			$r = new stdClass();
			$r->level = $level;
			$r->id = $row->id;
			$r->title = $row->title;
			$r->hasChildren = sizeof($row->children) > 0;

			// additional data
			$r->isSkill = $row->is_skill;

			if ($parentId != null) {
				$r->parent_id = $parentId;
			}

			$result[] = $r;

			if ($r->hasChildren) {
				$result = array_merge($result, $this->flattenTree(
								$row->children, $level + 1, $row->id));
			}
		}

		return $result;
	}

	public function getDataForTreelist($list, $addSelect = array()) {
		$table = & $this->getTable($this->_table);

		$ids = array();
		foreach ($list as $row) {
			$ids[] = $row->id;
		}
		if (sizeof($ids) == 0) {
			return array();
		}

		$addFields = "";
		$addJoin = "";
		$addWhere = "";
		if (sizeof($addSelect) > 0) {
			foreach ($addSelect as $r) {
				$addFields .= $r["fields"] != "" ? (" , " . $r["fields"]) : "";
				$addJoin .= $r["join"] != "" ? (" " . $r["join"]) : "";
				$addWhere .= $r["where"] != "" ? (" and " . $r["where"]) : "";
			}
		}

		$ids = implode(", ", $ids);
		$query = "select a.* {$addFields} from " . $table->getTableName() . " as a
				{$addJoin}
				where a.id in ({$ids}) {$addWhere}";

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();

		$result = array();
		foreach ($rows as $row) {
			$result[$row->id] = $row;
		}

		return $result;
	}

	public function setFilterTargetBalance($balance) {
		$this->_targetBalance = $balance;
	}

	public function setFilterStateByBalance($value) {
		$this->_filterStateByBalance = $value;
	}

	public function calcFieldsForParent($parentId, $srcRow = null, $filter = array()) {
		if ($srcRow == null) {
			$result = new stdClass();
			$itemsCount = 0;
		}
		else {
			$result = $srcRow;
			$itemsCount = 1;
		}

		$list = $this->flattenTree($this->getTree(array(), $parentId));

		// filter for user targets
		if ($this->_filterStateByBalance) {
			foreach ($list as $i => $row) {
				if (!isset($this->_targetBalance[$row->id])) {
					unset($list[$i]);
				}
			}
		}

		$where = count($filter) ? implode(' AND ', $filter) : '';

		$addSelect = array(
			array(
				//"fields" => "rd.repeat_date",
				//"join" => "LEFT JOIN #__teamtime_todo_repeatdate AS rd ON a.id = rd.todo_id",
				"fields" => "",
				"join" => "",
				"where" => $where
			)
		);

		$data = $this->getDataForTreelist($list, $addSelect);

		foreach ($data as $row) {
			$result->num_tree += $row->num;

			$itemsCount++;
		}

		//init real_hours_fact
// 		$ids = array();
// 		foreach ($list as $row) {
// 		 $ids[] = $row->id;
// 		}
// 		$item->hours_fact += $todo->get_hours_fact($ids, $where_hours);

		return $result;
	}

	public function getTargetIdByTaskId($taskId) {

		// error_log("taskid..." . $taskId);
		$query = "select * from #__teamtimecareer_task_target as a
      where id = " . (int) $taskId;

		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();

		if ($row) {
			return $row->target_id;
		}
		else {
			return null;
		}
	}

	public function setTargetForTask($taskId, $targetId) {
		$query = "insert into `#__teamtimecareer_task_target`
      (id, target_id)
      values(" . (int) $taskId . ", " . (int) $targetId . ")
      on duplicate key update target_id = " . (int) $targetId;

		$this->_db->Execute($query);
	}

	public function deleteTargetForTask($taskId) {
		$query = "delete from `#__teamtimecareer_task_target`
      where id = " . (int) $taskId;

		$this->_db->Execute($query);
	}

	public function getTargetIdByTodoId($todoId) {
		$query = "select * from #__teamtimecareer_todo_target as a
      where id = " . (int) $todoId;

		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();

		if ($row) {
			return $row->target_id;
		}
		else {
			return null;
		}
	}

	public function setTargetForTodo($todoId, $targetId) {
		$query = "insert into `#__teamtimecareer_todo_target`
      (id, target_id)
      values(" . (int) $todoId . ", " . (int) $targetId . ")
      on duplicate key update target_id = " . (int) $targetId;

		$this->_db->Execute($query);
	}

	public function deleteTargetForTodo($todoId) {
		$query = "delete from `#__teamtimecareer_todo_target`
      where id = " . (int) $todoId;

		$this->_db->Execute($query);
	}

	public function isTaskPrice($taskId) {
		$query = "select * from #__teamtimecareer_task_price as a
      where id = " . (int) $taskId;

		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();

		if ($row) {
			return $row->price;
		}
		else {
			return false;
		}
	}


	// Set Dynamic calculation of the task price (task is not todo)
	public function setTaskPrice($taskId, $check) {
		$query = "insert into `#__teamtimecareer_task_price`
      (id, price)
      values(" . (int) $taskId . ", " . (int) $check . ")
      on duplicate key update price = " . (int) $check;

		$this->_db->Execute($query);
	}

	public function setTargetBalance($userId, $targetBalance) {
		$result = array();

		foreach ($targetBalance as $targetId => $num) {
			if (trim($num) == "" || $num == 0) {
				$query = "delete from `#__teamtimecareer_target_balance`
          where target_id = " . (int) $targetId
						. " and user_id = " . (int) $userId;
			}
			else {
				$num = $this->_db->Quote($num);

				$query = "insert into `#__teamtimecareer_target_balance`
        (target_id, user_id, num)
        values(" . (int) $targetId . ", " . (int) $userId . ", " . $num . ")
        on duplicate key update num = " . $num;
			}

			$this->_db->Execute($query);
		}

		return $result;
	}

	public function getTargetBalance($userId) {
		$result = array();

		$query = "select a.* from #__teamtimecareer_target_balance as a
      where a.user_id = " . (int) $userId;

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();

		if (!$rows) {
			return $result;
		}

		foreach ($rows as $row) {
			$result[$row->target_id] = $row->num;
		}

		return $result;
	}

	public function getTargetBalanceValue($userId, $targetId) {
		$result = 0;

		$query = "select a.* from #__teamtimecareer_target_balance as a
      where a.user_id = " . (int) $userId . " and a.target_id = " . (int) $targetId;

		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();
		if ($row) {
			$result = $row->num;
		}

		return $result;
	}

	public function getSvTvBalance($userId) {
		if (isset($this->_cache_SvTvBalance[$userId])) {
			$rows = $this->_cache_SvTvBalance[$userId];
		}
		else {
			$rows = $this->getTargetBalance($userId);
			$this->_cache_SvTvBalance[$userId] = $rows;
		}

		return $rows;
	}

	private function _getDotuPriceForTask($targetData, $userId) {
		$result = 0;

		$tvValue = $targetData->num;

		// get state vector value
		$mstate = new TeamtimecareerModelStatevector();
		$svValue = $mstate->getStateVectorValue($targetData->id, $userId);

		if ($tvValue == 0) {
			$result = 0;
		}
		else {
// TODO
			$result = $targetData->hourprice * ($svValue / $tvValue);
		}

		if ($result > $targetData->hourprice) {
			$result = $targetData->hourprice;
		}

		return $result;
	}

	public function getBalanceHourPrice($targetData, $userId) {
		$result = 0;
		$b = $this->getTargetBalanceValue($userId, $targetData->id);
// TODO
		$result = $targetData->hourprice * $b / 100;

		$mstate = new TeamtimecareerModelStatevector();
		$svValue = $mstate->getStateVectorValue($targetData->id, $userId);
		$tvValue = $targetData->num;



		return $result;
	}

	public function getStateUserData($balanceSvTv, $userId) {
		$result = array();

		if (isset($this->_cache_StateUserData[$userId])) {
			$result = $this->_cache_StateUserData[$userId];
		}
		else {
			$mstate = new TeamtimecareerModelStatevector();
			foreach ($balanceSvTv as $targetId => $b) {
				$result[$targetId] = $mstate->getStateVectorValue($targetId, $userId);
			}

			$this->_cache_StateUserData[$userId] = $result;
		}

		return $result;
	}

	public function getTargetUserData($balanceSvTv, $userId) {
		$result = array();

		if (isset($this->_cache_TargetUserData[$userId])) {
			$result = $this->_cache_TargetUserData[$userId];
		}
		else {
			foreach ($balanceSvTv as $targetId => $b) {
				$this->setId($targetId);
				$data = $this->getData();
				$result[$targetId] = $data->num;
			}

			$this->_cache_TargetUserData[$userId] = $result;
		}

		return $result;
	}

	public function getSvTvAvg($balanceSvTv, $stateUserData, $targetUserData) {
		$result = 0;

		if (sizeof($balanceSvTv) == 0) {
			return $result;
		}


		// error_log("-------- balanceSvTv ------ s");
		// error_log(print_r($balanceSvTv, true));
		// error_log("-------------- e");

		// error_log("-------- stateUserData ------ s");
		// error_log(print_r($stateUserData, true));
		// error_log("-------------- e");

		// error_log("------- targetUserData ------- s");
		// error_log(print_r($targetUserData, true));
		// error_log("-------------- e");


        /*
		foreach ($balanceSvTv as $target_id => $b) {
		  $tv_value = $target_user_data[$target_id];

		  // get state vector value
		  $sv_value = $state_user_data[$target_id];

		  $tmp = $sv_value / $tv_value;
		  if ($tmp > 1) {
		  $tmp = 1;
		  }

		  $result += $tmp;
		} */

		$targetResult = 0;
		$stateResult = 0;
		foreach ($balanceSvTv as $targetId => $b) {
			$tvValue = $targetUserData[$targetId];

			// get state vector value
			$svValue = $stateUserData[$targetId];

			$targetResult += $tvValue;
			$stateResult += $svValue;
		}

		if ($targetResult != 0) {
			$result = $stateResult / $targetResult;
		}

		if ($result > 1) {
			$result = 1;
		}


//		error_log("targetData->hourprice: " . $targetData->hourprice);

//		error_log("targetData: ---------- s ");
//		error_log(print_r($targetData, true));
//		error_log("targetData: ---------- e ");

		//return $result / sizeof($balanceSvTv);
		return $result;
	}

	/*public function getDotuPriceForTask($task, $userId, $targetId = null) {

		if ($targetId == null) {
			$targetId = $this->getTargetIdByTaskId($task->id);
		}

		// get target vector value
		$this->setId($targetId);
		$targetData = $this->getData();

		$balanceSvTv = $this->getSvTvBalance($userId);
		$stateUserData = $this->getStateUserData($balanceSvTv, $userId);
		$targetUserData = $this->getTargetUserData($balanceSvTv, $userId);


		if (sizeof($balanceSvTv) == 0) {
			$result = $this->_getDotuPriceForTask($targetData, $userId);
			return $result;
		}

		$balanceHourPrice = $this->getBalanceHourPrice($targetData, $userId);
		error_log("balanceHourPrice: " . $balanceHourPrice);
		$diffHourPrice = $targetData->hourprice - $balanceHourPrice;
		

		$svTvAvg = $this->getSvTvAvg($balanceSvTv, $stateUserData, $targetUserData);

		$addBalanceHourPrice = $diffHourPrice * $svTvAvg;


		$result = $balanceHourPrice + $addBalanceHourPrice;
		
		error_log("result: " . $result);
		error_log("userId " . $userId);
		error_log("targetId " . $targetId);
		error_log("svTvAvg " . $svTvAvg);
		error_log("--------------");
		error_log(print_r($task, true));
		
		return $result;
	}*/


	public function getDotuPriceForTask($task, $userId, $targetId = null) {

		if ($targetId == null) {
			$targetId = $this->getTargetIdByTaskId($task->id);
		}

		// get target vector value
		$this->setId($targetId);
		$targetData = $this->getData();

		$balanceSvTv = $this->getSvTvBalance($userId);
		if (sizeof($balanceSvTv) == 0) {
			$result = $this->_getDotuPriceForTask($targetData, $userId);
			return $result;
		}
		$stateUserData = $this->getStateUserData($balanceSvTv, $userId);
		$targetUserData = $this->getTargetUserData($balanceSvTv, $userId);

		$svTvAvg = $this->getSvTvAvg($balanceSvTv, $stateUserData, $targetUserData);


		$balanceHourPrice = $this->getBalanceHourPrice($targetData, $userId);

		$result = $balanceHourPrice * $svTvAvg;
		
		
		return $result;
	}

	public function getDotuPriceTasks() {
		$query = "select a.* from #__teamtime_task as a
      left join #__teamtimecareer_task_price as p on a.id = p.id
      where p.price = 1";

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		

		return $rows;
	}

	public function getMaxDotuPrice($userId) {
		$result = 0;

		//!!! error_log("\n\ngetMaxDotuPrice");

		$rows = $this->getDotuPriceTasks();
		if (!$rows) {
			return $result;
		}

		foreach ($rows as $task) {
			$price = $this->getDotuPriceForTask($task, $userId);

			if ($price > $result) {
				$result = $price;
			}
		}

		return $result;
	}

	/* function getVectorValueForParent($target_id) {
	  $result = 0;
	  $params = array();

	  $tree = $this->getTree($params, $target_id);
	  $items = $this->flattenTree($tree);
	  foreach ($items as $target) {
	  $this->setId($target->id);

	  $tmp = $this->getData();

	  $result += $tmp->num;
	  }

	  return $result;
	  } */

	public function getSkills($targetId = null) {
		if ($targetId == null) {
			$targetId = $this->_id;
		}
		if ($targetId == null) {
			return null;
		}

		$query = "select * from #__teamtimecareer_targetvector as a
      where parent_id = " . (int) $targetId . " and is_skill = 1";

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();

		return $rows;
	}

	public function getSkillsForTargets() {
		$result = array("fakeitem" => "");

		$query = "select * from #__users as u";
		$this->_db->setQuery($query);
		$userRows = $this->_db->loadObjectList();
		if (!$userRows) {
			return $result;
		}

		foreach ($userRows as $user) {
			$skillsCounts = array("fakeitem" => "");

			foreach ($this->getTargetBalance($user->id) as $targetId => $num) {
				$skillsCounts["t" . $targetId] = sizeof($this->getSkills($targetId));
			}

			$result["u" . $user->id] = $skillsCounts;
		}

		return $result;
	}

	//
	// ordering methods
	//

	public function orderItem($item, $movement) {
		$row = & $this->getTable();
		$row->load($item);

		if (!$row->move($movement, 'parent_id = ' . (int) $row->parent_id)) {
			$this->setError($row->getError());
			return false;
		}

		return true;
	}

	public function setOrder($items) {
		$total = count($items);
		$row = & $this->getTable();
		$groupings = array();

		$order = JRequest::getVar('order', array(), 'post', 'array');
		JArrayHelper::toInteger($order);

		// update ordering values
		for ($i = 0; $i < $total; $i++) {
			$row->load($items[$i]);
			// track parents
			$groupings[] = $row->parent_id;
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$this->setError($row->getError());
					return false;
				}
			} // if
		} // for
		// execute updateOrder for each parent group
		$groupings = array_unique($groupings);
		foreach ($groupings as $group) {
			$row->reorder('parent_id = ' . (int) $group);
		}

		return true;
	}

}