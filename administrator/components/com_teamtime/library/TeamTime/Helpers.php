<?php

class TeamTime_Helpers_Base {

	const ORDER_INDEX = 1;

	function __construct() {
		//...
	}

	public function __call($name, $arguments) {
		return new TeamTime_Undefined();
	}

	public function addJavaScript($data = array(), $init = false) {
		$doc = & JFactory::getDocument();

		$result = "";

		if ($init) {
			$result = "
				TeamTime.baseUrl = '" . JURI::base() . "';
			";
		}

		if (isset($data["option"])) {
			$result .= "
				TeamTime.option = '" . $data["option"] . "';
			";
		}
		if (isset($data["controller"])) {
			$result .= "
				TeamTime.controller = '" . $data["controller"] . "';
			";
		}

		if (isset($data["resource"]) && is_array($data["resource"])) {
			foreach ($data["resource"] as $k => $v) {
				$result .= "
					TeamTime.resource.{$k} = " . json_encode($v) . ";
				";
			}
		}

		$doc->addScriptDeclaration($result);
	}

	public function notifyUserByEmail($post) {
		$config = new JConfig();
		$db = & JFactory::getDBO();
		$confData = TeamTime::getConfig();

		$query = "SELECT * FROM #__users
				where id = " . $db->Quote($post["user_id"]);
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if (sizeof($rows) == 0) {
			return;
		}

		$recipient = $rows[0]->email;
		$subject = JText::_("USER NOTIFY TITLE") . " " . $post["title"];
		$body = $post["title"] . "\n" . $confData->baseurl . " " .
				JText::_("USER NOTIFY CHECK TODO") . "\n";
		JUTility::sendMail($config->mailfrom, $config->fromname, $recipient, $subject, $body);
	}

	public function processRelativeLinks($content, $base = "") {
		$result = $content;

		if ($base == "") {
			return $result;
		}

		if (substr($base, -1, 1) != "/") {
			$base = $base . "/";
		}

		$pattern1 = '/(href|src)="(?!http|ftp|https)([^"]*)"/';
		$pattern2 = "/(href|src)='(?!http|ftp|https)([^\"]*)'/";

		$result = preg_replace($pattern1, '$1="' . $base . '$2$3"', $result);
		$result = preg_replace($pattern2, "$1='" . $base . "$2$3'", $result);
		$result = preg_replace('{<a\s+}i', '<a target="_blank" ', $result);

		return $result;
	}

	/*
	  private function _convertTextLinks($m) {
	  if (mb_strpos($m[0], '<a') !== false || mb_strpos($m[0], '>') !== false) {
	  return $m[0];
	  }

	  $s = $m[2];
	  $prefix = mb_strpos($m[2], "http://") !== false ? "" : "http://";
	  if (mb_strlen($s) > 30) {
	  $s = mb_substr($s, 0, 20) . "..." . mb_substr($s, -10);
	  }

	  return '<a href="' . $prefix . $m[2] . '" target=_blank>' .
	  $prefix . $s . '</a>';
	  }

	  public function convertTextLinks($text) {
	  $text = preg_replace(
	  "/(?<!http:\\/\\/)(www)([^<\\s]+)/si", 'http://www\\2', $text);
	  $text = preg_replace_callback(
	  "/(<a\s+[^>]+>http:\\/\\/[^<\\s]+)|(http:\\/\\/[^<\\s]+)/si",
	  array($this, "_convertTextLinks"), $text);

	  return $text;
	  }
	 */

