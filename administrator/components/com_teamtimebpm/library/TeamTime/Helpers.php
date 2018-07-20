<?php

class TeamTime_Helpers_Bpmn {

	const ORDER_INDEX = 5;

	function __construct() {
		//...
	}

	public function initTodoData($todos) {
		$mProcess = new TeamtimebpmModelProcess();
		$mSpace = new TeamtimebpmModelSpace();

		foreach ($todos as $i => $todo) {
			$todos[$i]->processUrl = "";
			$todos[$i]->processName = "";
			$todos[$i]->spaceName = "";

			$row = $mProcess->getTodoData($todo->id);
			if ($row) {
				$process = $mProcess->getById($row->process_id);
				if ($process) {
					$todos[$i]->processUrl = JURI::root(true) .
							"/administrator/index.php?option=com_teamtimebpm" .
							"&controller=process&view=processdiagrampage&id=" . $process->id;
					$todos[$i]->processName = $process->name;

					$space = $mSpace->getById($process->space_id);
					$todos[$i]->spaceName = $space->name;
				}
			}
		}

		return $todos;
	}

	public function getProcessLink($todoId) {
		$result = "";

		$model = new TeamtimebpmModelProcess();
		$row = $model->getTodoData($todoId);
		if ($row) {
			$model->setId($row->process_id);
			$data = $model->getData();
			if ($data) {
				$url = JURI::base() . "administrator/index.php?option=com_teamtimebpm" .
						"&controller=process&view=processdiagrampage&id=" . $data->id;
				$name = htmlspecialchars($data->name, ENT_COMPAT);

				$result = '<a target="_blank" href="' . $url . '" title="' . $name . '">' .
						$name . '</a>';
			}
		}

		return $result;
	}

	public function getCheckboxSendReport($todoId) {
		JHTML::script("send-report.js", "media/com_teamtimebpm/assets/js/teamtime/");

		$mProccess = new TeamtimebpmModelProcess();

		$users = array();
		foreach ($mProccess->getDestUsers($todoId) as $user) {
			$users[] = $user->name;
		}

		$disabled = "";
		if (sizeof($users) > 0) {
			$users = implode(", ", $users);
		}
		else {
			$users = JText::_("No users");
			$disabled = "disabled";
		}
		?>
		<div>
			<input type="checkbox"  <?= $disabled ?>
						 name="send_report" id="send_report" value="1">
			<a href="#" class="<?= $disabled ?>"
				 id="toggle_send_report"><?= JText::_("Send report for users") ?>: <?= $users ?></a>
		</div>
		<?
	}

	private function generateReportContent($data) {
		$fname = JPATH_ADMINISTRATOR .
				"/components/com_teamtimebpm/assets/templates/reportnotice.html";

		$helperBase = TeamTime::helper()->getBase();

		$tpl = new HTML_Template_IT("");
		$tpl->loadTemplatefile($fname, true, true);

		$url = JURI::root() . "administrator/index.php?option=com_teamtimebpm" .
				"&controller=process&view=processdiagrampage" .
				"&id=" . $data['process']->id;
		$tpl->setVariable("process_name", $data['process']->name);
		$tpl->setVariable("process_url", $url);

		$tpl->setVariable("todo_name", $data['todo']->title);
		$tpl->setVariable("current_user_name", $data['current_user']->name);

		$url = JURI::root();
		foreach ($data['user']->todos as $userTodo) {
			$tpl->setVariable("user_todo_name", $userTodo->title);
			$tpl->setVariable("user_todo_url", $url); //$userTodo->id
			$tpl->parse("todorow");
		}

		foreach ($data['logs'] as $log) {
			$tpl->setVariable("log_date", JHTML::_('date', $log->date, "%d.%m.%Y"));
			$tpl->setVariable("log_report",
					$helperBase->processRelativeLinks($log->description, JURI::root()));
			$tpl->parse("row");
		}

		return $tpl->get();
	}

