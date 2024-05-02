<?php

class TeamTime_Helpers_Calendar {

	const ORDER_INDEX = 2;

	function __construct() {
//...
	}

	public function getLink($s, $url) {
		if (JPATH_BASE == JPATH_ADMINISTRATOR) {
			return "<a
				href='index.php?option=com_teamtimecalendar&controller=calendar&" .
					$url . "'>" . $s . "</a>";
		}

		return $s;
	}

	public function getAddonButton() {
		$lang = & JFactory::getLanguage();
		$lang->load("com_teamtimecalendar", JPATH_BASE);

		return TeamTime::helper()->getBase()->quickiconButton(
						'index.php?option=com_teamtimecalendar&controller=calendar&view_type=month',
						'components/com_teamtimecalendar/assets/images/icon-48-calendar.png',
						JText::_('TeamTime Calendar'));
	}

	public function addonMenuItem($controller) {
		JSubMenuHelper::addEntry(JText::_("Calendar"),
				"index.php?option=com_teamtimecalendar&controller=calendar&view_type=month",
				$controller == "calendar");
	}

	public function getTodosQueryPart($filter = array()) {
		$db = & JFactory::getDBO();
		$result = array(
			"fields" => "",
			"join" => "
				left join #__teamtimebpm_todo as pt on a.id = pt.todo_id
				left join #__teamtimebpm_process as proc on pt.process_id = proc.id
			",
			"where" => "((pt.todo_id is null) or
				proc.archived = " . $db->Quote($filter["archived"]) . ")"
		);

