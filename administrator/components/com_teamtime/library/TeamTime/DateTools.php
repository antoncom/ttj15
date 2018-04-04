<?php

class TeamTime_DateTools {

	public function js2PhpTime($jsdate) {
		if (preg_match('@(\d+)/(\d+)/(\d+)\s+(\d+):(\d+)@', $jsdate, $matches) == 1) {
			$ret = mktime($matches[4], $matches[5], 0, $matches[1], $matches[2], $matches[3]);
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
				mktime($arr["hours"], $arr["minutes"], $arr["seconds"], $arr["mon"], $arr["mday"], $arr["year"]);

		return $date;
	}

	public function php2MySqlTime($phpDate) {
		return date("Y-m-d H:i:s", $phpDate);
	}

}