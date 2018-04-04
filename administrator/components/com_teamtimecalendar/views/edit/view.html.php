<?php

class TeamtimecalendarViewEdit extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');
		
		$controller = JRequest::getCmd('controller');

		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE, TEAMLOG_ICON);

		JHTML::stylesheet('main_edit.css', URL_MEDIA_COMPONENT_ASSETS . 'css/');
		JHTML::script('todo-details.js', URL_MEDIA_COMPONENT_ASSETS . "js/teamtimecalendar/");

		$helperBase = TeamTime::helper()->getBase();
		$helperCalendar = TeamTime::helper()->getCalendar();

		// get todo_id
		$parentTodoId = null;
		$event = new stdClass();
		$todoId = JRequest::getVar("id", "", 'get');
		if ($todoId) {
			$tmpId = explode("_", $todoId);

			// for make copy todo with parent data
			if ($tmpId[0] == "copy") {
				$event = $helperCalendar->getCalendarByRange($tmpId[1]);
				$event->id = null;
				$event->title = JText::_('SUBTODO FOR') . " " . $event->title;
				$event->is_parent = false;
				$parentTodoId = $tmpId[1];
			}
			else {
				$event = $helperCalendar->getCalendarByRange($tmpId[0]);
			}
		}
		$todo = new Todo();
		$hourlyRate = $todo->getHourlyRate(JRequest::getVar('id', 0));

		$todoHours = JRequest::getVar("todo_hours", 0);
		if ($todoHours) {
			$todoHours = explode(":", $todoHours);
			$todoHours[0] = str_pad($todoHours[0], 2, "0", STR_PAD_LEFT);
			$todoHours[1] = str_pad($todoHours[1], 2, "0", STR_PAD_LEFT);
			$todoHours = implode(":", $todoHours);
		}
		else {
			$todoHours = "";
		}

		$todoHoursPlan = JRequest::getVar("todo_hours_plan", null);
		if ($todoHoursPlan) {
			$todoHoursPlan = $todoHoursPlan / 1000;
		}

		$userId = JRequest::getVar('user_id', null);
		$projectId = JRequest::getVar('project_id', null);

		$taskId = JRequest::getVar('task_id', null);
		$typeId = JRequest::getVar('type_id', null);
		if ($taskId && $typeId) {
			$taskId = $helperCalendar->getTaskId($taskId, $typeId, $projectId);
		}

		$currentDate = JRequest::getVar("current_date", "");
		$todoDate = JRequest::getVar("todo_date", "");
		$defaultIsAllDay = JRequest::getVar("isallday", "");
		$defaultIsAllDay = $defaultIsAllDay == "true" ? 1 : 0;

		$lists['select_user'] = $helperBase->getUserSelect($event->user_id ? $event->user_id : $userId,
				array(
			"autosubmit" => false,
			"attrs" => 'style="width:150px"',
				));

		$options = JHTML::_('select.option', '', '- ' . JText::_('State') . ' -');
		$lists['select_state'] = JHTML::_('teamtime.todostatelist', $options, 'state',
						'style="width:130px" class="inputbox"', 'value', 'text', $event ? $event->state : '0');

		$lists['select_project'] = $helperBase->getProjectSelect(
				$event->project_id ? $event->project_id : $projectId,
				array(
			"autosubmit" => false,
			"attrs" => 'style="width:100%"',
			"firstOptions" => JHTML::_('select.option', '', '- ' . JText::_('Project') . ' -'),
			"showClosed" => false
				));

		$lists['select_task'] = $helperBase->getTypesTasksSelect(
				$event->project_id ? $event->project_id : $projectId,
				$event->task_id ? $event->task_id : $taskId,
				array(
			"attrs" => 'style="width:150px"',
			"fieldId" => 'curtaskid'
				));

		// todo select
		$mTodo = new TeamtimeModelTodo();
		if ($parentTodoId) {
			$event->parent_id = $parentTodoId;
		}
		else {
			$event->parent_id = $mTodo->getParentTodo($event->id);
		}
		$options = JHTML::_('select.option', '', '- ' . JText::_('INCLUDED TO TEAM TODO') . ' -', 'value',
						'text', false);

		if ($event->project_id != "") {
			$lists['select_todo'] = JHTML::_('teamtime.todolist', $event->project_id, $event->id, $options,
							'curtodoid', 'class="inputbox"', 'value', 'text', $event->parent_id ? $event->parent_id : "-");
		}
		else {
			$lists['select_todo'] = JHTML::_('select.genericlist',
							array(JHTML::_('select.option', '', '- ' . JText::_("Select project first") . ' -')),
							'curtodoid', 'class="inputbox"', 'value', 'text', '');
		}

		if ($event->id || $parentTodoId) {
			$descr = $event->description;
			$hourlyRate = $event->hourly_rate;
		}
		else {
			$params = array();
			if ($userId) {
				$params["user_id"] = $userId;
			}
			if ($taskId) {
				$params["task_id"] = $taskId;
			}
			if ($typeId) {
				$params["type_id"] = $typeId;
			}
			if ($projectId) {
				$params["project_id"] = $projectId;
			}
			$hourlyRate = $todo->getHourlyRateByParams($params, $hourlyRate);
			$descr = $mTodo->getDefaultDescription();
		}

		// goals select
		$lists['select_goals'] = TeamTime::helper()->getDotu()
				->targetVectorSelectorByTodo($event, 'Select vector of goals');

		$controllerUrl = JURI::base() .
				"index.php?option=" . $option . "&controller=calendar";
		$calendarVariables = array(
			"controllerUrl" => $controllerUrl,
			"todoId" => $event->id ? $event->id : 0,
			"currentDate" => array(
				date("Y", $currentDate),
				date("m", $currentDate),
				date("d", $currentDate),
				date("H", $currentDate),
				date("i", $currentDate),
				date("s", $currentDate)
			),
			//
			"text" => array(
				"select_user_project_task" => JText::_("Please select user, project and task"),
				"are_you_sure_to_delete_this_todo" => JText::_("Are you sure to delete this Todo"),
				"check_project_user_str" => JText::_("CHECK_PROJECT_USER_STR"),
				"alert_project_user_str" => JText::_("ALERT_PROJECT_USER_STR")
			)
		);
		TeamTime::helper()->getBase()->addJavaScript(array(
			"resource" => array("calendar" => $calendarVariables)
		));

		$this->assignRef('todo', $todo);
		$this->assign('hourly_rate', $hourlyRate);
		$this->assignRef('event', $event);
		$this->assignRef('controllerUrl', $controllerUrl);
		$this->assignRef('default_isallday', $defaultIsAllDay);
		$this->assignRef('todo_date', $todoDate);
		$this->assignRef('todo_hours', $todoHours);
		$this->assignRef('todo_hours_plan', $todoHoursPlan);
		$this->assignRef('lists', $lists);
		$this->assignRef('descr', $descr);

		parent::display($tpl);
	}

}