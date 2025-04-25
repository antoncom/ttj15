<?php

require_once(dirname(__FILE__) . "/EventIterator.php");

class Calendar_EventWeekIterator extends Calendar_EventIterator {

	private $days;
	private $week_day_index;
	private $week = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');

	function __construct($start, $end, $repeat_mode = "weekly", $repeat_interval = 1, $days = array()) {
		parent::__construct($start, $end, "weekly", $repeat_interval);

		$this->days = $days;
	}

	function rewind() {
		parent::rewind();

		$this->reset_week();
		$this->next_marked_weekday();
	}

	function reset_week() { //start process week
		$this->week_day_index = 0;

		// align date to monday and check marked week days
		$offset_days = date("w", $this->current) - 1;

		if ($offset_days != 0) {
			if ($offset_days < 0) //for sunday -6 day
				$offset_days = 6;
			$this->current = strtotime(date("Y-m-d H:i:s", $this->current) .
					($offset_days > 0 ? " -" : " +") . abs($offset_days) . " days");
		}
	}

	function next_marked_weekday() { //get next week marked day date
		while ($this->week_day_index < sizeof($this->week)) {
			$week_day_name = $this->week[$this->week_day_index];
			if (isset($this->days[$week_day_name]) && (int) $this->days[$week_day_name] == 1)
				break;

			$this->week_day_index++;
			$this->current += 86400; //24 * 60 * 60
		}
	}

	function next() {
		$this->week_day_index++;
		$this->current += 86400; //24 * 60 * 60
		$this->next_marked_weekday();

		//if all week day processed
		if ($this->week_day_index >= sizeof($this->week)) {
			//append date interval
			$this->i += $this->repeat_interval * $this->direction;
			$this->current = strtotime($this->start .
					($this->direction == 1 ? " +" : " -") . abs($this->i) . " " . $this->repeat_str);

			$this->reset_week();
			$this->next_marked_weekday();
		}

		$this->position++;
	}

}
