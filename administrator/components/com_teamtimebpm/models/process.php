<?php

class TeamtimebpmModelProcess extends Core_Joomla_Manager {

	public $_table = 'teamtimebpmprocess';

	public function store($data) {
		$row = & $this->getTable($this->_table);

		// bind the form fields
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// remove spaces in tag list
		$row->tags = str_replace(" ", "", $row->tags);

		// save modified data
		$datenow = & JFactory::getDate();
		$row->modified = $datenow->toMySQL();

		$user = & JFactory::getUser();
		$row->modified_by = $user->get("id");

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

		$this->_data = $row;

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
			else {
				foreach ($cid as $id) {
					$this->removeDiagram($id);
					$this->removeProcessLinks($id);
					$this->removeFollowed($id);
				}
			}
		}

		return true;
	}

	public function getModifiedUserName($user_id = null) {
		if ($user_id == null) {
			$user_id = $this->modified_by;
		}

		$query = 'select * from #__users
			where id = ' . (int) $user_id;
		$this->_db->setQuery($query);

		$row = $this->_db->loadObject();

		return $row->name;
	}

	public function getTags($stags) {
		$result = array();

		foreach (explode(",", $stags) as $tag) {
			$s = trim($tag);
			if ($s != "") {
				$result[] = $s;
			}
		}

		return $result;
	}

	public function getAllTags() {
		$table = & $this->getTable($this->_table);
		$result = array();

		$query = 'select * from ' . $table->getTableName();
		$this->_db->setQuery($query);

		foreach ($this->_db->loadObjectList() as $row) {
			foreach ($this->getTags($row->tags) as $tag) {
				$result[$tag] = 1;
			}
		}

		return array_keys($result);
	}

	public function removeTag($tag, $id) {
		$this->setId($id);
		$data = $this->getData($id);

		$result = $this->getTags($data->tags);
		$i = array_search($tag, $result);
		if ($i !== false) {
			unset($result[$i]);
		}
		$data->tags = implode(",", $result);

		$this->store($data);

		return $data->tags;
	}

	public function appendTag($tags, $id) {
		if (!$id) {
			return null;
		}

		$this->setId($id);
		$data = $this->getData($id);

		$result = array();
		foreach ($this->getTags($data->tags) as $tag) {
			$result[$tag] = 1;
		}
		foreach (explode(",", $tags) as $tag) {
			$result[$tag] = 1;
		}

		$data->tags = implode(",", array_keys($result));

		$this->store($data);

		return $data->tags;
	}

	public function setDiagram($id, $data) {
		if (!$id) {
			return;
		}

		$data = $this->_db->Quote($data);
		$query = "insert into `#__teamtimebpm_processdiagram`
      (process_id, `data`)
      values(" . (int) $id . ", " . $data . ")
      on duplicate key update `data` = " . $data;

		$this->_db->Execute($query);
	}

	public function getDiagram($id) {
		$result = "";

		$query = "select * from #__teamtimebpm_processdiagram
			where process_id = " . (int) $id;

		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();
		if ($row) {
			$result = $row->data;
		}

		return $result;
	}

	public function removeDiagram($id) {
		$result = "";

		// remove todos
		$diagramData = json_decode($this->getDiagram($id));

		if (isset($diagramData->figures)) {
			$mTodo = new TeamtimeModelTodo();

			$ids = $this->getTodoIds($diagramData->figures);
			//error_log(print_r($ids, true));

			$mTodo->delete($ids);
		}

		// remove diagram
		$query = "delete from #__teamtimebpm_processdiagram
			where process_id = " . (int) $id;

		$this->_db->setQuery($query);
		$result = $this->_db->query();

		return $result;
	}

	public function getTodoIds($figures = array()) {
		$result = array();

		foreach ($figures as $i => $figure) {
			if ($figure->type != "bpmn.Activity") {
				continue;
			}

			if (isset($figure->paramsData) && isset($figure->paramsData->_id)) {
				$result[] = $figure->paramsData->_id;
			}

			if ($figure->children && sizeof($figure->children) > 0) {
				$result = array_merge($result, $this->getTodoIds($figure->children));
			}
		}

		return $result;
	}

	public function getProcessTodoIds($processId) {
		$result = array();

		$query = "select * from #__teamtimebpm_todo
			where process_id = " . (int) $processId;

		//error_log($query);

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		if ($rows) {
			foreach ($rows as $row) {
				$result[] = $row->todo_id;
			}
		}

		return $result;
	}

	public function removeProcessLinks($parentId) {
		$query = "delete from `#__teamtimebpm_processlink`
      where parent_id = " . (int) $parentId;
		$this->_db->Execute($query);
	}

	public function linkProcessTo($parentId, $processId) {
		if (!$parentId || !$processId) {
			return;
		}

		$query = "insert into `#__teamtimebpm_processlink`
      (parent_id, process_id)
      values(" . (int) $parentId . ", " . (int) $processId . ")";
		$this->_db->Execute($query);
	}

	public function getParentProcess($processId) {
		$table = & $this->getTable($this->_table);
		$result = null;

		$query = "select * from `#__teamtimebpm_processlink`
      where process_id = " . (int) $processId;

		//error_log($query);

		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();

		// process link exists
		if ($row) {
			$query = "select * from " . $table->getTableName() . "
				where id = " . $row->parent_id;

			//error_log($query);

			$this->_db->setQuery($query);
			$result = $this->_db->loadObject();
		}

		return $result;
	}

	public function getLinkedProcesses($parentId, $result = array()) {
		$query = "select * from `#__teamtimebpm_processlink`
      where parent_id = " . (int) $parentId;

		//error_log($query);

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		if (sizeof($rows) > 0) {
			foreach ($rows as $row) {
				if (!in_array($row->process_id, $result)) {
					$result[] = $row->process_id;
					$result = $this->getLinkedProcesses($row->process_id, $result);
				}
			}
		}

		return $result;
	}

	protected function sortProcessOptionsByName($a, $b) {
		return strcmp($a->text, $b->text);
	}

	public function addParentLinkedProcesses($rows) {
		$result = array();

		foreach ($rows as $row) {
			$parentProcess = $this->getParentProcess($row->value);
			if ($parentProcess) {
				if (!isset($result[$parentProcess->id])) {
					$tmp = new stdClass();
					$tmp->value = $parentProcess->id;
					$tmp->text = $parentProcess->name;
					$result[$parentProcess->id] = $tmp;
				}
			}
		}

		$result = array_merge($rows, array_values($result));
		usort($result, array($this, "sortProcessOptionsByName"));

		return $result;
	}

	public function makeTodos($figures, $parentId = null, $changedFigures = array(), $processId = null) {
		foreach ($figures as $i => $figure) {
			if ($figure->type != "bpmn.Activity") {
				continue;
			}

			if ($figure->viewState == "linked") {
				// remove _id for linked blocks
				if (isset($figure->paramsData)) {
					unset($figure->paramsData->_id);
				}
				$this->linkProcessTo($processId, $figure->paramsData->linkedId);
				continue;
			}

			// process other blocks
			if (isset($figure->paramsData)) {
				$params = $figure->paramsData;
			}
			else {
				$params = new stdClass();
				$params->name = $figure->text;
				$params->description = "";
				$params->user = 0;
				$params->state = 0;
				$params->project = 0;
				$params->task = 0;
				$params->type = 0;
				$params->target = 0;
				$params->hoursPlan = 0;
				$params->hourlyRate = 0;
				$params->costs = 0;
				$params->sendmail = 0;
				$params->clientmail = 0;
				$params->showSkills = 0;
				$params->markHoursPlan = 0;
				$params->markExpenses = 0;
			}

			// create todo
			$mTodo = new TeamtimeModelTodo();
			$data = array(
				"title" => $params->name,
				"description" => $params->description,
				"user_id" => $params->user,
				"state" => $params->state,
				"project_id" => $params->project,
				"task_id" => $params->task,
				"type_id" => $params->type,
				"target_id" => $params->target,
				"hours_plan" => round($params->hoursPlan / 60, 2),
				"hourly_rate" => $params->hourlyRate,
				"costs" => $params->costs,
				"sendmail" => $params->sendmail,
				"clientmail" => $params->clientmail,
				"showskills" => $params->showSkills,
				"mark_hours_plan" => $params->markHoursPlan,
				"mark_expenses" => $params->markExpenses,
				"created" => isset($params->created) ? $params->created : date("Y-m-d H:i:s"),
				//"ignore_offset" => true
				"process_id" => $processId
			);

			// set parent for todo
			if ($parentId) {
				$data["curtodoid"] = $parentId;
			}

			// set current id of existing todo
			if (isset($params->_id)) {
				$data["id"] = $params->_id;
				$isChanged = isset($changedFigures->{$figure->id}) &&
						$changedFigures->{$figure->id}->_action == "changed";

				if ($parentId) {
					$mTodo->setParentTodo($data["id"], $data["curtodoid"]);
				}
				else {
					$mTodo->deleteParentTodo($data["id"]);
				}
			}
			else {
				//$data["state"] = TODO_STATE_PROJECT;
				$isChanged = true;
			}

			if ($isChanged) {
				$mTodo->store($data);
				$params->_id = $mTodo->_data->id;

				//error_log("save changes for " . $params->_id);
			}
			$figures[$i]->paramsData = $params;

			// process children todos
			if ($figure->children && sizeof($figure->children) > 0) {
				$figures[$i]->children = $this->makeTodos(
						$figure->children, $params->_id, $changedFigures, $processId);
			}
		}

		return $figures;
	}

	public function removeTodos($changedFigures = array()) {
		$mTodo = new TeamtimeModelTodo();
		$ids = array();

		foreach (get_object_vars($changedFigures) as $figure) {
			if ($figure->_action && $figure->_action == "delete" && $figure->_id) {
				$ids[] = $figure->_id;
			}
		}

		//error_log(print_r($ids, true));

		$mTodo->delete($ids);
	}

	public function makeTodosFromDiagram($diagram, $processId) {
		$result = $diagram;

		$diagramData = json_decode($result);
		if (!$diagramData->figures) {
			return $result;
		}

		$this->removeProcessLinks($processId);
		$diagramData->figures = $this->makeTodos(
				$diagramData->figures, null, $diagramData->changed, $processId);
		$this->removeTodos($diagramData->changed);
		unset($diagramData->changed);

		$result = json_encode($diagramData);

		return $result;
	}

	public function getBlocks($id, $calcStat = false, $calcLinked = false) {
		$todosStat = new stdClass();
		$todosStat->totalPrice = 0;
		$todosStat->totalHoursPlan = 0;
		$todosStat->totalHoursFact = 0;
		$todosStat->totalOperations = 0;
		$todosStat->totalUsers = 0;
		$result = array(array(), $todosStat);

		$diagram = $this->getDiagram($id);
		if ($diagram == "") {
			return $result;
		}

		$data = json_decode($diagram);
		if (!isset($data->figures)) {
			return $result;
		}

		$ids = array();
		$linkedBlocks = array();
		foreach ($data->figures as $figure) {
			if (isset($figure->paramsData)) {
				if (isset($figure->paramsData->_id) && $figure->paramsData->_id) {
					$ids[] = $figure->paramsData->_id;
				}
				else if ($calcLinked && isset($figure->paramsData->linkedId) &&
						$figure->paramsData->linkedId) {
					$linkedBlocks[$figure->paramsData->linkedId] = $figure->paramsData;
				}
			}
		}
		if (sizeof($ids) > 0) {
			$mTodo = new TeamtimeModelTodo();
			$todos = $mTodo->getTodosByTree(array("ids" => $ids));
			$todos = $mTodo->initTodosPrice($todos);
		}
		else {
			$todos = array();
		}

		if ($calcLinked && sizeof($linkedBlocks) > 0) {
			foreach (array_keys($linkedBlocks) as $processId) {
				$processInfo = $this->getProcessStateInfo($processId);

				$tmp = new stdClass();
				$tmp->title = $linkedBlocks[$processId]->name;
				$tmp->created = $processInfo->date;
				$tmp->price = $processInfo->price;
				$tmp->hours_plan = $processInfo->plan;
				$tmp->hours_fact = $processInfo->fact;
				$tmp->user_id = null;
				$tmp->project_name = "";
				$tmp->type_name = "";
				$tmp->user_name = "";

				$todos[] = $tmp;
			}

			//usort($todos, array($mTodo, "sortTodoByCreated"));
		}

		// calculate stat
		if ($calcStat) {
			$todosStat->totalPrice = 0;
			$todosStat->totalOperations = sizeof($todos);
			$users = array();
			foreach ($todos as $todo) {
				$todosStat->totalPrice += $todo->price;
				$todosStat->totalHoursPlan += $todo->hours_plan;
				$todosStat->totalHoursFact += $todo->hours_fact;

				if ($todo->user_id) {
					$users[$todo->user_id] = true;
				}
			}
			$todosStat->totalPrice = round($todosStat->totalPrice);
			$todosStat->totalHoursPlan = round($todosStat->totalHoursPlan, 2);
			$todosStat->totalHoursFact = round($todosStat->totalHoursFact, 2);
			$todosStat->totalUsers = sizeof(array_keys($users));
		}

		return array($todos, $todosStat);
	}

	public function startTodos($id) {
		$mTodo = new TeamtimeModelTodo();
		list($todos, $stat) = $this->getBlocks($id);

		foreach ($todos as $todo) {
			if ($todo->state == TODO_STATE_PROJECT) {
				$todo->state = TODO_STATE_OPEN;
				$mTodo->store($todo);
			}
		}
	}

	public function stopTodos($id) {
		$mTodo = new TeamtimeModelTodo();
		list($todos, $stat) = $this->getBlocks($id);

		foreach ($todos as $todo) {
			$logsNum = $mTodo->getLogs(array("todo_id" => $todo->id, "count" => true));

			if ($logsNum == 0) {
				$todo->state = TODO_STATE_PROJECT;
				$mTodo->store($todo);
			}
		}
	}

	public function _removeTodoIds($figures) {
		foreach ($figures as $i => $figure) {
			if ($figure->type != "bpmn.Activity") {
				continue;
			}

			// reset _id
			if (isset($figure->paramsData) && isset($figure->paramsData->_id)) {
				unset($figures[$i]->paramsData->_id);
			}

			// process children todos
			if ($figure->children && sizeof($figure->children) > 0) {
				$figures[$i]->children = $this->_removeTodoIds($figure->children);
			}
		}

		return $figures;
	}

	public function removeTodoIds($diagram) {
		$result = $diagram;

		$diagramData = json_decode($result);
		if (!isset($diagramData->figures)) {
			return $result;
		}

		$diagramData->figures = $this->_removeTodoIds($diagramData->figures);
		$result = json_encode($diagramData);

		return $result;
	}

	public function getProcessState($id) {
		$result = "";

		$mTodo = new TeamtimeModelTodo();

		list($todos, $tmp) = $this->getBlocks($id);

		$numDone = 0;
		foreach ($todos as $todo) {
			$res = $this->getTodoStateInfo($todo);
			if ($res[0] == "error") {
				$result = "error";
				break;
			}

			if ($res[0] == "done") {
				$numDone++;
			}
		}

		if ($numDone == sizeof($todos)) {
			$result = "done";
		}

		return $result;
	}

	public function getProcesses($filter = array()) {
		$table = & $this->getTable($this->_table);

		$where = array();

		$query = "select a.*, p.name as project_name from " . $table->getTableName() . " a
			left join #__teamtime_project p on a.project_id = p.id";

		if (isset($filter["space_id"])) {
			$where[] = "a.space_id = " . (int) $filter["space_id"];
		}

		if (isset($filter["name"])) {
			$where[] = 'LOWER(a.name) LIKE ' .
					$this->_db->Quote('%' . $this->_db->getEscaped($filter["name"], true) . '%', false);
		}

		$acl = new TeamTime_Acl();
		$projectId = $acl->filterUserProjectIds($projectId);
		if ($projectId !== null) {
			$where[] = 'a.project_id in (' . implode(",", $projectId) . ")";
		}

		if (sizeof($where) > 0) {
			$query .= " where " . implode(" and ", $where);
		}

		$query .= " order by a.name";

		//error_log($query);

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();

		return $rows ? $rows : array();
	}

	public function getTodoData($todo_id) {
		$query = "select * from #__teamtimebpm_todo a
			where a.todo_id = " . (int) $todo_id;

		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();

		return $row;
	}

	public function deleteTodoData($todo_id) {
		$query = "delete from `#__teamtimebpm_todo`
      where todo_id = " . (int) $todo_id;

		$this->_db->Execute($query);
	}

	public function setTodoData($data) {
		$todoId = (int) $data["todo_id"];
		$processId = (int) $data["process_id"];

		$query = "insert into `#__teamtimebpm_todo`
      (todo_id, process_id)
      values($todoId, $processId)
      on duplicate key update process_id = $processId";

		$this->_db->Execute($query);
	}

	public function getTodoStateInfo($row) {
		$mTodo = new TeamtimeModelTodo();

		$result = array("", "");

		// "error", "done", "done-part"

		if ($row->state == TODO_STATE_CLOSED) {
			$result[0] = "done";
			return $result;
		}

		if ($row->state != TODO_STATE_PROJECT) {
			$diff = strtotime(date("Y-m-d H:i:s")) - strtotime($row->created);
			$diff = $diff / (24 * 60 * 60);
			if ($diff > 7) {
				$result[0] = "error";
				return $result;
			}

			$logsNum = $mTodo->getLogs(array("todo_id" => $row->id, "count" => true));
			if ($logsNum > 0 || ($logsNum == 0 && $row->state == TODO_STATE_DONE)) {
				$result[0] = "done-part";
				$result[1] = "0%";

				if (!$row->is_parent) {
					$result[1] = min(round(($row->hours_fact / $row->hours_plan) * 100), 100) . "%";
				}
				else {
					$ids = array();
					foreach ($mTodo->treeToList($mTodo->getTree(array(), $row->id)) as $todo) {
						$ids[] = $todo->id;
					}
					if (sizeof($ids) > 0) {
						$sumPlan = 0;
						$sumPlanClosed = 0;
						foreach ($mTodo->getTodos(array("ids" => $ids)) as $todo) {
							$sumPlan += $todo->hours_plan;
							if ($todo->state == TODO_STATE_CLOSED) {
								$sumPlanClosed += $todo->hours_plan;
							}
						}
						$result[1] = min(round(($sumPlanClosed / $sumPlan) * 100), 100) . "%";
					}
				}
			}
		}

		return $result;
	}

	public function getProcessStateInfo($id) {
		$result = new stdClass();
		$result->state = "done-part";
		$result->part = "0%";
		$result->plan = 0;
		$result->fact = 0;
		$result->price = 0;
		$result->date = 0;

		list($todos, $todoInfo) = $this->getBlocks($id, true);

		$result->plan = $todoInfo->totalHoursPlan;
		$result->fact = $todoInfo->totalHoursFact;
		$result->price = $todoInfo->totalPrice;

		$numDone = 0;
		$sumPlanDone = 0;
		foreach ($todos as $todo) {
			$res = $this->getTodoStateInfo($todo);
			if ($res[0] == "error") {
				$result->state = "error";
				$result->part = "";
			}
			else if ($res[0] == "done") {
				$numDone++;
				$sumPlanDone += $todo->hours_plan;
			}

			// find last date
			$d = strtotime($todo->created);
			if ($result->date < $d) {
				$result->date = $d;
			}
		}

		if ($numDone == sizeof($todos)) {
			$result->state = "done";
			$result->part = "";
		}
		else if ($result->state != "error") {
			$result->part = round(($sumPlanDone / $result->plan) * 100);
			$result->part .= "%";
		}

		if ($result->date == 0) {
			$result->date = "0000-00-00";
		}

		return $result;
	}

	public function getBlocksInfo($figures, $typeInfo) {
		$mTodo = new TeamtimeModelTodo();

		$result = array();

		//error_log(print_r($figures, true));

		$data = array();
		$linkedData = array();
		foreach ($figures as $i => $figure) {
			if (isset($figure->_id)) {
				$data[$figure->_id] = array(
					"id" => $figure->id
				);
			}
			else if (isset($figure->linkedId)) {
				$linkedData[$figure->linkedId] = array(
					"id" => $figure->id
				);
			}
		}

		foreach ($mTodo->getTodos(array("ids" => array_keys($data))) as $row) {
			$params = new stdClass();
			switch ($typeInfo) {
				case "status";
					list($params->state, $params->part) = $this->getTodoStateInfo($row);
					break;

				case "plan";
					$params->plan = number_format($row->hours_plan, 2, ",", "");
					break;

				case "time";
					$params->plan = number_format($row->hours_plan, 2, ",", "");
					$params->fact = number_format($row->hours_fact, 2, ",", "");
					break;

				case "price";
					$params->price = number_format($row->hourly_rate * $row->hours_plan, 2, ",", "");
					break;

				case "performer";
					$params->userName = $row->user_name;
					break;

				case "date":
					$params->date = JHTML::_('date', $row->created, "%d") . " " .
							JText::_("STR_MONTH" . (int) JHTML::_('date', $row->created, "%m")) . " " .
							JHTML::_('date', $row->created, "%Y");
					break;

				default:
					break;
			}

			$tmp = new stdClass();
			$tmp->id = $data[$row->id]["id"];
			$tmp->_id = $row->id;
			$tmp->params = $params;

			$result[] = $tmp;
		}

		foreach ($linkedData as $processId => $v) {
			$params = new stdClass();
			$processInfo = $this->getProcessStateInfo($processId);

			switch ($typeInfo) {
				case "status";
					$params->state = $processInfo->state;
					$params->part = $processInfo->part;
					break;

				case "plan";
					$params->plan = number_format($processInfo->plan, 2, ",", "");
					break;

				case "time";
					$params->plan = number_format($processInfo->plan, 2, ",", "");
					$params->fact = number_format($processInfo->fact, 2, ",", "");
					break;

				case "price";
					$params->price = number_format($processInfo->price, 2, ",", "");
					break;

				case "performer";
					$params->userName = "";
					break;

				case "date":
					$params->date = JHTML::_('date', $processInfo->date, "%d") . " " .
							JText::_("STR_MONTH" . (int) JHTML::_('date', $processInfo->date, "%m")) . " " .
							JHTML::_('date', $processInfo->date, "%Y");
					break;

				default:
					break;
			}

			$tmp = new stdClass();
			$tmp->id = $v["id"];
			$tmp->linkedId = $processId;
			$tmp->params = $params;

			$result[] = $tmp;
		}

		return $result;
	}

	public function getOutputFigures($diagramData, $figureId) {
		$result = array();

		if (!isset($diagramData->connections)) {
			return $result;
		}

		$figureIds = array();
		foreach ($diagramData->connections as $conn) {
			if ($conn->source->figureId == $figureId) {
				$figureIds[] = $conn->target->figureId;
			}
		}

		foreach ($diagramData->figures as $figure) {
			if (in_array($figure->id, $figureIds)) {
				$result[] = $figure;
			}
		}

		return $result;
	}

	public function getDestUsers($todoId) {
		$result = array();

		$todoData = $this->getTodoData($todoId);
		if (!$todoData) {
			return $result;
		}

		// get diagram
		$diagram = $this->getDiagram($todoData->process_id);
		if ($diagram == "") {
			return $result;
		}
		$data = json_decode($diagram);
		if (!isset($data->figures)) {
			return $result;
		}

		// find figure for todo
		$todoFigure = null;
		foreach ($data->figures as $figure) {
			if (isset($figure->paramsData)) {
				if (isset($figure->paramsData->_id) && $figure->paramsData->_id
						&& $figure->paramsData->_id == $todoId) {
					$todoFigure = $figure;
					break;
				}
			}
		}
		if (!$todoFigure) {
			return $result;
		}

		$mTodo = new TeamtimeModelTodo();
		foreach ($this->getOutputFigures($data, $todoFigure->id) as $figure) {
			if (isset($figure->paramsData)) {
				if (isset($figure->paramsData->_id) && $figure->paramsData->_id) {
					$mTodo->setId($figure->paramsData->_id);
					$tmp = $mTodo->getData();
					if ($tmp->id) {
						// add user data
						if (!isset($result[$tmp->user_id])) {
							$result[$tmp->user_id] = $mTodo->getUser($tmp->user_id);
						}

						// add todos data for user
						$result[$tmp->user_id]->todos[$tmp->id] = $tmp;
					}
				}
			}
		}
		$result = array_values($result);

		return $result;
	}

	public function setFollowed($follow, $id, $userId = null) {
		if ($userId == null) {
			$user = &JFactory::getUser();
			$userId = $user->id;
		}

		$query = "insert into #__teamtimebpm_followprocess
			(process_id, user_id, follow)
      values(" . (int) $id . ", " . (int) $userId . ", " . (int) $follow . ")
      on duplicate key update follow = " . (int) $follow;

		$this->_db->Execute($query);
	}

	public function isFollowed($id, $userId = null) {
		$result = false;

		if ($userId == null) {
			$user = &JFactory::getUser();
			$userId = $user->id;
		}

		$query = "select * from #__teamtimebpm_followprocess
			where process_id = " . (int) $id . " and user_id = " . (int) $userId;

		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();
		if ($row) {
			$result = $row->follow;
		}

		return $result;
	}

	public function removeFollowed($id, $userId = null) {
		if ($userId == null) {
			$user = &JFactory::getUser();
			$userId = $user->id;
		}

		$query = "delete from #__teamtimebpm_followprocess
			where process_id = " . (int) $id . " and user_id = " . (int) $userId;

		$this->_db->Execute($query);
	}

	public function filterWithAllowedProjects($ids, $acl) {
		$result = array();

		foreach ($ids as $id) {
			$item = $this->getById($id);
			if (sizeof($acl->filterUserProjectIds(array($item->project_id))) > 0) {
				$result[] = $id;
			}
		}

		return $result;
	}

}
