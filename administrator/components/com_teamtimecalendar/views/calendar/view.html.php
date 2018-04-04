<?php

class TeamtimecalendarViewCalendar extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$controller = JRequest::getCmd('controller');

		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE, TEAMLOG_ICON);
		JHTML::stylesheet('main.css', URL_MEDIA_COMPONENT_ASSETS . 'css/');
		JHTML::script('calendar.js', URL_MEDIA_COMPONENT_ASSETS . 'js/teamtimecalendar/');

		$helperBase = TeamTime::helper()->getBase();
		$helperCalendar = TeamTime::helper()->getCalendar();

		$filter = "";
		$projectId = JRequest::getVar('project_id', '');
		$userId = JRequest::getVar('user_id', '');
		$typeId = JRequest::getVar('type_id', 0);
		$taskId = JRequest::getVar('task_id', 0);
		$filterPeriod = JRequest::getVar('filter_period', '');

		$lists['select_project'] = $helperBase->getProjectSelect($projectId,
				array(
			"autosubmit" => false,
			"attrs" => 'style="width:150px;"',
			"showClosed" => false
				));

		$projectFilter = $helperBase->getProjectFilter($projectId);
		list($lists['select_type'], $typeId) = $helperBase->getTypesSelect($typeId, $projectFilter,
				array(
			"autosubmit" => false,
			"type" => "todo",
			"attrs" => 'style="width:150px;"'
				));
		list($lists['select_task'], $taskId) = $helperBase->getTasksSelect(
				$typeId, $taskId, $projectFilter,
				array(
			"autosubmit" => false,
			"type" => "task",
			"attrs" => 'style="width:150px;"'
				));

		// init filter
		if ($projectId != "") {
			$filter = "&project_id=" . $projectId;
		}
		if ($typeId) {
			$filter .= "&type_id=" . $typeId;
		}
		if ($taskId != "") {
			$filter .= "&task_id=" . $taskId;
		}
		if ($userId != "") {
			$filter .= "&user_id=" . $userId;
		}
		$filter .= "&filter_period=" . $filterPeriod;

		$lists['select_user'] = $helperBase->getUserSelect($userId,
				array(
			"autosubmit" => false,
			"firstOptions" => JHTML::_('select.option', '', '- ' . JText::_('Select User') . ' -'),
			"attrs" => 'style="width:150px"',
				));

		$options = array();
		$lists['select_period'] = JHTML::_(
						'teamtime.todoperiodlistadmin', $options, 'filter_period', 'class="inputbox"', 'value', 'text',
						$filterPeriod);

		// TODO !!!move to controller
		if (isset($_REQUEST["load_types"])) {
			print $lists["select_type"];
			jexit();
		}
		else if (isset($_REQUEST["load_tasks"])) {
			print $lists["select_task"];
			jexit();
		}

		$viewType = JRequest::getVar("view_type", "week");
		$startDate = JRequest::getVar("start_date", date("Y-m-d"));
		list($startYear, $startMonth, $startDay) = explode("-", $startDate);
		$startMonth--;
		$this->assignRef('view_type', $viewType);
		$this->assignRef('lists', $lists);		
		$this->assignRef('start_year', $startYear);
		$this->assignRef('start_month', $startMonth);
		$this->assignRef('start_day', $startDay);

		$this->assignRef('filter', $filter);

		$calendarVariables = array(
			"viewType" => $viewType,
			"controllerUrl" => JURI::base() .
				"index.php?option=" . $option . "&controller=" . $controller,
			"startYear" => $startYear,
			"startMonth" => $startMonth,
			"startDay" => $startDay,
			"filter" => $filter,
			"editScript" => JURI::base() .
			"index.php?option=" . $option . "&controller=" . $controller .
			"&view=edit&tmpl=component",
			//
			"text" => array(
				"create_new_calendar" => JText::_('Create New Calendar'),
				"ok" => JText::_("Ok"),
				"cancel" => JText::_("Cancel"),
				"are_you_sure_to_delete_this_todo" => JText::_("Are you sure to delete this Todo"),
				"confirm" => JText::_("Confirm"),
			)
		);
		TeamTime::helper()->getBase()->addJavaScript(array(
			"resource" => array("calendar" => $calendarVariables)
		));

		parent::display($tpl);
	}

}