	private function generateReportContentFollowed($data, $proc, $user) {
		$fname = JPATH_ADMINISTRATOR .
			"/components/com_teamtimebpm/assets/templates/reportnotice_followed.html";

		$helperBase = TeamTime::helper()->getBase();

		$tpl = new HTML_Template_IT("");
		$tpl->loadTemplatefile($fname, true, true);

		$url = JURI::root() . "administrator/index.php?option=com_teamtimebpm" .
			"&controller=process&view=processdiagrampage" .
			"&id=" . $proc->id;
		$tpl->setVariable("process_name", $proc->name);
		$tpl->setVariable("process_url", $url);

		$log_total_hours = 0;
		foreach($data as $todo)	{
			foreach($todo['todo_logs'] as $log)	{
				// выводим время выполнения по каждому рапорту
				$duration = $log->duration;
				$minutes = $duration%60;
				if($minutes < 10) $minutes = '0' . $minutes;
				$hours = intval($duration/60);
				$tpl->setVariable("log_duration", $hours . ':' . $minutes);

				$tpl->setVariable("log_date", JHTML::_('date', $log->date, "%d.%m.%Y"));
				$tpl->setVariable("log_report",
					$helperBase->processRelativeLinks($log->description, JURI::root()));
				$tpl->parse("row");

				// Считаем суммарное время выполнения по данным рапортам
				$log_total_minutes += $duration;
			}
			$tpl->setVariable("todo_name", $todo['todo_title']);
			$tpl->setVariable("todo_process_name", $todo['todo_process']);
			$tpl->parse("rowgroup");
		}

		// Выводим суммарное время выполнения по данным рапортам
		$minutes = $log_total_minutes%60;
		if($minutes < 10) $minutes = '0' . $minutes;
		$hours = intval($log_total_minutes/60);
		$tpl->setVariable("log_total_hours", '= ' . $hours . 'ч ' . $minutes . 'мин');

		$tpl->setVariable("current_user_name", $user->name);

		return $tpl->get();
	}

	public function sendReport($todo, $post) {
		if (!isset($post["send_report"]) || $post["send_report"] == 0) {
			return;
		}

		$currentUser = & JFactory::getUser();
		$config = new JConfig();
		$mLog = new TeamtimeModelLog();
		$mProccess = new TeamtimebpmModelProcess();

		$todoData = $mProccess->getTodoData($todo->id);
		if (!$todoData) {
			return;
		}

		$mProccess->setId($todoData->process_id);
		$process = $mProccess->getData();
		$mSpace = new TeamtimebpmModelSpace();
		$mSpace->setId($process->space_id);
		$space = $mSpace->getData();

		foreach ($mProccess->getDestUsers($todo->id) as $user) {
			$subject = $space->name . " / " . $process->name;
			$body = $this->generateReportContent(array(
				"logs" => $mLog->getLogs(array("todo_id" => $todo->id)),
				"process" => $process,
				"todo" => $todo,
				"user" => $user,
				"current_user" => $currentUser));

			//error_log($subject);
			//error_log($body);

			JUTility::sendMail($config->mailfrom, $config->fromname, $user->email, $subject, $body, true);
		}
	}

	public function sendReportToFollower($todo) {
		$config = new JConfig();
		$userFollower = & JFactory::getUser($todo->user_id);
		$currentUser = & JFactory::getUser();

		// Находим все родительские процессы
		$mProccess = new TeamtimebpmModelProcess();
		$todoData = $mProccess->getTodoData($todo->id);
		if (!$todoData) {
			return;
		}
		$parents = $mProccess->getParentProcesses($todoData->process_id);
		array_unshift($parents, $todoData->process_id);

		// Находим ближайший родительский процесс, имеющий флаг "Followed"
		foreach($parents as $id)	{
			if($mProccess->isFollowedBySomeone($id))	{
				$closeParent = $id;
				$proc_to_follow = $mProccess->getById($id);
				break;
			}
		}
		// error_log('===$closeParent ' . print_r($closeParent, true), 3, '/home/mediapub/teamlog.teamtime.info/docs/logs/my.log');

		// Находим все дочерние процессы от найденного
		$processesFollowed = $mProccess->getLinkedProcesses($closeParent);
		array_unshift($processesFollowed, $closeParent);

		// Находим все todo
		$todos = [];
		foreach ($processesFollowed as $id) {
			$todos = array_merge($todos, $mProccess->getProcessTodoIds($id, $currentUser->id));
		}

		// Находим все рапорты по днным todo
		$mLog = new TeamtimeModelLog();
		$mTodo = new TeamtimeModelTodo();
		$todosFollowed = [];
		foreach($todos as $todoId)	{
			$todoData = $mProccess->getTodoData($todoId);
			$mProccess->setId($todoData->process_id);
			$process = $mProccess->getData();
			$mTodo->setId($todoId);
			$logs = $mLog->getLogs(array("todo_id" => $todoId));
			if(count($logs) > 0)	{
				$todosFollowed[] = array(
					"todo_title" => $mTodo->getData()->title,
					"todo_process" => htmlspecialchars($process->name, ENT_COMPAT),
					"todo_logs" => $logs);
			}
		}
		$body = $this->generateReportContentFollowed($todosFollowed, $proc_to_follow, $currentUser);
		//error_log('===$todosFollowed ' . print_r($todosFollowed, true), 3, '/home/mediapub/teamlog.teamtime.info/docs/logs/my.log');
		JUTility::sendMail($config->mailfrom, $config->fromname, $userFollower->email, $subject, $body, true);
	}