		return $result;
	}

	public function listCalendarByRange($sd, $ed) {
		$config = TeamTime::getConfig();
		$reqParams = JRequest::get();

		$ret = array();
		$ret['events'] = array();
		$ret["issort"] = true;

		// add timezone offset
		$jconfig = & JFactory::getConfig();
		$tzoffset = $jconfig->getValue('config.offset');
		$date = & JFactory::getDate(TeamTime_DateTools::php2MySqlTime($sd), $tzoffset);
		$sd = $date->toMySQL();
		$ret["start"] = TeamTime_DateTools::php2JsTime($date->toUnix());

		$date = & JFactory::getDate(TeamTime_DateTools::php2MySqlTime($ed), $tzoffset);
		$ed = $date->toMySQL();
		$ret["end"] = TeamTime_DateTools::php2JsTime($date->toUnix());

		$ret['error'] = null;
		try {
			$db = & JFactory::getDBO();

			$projectId = isset($reqParams["project_id"]) && $reqParams["project_id"] ?
					$reqParams["project_id"] : "";

			$type_id = isset($reqParams["type_id"]) && $reqParams["type_id"] ?
					$reqParams["type_id"] : "";

			$task_id = isset($reqParams["task_id"]) && $reqParams["task_id"] ?
					$reqParams["task_id"] : "";

			$user_id = isset($reqParams["user_id"]) && $reqParams["user_id"] ?
					$reqParams["user_id"] : "";

			/*
			  $costs_check = isset($req_params["costs_check"]) && $req_params["costs_check"] ?
			  $req_params["costs_check"] : "";
			 */
			$filterPeriod = isset($reqParams["filter_period"]) ? $reqParams["filter_period"] : "";

			$hidesuborders = isset($reqParams["hidesuborders"]) ? $reqParams["hidesuborders"] : false;

			$sqlFilter = array();

			if ($projectId != "") {
				$projectId = array($projectId);
			}
			else {
				$projectId = null;
			}
			$acl = new TeamTime_Acl();
			$projectId = $acl->filterUserProjectIds($projectId);
			if ($projectId !== null) {
				$sqlFilter[] = 'a.project_id in (' . implode(",", $projectId) . ")";
			}

			if ($type_id != "") {
				$sqlFilter[] = " a.type_id = " . (int) $type_id;
			}
			if ($task_id != "") {
				$task_id = $db->Quote($task_id);
				$sqlFilter[] = " b.name = " . $task_id;
			}
			if ($user_id != "") {
				$sqlFilter[] = " a.user_id = " . (int) $user_id;
			}

			/*
			  //expenses filter
			  if ($costs_check != "") {
			  if ($costs_check == "in_act")
			  $sql_filter[] = " (todo_formals.mark_expenses = 1 or todo_formals.mark_hours_plan = 1) ";
			  else if ($costs_check == "not_in_act")
			  $sql_filter[] = " (todo_formals.todo_id is null or
			  (todo_formals.mark_expenses = 0 and todo_formals.mark_hours_plan = 0)) ";
			  else
			  $sql_filter[] = " a.costs > 0";
			  }
			  $formals_sql = TeamTime::helper()->getFormals()->getSqlTodoExpenses();
			 */

			// period filter
			if ($filterPeriod != "") {
				if ($filterPeriod == "week") {
					$sqlFilter[] = "(p.repeat_mode = 'weekly' or rr.repeating_history = 'weekly')";
				}
				else if ($filterPeriod == "month") {
					$sqlFilter[] = "(p.repeat_mode = 'monthly' or rr.repeating_history = 'monthly')";
				}
				else if ($filterPeriod == "year") {
					$sqlFilter[] = "(p.repeat_mode = 'yearly' or rr.repeating_history = 'yearly')";
				}
				else if ($filterPeriod == "urgent") {
					$sqlFilter[] = "(p.todo_id is null and rr.todo_id is null)";
				}
			}

			$sqlFilter[] = "a.state != 4"; // TODO_STATE_PROJECTED

			// change to hide completed
			if ($hidesuborders) {
				//$sql_filter[] = "(tp.parent_id is null or a.is_parent = 1)";
				$sqlFilter[] = "a.state != 2"; // TODO_STATE_CLOSED

			}

			$todoModel = new TeamtimeModelTodo();
			$query = $todoModel->getTodosQuery(array(
				"archived" => "active",
				"start_date" => $sd,
				"end_date" => $ed
					), $sqlFilter);
			$db->setQuery($query);
			$result = $db->loadObjectList();

			foreach ($result as $row) {
				if ($row->todo_id) { // if repeated - use repeat date
					$row->created =
							date("Y-m-d", strtotime($row->repeat_date)) . " " .
							JHTML::_('date', $row->created, '%H:%M:%S');
				}
				else {
					$row->created = JHTML::_('date', $row->created, '%Y-%m-%d %H:%M:%S');
				}

				//$ret['events'][] = $row;
				//$attends = $row->AttendeeNames;
				//if($row->OtherAttendee){
				//  $attends .= $row->OtherAttendee;
				//}
				//echo $row->StartTime;

				if ($row->isalldayevent) {
					$hours_plan = floor(($row->hours_plan / 8) * 24 * 60 * 60) - 60;
				}
				else {
					$hours_plan = floor($row->hours_plan * 60 * 60);
				}

				$smin = round($hours_plan / 60 - floor(($hours_plan / 60) / 60) * 60);
				$stime = floor(($hours_plan / 60) / 60) . ":" . str_pad($smin, 2, "0", STR_PAD_LEFT);

				$color = $row->color;
				if ($row->state == TODO_STATE_CLOSED) {
					$color = "0";
				}
				else if ($row->state == TODO_STATE_PROJECT) {
					$color = "9";
				}

				$tmp = array(
					$row->id,
					$row->title,
					TeamTime_DateTools::php2JsTime(TeamTime_DateTools::mySql2PhpTime($row->created,
									$row->isalldayevent)),
					TeamTime_DateTools::php2JsTime(TeamTime_DateTools::mySql2PhpTime($row->created,
									$row->isalldayevent) + $hours_plan),
					$row->isalldayevent,
					0, //more than one day event
					0, //$row->InstanceType,//Recurring event

					$color,
					//$row->state == 2? "0" :
					//	($row->costs > 0? "1" : $row->color),

					1, //editable
					$row->project_name, //$row->Location,
					$row->user_name, //$attends,

					$stime, //plan
					$row->costs ? ($row->costs . ' ' . $config->currency) : '', //expenses

					$row->todo_id
				);

				$ret['events'][] = $tmp;
			}
		}
		catch (Exception $e) {
			$ret['error'] = $e->getMessage();
		}

		return $ret;
	}

	public function listCalendar($day, $type) {
		$phpTime = TeamTime_DateTools::js2PhpTime($day);

		switch ($type) {
			case "month":
				//$st = mktime(0, 0, 0, date("m", $phpTime), 1, date("Y", $phpTime));
				//$et = mktime(0, 0, -1, date("m", $phpTime)+1, 1, date("Y", $phpTime));

				$st = mktime(0, 0, 0, date("m", $phpTime), -7, date("Y", $phpTime));
				$et = mktime(0, 0, -1, date("m", $phpTime) + 1, 7, date("Y", $phpTime));

				break;
			case "week":
				//suppose first day of a week is monday
				$monday = date("d", $phpTime) - date('N', $phpTime) + 1;
				//echo date('N', $phpTime);
				$st = mktime(0, 0, 0, date("m", $phpTime), $monday, date("Y", $phpTime));
				$et = mktime(0, 0, -1, date("m", $phpTime), $monday + 7, date("Y", $phpTime));
				break;

			case "day":
				$st = mktime(0, 0, 0, date("m", $phpTime), date("d", $phpTime), date("Y", $phpTime));
				$et = mktime(0, 0, -1, date("m", $phpTime), date("d", $phpTime) + 1, date("Y", $phpTime));
				break;
		}

		return $this->listCalendarByRange($st, $et);
	}

	public function checkTaskItem($query, $value) {
		$db = & JFactory::getDBO();

		$db->setQuery($query);
		$result = $db->loadObjectList();
		foreach ($result as $row) {
			if ($row->value == $value)
				return true;
		}

		return false;
	}

	public function getCalendarByRange($id) {
		$db = & JFactory::getDBO();

		$query = "select * from #__teamtime_todo
			where id = " . $id;
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result[0];
	}

	public function getTaskId($task, $type, $project) {
		if ($task == "" || $type == "") {
			return "";
		}

		$db = & JFactory::getDBO();
		$task = $db->Quote($task);
		$query = "SELECT * from #__teamtime_task
			where project_id = " . (int) $project .
				" and type_id = " . (int) $type . " and name = " . $task;
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows[0]->id;
	}

	private function notAllowedResult() {
		$result = array();
		$result['IsSuccess'] = false;
		$result['Msg'] = JText::_('Access denied');

		return $result;
	}

	private function isAllowed($id) {
		$acl = new TeamTime_Acl();
		return $acl->isAllowByProject(array($id), "todo", "teamtime");
	}

	public function addDetailedCalendar($st, $et, $sub, $ade, $dscr, $loc, $color, $tz, $id = null) {
		$result = array();

		$description = JRequest::getVar('descr', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post = JRequest::get('post');
		if ($id != null) {
			$post['id'] = $id;
			//$post['ignore_offset'] = true;
		}
		if (!$this->isAllowed($post["id"])) {
			return $this->notAllowedResult();
		}

		$post['description'] = $description;
		$post['title'] = $post['Subject'];
		$time = explode(":", $et);
		$post['hours_plan'] = round(($time[0] * 60 + $time[1]) / 60, 2);

		// reset event - as one day
		if ($post['hours_plan'] <= 7) {
			$post['isalldayevent'] = 0;
		}

		$post["color"] = $color;
		if ($post["state"] == 2) {
			$post["color"] = 0;
		}

		if (trim($post["hourly_rate"]) == "") {
			$todo = new Todo();
			$params = array();
			if ($post["user_id"]) {
				$params["user_id"] = $post["user_id"];
			}
			if ($post["task_id"]) {
				$params["task_id"] = $post["task_id"];
			}
			if ($post["project_id"]) {
				$params["project_id"] = $post["project_id"];
			}
			$post["hourly_rate"] = $todo->getHourlyRateByParams($params, 0);
		}

		// set type_id from task
		$mTask = new TeamtimeModelTask();
		$task = $mTask->getById($post['task_id']);
		$post["type_id"] = $task->type_id;

		$post["created"] = TeamTime_DateTools::php2MySqlTime(
						TeamTime_DateTools::js2PhpTime($st));
		$post["saveFromCalendar"] = true;

		$mTodo = new TeamtimeModelTodo();
		$msg = "";
		if ($mTodo->store($post)) {
			$result['IsSuccess'] = true;

			if ($id == null) { // add
				$result['Msg'] = JText::_('Todo Saved');
				$result['Data'] = $mTodo->_data->id;
			}
			else { // edit
				$result['IsSuccess'] = true;
				$result['Msg'] = 'Succefully';
			}
		}
		else {
			$result['IsSuccess'] = false;
			$result['Msg'] = JText::_('Error Saving Todo');
		}

		return $result;
	}

	public function updateDetailedCalendar($id, $st, $et, $sub, $ade, $dscr, $loc, $color, $tz) {
		return $this->addDetailedCalendar(
						$st, $et, $sub, $ade, $dscr, $loc, $color, $tz, $id);
	}

	public function removeCalendar($id) {
		if (!$this->isAllowed($id)) {
			return $this->notAllowedResult();
		}

		$result = array();
		try {
			$todo = new TeamtimeModelTodo();
			$res = $todo->delete(array((int) $id));

			if (!$res) {
				$result['IsSuccess'] = false;
				$result['Msg'] = mysql_error();
			}
			else {
				$result['IsSuccess'] = true;
				$result['Msg'] = 'Succefully';
			}
		}
		catch (Exception $e) {
			$result['IsSuccess'] = false;
			$result['Msg'] = $e->getMessage();
		}
		return $result;
	}

	public function updateCalendar($id, $st, $et) {
		if (!$this->isAllowed($id)) {
			return $this->notAllowedResult();
		}

		$config = & JFactory::getConfig();
		$offset = $config->getValue('config.offset');

		$ret = array();
		try {
			$st = TeamTime_DateTools::php2MySqlTime(
							TeamTime_DateTools::js2PhpTime($st) - $offset * 60 * 60);

			$db = & JFactory::getDBO();
			$query = "update #__teamtime_todo
				set created = '" . $st . "'
				where `id` = " . $id;

			$res = $db->Execute($query);

			if (!$res) {
				$ret['IsSuccess'] = false;
				$ret['Msg'] = mysql_error();
			}
			else {
				$ret['IsSuccess'] = true;
				$ret['Msg'] = 'Succefully';
			}
		}
		catch (Exception $e) {
			$ret['IsSuccess'] = false;
			$ret['Msg'] = $e->getMessage();
		}

		return $ret;
	}

	public function todoTree($params) {
		if (!$this->isAllowed($params["id"])) {
			return;
		}

		$todo = new TeamtimeModelTodo();
		$todoData = $todo->getById($params["id"]);
		$repeatHoursStr = JText::_("NUM_HOURS");

		// get date range
		$phpTime = strtotime($params["date"]);
		switch ($params["view"]) {
			case "day":
			case "month":
				$startRange = mktime(0, 0, 0, date("m", $phpTime), 1, date("Y", $phpTime));
				$endRange = mktime(23, 59, 59, date("m", $phpTime), date("t", $phpTime), date("Y", $phpTime));
				$repeatNumStr = JText::_("NUM_PER_MONTH");
				break;

			case "week":
				// suppose first day of a week is monday
				$monday = date("d", $phpTime) - date('N', $phpTime) + 1;
				$startRange = mktime(0, 0, 0, date("m", $phpTime), $monday, date("Y", $phpTime));
				$endRange = mktime(0, 0, -1, date("m", $phpTime), $monday + 7, date("Y", $phpTime));
				$repeatNumStr = JText::_("NUM_PER_WEEK");
				break;
		}

		$data = array();
		if ($todoData->is_parent) {
			$list = $todo->treeToList($todo->getTree(array(), $todoData->id));
			$listExtended = $todo->getDataForTreelist($list);

			// get total
			$total1 = $todoData->hours_fact;
			$total2 = $todoData->hours_plan;

			$html = '<div id="orders-list" style="display:none;"><br>
				<table id="orders-table" border="0" cellspacing="0" cellpadding="0">';
			foreach ($list as $item) {
				$userData = $todo->getUser($listExtended[$item->id]->user_id);
				$closedStr = $listExtended[$item->id]->state == TODO_STATE_CLOSED ?
						'<img src="' . JURI::root(true) .
						'/media/com_teamtimecalendar/assets/images/calendar_check.png" alt="" />' : '';
				$parentStr = $listExtended[$item->id]->is_parent ? ' bold' : '';

				$repeatParams = $todo->get_repeat_params($item->id);
				if ($repeatParams) {
					// get repeat hours
					$startRange = date("Y-m-d H:i:s", $startRange);
					$endRange = date("Y-m-d H:i:s", $endRange);
					$repeatNum = $todo->get_repeat_num($item->id, $startRange, $endRange);
					$repeatStr = "(" .
							$listExtended[$item->id]->hours_plan . " " . $repeatHoursStr . " x " .
							$repeatNum . " " . $repeatNumStr . ")";

					$total1 += $listExtended[$item->id]->hours_fact;
					$total2 += $listExtended[$item->id]->hours_plan * $repeatNum;
					$timeStr =
							DateHelper::formatTimespan($listExtended[$item->id]->hours_fact * 60, 'h:m')
							. "&nbsp;/&nbsp;"
							. DateHelper::formatTimespan(
									$listExtended[$item->id]->hours_plan * 60 * $repeatNum, 'h:m');
				}
				else {
					// get normal hours
					$repeatStr = "";
					$total1 += $listExtended[$item->id]->hours_fact;
					$total2 += $listExtended[$item->id]->hours_plan;
					$timeStr =
							DateHelper::formatTimespan($listExtended[$item->id]->hours_fact * 60, 'h:m')
							. "&nbsp;/&nbsp;"
							. DateHelper::formatTimespan($listExtended[$item->id]->hours_plan * 60, 'h:m');
				}

				$html .= '<tr class="sublevel' . $item->level . $parentStr . '">
					<td class="order-name">
						<span class="hasTip" title="' . $userData->name . '">
						<a href="#' . $item->id . '" class="open_edit_todo" onclick="return false;">'
						. $item->title .
						'</a>
						</span>' .
						' <span class="repeat_str">' . $repeatStr . '</span>' .
						'</td>
					<td class="order-done" valign="top">' . $closedStr . '</td>
					<td class="order-time" valign="top">' . $timeStr . '</td>
				</tr>';
			}
			$html .= '</table>
					<div id="bbit-cs-treeLink2" class="lk">' .
					JText::_("Add child todo") . '
					</div>
				</div>';

			$data[0] = JText::_("Children todos") . ': ' . sizeof($list);
			$data[1] = $html;

			$total = DateHelper::formatTimespan($total1 * 60, 'h:m')
					. "&nbsp;/&nbsp;"
					. DateHelper::formatTimespan($total2 * 60, 'h:m');
		}
		else {
			$data[0] = JText::_("Add child todo") . ' &gt;&gt;';
			$data[1] = '';

			$total = DateHelper::formatTimespan($todoData->hours_fact * 60, 'h:m')
					. "&nbsp;/&nbsp;"
					. DateHelper::formatTimespan($todoData->hours_plan * 60, 'h:m');
		}

		$data[2] = $total;

		return $data;
	}

	public function todoInfo($params) {
		if (!$this->isAllowed($params["id"])) {
			return;
		}

		$mTodo = new TeamtimeModelTodo();
		$todoData = $mTodo->getById($params["id"]);

		$parentId = $mTodo->getParentTodo($todoData->id);
		if ($parentId) {
			$parentTodoData = $mTodo->getById($parentId);
			print JText::_("Is in the order") . ': ' . $parentTodoData->title . "<br>";
		}

		$user = $mTodo->getUser($todoData->user_id);
		$result = $user->name . " (" . $todoData->hours_fact . " / " .
				$todoData->hours_plan . " часов)";

		$res = TeamTime::helper()->getBpmn()->initTodoData(array($todoData));
		if (!$res instanceof TeamTime_Undefined) {
			$todoData = $res[0];
			if ($todoData->processName != "") {
				if ($todoData->spaceName != "") {
					$title = $todoData->spaceName . " / " . $todoData->processName;
				}
				else {
					$title = $todoData->processName;
				}

				$escTitle = htmlspecialchars($title);
				$result .= '<div class="calendar-process-icon">
					<a class="calendar-process-link"
						target="_blank" href="' . $todoData->processUrl . '"
						title="' . $escTitle . '"><img alt="' . $escTitle . '"
						title="' . $escTitle . '" src="' . JURI::root(true) .
						'/media/com_teamtimebpm/assets/images/project.png">
						' . JText::_('Process') . '</a>
					</div>';
			}
		}

		return $result;
	}

	public function saveTodo($getParams, $postParams) {
		$id = $getParams["id"];
		$st = $postParams["stpartdate"] . " " . $postParams["stparttime"];
		$et = $postParams["etparttime"];

		if (!isset($postParams["IsAllDayEvent"])) {
			$postParams["IsAllDayEvent"] = 0;
		}
		if (!isset($postParams["Description"])) {
			$postParams["Description"] = "";
		}
		if (!isset($postParams["Location"])) {
			$postParams["Location"] = "";
		}
		if (!isset($postParams["colorvalue"])) {
			$postParams["colorvalue"] = "";
		}
		if (!isset($postParams["timezone"])) {
			$postParams["timezone"] = "";
		}

		if (isset($getParams["copy"]) && $getParams["copy"] == "1") {
			// save as copy
			$result = $this->addDetailedCalendar(
					$st, $et, $postParams["Subject"], $postParams["IsAllDayEvent"] ? 1 : 0,
					$postParams["Description"], $postParams["Location"], $postParams["colorvalue"],
					$postParams["timezone"]);
		}
		else if ($id) {
			// save
			$result = $this->updateDetailedCalendar(
					$id, $st, $et, $postParams["Subject"], $postParams["IsAllDayEvent"] ? 1 : 0,
					$postParams["Description"], $postParams["Location"], $postParams["colorvalue"],
					$postParams["timezone"]);
		}
		else {
			// save as new
			$result = $this->addDetailedCalendar(
					$st, $et, $postParams["Subject"], $postParams["IsAllDayEvent"] ? 1 : 0,
					$postParams["Description"], $postParams["Location"], $postParams["colorvalue"],
					$postParams["timezone"]);
		}

		return $result;
	}

}