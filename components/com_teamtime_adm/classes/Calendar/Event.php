<?php

require_once(dirname(__FILE__) . "/EventIterator.php");
require_once(dirname(__FILE__) . "/EventWeekIterator.php");
require_once(dirname(__FILE__) . "/EventDateFilter.php");

class Calendar_Event {

	private $data;

	function __construct($opts) {
		$this->data = $opts;
	}

	function generate_dates() {
		$result = array();

		$days = $this->get_marked_weekdays();

		if (sizeof($days) == 0) {
			$it = new Calendar_EventDateFilter(
							new Calendar_EventIterator($this->data->created, $this->data->end_date,
									$this->data->repeat_mode, $this->data->repeat_interval),
							$this->data->start_date, $this->data->end_date);
			foreach ($it as $key => $value) {
				$result[] = $value;
			}

			$it = new Calendar_EventDateFilter(
							new Calendar_EventIterator($this->data->created, $this->data->start_date,
									$this->data->repeat_mode, $this->data->repeat_interval),
							$this->data->start_date, $this->data->end_date);
			foreach ($it as $key => $value) {
				$result[] = $value;
			}
		}
		else {
			$it = new Calendar_EventDateFilter(
							new Calendar_EventWeekIterator($this->data->created, $this->data->end_date,
									$this->data->repeat_mode, $this->data->repeat_interval, $days),
							$this->data->start_date, $this->data->end_date);
			foreach ($it as $key => $value) {
				$result[] = $value;
			}

			$it = new Calendar_EventDateFilter(
							new Calendar_EventWeekIterator($this->data->created, $this->data->start_date,
									$this->data->repeat_mode, $this->data->repeat_interval, $days),
							$this->data->start_date, $this->data->end_date);
			foreach ($it as $key => $value) {
				$result[] = $value;
			}
		}

		$result = array_unique($result);
		sort($result);

		return $result;
	}

	function get_marked_weekdays() {
		$week = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');

		$result = array();
		$has_marked = false;

		foreach ($week as $week_day_name) {
			$f = "repeat_{$week_day_name}";

			if (isset($this->data->{$f}) && (int) $this->data->{$f} == 1) {
				$has_marked = true;
				$result[$week_day_name] = 1;
			}
			else
				$result[$week_day_name] = 0;
		}

		return $has_marked ? $result : array();
	}

}