	public function jsonTodoInfo($todo) {
		$result = array();

		$result["id"] = $todo->id;
		$result["title"] = $todo->title;

		$project = new Project($todo->project_id);
		$result["project"] = $project->name;
		$result["project_id"] = $project->id;

		$model = new TeamtimebpmModelProcess();
		$row = $model->getTodoData($todo->id);
		if ($row) {
			$model->setId($row->process_id);
			$data = $model->getData();

			if ($data) {
				$result["process_id"] = $data->id;
				$result["process"] = $data->name;

				$mSpace = new TeamtimebpmModelSpace();
				$mSpace->setId($data->space_id);
				$data = $mSpace->getData();

				if ($data) {
					$result["space_id"] = $data->id;
					$result["space"] = $data->name;
				}
			}
		}

		return '
			<script id="todo-json-info">
			/*' . json_encode($result) . '*/
			</script>
		';
	}

	public function getAddonButton() {
		$lang = & JFactory::getLanguage();
		$lang->load("com_teamtimebpm", JPATH_BASE);

		return TeamTime::helper()->getBase()->quickiconButton(
						'index.php?option=com_teamtimebpm&controller=process',
						'components/com_teamtimebpm/assets/images/icon-48-bpmn.png', JText::_('TeamTime BPM'));
	}

	public function addonMenuItem($controller) {
		JSubMenuHelper::addEntry(JText::_("BPM"), "index.php?option=com_teamtimebpm&controller=process",
				$controller == "bpmnrole");
	}

	private function getPathForTodo($todoId) {
		$helper = TeamTime::helper()->getBase();
		$path = "";
		$prefix = "";

		$todo = new Todo($todoId);

		// set prefix by todo_id
		$prefix = "r" . $todo->id . "-";

		$mProcess = new TeamtimebpmModelProcess();
		$row = $mProcess->getTodoData($todo->id);
		if ($row) {
			$mProcess->setId($row->process_id);
			$process = $mProcess->getData();

			$mSpace = new TeamtimebpmModelSpace();
			$mSpace->setId($process->space_id);
			$space = $mSpace->getData();

			// set space/process
			$path = $helper->translit($space->name) . "/" .
					$helper->translit($process->name) . "/";
		}
		else {
			$project = new Project($todo->project_id);
			$type = new Type($todo->type_id);

			// set project/type
			$path = $helper->translit($project->name) . "/" .
					$helper->translit($type->name) . "/";
		}

		return array($path, $prefix);
	}

	private function getPathForReport($logId) {
		$helper = TeamTime::helper()->getBase();
		$path = "";
		$prefix = "";

		$log = new Log($logId);
		if ($log->todo_id) {
			// for orders with todo
			list($path, $prefix) = $this->getPathForTodo($log->todo_id);
		}
		else {
			// for orders without todo
			$project = new Project($log->project_id);
			$type = new Type($log->type_id);

			// set prefix by log_id
			$prefix = "nr" . $log->id . "-";

			// set project/type
			$path = $helper->translit($project->name) . "/" .
					$helper->translit($type->name) . "/";
		}

		return array($path, $prefix);
	}

	public function getUploadPath($params) {
		$path = "";
		$prefix = "";

		if (!isset($params["type"])) {
			$params["type"] = "";
		}

		switch ($params["type"]) {
			case "todo":
				list($path, $prefix) = $this->getPathForTodo($params["id"]);
				break;

			case "report":
				list($path, $prefix) = $this->getPathForReport($params["id"]);
				break;

			default:
				break;
		}

		return array($path, $prefix);
	}

	public function getStatusImage($item) {
		$statusImage = "process_created.png";

		if ($item->archived == "archived") {
			$statusImage = "process_archiv.png";
		}
		else if ($item->is_started) {
			$statusImage = "process_start.png";

			$mProcess = new TeamtimebpmModelProcess();
			$res = $mProcess->getProcessState($item->id);
			if ($res == "error") {
				$statusImage = "process_error.png";
			}
			else if ($res == "done") {
				$statusImage = "process_inmade.png";
			}
		}

		return "/" . URL_MEDIA_COMPONENT_ASSETS . "/css/images/" . $statusImage;
	}

	public function getProjectsBySpaceSql($spaceId, $where) {
		$result = "select b.id as value, b.name as text
			from #__teamtimebpm_project_space as a
			left join #__teamtime_project as b on a.project_id = b.id
			" . $where . " and space_id = " . (int) $spaceId . "
			group by b.id
			order by b.name";

		return $result;
	}

