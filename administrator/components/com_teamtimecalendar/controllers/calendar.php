<?php

class TeamtimecalendarControllerCalendar extends Core_Joomla_Controller {

	public function __construct($default = array()) {
		parent::__construct($default);
	}

	public function display() {
		$view = JRequest::getCmd('view');
		if (empty($view)) {
			JRequest::setVar('view', 'calendar');
		}

		parent::display();
	}

	//
	// ajax actions
	//

	public function check_project_for_user() {
		$params = JRequest::get('get');
		print TeamTime::helper()->getBase()->checkProjectForUser($params);
		jexit();
	}

	public function enable_project_for_user() {
		$params = JRequest::get('get');
		print TeamTime::helper()->getBase()->enableProjectForUser($params);
		jexit();
	}

	public function todo_subtree() {
		$params = JRequest::get('get');
		$data = TeamTime::helper()->getCalendar()->todoTree($params);
		$this->toJson($data);
	}

	public function todo_info() {
		$params = JRequest::get('get');
		print TeamTime::helper()->getCalendar()->todoInfo($params);
		jexit();
	}

	public function list_data() {
		$params = JRequest::get('post');
		$helperCalendar = TeamTime::helper()->getCalendar();
		$this->toJson($helperCalendar->listCalendar($params["showdate"], $params["viewtype"]));
	}

	public function update_data() {
		$params = JRequest::get('post');
		$helperCalendar = TeamTime::helper()->getCalendar();
		$this->toJson($helperCalendar->updateCalendar(
						$params["calendarId"], $params["CalendarStartTime"], $params["CalendarEndTime"]));
	}

	public function remove_data() {
		$params = JRequest::get('post');
		$helperCalendar = TeamTime::helper()->getCalendar();
		$this->toJson($helperCalendar->removeCalendar($params["calendarId"]));
	}

	public function adddetails() {
		$getParams = JRequest::get('get');
		$postParams = JRequest::get('post');
		$result = TeamTime::helper()->getCalendar()->saveTodo($getParams, $postParams);
		$this->toJson($result);
	}

}