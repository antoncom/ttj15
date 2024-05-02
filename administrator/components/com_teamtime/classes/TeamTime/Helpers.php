<?php

class TeamTime {

	public static $basePath = "administrator/components/com_teamtime";
	public static $filePath = "";

	public static function helper() {
		return new TeamTime_HelpersDispatcher();
	}

	/**
	 * helper for calling event handler
	 * @return TeamTime_EventHandlers_Base|TeamTime_EventHandlers_Bpmn|TeamTime_EventHandlers_Calendar|TeamTime_EventHandlers_Dotu|TeamTime_EventHandlers_Formals
	 */
	public static function trigger() {
		return new TeamTime_EventDispatcher();
	}

	//public static function loader($className) {
	//	$fname = str_replace("_", "/", $className) . ".php";
	//	require_once($fname);
	//}

	public static function init() {
		//spl_autoload_register(array('TeamTime', 'loader'));

		self::$filePath = JPATH_ROOT . "/" . self::$basePath;

		// include helpers for other components
		$path = dirname(self::$filePath);
		// get list of all teamtime components
		foreach (glob($path . "/com_teamtime*") as $fname) {
			$f = $fname . "/classes/TeamTime/init.php";
			if (file_exists($f)) {
				include_once($f);
			}
		}

		TeamTime::trigger()->onInit();
	}

	public static function _() {
		$args = func_get_args();

		// first argument - name of real teamtime component function
		$name = "TeamTime_" . array_shift($args);

		// second argument - default value - if not exists teamtime component function
		if (!function_exists($name)) {
			return array_shift($args);
		}

		return call_user_func_array($name, $args);
	}

	public static function addonExists($name) {
		$path = dirname(self::$filePath);
		return file_exists($path . "/" . $name);
	}

	public function getConfig() {
		$config_name = self::$filePath . "/config.json";
		if (file_exists($config_name)) {
			$json = new Services_JSON();
			$result = $json->decode(file_get_contents($config_name));
		}
		else {
			$result = new stdClass();
			$result->show_costs = 1;
			$result->currency = "р.";
			$result->show_todos_datefilter = 0;
		}

		return $result;
	}

	public function initJS($data = array(), $init = false) {
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

	//
	// some util functions
	//

	public function js2PhpTime($jsdate) {
		if (preg_match('@(\d+)/(\d+)/(\d+)\s+(\d+):(\d+)@', $jsdate, $matches) == 1) {
			$ret = mktime($matches[4], $matches[5], 0, $matches[1], $matches[2],
					$matches[3]);
		}
		else if (preg_match('@(\d+)/(\d+)/(\d+)@', $jsdate, $matches) == 1) {
			$ret = mktime(0, 0, 0, $matches[1], $matches[2], $matches[3]);
		}

		return $ret;
	}

	public function php2JsTime($phpDate) {
		return date("m/d/Y H:i", $phpDate);
	}

	public function mySql2PhpTime($sqlDate, $trunc_hours = false) {
		$arr = getdate(strtotime($sqlDate));

		$date = $trunc_hours ?
				mktime(0, 0, 0, $arr["mon"], $arr["mday"], $arr["year"]) :
				mktime($arr["hours"], $arr["minutes"], $arr["seconds"], $arr["mon"],
						$arr["mday"], $arr["year"]);

		return $date;
	}

	public function php2MySqlTime($phpDate) {
		return date("Y-m-d H:i:s", $phpDate);
	}

}

class TeamTime_Helpers_Base {

	const ORDER_INDEX = 1;

	function __construct() {
		//...
	}

	public function __call($name, $arguments) {
		return new TeamTime_Undefined();
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
		JUTility::sendMail($config->mailfrom, $config->fromname, $recipient, $subject,
				$body);
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

	/**
	 * sql filter for project, enabled for current user
	 * @param type $userId
	 */
	public function getProjectsForUserSqlFilter($userId, $colName = "project_id") {
		$result = "(
			{$colName} not in (
				select project_id from #__teamlog_project_user group by project_id
			) or
			{$colName} in (
				select project_id from #__teamlog_project_user
					where user_id = {$userId} group by project_id
			)
		)";

		return $result;
	}

	public function getFormatedDate($date) {
		return JHTML::_('date', $date, "%d") . " " .
				JText::_("STR_MONTH" . (int) JHTML::_('date', $date, "%m")) . " " .
				JHTML::_('date', $date, "%Y");
	}

	public function quickiconButton($link, $image, $text, $disabled = false) {
		global $mainframe;
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

		$query = "SELECT * FROM #__teamlog_project_user
			where project_id = " . (int) $params["project_id"];
		$db->setQuery($query);
		$row = $db->loadObject();

		// if not found - enabled for all
		if (!$row) {
			return 1;
		}

		$query = "SELECT * FROM #__teamlog_project_user
			where project_id = " . (int) $params["project_id"] .
				" and user_id = " . (int) $params["user_id"];
		$db->setQuery($query);
		$row = $db->loadObject();

		return $row ? "1" : "0";
	}

	public function enableProjectForUser($params) {
		$db = & JFactory::getDBO();

		$query = "insert into #__teamlog_project_user
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
		$mTodo = new TodoModelTodo();

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
				$post["start_date"] = TeamTime::php2MySqlTime(TeamTime::js2PhpTime($post["start_date"]));
			}

			if ((int) $post["end_date_type"] != 0) {
				$post["end_date"] = TeamTime::php2MySqlTime(TeamTime::js2PhpTime($post["end_date"]));
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

}