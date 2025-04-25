<?php

class TeamTime {

	public static $basePath = "administrator/components/com_teamtime";
	public static $filePath = "";
	public static $mediaPath = "media/com_teamtime";
	public static $componentName = "";

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

		self::$componentName = substr(JRequest::getVar('option'), 4);
		self::$filePath = JPATH_ROOT . "/" . self::$basePath;

		// include helpers for other components
		$path = dirname(self::$filePath);
		// get list of all teamtime components
		foreach (glob($path . "/com_teamtime*") as $fname) {
			$f = $fname . "/library/TeamTime/init.php";
			if (file_exists($f)) {
				include_once($f);
			}
		}

		TeamTime::trigger()->onInit();
	}

	public static function addonExists($name) {
		$path = dirname(self::$filePath);
		return file_exists($path . "/" . $name);
	}

	public function getConfig() {
		$configName = self::$filePath . "/config.json";
		if (file_exists($configName)) {		
			$result = json_decode(file_get_contents($configName));
		}
		else {
			$result = new stdClass();
			$result->show_costs = 1;
			$result->currency = "р.";
			$result->show_todos_datefilter = 0;
		}

		return $result;
	}

}
