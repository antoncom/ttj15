<?php

// Daily report
defined('_JEXEC') or die('Restricted access');
TeamTime::helper()->getBpmn()->addLinkedProcesses($data, $params, $variables);



$result_content = "";
$total_sum_price = 0;
$total_sum_hour = 0;
$total_sum_expenses = 0;
$user_id = 121;

foreach ($data as $type_data) {
	$type_data = TeamTime::helper()->getFormals()
			->generatorGroupByProjectTaskType($type_data);

	$tpl->setCurrentBlock("types");

	$tpl->setVariable("type_name", $type_data[0]->type_name);

	foreach ($type_data as $todo_data) {
		if ($user_id == $todo_data->user_id) {

			$todo_log = TeamTime::helper()->getFormals()->getTodoLog($todo_data);

			error_log("--- TODO_LOG_DATA --");
			error_log(print_r($todo_log, true));

			$tpl->setCurrentBlock("todos");

			$tpl->setVariable("unit_measured", JText::_('MAN HR'));
			//$tpl->setVariable("todo_date", $j);
			$tpl->setVariable("todo_name", $todo_data->title);
			$tpl->setVariable("todo_hours_plan", $todo_data->hours_plan);
			$tpl->setVariable("todo_hourly_rate", $todo_data->hourly_rate);
		}
	}

}

// return generated content
$result_content .= $tpl->get();
