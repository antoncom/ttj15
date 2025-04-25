<?php

// Смета
defined('_JEXEC') or die('Restricted access');

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

	$type_sum_price = 0;
	$type_sum_hour = 0;
	$type_sum_expenses = 0;

	foreach ($type_data as $todo_data) {
		$tpl->setCurrentBlock("todos");

		$tpl->setVariable("unit_measured", JText::_('MAN HR'));
		$tpl->setVariable("type_num", $i);
		$tpl->setVariable("todo_num", $j);
		$tpl->setVariable("todo_name", $todo_data->title);
		$tpl->setVariable("todo_hours_plan", $todo_data->hours_plan);

		// calculate dynamic rate
		if ($todo_data->project_dynamic_rate) {
			// check price by dotu
			$tmp_rate = TeamTime::helper()->getDotu()->getTargetPrice(array(
				"task_id" => $todo_data->task_id
					), true);
			if ($tmp_rate === null || ($tmp_rate instanceof TeamTime_Undefined)) {
				$tmp_rate = $todo_data->task_rate;
			}
			$tmp_rate = $todo_data->project_hourly_rate * $tmp_rate;
		}
		// get static rate
		else {
			$tmp_rate = $todo_data->project_hourly_rate;
		}
		$tpl->setVariable("todo_project_hourly_rate", round($tmp_rate));

		$tmp_expenses = $todo_data->costs;
		$tpl->setVariable("todo_expenses", $tmp_expenses);

		$tmp_price = round($tmp_rate * $todo_data->hours_plan + $tmp_expenses);
		$tpl->setVariable("todo_price", $tmp_price);

		$tpl->parse("todos");
		$j++;
		$type_sum_expenses += $tmp_expenses;
		$type_sum_price += $tmp_price;
		$type_sum_hour += $todo_data->hours_plan;
	}

	$tpl->setCurrentBlock("types");
	$tpl->setVariable("type_num", $i);
	$tpl->setVariable("type_sum_expenses", $type_sum_expenses);
	$tpl->setVariable("type_sum_price", round($type_sum_price));
	$tpl->setVariable("type_sum_hour", $type_sum_hour);

	$tpl->parseCurrentBlock("types");
	$i++;
	$total_sum_expenses += $type_sum_expenses;
	$total_sum_price += $type_sum_price;
	$total_sum_hour += $type_sum_hour;
}

$tpl->setCurrentBlock();

$tpl->setVariable("total_overhead_expenses", $total_sum_expenses);
$tpl->setVariable("total_sum", round($total_sum_price));
$tpl->setVariable("total_manhours", $total_sum_hour);

$price_str = TeamTime::helper()->getFormals()->num2curr(round($total_sum_price));

$tpl->setVariable("total_sum_string", $price_str[1]);
$tpl->setVariable("total_sum_string_rest", $price_str[2]);

// return generated content
$result_content .= $tpl->get();