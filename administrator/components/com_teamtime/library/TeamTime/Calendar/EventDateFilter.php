<?php

class TeamTime_Calendar_EventDateFilter extends FilterIterator {

	private $start_date;
	private $end_date;

	public function __construct($it, $start_date, $end_date) {
		parent::__construct($it);

		$this->start_date = strtotime($start_date);
		$this->end_date = strtotime($end_date);
	}

	public function accept() {
		$current = $this->getInnerIterator()->current_unix();

		return $current >= $this->start_date && $current <= $this->end_date;
	}

}