<?php

class TeamlogViewLog extends JView {

	public function display($tpl = null) {
		$mainframe = & JFactory::getApplication();

		// get request vars
		$option = JRequest::getCmd('option');
		$controller = JRequest::getWord('controller');

		$lists = array();

		$mLog = new TeamtimeModelLog();
		$user = & JFactory::getUser();
		$config = TeamTime::getConfig();

		// include assets
		$templatepre = strtolower(substr($mainframe->getTemplate(), 0, 3));
		JHTML::stylesheet('default.css', 'components/com_teamtime/assets/css/');
		if ($templatepre != 'yoo') {
			JHTML::stylesheet('reset.css', 'components/com_teamtime/assets/css/');
		}
		JHTML::stylesheet('todos.css', 'components/com_teamtime/assets/css/');
		JHTML::script('log.js', 'components/com_teamtime/assets/js/teamtime/');

		$unclog = $mLog->getUncompletedLog($user->id);
		if (sizeof($unclog) > 0) {
			$currentLog = $unclog[0];
		}
		else {
			$currentLog = null;
		}

		$todosVariables = array(
			"removeLogUrl" => JRoute::_('index.php?option=' . $option .
					'&controller=' . $controller . '&view=log&format=raw', false),
			"currentLog" => $currentLog,
			"text" => array(
				"are_you_sure_to_delete_this_log" => JText::_('Are you sure you want to delete the log?'),
				"todos_str" => JText::_("TODOS") . ": ",
				"done" => JText::_("DONE") . ": ",
				"confirm_remove_option"	=> JText::_("CONFIRM_REMOVE_CHECKLISTOPTION")
			)
		);
		TeamTime::helper()->getBase()->addJavaScript(array(
			"resource" => array("todos" => $todosVariables)
		));

		//reset filter (after fetch data)
		JRequest::setVar("reset_filter", 1);

		// get data from the model
		$mLog = new TeamlogModelLog();

		$user_logs = $mLog->getUserLogs();
		$other_logs = $mLog->getOtherLogs();
		$projects = $mLog->getProjects(true);

		$todos = $mLog->getUserTodos();
		$res = TeamTime::helper()->getBpmn()->initTodoData($todos);
		if ($res instanceof TeamTime_Undefined) {
			$showProcessColumn = false;
		}
		else {
			$todos = $res;
			$showProcessColumn = true;
		}

		$todo = $mLog->loadTodo();

		$filter_period = '';
		$mainframe->setUserState($option . '.filter_period', $filter_period);
		$options = array();
		$lists['select_period'] = JHTML::_('teamtime.todoperiodlist', $options, 'filter_period',
						'class="inputbox" onchange="set_filter_period();"', 'value', 'text', $filter_period);

		$filter_state = TODO_STATE_OPEN;
		$mainframe->setUserState($option . '.filter_state', TODO_STATE_OPEN);
		$options = array();
		$lists['select_state'] = JHTML::_('teamtime.todostatelist2', $options, 'filter_state',
						'class="inputbox" onchange="set_filter_state();"', 'value', 'text', $filter_state);

		if ($config->show_todos_datefilter) {
			$filter_date = 'today';
			$mainframe->setUserState($option . '.filter_date', $filter_date);
			$options = array(
				JHTML::_('select.option', '', '- ' . JText::_('All dates') . ' -', 'value', 'text')
			);
			$selector_data = JHTML::_(
							'teamtime.dateselector2', $options, 'filter_date',
							'class="inputbox" size="1" onchange="set_filter_date();"', 'value', 'text', $filter_date);
			$lists['select_date'] = $selector_data["select_date"];

			$filter_sproject = "";
			$mainframe->setUserState($option . '.filter_sproject', $filter_sproject);

			$filter_stodo = "";
			$mainframe->setUserState($option . '.filter_stodo', $filter_stodo);
		}

		// set template vars
		$this->assignRef('user_logs', $user_logs);
		$this->assignRef('other_logs', $other_logs);

		$this->assignRef('todos', $todos);
		$this->assignRef('showProcessColumn', $showProcessColumn);

		//		$this->assignRef('todo', $todo);
		$this->assignRef('projects', $projects);
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);

		$this->assignRef('lists', $lists);

		$this->assignRef('filter_sproject', $filter_sproject);
		$this->assignRef('filter_stodo', $filter_stodo);

		parent::display($tpl);
	}

}