	public function getProcessSelect($processIds, $params = array()) {
		// default options
		if (!isset($params["autosubmit"])) {
			$params["autosubmit"] = false;
		}
		if (!isset($params["firstOptions"])) {
			$params["firstOptions"] = JHTML::_(
							'select.option', '- ' . JText::_('Select process') . ' -', '', 'text', 'value');
		}
		if (!isset($params["fieldId"])) {
			$params["fieldId"] = "process_id";
		}
		if (!isset($params["type"])) {
			$params["type"] = "formals";
		}
		if (!isset($params["attrs"])) {
			$params["attrs"] = 'size="1"';
		}

		$result = "";
		$db = & JFactory::getDBO();
		$mProcess = new TeamtimebpmModelProcess();
		$mTodo = new TeamtimeModelTodo();
		$sqlPart = $mTodo->getRepeatParamsSqlPart($params);
		$where = $sqlPart["where"];

		if ($params["type"] == "formals") {
			$mFormals = new TeamtimeformalsModelFormal();
			$doctype = $mFormals->getDoctype($params["doctype_id"]);
			if ($doctype->using_in == "user") {
				$where[] = "t.user_id = " . (int) $params["id"];
			}
			else if ($doctype->using_in == "project") {
				$where[] = "t.project_id = " . (int) $params["id"];
			}
		}

		if (sizeof($where) > 0) {
			$where = "where (" . implode(" and ", $where) . ")";

			//if ($params["type"] == "formals") {
			//	$where .= " or (a.project_id = " . (int) $params["id"] . " and " .
			//			. ")";
			//}
		}

		$query = 'select a.id as value, a.name as text
			from #__teamtimebpm_process as a
			left join #__teamtimebpm_todo as tx on a.id = tx.process_id
			left join #__teamtime_todo as t on tx.todo_id = t.id
			' . $sqlPart["join"] . '
			' . $where . '
			group by a.id
			order by a.name';

		//error_log($query);

		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$rows = $mProcess->addParentLinkedProcesses($rows);

		//error_log(print_r($rows, true));

		$options = JHTML::_(
						'select.option', '- ' . JText::_('Work orders out of processes') . ' -', 'none', 'text',
						'value');
		if (is_array($params["firstOptions"])) {
			$params["firstOptions"][] = $options;
		}
		else {
			$params["firstOptions"] = array($params["firstOptions"], $options);
		}
		$params["firstOptions"] = array_merge($params["firstOptions"], $rows);

		if ($query != "") {
			$autosubmit = $params["autosubmit"] ? "auto-submit" : "";
			$result = JHTML::_(
							'select.genericlist', $params["firstOptions"], $params["fieldId"],
							'class="inputbox ' . $autosubmit . '" ' . $params["attrs"], 'value', 'text', $processIds);
		}

		return $result;
	}

	public function getFormalsSqlPart($params = array()) {
		$result = array();
		$where = array();
		$join = "";

		if (isset($params["process_id"])) {
			$join = '
				left join #__teamtimebpm_todo as tx on a.id = tx.todo_id
			';

			// todos not in process
			$i = array_search("none", $params["process_id"]);
			if ($i !== false) {
				$where[] = "tx.process_id is null";
				unset($params["process_id"][$i]);
			}

			// todos in process
			if (sizeof($params["process_id"]) > 0) {
				$where[] = "tx.process_id in (" . implode(",", $params["process_id"]) . ")";
			}

			if (sizeof($where) > 0) {
				$where = array("(" . implode(" or ", $where) . ")");
			}
		}

		return array(
			"join" => $join,
			"where" => $where
		);
	}

	public function addLinkedProcesses(&$data, $params, $variables) {
		if (!isset($params["process_id"])) {
			return;
		}

		$mProcess = new TeamtimebpmModelProcess();
		$mFormal = new TeamtimeformalsModelFormal();
		$linkedProcesses = array();
		foreach ($params["process_id"] as $processId) {
			$linkedProcesses = array_merge($linkedProcesses, $mProcess->getLinkedProcesses($processId));
		}

		//error_log("linked: " . print_r($linkedProcesses, true));

		foreach ($linkedProcesses as $processId) {
			$tmpParams = $params;
			$tmpParams["process_id"] = array($processId);
			$processData = $mFormal->getDataForProject($tmpParams, $variables);

			$process = $mProcess->getById($processId);
			foreach ($processData["rows_todos"] as $i => $row) {
				$processData["rows_todos"][$i]->type_name = $process->name;
			}

			if (sizeof($processData["rows_todos"]) > 0) {
				$data[$process->name] = $processData["rows_todos"];
			}
			//error_log(print_r($processData["rows_todos"], true));
		}

		//error_log(print_r($data, true));
	}

}
