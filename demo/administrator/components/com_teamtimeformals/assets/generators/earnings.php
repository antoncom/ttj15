<?php

// Мое вознаграждение
defined('_JEXEC') or die('Restricted access');

$result_content = "";
$total_hours_plan = 0;
$total_price = 0;
$total_price_hours = 0;
$total_sum = 0;
$total_tax = array();

$is_dynamic = isset($params["is_dynamic"]) && $params["is_dynamic"];

// date filter
if (!$is_dynamic) {
	// process as document
	$tpl->setCurrentBlock("block_header_normal");
	$tpl->touchBlock("block_header_normal");

	$tpl->setCurrentBlock("block_footer_normal");
	$tpl->touchBlock("block_footer_normal");
}
else {
	// process as dynamic page
	foreach ($rows_year_data as $year => $months) {
		$tpl->setCurrentBlock("row_year");
		$tpl->setVariable("dynamic_page_url",
				$params['dynamic_url'] .
				"&from_period={$year}-{$months[0]}-01&until_period={$year}-{$months[0]}-31");
		$tpl->setVariable("current_year", $year);
		if ($year == date("Y", strtotime($params["from_period"]))) {
			$tpl->setVariable("class_active", "class='active'");
		}
		$tpl->parse("row_year");
	}

	foreach ($rows_year_data as $year => $months) {
		$tpl->setCurrentBlock("row_year1");
		$tpl->setVariable("dynamic_page_url",
				$params['dynamic_url'] .
				"&from_period={$year}-{$months[0]}-01&until_period={$year}-{$months[0]}-31");
		$tpl->setVariable("current_year", $year);
		if ($year == date("Y", strtotime($params["from_period"]))) {
			$tpl->setVariable("selected", "selected");
		}
		$tpl->parse("row_year1");
	}

	$year = date("Y", strtotime($params["from_period"]));
	if (isset($rows_year_data[$year])) {
		foreach ($rows_year_data[$year] as $month) {
			$tpl->setCurrentBlock("row_month1");
			$tpl->setVariable("dynamic_page_url",
					$params['dynamic_url'] .
					"&from_period={$year}-{$month}-01&until_period={$year}-{$month}-31");
			$tpl->setVariable("current_month",
					JText::_(
							"STR1_MONTH" . (int) date("m", strtotime("{$year}-{$month}-01"))));
			if ((int) $month == (int) date("m", strtotime($params["from_period"]))) {
				$tpl->setVariable("selected", "selected");
			}
			$tpl->parse("row_month1");
		}
	}
}

$tpl->setCurrentBlock();

$url_info = parse_url($params['dynamic_url']);
$url_info["path"] = JURI::base(); 
$tpl->setVariable(
		"images_url", $url_info["path"] . "components/com_teamtimeformals/assets");

// process tax headers blocks
foreach (range(1, 10) as $v) {
	if (!isset($variables["current_user_tax_" . $v])) {
		continue;
	}
	$tpl->setVariable(
			"user_tax_" . $v . "_name", $variables_names["user_tax_" . $v]);
	$total_tax["user_tax_" . $v] = 0;
}

$i = 1;
foreach ($data as $project_type_data) {
	$tpl->setCurrentBlock("rows");

	$tpl->setVariable("current_project_name", $project_type_data[0]->project_name);
	$tpl->setVariable("current_type_name", $project_type_data[0]->type_name);
	$tpl->setVariable("class_even_row", $i % 2 == 0 ? "class='even_row'" : "");

	$current_hours_plan = 0;
	$current_price = 0;
	$current_price_hours = 0;
	$current_sum = 0;

	// calc current price
	foreach ($project_type_data as $todo_data) {
		$current_hours_plan += $todo_data->hours_plan;
		$current_price += $todo_data->hours_plan * $todo_data->hourly_rate;
	}
	$current_price_hours = $current_price;

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
		$tpl->setVariable("user_tax_" . $v . "_value", round($t, 2));
	}

	if (!$is_dynamic) {
		$tpl->setVariable("current_hours_plan", $current_hours_plan);
	}
	else {
		$filter_str = "&filter_project={$todo_data->project_id}&filter_type={$todo_data->type_id}" .
				"&filter_from={$params['from_period']}&filter_until={$params['until_period']}";
		$tpl->setVariable(
				"current_hours_plan",
				"<a href='{$url_info["path"]}index.php?option=com_teamtime&view=reports"
				. $filter_str . "'>" . $current_hours_plan . "</a>");
	}
	$tpl->setVariable("current_price", round($current_price, 2));
	$tpl->setVariable("current_price_hours", round($current_price_hours, 2));
	$tpl->setVariable("current_sum", round($current_sum, 2));

	$tpl->parse("rows");

	$total_hours_plan += $current_hours_plan;
	$total_price_hours += $current_price_hours;
	$total_price += $current_price;
	$total_sum += $current_sum;

	$i++;
}

