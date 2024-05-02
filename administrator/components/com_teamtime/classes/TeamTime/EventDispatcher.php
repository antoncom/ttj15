<?php

class TeamTime_EventDispatcher {

	private static $handlers = array();

	public static function add($obj) {
		$className = get_class($obj);
		self::$handlers[$className] = $obj;
	}

	public static function remove($obj) {
		$className = get_class($obj);
		unset(self::$handlers[$className]);
	}

	public function __call($name, $arguments) {
		foreach (self::$handlers as $k => $handlerObj) {
			if (method_exists($handlerObj, $name)) {
				call_user_func_array(array($handlerObj, $name), $arguments);
			}
		}
	}

}