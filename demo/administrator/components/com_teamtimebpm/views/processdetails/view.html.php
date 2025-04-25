<?php

class TeamtimebpmViewProcessdetails extends JView {

	protected function isAllowed() {
		$isTemplate = JRequest::getVar("is_template", 0);
		$controller = JRequest::getWord('controller');
		if ($isTemplate) {
			$controller = "template";
		}

		$cid = array(JRequest::getVar("process_id", 0));

		$acl = new TeamTime_Acl();
		if (!$acl->isAllowByProject($cid, $controller)) {
			JError::raiseWarning(0, JText::_('Access denied'));
			return false;
		}

		return true;
	}

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		JHTML::script('process-details.js', URL_MEDIA_COMPONENT_ASSETS . "js/teamtimebpm/");

		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE, TEAMLOG_ICON);

		$controller = JRequest::getWord('controller');

		$todo = new Todo();
		$mTodo = new TeamtimeModelTodo();
		$id = JRequest::getVar("_id", 0);
		$item = $mTodo->getById($id);

		$isTemplate = JRequest::getVar("is_template", 0);
		$processId = JRequest::getVar("process_id", 0);
		if (!$isTemplate) {
			$model = new TeamtimebpmModelProcess();
		}
		else {
			$model = new TeamtimebpmModelTemplate();
		}

		if (!$this->isAllowed()) {
			return;
		}

		$process = $model->getById($processId);

		//$item->parent_id = $mTodo->getParentTodo($item->id);
		$item->parent_id = JRequest::getVar("_parent_id", "");

		$hourlyRate = $todo->getHourlyRate($id);

		$mRole = new BpmnRoleModelBpmnRole();
		$mSpace = new TeamtimebpmModelSpace();

		// user select
		if ($item->id) {
			$userId = $item->user_id;
		}
		else {
			$role = JRequest::getVar("role", "");
			$userId = $mRole->getUserIdByRole($role);
		}
		$options = JHTML::_('select.option', '', '- ' . JText::_('User') . ' -');
		$lists['select_user'] = JHTML::_('teamtime.userlist', $options, 'user_id',
						'style="width:150px" class="inputbox"', 'value', 'text', $userId);

		// state select
		$options = JHTML::_('select.option', '', '- ' . JText::_('State') . ' -');
		$lists['select_state'] = JHTML::_('teamtime.todostatelist', $options, 'state',
						'style="width:130px" class="inputbox"', 'value', 'text',
						$item->id ? $item->state : TODO_STATE_PROJECT);

		// project select
		if ($item->id) {
			$projectId = $item->project_id;
		}
		else {
			$projectId = $process->project_id;
		}

		$options = JHTML::_('select.option', '', '- ' . JText::_('Project') . ' -');
		$lists['select_project'] = JHTML::_(
						'teamtime.projectListState0', $options, 'project_id', 'style="width:100%" class="inputbox"',
						'value', 'text', $projectId);
		$lists['select_project'] = str_replace(
				'<option value=""  selected="selected"></option>', "",
				str_replace(
						'<option value="" ></option>', "", $lists['select_project']));

		// task select
		$options = array(array("value" => '', "text" => '- ' . JText::_('Task') . ' -'));
		$lists['select_task'] = TeamTime::helper()->getBase()
				->getTasksList($item->project_id, $options, 'curtaskid', $item->task_id,
				'style="width:150px" class="inputbox"');

		// todo select
		$options = JHTML::_('select.option', '', '- ' . JText::_('INCLUDED TO TEAM TODO') . ' -', 'value',
						'text', false);
		if ($projectId != "") {
			$lists['select_todo'] = JHTML::_('teamtime.todolist', $projectId, $item->id, $options,
							'curtodoid', 'class="inputbox"', 'value', 'text', $item->parent_id ? $item->parent_id : "-");
		}
		else {
			$lists['select_todo'] = JHTML::_('select.genericlist',
							array(JHTML::_('select.option', '', '- ' . JText::_("Select project first") . ' -')),
							'curtodoid', 'class="inputbox"', 'value', 'text', '');
		}

		if ($item->id) {
			$descr = $item->description;
			$hourlyRate = $item->hourly_rate;
			$currentDate = JHTML::_('date', $item->created, '%Y-%m-%d %H:%M:%S');
			$todoHoursPlan = $item->hours_plan;
		}
		else {
			$descr = $mTodo->getDefaultDescription();
			$currentDate = date("Y-m-d H:i:s");
		}

		list($todoDate, $todoHours) = explode(" ", $currentDate);
		$todoHours = explode(":", $todoHours);
		array_pop($todoHours);
		$todoHours = implode(":", $todoHours);

		$defaultIsAllday = 0;

		// goals select
		$lists['select_goals'] = TeamTime::helper()->getDotu()
				->targetVectorSelectorByTodo($item, 'Select vector of goals');

		$controllerUrl = JURI::base() . "index.php?option={$option}&controller={$controller}";

		$this->assignRef('todo', $todo);
		$this->assign('hourly_rate', $hourlyRate);

		$this->assignRef('controllerUrl', $controllerUrl);
		$this->assignRef('event', $item);
		$this->assignRef('item', $item);
		$this->assignRef('process', $process);
		//$this->assignRef('default_isallday', $default_isallday);
		$this->assignRef('todo_date', $todoDate);
		$this->assignRef('todo_hours', $todoHours);
		$this->assignRef('todo_hours_plan', $todoHoursPlan);
		$this->assignRef('lists', $lists);
		$this->assignRef('descr', $descr);

		parent::display($tpl);
	}

}