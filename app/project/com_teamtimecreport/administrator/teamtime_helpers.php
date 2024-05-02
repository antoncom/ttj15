<?php

set_include_path(
		get_include_path() . PATH_SEPARATOR .
		JPATH_SITE . "/administrator/components/com_teamtimecreport/assets/PEAR");

function TeamTimeCReport_get_config() {
	$configName = JPATH_SITE
			. "/administrator/components/com_teamtimecreport/config.json";

	if (file_exists($configName)) {
		$result = json_decode(file_get_contents($configName));
	}
	else {
		$result = new stdClass();
		$result->col_date = 1;
		$result->col_project = 1;
		$result->col_type = 1;
		$result->col_task = 1;
		$result->col_todo = 1;
		$result->col_log = 1;
		$result->col_planned_actual_hours = 1;
		$result->col_actual_hours = 1;
		$result->col_hourly_rate = 1;
		$result->col_planned_cost = 1;
		$result->col_actual_cost = 1;
		$result->col_statement_cost = 1;
		$result->col_overhead_expenses = 1;
		$result->col_user = 1;
		$result->base_url = "http://teamtime.mediapublish.ru";
	}

	return $result;
}

