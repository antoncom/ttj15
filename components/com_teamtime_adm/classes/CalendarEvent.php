<?php

class CalendarEvent {

	function has_next($direction, $date, $start, $end) {
		$result = false;

		if ($direction == 1)
			$result = $date <= $end;
		else if ($direction == -1)
			$result = $date >= $start;

		return $result;
	}

	function in_range($direction, $date, $start, $end, $start_date, $end_date, $event_left_bound_date) {
		if ($direction == 1) {
			return $date >= $start_date &&
					($end_date != 0 ? $date <= $end_date : true) &&
					($date >= $start && $date <= $end);
		}

		if ($direction == -1) {
			return $date < $start_date &&
					($end_date != 0 ? $date <= $end_date : true) &&
					($date >= $event_left_bound_date && $date <= $end);
		}

		return false;
	}

	function process_repeated($events, $start, $end) {
		$week = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
		$result = array();

		foreach ($events as $row) {
			if ($row->id == null)
				continue;

			//check repeat mode
			switch ($row->repeat_mode) {
				case 'weekly':
					$str_interval = "week";
					break;
				case 'monthly':
					$str_interval = "month";
					break;
				case 'yearly':
					$str_interval = "year";
					break;
				default:
					$str_interval = "week";
			}

			//check day on last in month
			$is_month_last_day = date('t', strtotime($row->created)) == date('j', strtotime($row->created));

			//date for start repeated event
			$start_date = strtotime($row->created);
			$row_start_date = strtotime($row->start_date);

			//date for end repeated event
			$end_date = $row->end_date != '0000-00-00 00:00:00' ?
					strtotime($row->end_date) : 0;
			$end_date = $end_date ?
					mktime(23, 59, 59, date("m", $end_date), date("d", $end_date), date("Y", $end_date)) : 0;

			foreach (array(1, -1) as $direction) {
				$i = 0;
				$date = $start_date;
				while (CalendarEvent::has_next($direction, $date, $start, $end)) {
					//create copy for repeated event
					$current_date = $date;

					//align date to monday and check marked week days
					$offset_days = date("w", $date) - 1;
					if ($offset_days < 0) //for sunday -6 day
						$offset_days = 6;
					$date_str = date("Y-m-d H:i:s", $date) .
							($offset_days > 0 ? " -" : " +") . abs($offset_days) . " days";
					$date = strtotime($date_str);

					//default - is one day
					$is_one_day = true;
					foreach ($week as $week_day_name) {
						$field_name = "repeat_" . $week_day_name;

						//any week day checked
						if ((int) $row->{$field_name}) {
							$is_one_day = false; //reset is one day
							//check date in current range and repeated event range
							if (CalendarEvent::in_range($direction, $date, $start, $end, $start_date, $end_date,
											$row_start_date)) {
								//copy event with current date
								$new_row = clone($row);
								$new_row->created = date("Y-m-d H:i:s", $date);
								$result[] = $new_row;
							}
						}

						//$date = strtotime(date("Y-m-d H:i:s", $date) . " + 1 days");
						$date += 86400; //24 * 60 * 60
					}
					// if week days not checked - process as on day
					if ($is_one_day) {
						$date = $current_date;

						if (CalendarEvent::in_range($direction, $date, $start, $end, $start_date, $end_date,
										$row_start_date)) {
							//copy event with current date
							$new_row = clone($row);
							$new_row->created = date("Y-m-d H:i:s", $date);
							$result[] = $new_row;
						}
					}

					//append date interval
					if ($str_interval == "month" && $is_month_last_day) {
						//for month set last day month date
						$date = mktime(
								date("H", $date), date("i", $date), date("s", $date), date("m", $date) + $direction * 1, 1,
								date("Y", $date));
						$date = strtotime(date("Y-m", $date) . "-" . date('t', $date) . " " . date("H:i:s", $date));
					}
					else {
						$i += $row->repeat_interval * $direction;
						$date = strtotime($row->created . ($direction == 1 ? " +" : " -") . abs($i) . " " . $str_interval);
					}
				}
			}
		}

		return $result;
	}

	function sort_by_date($a, $b) {
		$at = strtotime($a->created);
		$bt = strtotime($b->created);
		return $at > $bt ?
				1 : ($at < $bt ? -1 : 0);
	}

	function sort_by_date2($a, $b) {
		$at = strtotime($a->tmp_repeat_date != "" ? $a->tmp_repeat_date : $a->created);
		$bt = strtotime($b->tmp_repeat_date != "" ? $b->tmp_repeat_date : $b->created);
		return $at > $bt ?
				1 : ($at < $bt ? -1 : 0);
	}

	function process_repeated_as_array($todos, $start, $end) {
		$result = array();
		$todos = CalendarEvent::process_repeated($todos, $start, $end);

		foreach ($todos as $row) {
			$row->real_created = $row->created;
			$result[] = get_object_vars($row);
		}

		return $result;
	}

	function sort_by_date_as_array($a, $b) {
		$at = strtotime($a["created"]);
		$bt = strtotime($b["created"]);
		return $at > $bt ?
				1 : ($at < $bt ? -1 : 0);
	}

	function filter_for_week($todos) {
		$w = date("w") - 1;
		if ($w < 0)
			$w = 6;

		$week_start = mktime(0, 0, 0, date("n"), date("j") - $w, date("Y"));
		$week_end = mktime(23, 59, 59, date("n"), date("j") - $w + 6, date("Y"));

		foreach ($todos as $i => $row) {
			if (strtotime($row->created) > $week_end)
				unset($todos[$i]);
		}

		return $todos;
	}

}