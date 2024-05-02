<?php

/**
 * @method TeamTime_Helpers_Base getBase() getBase()
 * @method TeamTime_Helpers_Bpmn getBpmn() getBpmn()
 * @method TeamTime_Helpers_Calendar getCalendar() getCalendar()
 * @method TeamTime_Helpers_Dotu getDotu() getDotu()
 * @method TeamTime_Helpers_Formals getFormals() getFormals()
 */
class TeamTime_HelpersDispatcher {

	private static $helpers = array();

	public static function add($obj) {
		$className = get_class($obj);
		self::$helpers[$className] = $obj;
	}

	public static function remove($obj) {
		$className = get_class($obj);
		unset(self::$helpers[$className]);
	}

	public function __call($name, $arguments) {
		$name = substr($name, 3);

		$className = "TeamTime_Helpers_$name";
		if (!isset(self::$helpers[$className])) {
			$className = "TeamTime_Helpers_Base";
		}

		return self::$helpers[$className];
	}

	public function sortByOrder($a, $b) {
// 		if ($a::ORDER_INDEX > $b::ORDER_INDEX) {
// 			return 1;
// 		}
// 		else if ($a::ORDER_INDEX < $b::ORDER_INDEX) {
// 			return -1;
//		}

		return 0;
	}

	public function getList() {
		$result = array_values(self::$helpers);
		usort($result, array($this, "sortByOrder"));

		return $result;
	}

}