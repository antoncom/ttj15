<?php

// Кассовый ордер
defined('_JEXEC') or die('Restricted access');

$result_content = "";
$total_hours_plan = 0;
$total_price = 0;
$total_sum = 0;
$total_tax = array();

$i = 1;
foreach ($data as $project_type_data) {
	$current_hours_plan = 0;
	$current_price = 0;
	$current_sum = 0;

	// calc current price
	foreach ($project_type_data as $todo_data) {
		$current_hours_plan += $todo_data->hours_plan;
		$current_price += $todo_data->hours_plan * $todo_data->hourly_rate;
	}

	// process tax
	$current_tax = 0;
	foreach (range(1, 10) as $v) {
		if (!isset($variables["current_user_tax_" . $v])) {
			continue;
		}

		// exclude user_tax1
		if ($v > 1) {
			$current_tax += $variables["current_user_tax_" . $v] / 100;
		}
	}
	$current_price = $current_price / (1 + $current_tax);
	$current_sum = $current_price - $current_price * ($variables["current_user_tax_1"] / 100);

	foreach (range(1, 10) as $v) {
		if (!isset($variables["current_user_tax_" . $v])) {
			continue;
		}

		$t = $variables["current_user_tax_" . $v] * ($current_price / 100);
		$total_tax["user_tax_" . $v] += $t;
	}

	$total_hours_plan += $current_hours_plan;
	$total_price += $current_price;
	$total_sum += $current_sum;

	$i++;
}

// replace default sum values
$total_sum_price = round($total_sum, 2);

$variables["total_user_sum"] = $total_sum_price;
$price_str = TeamTime::helper()->getFormals()->num2curr($total_sum_price);
$variables["total_user_sum_string"] = $price_str[1];
$variables["total_user_sum_string_rest"] = $price_str[2];

// return generated content
$tpl->setCurrentBlock();
$tpl->touchBlock("__global__"); // use if not set variables

$result_content .= $tpl->get();