// months block
$total_price_by_date = array();
// process months data
foreach ($rows_todos_year as $todo_data) {
	// sum price for month-year
	$date_str = JText::_("STR1_MONTH" . (int) date("m",
							strtotime($todo_data->created))) . " " .
			JHTML::_('date', $todo_data->created, "%Y");

	if (JHTML::_('date', $todo_data->created, "%Y") != JHTML::_('date',
					$params["from_period"], "%Y")) {
		continue;
	}

	if (!isset($total_price_by_date[$date_str])) {
		$total_price_by_date[$date_str] =
				array(0, 0, JHTML::_('date', $todo_data->created, "%Y-%m"));
	}

	$current_price = $todo_data->hours_plan * $todo_data->hourly_rate;
	$current_date_tax = strtotime(date("Y-m-01", strtotime($todo_data->created)));

	// get current tax sum
	$current_tax = 0;
	$current_tax_1 = 0;
	foreach (range(1, 10) as $v) {
		if (!isset($variables["current_user_tax_" . $v])) {
			continue;
		}

		// set tax N for selected date
		$tmp_tax = 0;
		foreach ($variables["user_tax_" . $v] as $d => $tax)
			if (strtotime($d) <= $current_date_tax) {
				$tmp_tax = $tax;
				if ($v == 1) {
					$current_tax_1 = $tmp_tax;

					// exclude user_tax_1
					$tmp_tax = 0;
				}
				break;
			}

		if ($tmp_tax > 0)
			$current_tax += $tmp_tax / 100;
	}
	$current_price = $current_price / (1 + $current_tax);
	$current_sum = $current_price - $current_price * ($current_tax_1 / 100);

	$total_price_by_date[$date_str][0] += $current_price;
	$total_price_by_date[$date_str][1] += $current_sum;
}

$period_total_price = 0;
$period_total_sum = 0;
foreach ($total_price_by_date as $k => $price_value) {
	$tpl->setCurrentBlock("periodrows");

	if ($is_dynamic) {
		$k = "<a href='" . $params['dynamic_url'] .
				"&from_period={$price_value[2]}-01&until_period={$price_value[2]}-31" .
				"'>" . $k . "</a>";
	}

	$tpl->setVariable("period_current_date", $k);
	$tpl->setVariable("period_current_price", round($price_value[0], 2));
	$tpl->setVariable("period_current_sum", round($price_value[1], 2));

	$tpl->parse("periodrows");

	$period_total_price += $price_value[0];
	$period_total_sum += $price_value[1];
}

$tpl->setCurrentBlock();

// process tax total sum blocks
foreach (range(1, 10) as $v) {
	if (!isset($variables["current_user_tax_" . $v])) {
		continue;
	}
	$tpl->setVariable("user_tax_" . $v . "_sum",
			round($total_tax["user_tax_" . $v], 2));
}

$tpl->setVariable("total_hours_plan", $total_hours_plan);
$tpl->setVariable("total_price", round($total_price, 2));
$tpl->setVariable("total_price_hours", round($total_price_hours, 2));
$tpl->setVariable("total_sum", round($total_sum, 2));

$tpl->setVariable("period_total_price", round($period_total_price, 2));
$tpl->setVariable("period_total_sum", round($period_total_sum, 2));

// return generated content
$result_content .= $tpl->get();