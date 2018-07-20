<?php

// Смета
defined('_JEXEC') or die('Restricted access');

//error_log('====PLAN-FACT-DATA: ' . print_r($data, true), 3, "/home/mediapub/teamlog.teamtime.info/docs/logs/my-errors.log");
//error_log('====PLAN-FACT-PARAMS: ' . print_r($params, true), 3, "/home/mediapub/teamlog.teamtime.info/docs/logs/my-errors.log");
//error_log('====PLAN-FACT-VARS: ' . print_r($variables, true), 3, "/home/mediapub/teamlog.teamtime.info/docs/logs/my-errors.log");

TeamTime::helper()->getBpmn()->addLinkedProcesses($data, $params, $variables);

$result_content = "";
$total_sum_price = 0;
$total_sum_hour = 0;
$total_sum_expenses = 0;

$i = 1;
foreach ($data as $type_data) {
	$type_data = TeamTime::helper()->getFormals()
			->generatorGroupByProjectTaskType($type_data);

	$j = 1;
	$tpl->setCurrentBlock("types");

	$tpl->setVariable("type_name", $type_data[0]->type_name);

	$type_sum_facthour = 0;
	$type_sum_planhour = 0;
	$progress = 0;

	foreach ($type_data as $todo_data) {
		$tpl->setCurrentBlock("todos");

		$tpl->setVariable("type_num", $i);
		$tpl->setVariable("todo_num", $j);
		$tpl->setVariable("todo_name", $todo_data->title);
		$tpl->setVariable("todo_hours_plan", $todo_data->hours_plan);
		$tpl->setVariable("todo_hours_fact", $todo_data->hours_fact);
		if($todo_data->hours_plan > $todo_data->hours_fact) {
			$progress = round(100 * ($todo_data->hours_fact / $todo_data->hours_plan ));
		}
		else {
			$progress = 100;
		}
		$tpl->setVariable("todo_progress", $progress . ' %');

		// calculate dynamic rate
//		if ($todo_data->project_dynamic_rate) {
//			// check price by dotu
//			$tmp_rate = TeamTime::helper()->getDotu()->getTargetPrice(array(
//				"task_id" => $todo_data->task_id
//					), true);
//			if ($tmp_rate === null || ($tmp_rate instanceof TeamTime_Undefined)) {
//				$tmp_rate = $todo_data->task_rate;
//			}
//			$tmp_rate = $todo_data->project_hourly_rate * $tmp_rate;
//		}
//		// get static rate
//		else {
//			$tmp_rate = $todo_data->project_hourly_rate;
//		}
//		$tpl->setVariable("todo_project_hourly_rate", round($tmp_rate));
//
//		$tmp_expenses = $todo_data->costs;
//		$tpl->setVariable("todo_expenses", $tmp_expenses);
//
//		$tmp_price = round($tmp_rate * $todo_data->hours_plan + $tmp_expenses);
//		$tpl->setVariable("todo_price", $tmp_price);

		$tpl->parse("todos");
		$j++;
//		$type_sum_expenses += $tmp_expenses;
//		$type_sum_price += $tmp_price;
		$type_sum_facthour += $todo_data->hours_fact;
		$type_sum_planhour += $todo_data->hours_plan;
	}
	if($type_sum_planhour > $type_sum_facthour) {
		$type_sum_progress = round(100 * ($type_sum_facthour / $type_sum_planhour ));
	}
	else {
		$type_sum_progress = 100;
	}

	$tpl->setCurrentBlock("types");
	$tpl->setVariable("type_num", $i);
//	$tpl->setVariable("type_sum_expenses", $type_sum_expenses);
//	$tpl->setVariable("type_sum_price", round($type_sum_price));
	$tpl->setVariable("type_sum_planhour", round($type_sum_planhour));
	$tpl->setVariable("type_sum_facthour", round($type_sum_facthour));
	$tpl->setVariable("type_sum_progress", $type_sum_progress . ' %');

	$tpl->parseCurrentBlock("types");
	$i++;
//	$total_sum_expenses += $type_sum_expenses;
//	$total_sum_price += $type_sum_price;
	$total_sum_planhour += $type_sum_planhour;
	$total_sum_facthour += $type_sum_facthour;
}

if($total_sum_planhour > $total_sum_facthour) {
	$progressTotal = round(100 * ($total_sum_facthour / $total_sum_planhour ));
}
else {
	$progressTotal = 100;
}

$tpl->setCurrentBlock();

$tpl->setVariable("type_sum_total_planhour", round($total_sum_planhour));
$tpl->setVariable("type_sum_total_facthour", round($total_sum_facthour));
$tpl->setVariable("type_sum_total_progress", $progressTotal . ' %');



// return generated content
$result_content .= $tpl->get();