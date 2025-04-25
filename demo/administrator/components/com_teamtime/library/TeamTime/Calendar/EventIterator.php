<?php

class TeamTime_Calendar_EventIterator implements Iterator {

	// iterator params
	protected $start; //start date for iteration
	protected $end; //end date
	protected $repeat_mode;
	protected $repeat_interval;
	// calculated
	protected $start_time; //start date for iteration in unix time
	protected $end_time; //end date in unix time
	protected $direction;
	protected $repeat_str;
	protected $is_month_last_day;
	protected $current;
	protected $position;
	protected $i;

	function __construct($start, $end, $repeat_mode = "weekly",
			$repeat_interval = 1) {
		$this->start = $start;
		$this->end = $end;
		$this->repeat_mode = $repeat_mode;
		$this->repeat_interval = $repeat_interval;

		$this->start_time = strtotime($this->start);
		$this->end_time = strtotime($this->end);

		$this->direction = $this->start_time < $this->end_time ? 1 : -1;

		//check repeat mode
		switch ($this->repeat_mode) {
			case 'weekly':
				$this->repeat_str = "week";
				break;
			case 'monthly':
				$this->repeat_str = "month";
				break;
			case 'yearly':
				$this->repeat_str = "year";
				break;
			default:
				$this->repeat_str = "week";
		}

		// check day on last in month
		$this->is_month_last_day = date('t', strtotime($this->start)) == date('j',
						strtotime($this->start));
	}

	function rewind() {
		$this->i = 0;
		$this->position = 0;
		$this->current = $this->start_time;
	}

	function current() {
		return date("Y-m-d H:i:s", $this->current);
	}

	function current_unix() {
		return $this->current;
	}

	function key() {
		return $this->position;
	}

	function next() {
		$date = $this->current;

		// append date interval
		if ($this->repeat_str == "month" && $this->is_month_last_day) {
			// for month - set last day month date
			$date = mktime(
					date("H", $date), date("i", $date), date("s", $date),
					date("m", $date) + $this->direction, 1, date("Y", $date));
			$date = strtotime(date("Y-m", $date) . "-" . date('t', $date) . " " . date("H:i:s",
							$date));
		}
		else {
			$this->i += $this->repeat_interval * $this->direction;
			$date = strtotime($this->start .
					($this->direction == 1 ? " +" : " -") . abs($this->i) . " " . $this->repeat_str);
		}

		$this->current = $date;
		$this->position++;
	}

	function valid() {
		$result = false;

		if ($this->direction == 1)
			$result = $this->current <= $this->end_time;
		else if ($this->direction == -1)
			$result = $this->current >= $this->end_time;

		return $result;
	}

}