	public function getReportText($log, $convertTextLinks = false) {
		$oldEncoding = mb_internal_encoding();
		mb_internal_encoding("utf-8");

		$maxLen = 180;
		$result = strip_tags($log["content"], "<a>");

		$addLink = false;
		if (mb_strlen($result) > $maxLen) {
			$addLink = true;
			$result = mb_substr($result, 0, $maxLen);

			// remove last link
			if (preg_match_all('{\<a[^<]+href=([^<]+)}i', $result, $matches,
							PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
				$m = $matches[sizeof($matches) - 1];
				$p = $m[0][1];
				$result = substr($result, 0, $p);
			}
		}

		//if ($convertTextLinks) {
		//	$result = $this->convertTextLinks($result);
		//}

		if ($addLink) {
			// make link for popup content
			$loaderUrl = JURI::root() .
					"index.php?option=com_teamtime&controller=reports&task=loadReport";

			$result .= '&nbsp;<a href="' . $loaderUrl . "&id=" . $log["id"] .
					'" class="fancybox">[....]</a>';
		}

		mb_internal_encoding($oldEncoding);

		return $result;
	}

	public function getFormatedDate($date) {
		return JHTML::_('date', $date, "%d") . " " .
				JText::_("STR_MONTH" . (int) JHTML::_('date', $date, "%m")) . " " .
				JHTML::_('date', $date, "%Y");
	}

	public function quickiconButton($link, $image, $text, $disabled = false) {
		$mainframe = & JFactory::getApplication();
		$lang = & JFactory::getLanguage();
		$template = $mainframe->getTemplate();

		if ($disabled) {
			$link = '#';
		}
		?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="<?php echo $link; ?>">
					<?php
					echo JHTML::_('image.site', $image, '', NULL, NULL, $text);
					?>
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
		<?php
	}

	public function getAddonButton() {
		return $this->quickiconButton(
						'index.php?option=com_teamtime&controller=report',
						'components/com_teamtime/assets/images/icon-48-accounting.png',
						JText::_('TeamTime Accounting'));
	}

	public function addonMenuItem($controller) {
		JSubMenuHelper::addEntry(JText::_("Control Panel"),
				"index.php?option=com_teamtime&controller=cpanel",
				$controller == "cpanel" || $controller == 'config');
		JSubMenuHelper::addEntry(JText::_("Accounting"),
				"index.php?option=com_teamtime&controller=report", $controller == "report");
	}

	public function translit($s, $maxlength = 50, $allowed = "") {
		$tr = array(
			"А" => "A", "Б" => "B", "В" => "V", "Г" => "G",
			"Д" => "D", "Е" => "E", "Ж" => "J", "З" => "Z", "И" => "I",
			"Й" => "Y", "К" => "K", "Л" => "L", "М" => "M", "Н" => "N",
			"О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T",
			"У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "TS", "Ч" => "CH",
			"Ш" => "SH", "Щ" => "SCH", "Ъ" => "", "Ы" => "YI", "Ь" => "",
			"Э" => "E", "Ю" => "YU", "Я" => "YA", "а" => "a", "б" => "b",
			"в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "j",
			"з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l",
			"м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
			"с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h",
			"ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "y",
			"ы" => "yi", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya"
		);

		$result = strtr($s, $tr);
		$result = preg_replace("{[^A-Za-z0-9{$allowed}]+}si", "_", $result);

		if ($maxlength > 0) {
			if (strlen($result) > $maxlength) {
				$result = substr($result, 0, $maxlength);
			}
		}

		return $result;
	}

	public function createDirRecurive($path) {
		$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
		if (is_dir($path) || empty($path)) {
			return true;
		}
		if (is_file($path)) {
			trigger_error('mkdir() File exists', E_USER_WARNING);
			return false;
		}

		$tmpPath = "";
		foreach (explode(DIRECTORY_SEPARATOR, $path) as $i => $name) {
			if ($i == 0) {
				continue;
			}
			$tmpPath .= "/" . $name;

			if (is_dir($tmpPath)) {
				continue;
			}
			if (file_exists($tmpPath)) {
				trigger_error('mkdir() File exists', E_USER_WARNING);
				return false;
			}

			mkdir($tmpPath);
		}

		return true;
	}

	private function getPathForTodo($todoId) {
		$helper = TeamTime::helper()->getBase();
		$path = "";
		$prefix = "";

		$todo = new Todo($todoId);

		// set prefix by todo_id
		$prefix = "r" . $todo->id . "-";

		$project = new Project($todo->project_id);
		$type = new Type($todo->type_id);

		// set project/type
		$path = $helper->translit($project->name) . "/" .
				$helper->translit($type->name) . "/";

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

	public function getProjectParams($item) {
		$result = "";

		$params = array();

		$res = TeamTime::helper()->getFormals()->getProjectParamsBlock($item);
		if (!$res instanceof TeamTime_Undefined) {
			$params[] = $res;
		}

		if (sizeof($params) > 0) {
			jimport('joomla.html.pane');
			$pane = & JPane::getInstance('sliders', array('allowAllClose' => true));

			$result .= '<div style="padding-top:7px;">';
			$result .= $pane->startPane('pane');
			foreach ($params as $p) {
				if (is_array($p)) {
					$result .= $pane->startPanel($p["title"], $p["name"]);
					$result .= $p["content"];
					$result .= $pane->endPanel();
				}
			}
			$result .= $pane->endPane();
			$result .= '</div>';
		}

		return $result;
	}

	public function getUserParams($item) {
		$result = "";
		$params = array();

		$res = TeamTime::helper()->getFormals()->getProjectParamsBlock($item, "user");
		if (!$res instanceof TeamTime_Undefined) {
			$params[] = $res;
		}

		if (sizeof($params) > 0) {
			jimport('joomla.html.pane');
			$pane = & JPane::getInstance('sliders', array('allowAllClose' => true));

			$result .= '<div style="padding-top:7px;">';
			$result .= $pane->startPane('pane');
			foreach ($params as $p) {
				if (is_array($p)) {
					$result .= $pane->startPanel($p["title"], $p["name"]);
					$result .= $p["content"];
					$result .= $pane->endPanel();
				}
			}
			$result .= $pane->endPane();
			$result .= '</div>';
		}

		$result .= TeamTime::helper()->getDotu()->getUserParamsBlock($item);

		return $result;
	}

	public function checkProjectForUser($params) {
		$db = & JFactory::getDBO();

		$query = "SELECT * FROM #__teamtime_project_user
			where project_id = " . (int) $params["project_id"];
		$db->setQuery($query);
		$row = $db->loadObject();

		// if not found - enabled for all
		if (!$row) {
			return 1;
		}

		$query = "SELECT * FROM #__teamtime_project_user
			where project_id = " . (int) $params["project_id"] .
				" and user_id = " . (int) $params["user_id"];
		$db->setQuery($query);
		$row = $db->loadObject();

		return $row ? "1" : "0";
	}

	public function enableProjectForUser($params) {
		$db = & JFactory::getDBO();

		$query = "insert into #__teamtime_project_user
			(project_id, user_id)
			values (" . (int) $params["project_id"] . "," . (int) $params["user_id"] . ")";
		$res = $db->Execute($query);

		return $res;
	}

	public function getTodoRepeatParams($event, $todo_date) {
		include(TeamTime::$filePath . "/helpers/partials/todorepeatparams.php");
	}

	public function getTodoRepeatParams2($event, $todo_date) {
		include(TeamTime::$filePath . "/helpers/partials/todorepeatparams2.php");
	}

	public function saveTodoRepeatParams($todo, $post) {
		$db = & JFactory::getDBO();
		$mTodo = new TeamtimeModelTodo();

		if (!isset($post["start_date"])) {
			$post["start_date"] = "";
		}
		if (!isset($post["created"])) {
			$post["created"] = "";
		}

		// save from calendar todo page
		if (isset($post["saveFromCalendar"])) {
			// add ref for source repeated todo
			if (isset($post["was_repeat"]) && $post["was_repeat"]) {
				$query = "insert ignore into #__teamtime_repeat_todo_ref
				(todo_id, repeating_history) values(" .
						(int) $todo->id . ", " . $db->Quote($post["repeating"]) . ")";
				$res = $db->Execute($query);
			}

			if (trim($post["start_date"]) == "") {
				$post["start_date"] = $post["created"];
			}
			else {
				$post["start_date"] = TeamTime_DateTools::php2MySqlTime(
								TeamTime_DateTools::js2PhpTime($post["start_date"]));
			}

			if ((int) $post["end_date_type"] != 0) {
				$post["end_date"] = TeamTime_DateTools::php2MySqlTime(
								TeamTime_DateTools::js2PhpTime($post["end_date"]));
			}
		}

		// save from todo page
		else {
			if (trim($post["start_date"]) == "") {
				$post["start_date"] = $post["created"];
			}
		}

		if (isset($post["repeat"])) {
			$mTodo->setRepeatParams($todo->id, $post);
		}
		else {
			$mTodo->deleteRepeatParams($todo->id);
		}
	}

	public function getRepeatTodoEditcode($s = "") {
		include(TeamTime::$filePath . "/helpers/partials/todorepeatparamsjs.php");
	}

	public function getDefaultFilter($dateName = "today") {
		$params = array(
			"filter_state" => TODO_STATE_OPEN,
			"filter_period" => "",
			"filter_date" => $dateName,
			"filter_stodo" => "",
			"filter_sproject" => "",
		);

		return $params;
	}

	public function getTasksList($projectId, $options, $name, $selected = null, $attribs = null) {
		$project = new Project($projectId);
		if ($project) {
			foreach ($project->getTaskTypeArray() as $typename => $tasks) {
				if (count($tasks)) {
					$options[] = array(
						"value" => $tasks[0]->type_id,
						"text" => $typename,
						"class" => "option2",
						"disabled" => true
					);

					foreach ($tasks as $task) {
						$option = array("value" => $task->id, "text" => '- ' . $task->name);
						if ($task->id === $selected) {
							$option["selected"] = true;
						}
						$options[] = $option;
					}
				}
			}
		}

		$result = array();
		$result[] = "<select name='" . $name . "' id='" . $name . "' " . $attribs . ">";
		foreach ($options as $option) {
			$result[] = "<option " .
					(isset($option["selected"]) ? "selected" : "") .
					(isset($option["class"]) ? (" class='" . $option["class"] . "'") : "") .
					(isset($option["disabled"]) && $option["disabled"] ? " disabled" : "") .
					" value='" . $option["value"] . "'>" . $option["text"] . "</option>";
		}
		$result[] = "</select>";


		return implode("", $result);
	}

	public function getTaskUsings($taskIds) {
		$mTodo = new TeamtimeModelTodo();
		$result = array();

		$rows = $mTodo->getTodos(array("task_ids" => $taskIds), "desc");
		if (sizeof($rows) > 0) {
			//$result[] = JText::_("Todos") . ":";
		}
		foreach ($rows as $row) {
			$result[] = $row->created . " / " . $row->title . " / " . $row->project_name;
		}

		/*
		  $rows = $mTodo->getLogs(array("task_ids" => $taskIds));
		  if (sizeof($rows) > 0) {
		  $result[] = JText::_("Logs") . ":";
		  }
		  foreach ($rows as $row) {
		  $result[] = $row->created . " / " . $row->title . " / " . $row->project_name;
		  } */

		return $result;
	}

	public function getTypeUsings($typeId, $taskId = null) {
		$mTodo = new TeamtimeModelTodo();
		$result = array();

		$filter = array();
		if (is_array($typeId)) {
			$filter["type_ids"] = $typeId;
		}
		else {
			$filter["type_id"] = $typeId;
		}

		if ($taskId) {
			$filter["task_id"] = $taskId;
		}

		// get task by type_id
		if (!$taskId) {
			$mTask = new TeamtimeModelTask();
			$rows = $mTask->getTasks($filter);
			if (sizeof($rows) > 0) {
				$result[] = JText::_("Tasks") . ":";
			}
			foreach ($rows as $row) {
				$result[] = $row->name . " / " . $row->project_name;
			}
		}

		$rows = $mTodo->getTodos($filter, "desc");
		if (sizeof($rows) > 0 && !$taskId) {
			$result[] = JText::_("Todos") . ":";
		}
		foreach ($rows as $row) {
			$result[] = $row->created . " / " . $row->title . " / " . $row->project_name;
		}

		/*
		  $rows = $mTodo->getLogs(array("task_ids" => $taskIds));
		  if (sizeof($rows) > 0) {
		  $result[] = JText::_("Logs") . ":";
		  }
		  foreach ($rows as $row) {
		  $result[] = $row->created . " / " . $row->title . " / " . $row->project_name;
		  } */

		return $result;
	}

	public function checkTaskItem($query, $value) {
		$db = & JFactory::getDBO();

		$db->setQuery($query);
		$result = $db->loadObjectList();
		foreach ($result as $row) {
			if ($row->value == $value) {
				return true;
			}
		}

		return false;
	}

	public function getProjectFilter($projectId, $client = array()) {
		if (sizeof($client) > 1) {
			$result = $client;
		}
		else if ($projectId != 0) {
			$result = array($projectId);
		}
		else {
			$result = null;
		}

		$acl = new TeamTime_Acl();
		$result = $acl->filterUserProjectIds($result);

		return $result;
	}

	public function getTypesSelect($typeId, $projectFilter, $params = array()) {
		// default options
		if (!isset($params["autosubmit"])) {
			$params["autosubmit"] = true;
		}
		if (!isset($params["firstOptions"])) {
			$params["firstOptions"] = JHTML::_('select.option', '', JText::_('All types'));
		}
		if (!isset($params["fieldId"])) {
			$params["fieldId"] = "type_id";
		}
		if (!isset($params["type"])) {
			$params["type"] = "";
		}
		if (!isset($params["attrs"])) {
			$params["attrs"] = '';
		}
		if (!isset($params["clientView"])) {
			$params["clientView"] = false;
		}

		$where = array();
		if (sizeof($projectFilter) > 0) {
			$where[] = "a.project_id in (" . implode(",", $projectFilter) . ")";
		}
		if (sizeof($where) > 0) {
			$where = " where " . implode(" and ", $where);
		}
		else {
			$where = "";
		}

		if ($params["type"] == "todo") {
			$query = 'SELECT b.id AS value, b.name AS text
			FROM #__teamtime_type AS b
			left join #__teamtime_task AS a on b.id = a.type_id
			left join #__teamtime_todo AS t on b.id = t.type_id
			' . $where . '
			group by b.name order by b.name';
		}
		else {
			if ($where != "") {
				$query = 'SELECT b.id AS value, b.name AS text
					FROM #__teamtime_log AS a
					LEFT JOIN #__teamtime_type AS b ON a.type_id = b.id
					' . $where . '
					group by b.name order by b.name';
			}
			else {
				$query = 'SELECT b.id AS value, b.name AS text
					FROM #__teamtime_type AS b
					' . $where . '
					group by b.name order by b.name';
			}
		}

		// reset filter if type not found
		if (!$params["clientView"]) {
			if (!$this->checkTaskItem($query, $typeId)) {
				$typeId = "";
			}
		}

		//error_log($query);

		$autosubmit = $params["autosubmit"] ? "auto-submit" : "";
		$result = JHTML::_(
						'teamtime.querylist', $query, $params["firstOptions"], $params["fieldId"],
						'class="inputbox ' . $autosubmit . '" ' . $params["attrs"], 'value', 'text', $typeId);

		return array($result, $typeId);
	}

	public function getTasksSelect($typeId, $taskId, $projectFilter, $params = array()) {
		// default options
		if (!isset($params["autosubmit"])) {
			$params["autosubmit"] = true;
		}
		if (!isset($params["firstOptions"])) {
			$params["firstOptions"] = JHTML::_('select.option', '', JText::_('All tasks'));
		}
		if (!isset($params["fieldId"])) {
			$params["fieldId"] = "task_id";
		}
		if (!isset($params["type"])) {
			$params["type"] = "log";
		}
		if (!isset($params["attrs"])) {
			$params["attrs"] = '';
		}
		if (!isset($params["clientView"])) {
			$params["clientView"] = false;
		}

		if ($params["type"] == "log") {
			$fieldName = "a.project_id";
		}
		else {
			$fieldName = "b.project_id";
		}

		$where = array();
		if ($typeId != "") {
			$where[] = "b.type_id = " . $typeId;
		}
		if (sizeof($projectFilter) > 0) {
			$where[] = $fieldName . " in (" . implode(",", $projectFilter) . ")";
		}
		if (sizeof($where) > 0) {
			$where = " where " . implode(" and ", $where);
		}
		else {
			$where = "";
		}

		if ($params["type"] == "log") {
			$query = 'SELECT b.name AS value, b.name AS text
				FROM #__teamtime_log AS a
				LEFT JOIN #__teamtime_task AS b ON a.task_id = b.id
				' . $where . '
				group by b.name order by b.name';
		}
		else {
			$query = 'SELECT b.name AS value, b.name AS text
				FROM #__teamtime_task AS b
				' . $where . '
				group by b.name order by b.name';
		}

		// reset filter if type not found
		if (!$params["clientView"]) {
			if (!$this->checkTaskItem($query, $taskId)) {
				$taskId = "";
			}
		}

		//error_log($query);

		$autosubmit = $params["autosubmit"] ? "auto-submit" : "";
		$result = JHTML::_(
						'teamtime.querylist', $query, $params["firstOptions"], $params["fieldId"],
						'class="inputbox ' . $autosubmit . '" ' . $params["attrs"], 'value', 'text', $taskId);

		return array($result, $taskId);
	}

	public function getProjectSelect($projectId, $params = array()) {
		// default options
		if (!isset($params["autosubmit"])) {
			$params["autosubmit"] = true;
		}
		if (!isset($params["firstOptions"])) {
			$params["firstOptions"] = JHTML::_('select.option', '', '- ' . JText::_('All Projects') . ' -');
		}
		if (!isset($params["fieldId"])) {
			$params["fieldId"] = "project_id";
		}
		if (!isset($params["type"])) {
			$params["type"] = "";
		}
		if (!isset($params["attrs"])) {
			$params["attrs"] = 'size="1"';
		}
		if (!isset($params["showClosed"])) {
			$params["showClosed"] = true;
		}

		if ($params["type"] != "") {
			$filterField = "a.project_id";
		}
		else {
			$filterField = "b.id";
		}

		$acl = new TeamTime_Acl();
		$projectFilter = $acl->filterUserProjectIds();

		$where = array();
		if (sizeof($projectFilter) > 0) {
			$where[] = $filterField . " in (" . implode(",", $projectFilter) . ")";
		}

		if (!$params["showClosed"]) {
			$where[] = "b.state = " . PROJECT_STATE_OPEN;
		}

		if (sizeof($where) > 0) {
			$where = " where " . implode(" and ", $where);
		}
		else {
			$where = "";
		}

		if ($params["type"] == "log") {
			$query = 'SELECT b.id AS value, b.name AS text
				FROM #__teamtime_log AS a
				LEFT JOIN #__teamtime_project AS b ON a.project_id = b.id
				' . $where . '
				group by b.id
				order by b.name';
		}
		else if ($params["type"] == "space") {
			$query = TeamTime::helper()->getBpmn()->getProjectsBySpaceSql($params["spaceId"], $where);
		}
		else {
			$query = 'SELECT b.id AS value, b.name AS text
				FROM #__teamtime_project AS b
				' . $where . '
				group by b.id
				order by b.name';
		}

		//error_log($query);

		$autosubmit = $params["autosubmit"] ? "auto-submit" : "";
		$result = JHTML::_(
						'teamtime.querylist', $query, $params["firstOptions"], $params["fieldId"],
						'class="inputbox ' . $autosubmit . '" ' . $params["attrs"], 'value', 'text', $projectId);

		return $result;
	}

	public function getUserSelect($userId, $params = array()) {
		// default options
		if (!isset($params["autosubmit"])) {
			$params["autosubmit"] = true;
		}
		if (!isset($params["firstOptions"])) {
			$params["firstOptions"] = JHTML::_('select.option', '', '- ' . JText::_('User') . ' -');
		}
		if (!isset($params["fieldId"])) {
			$params["fieldId"] = "user_id";
		}
		if (!isset($params["clientView"])) {
			$params["clientView"] = false;
		}
		if (!isset($params["type"])) {
			$params["type"] = "project";
		}
		if (!isset($params["attrs"])) {
			$params["attrs"] = '';
		}

		$acl = new TeamTime_Acl();
		$projectFilter = $acl->filterUserProjectIds();

		$where = array();
		if (!$params["clientView"]) {
			$where[] = "b.block = 0";
		}
		if (sizeof($projectFilter) > 0) {
			$where[] = "a.project_id in (" . implode(",", $projectFilter) . ")";
		}
		if (sizeof($where) > 0) {
			$where = " where " . implode(" and ", $where);
		}
		else {
			$where = "";
		}

		if ($params["type"] == "project") {
			$query = 'SELECT b.id AS value, b.name AS text
				FROM #__teamtime_project_user AS a
				LEFT JOIN #__users AS b ON a.user_id = b.id
				' . $where . '
				group by b.id order by b.name';
		}
		else {
			if ($params["type"] == "todo") {
				$tableName = "#__teamtime_todo";
			}
			else if ($params["type"] == "log") {
				$tableName = "#__teamtime_log";
			}
			$query = 'SELECT b.id AS value, b.name AS text
				FROM ' . $tableName . ' AS a
				LEFT JOIN #__users AS b ON a.user_id = b.id
				' . $where . '
				group by b.id order by b.name';
		}

		//error_log($query);

		$autosubmit = $params["autosubmit"] ? "auto-submit" : "";
		if ($params["clientView"]) {
			$result = JHTML::_('teamtime.userlist', $params["firstOptions"], $params["fieldId"],
							'class="inputbox" ' . $params["attrs"], 'value', 'text', $userId);
		}
		else {
			$result = JHTML::_(
							'teamtime.querylist', $query, $params["firstOptions"], $params["fieldId"],
							'class="inputbox ' . $autosubmit . '" ' . $params["attrs"], 'value', 'text', $userId);
		}

		return $result;
	}

	public function getStateSelect($state) {
		$options = JHTML::_('select.option', '', '- ' . JText::_('Select State') . ' -');
		return JHTML::_(
						'teamtime.todostatelist', $options, 'filter_state', 'class="inputbox auto-submit"', 'value',
						'text', $state);
	}

	public function getTypesTasksSelect($projectId, $taskId = null, $params = array()) {
		// default options
		if (!isset($params["firstOptions"])) {
			$params["firstOptions"] = JHTML::_('select.option', '', '- ' . JText::_('Select Task') . ' -');
		}
		if (!isset($params["fieldId"])) {
			$params["fieldId"] = "task_id";
		}
		if (!isset($params["attrs"])) {
			$params["attrs"] = '';
		}

		$acl = new TeamTime_Acl();
		$projectId = $acl->filterUserProjectIds(array($projectId));
		if (sizeof($projectId) == 0) {
			$projectId = 0;
		}
		else {
			$projectId = $projectId[0];
		}

		if (!$taskId) {
			$taskId = '-';
		}

		$result = JHTML::_('teamtime.tasklist', $projectId, $params["firstOptions"], $params["fieldId"],
						'class="inputbox" ' . $params["attrs"], 'value', 'text', $taskId);

		return $result;
	}

	public function getDateSelect($fromPeriod, $untilPeriod, $params = array()) {
		$config = & JFactory::getConfig();

		// default options
		if (!isset($params["addJScode"])) {
			$params["addJScode"] = false;
		}

		$date = JFactory::getDate();
		$date = $date->toUnix();
		$date = mktime(0, 0, 0, date('n', $date), date('j', $date), date('Y', $date));
		$monday = (date('w', $date) == 1) ? $date : strtotime('last Monday', $date);

		$datePresets['last_month'] = array(
			'name' => 'Last Month',
			'from' => date('Y-m-d', mktime(0, 0, 0, date('n', $date) - 1, 1, date('Y', $date))),
			'until' => date('Y-m-d', mktime(0, 0, 0, date('n', $date), 0, date('Y', $date))));

		$datePresets['last30'] = array(
			'name' => 'Last 30 days',
			'from' => date('Y-m-d', strtotime('-29 day', $date)),
			'until' => date('Y-m-d', $date));

		$datePresets['last_week'] = array(
			'name' => 'Last Week',
			'from' => date('Y-m-d', strtotime('-7 day', $monday)),
			'until' => date('Y-m-d', strtotime('-1 day', $monday)));

		$datePresets['today'] = array(
			'name' => 'Today',
			'from' => date('Y-m-d', $date),
			'until' => date('Y-m-d', $date));
		$datePresets['week'] = array(
			'name' => 'This Week',
			'from' => date('Y-m-d', $monday),
			'until' => date('Y-m-d', strtotime('+6 day', $monday)));
		$datePresets['month'] = array(
			'name' => 'This Month',
			'from' => date('Y-m-d', mktime(0, 0, 0, date('n', $date), 1, date('Y', $date))),
			'until' => date('Y-m-d', mktime(0, 0, 0, date('n', $date) + 1, 0, date('Y', $date))));
		$datePresets['year'] = array(
			'name' => 'This Year',
			'from' => date('Y-m-d', mktime(0, 0, 0, 1, 1, date('Y', $date))),
			'until' => date('Y-m-d', mktime(0, 0, 0, 12, 31, date('Y', $date))));

		$datePresets['next_week'] = array(
			'name' => 'Next Week',
			'from' => date('Y-m-d', strtotime('+7 day', $monday)),
			'until' => date('Y-m-d', strtotime('+13 day', $monday)));
		$datePresets['next_month'] = array(
			'name' => 'Next Month',
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
			$fromPeriod = $datePresets['last_month']['from'];
			$untilPeriod = $datePresets['last_month']['until'];
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
		$options = array(
			JHTML::_('select.option', '', '- ' . JText::_('Select Period') . ' -', 'text', 'value')
		);
		foreach ($datePresets as $name => $value) {
			$options[] = JHTML::_('select.option', $name, JText::_($value['name']), 'text', 'value');
			if ($value['from'] == $fromPeriod && $value['until'] == $untilPeriod) {
				$select = $name;
				$dateSelect = $value;
			}
		}

		$result = JHTML::_(
						'select.genericlist', $options, 'period', 'class="inputbox" size="1"', 'text', 'value',
						$select);

		if ($params["addJScode"]) {
			$doc = & JFactory::getDocument();
			$doc->addScriptDeclaration('
				TeamTime.form.initDateFilter(' . json_encode($datePresets) . ');
			');
		}

		return array($result, $dateSelect, $datePresets, $fromPeriod, $untilPeriod);
	}

	public function getCalendarView($dateSelect) {
		$result = "month";

		if (isset($dateSelect["name"])) {
			if (stripos($dateSelect["name"], "day") !== false &&
					stripos($dateSelect["name"], "days") === false) {
				$result = "day";
			}
			else if (stripos($dateSelect["name"], "week") !== false) {
				$result = "week";
			}
		}

		return $result;
	}

	public function getCalendarFilter($period, $fromPeriod) {
		$result = "";

		if (!in_array($period, array("last30", "month", "year"))) {
			$result = "&start_date=" . $fromPeriod;
		}

		return $result;
	}

	public function reportParamsRadio($label, $name, $value) {
		?>
		<tr>
			<td width="40%" class="paramlist_key" nowrap>
				<span class="editlinktip"><label
						class="hasTip" for="<?= $name ?>"
						title="<?= JText::_($label); ?>"
						id="<?= $name ?>-lbl"><?= JText::_($label); ?></label></span>
			</td>
			<td class="paramlist_value">
				<input type="radio" <?=
		$value == "1" ? "checked" : ""
		?>
							 value="1" id="<?= $name ?>_on" name="params[<?= $name ?>]">
				<label for="<?= $name ?>_on"><?= JText::_('On') ?></label>

				<input type="radio" <?=
		$value == "0" ? "checked" : ""
		?>
							 value="0" id="<?= $name ?>_off" name="params[<?= $name ?>]">
				<label for="<?= $name ?>_off"><?= JText::_('Off') ?></label>
			</td>
		</tr>
		<?
	}

	public function getComponentVersion() {
		$result = "";

		$xml = new JSimpleXML();
		$xml->loadFile(JPATH_COMPONENT . '/teamtime.xml');
		foreach ($xml->document->_children as $child) {
			if ($child->_name == "version") {
				$result = $child->_data;
				break;
			}
		}

		return $result;
	}

	public function saveConfig($data) {
		$fname = JPATH_COMPONENT . "/config.json";
		$content = json_encode($data);

		file_put_contents($fname, $content);
	}

}