<?php

class TeamlogControllerReports extends JController {

	function __construct($default = array()) {
		parent::__construct($default);
	}

	public function display() {
		// set the default view
		$view = JRequest::getCmd('view');
		if (empty($view)) {
			JRequest::setVar('view', 'reports');
		}

		parent::display();
	}

	public function load_description() {
		$mTodo = new TeamtimeModelTodo();
		$mTodo->showReportTodo();
		jexit();
	}

	public function loadReport() {
		$mReport = new TeamtimeModelReport();
		$mReport->showReport();
		jexit();
	}

}