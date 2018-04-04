<?php

define('TODO_STATE_OPEN', 0);
define('TODO_STATE_DONE', 1);
define('TODO_STATE_CLOSED', 2);
define('TODO_STATE_PROJECT', 4);

set_include_path(get_include_path() . PATH_SEPARATOR .
		JPATH_ROOT . "/administrator/components/com_teamtime/assets/PEAR");

if (!class_exists("Services_JSON")) {
	require_once("Services/JSON.php");
}
require_once(JPATH_ROOT . '/administrator/components/com_teamtime/classes/Calendar/Event.php');

class TeamTime {

	function init() {
		// include js libs
		JHTML::script('default.js', 'administrator/components/com_teamtime/assets/js/');
		JHTML::script('underscore.js', 'administrator/components/com_teamtime/assets/js/libs/');
		JHTML::script('json2.js', 'administrator/components/com_teamtime/assets/js/libs/');
		JHTML::script('purl.js', 'administrator/components/com_teamtime/assets/js/libs/');

		// set the table directory
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_teamtime/tables');

		// include models
		require_once(JPATH_ADMINISTRATOR . "/components/com_teamtime/models/task.php");

		// load language files for component
		$lang = & JFactory::getLanguage();
		$lang->load("com_teamtime", JPATH_BASE);

		// include helpers for other components
		$path = dirname(dirname(dirname(__FILE__)));

		// get list of all teamtime components
		foreach (glob($path . "/components/com_teamtime*") as $fname) {
			$f = $fname . "/teamtime_helpers.php";
			if (file_exists($f)) {
				include_once($f);
			}
		}
	}

	function _() {
		$args = func_get_args();

		// first argument - name of real teamtime component function
		$name = "TeamTime_" . array_shift($args);

		// second argument - default value - if not exists teamtime component function
		if (!function_exists($name)) {
			return array_shift($args);
		}

		return call_user_func_array($name, $args);
	}

	function addonsList() {
		$res = array();

		//$path = JPATH_BASE;
		$path = dirname(dirname(dirname(__FILE__)));

		foreach (glob($path . "/components/com_teamtime*") as $fname) {
			$res[] = str_replace("com_", "", basename($fname));
		}

		return $res;
	}

	function addonExists($name) {
		//$path = JPATH_BASE;
		$path = dirname(dirname(dirname(__FILE__)));

		return file_exists($path . "/components/" . $name);
	}

	function getConfig() {
		$config_name = JPATH_ROOT . "/administrator/components/com_teamtime/config.json";
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

	function initJS($data = array(), $init = false) {
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

		$doc->addScriptDeclaration($result);
	}

	function processRelativeLinks($content, $base = "") {
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

		return $result;
	}

	function getProjectParams($item) {
		$res = "";
		$params = array();

		$params[] = TeamTime::_("Formals_getProjectParams", null, $item);

		$params = array_filter($params);

		if (sizeof($params) > 0) {
			jimport('joomla.html.pane');
			$pane = & JPane::getInstance('sliders', array('allowAllClose' => true));

			$res .= '<div style="padding-top:7px;">';
			$res .= $pane->startPane('pane');
			foreach ($params as $p)
				if (is_array($p)) {
					$res .= $pane->startPanel($p["title"], $p["name"]);
					$res .= $p["content"];
					$res .= $pane->endPanel();
				}
			$res .= $pane->endPane();
			$res .= '</div>';
		}

		return $res;
	}

	function checkProjectForUser($params) {
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

	function enableProjectForUser($params) {
		$db = & JFactory::getDBO();

		$query = "insert into #__teamlog_project_user
		(project_id, user_id)
		values (" . (int) $params["project_id"] . "," . (int) $params["user_id"] . ")";
		$res = $db->Execute($query);

		return $res;
	}

	function getTodoRepeatParams($event, $todo_date) {
		include(dirname(__FILE__) . "/helpers/partials/todorepeatparams.php");
	}

	function getTodoRepeatParams2($event, $todo_date) {
		include(dirname(__FILE__) . "/helpers/partials/todorepeatparams2.php");
	}

	function saveTodoRepeatParams($todo, $post) {
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

	function getRepeatTodoEditcode($s) {
		include(dirname(__FILE__) . "/helpers/partials/todorepeatparamsjs.php");
	}

	function getDefaultFilter($dateName = "today") {
		$params = array(
				"filter_state" => TODO_STATE_OPEN,
				"filter_period" => "",
				"filter_date" => $dateName,
				"filter_stodo" => "",
				"filter_sproject" => "",
		);

		return $params;
	}

	function getUserParams($item) {
		$res = "";
		$params = array();

		$params[] = TeamTime::_("Formals_getProjectParams", null, $item, "user");

		$params = array_filter($params);

		if (sizeof($params) > 0) {
			jimport('joomla.html.pane');
			$pane = & JPane::getInstance('sliders', array('allowAllClose' => true));

			$res .= '<div style="padding-top:7px;">';
			$res .= $pane->startPane('pane');
			foreach ($params as $p)
				if (is_array($p)) {
					$res .= $pane->startPanel($p["title"], $p["name"]);
					$res .= $p["content"];
					$res .= $pane->endPanel();
				}
			$res .= $pane->endPane();
			$res .= '</div>';
		}

		$res .= TeamTime::_("Dotu_getUserParams", "", $item);

		return $res;
	}

	//
	// some util functions
	//

	function js2PhpTime($jsdate) {
		if (preg_match('@(\d+)/(\d+)/(\d+)\s+(\d+):(\d+)@', $jsdate, $matches) == 1) {
			$ret = mktime($matches[4], $matches[5], 0, $matches[1], $matches[2], $matches[3]);
		}
		else if (preg_match('@(\d+)/(\d+)/(\d+)@', $jsdate, $matches) == 1) {
			$ret = mktime(0, 0, 0, $matches[1], $matches[2], $matches[3]);
		}

		return $ret;
	}

	function php2JsTime($phpDate) {
		return date("m/d/Y H:i", $phpDate);
	}

	function mySql2PhpTime($sqlDate, $trunc_hours = false) {
		$arr = getdate(strtotime($sqlDate));

		$date = $trunc_hours ?
				mktime(0, 0, 0, $arr["mon"], $arr["mday"], $arr["year"]) :
				mktime($arr["hours"], $arr["minutes"], $arr["seconds"], $arr["mon"], $arr["mday"], $arr["year"]);

		return $date;
	}

	function php2MySqlTime($phpDate) {
		return date("Y-m-d H:i:s", $phpDate);
	}

}

function TeamTime_get_addon_button_teamtime() {
	return JCEHelper::quickiconButton(
					'index.php?option=com_teamtime&controller=report',
					'components/com_teamtime/assets/images/icon-48-accounting.png', JText::_('TeamTime Accounting'));
}

function TeamTime_addon_menuitem_teamtime($controller) {
	JSubMenuHelper::addEntry(JText::_("Control Panel"),
			"index.php?option=com_teamtime&controller=cpanel",
			$controller == "cpanel" || $controller == 'config');
	JSubMenuHelper::addEntry(JText::_("Accounting"), "index.php?option=com_teamtime&controller=report",
			$controller == "report");
}

function TeamTime_save_project_params($model, $post) {
	TeamTime::_("save_project_params_teamtimeformals", $model, $post);
}

function TeamTime_save_user_params($post) {
	TeamTime::_("save_user_params_teamtimeformals", $post);
}

TeamTime::init();