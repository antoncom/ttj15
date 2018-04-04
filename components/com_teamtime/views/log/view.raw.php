<?php

class TeamlogViewLog extends JView {

	function display($tpl = null) {
		$mainframe = & JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$lists = array();

		$user = & JFactory::getUser();
		$config = TeamTime::getConfig();

		// get request vars
		$controller = JRequest::getWord('controller');
		$task = JRequest::getWord('task');

		switch ($task) {
			case 'loadtasks':
				$task_type_array = & $this->get('tasktypearray');
				$this->assignRef('task_type_array', $task_type_array);
				break;

			case 'setproject':
			case 'loadprojects':
				$projects = $this->get('projects');
				$this->assignRef('projects', $projects);
				break;

			case 'loadtodos':
				$todos = & $this->get('usertodos');
				$res = TeamTime::helper()->getBpmn()->initTodoData($todos);
				if ($res instanceof TeamTime_Undefined) {
					$showProcessColumn = false;
				}
				else {
					$todos = $res;
					$showProcessColumn = true;
				}

				$this->assignRef('todos', $todos);
				$this->assignRef('showProcessColumn', $showProcessColumn);

				$filter_period = $mainframe->getUserState($option . '.filter_period', '');
				$options = array();
				$lists['select_period'] = JHTML::_(
								'teamtime.todoperiodlist', $options, 'filter_period',
								'class="inputbox" onchange="set_filter_period();"', 'value', 'text', $filter_period);

				$filter_state = $mainframe->getUserState(
						$option . '.filter_state', TODO_STATE_OPEN);
				$options = array();
				$lists['select_state'] = JHTML::_(
								'teamtime.todostatelist2', $options, 'filter_state',
								'class="inputbox" onchange="set_filter_state();"', 'value', 'text', $filter_state);

				if ($config->show_todos_datefilter) {
					$filter_date = $mainframe->getUserState($option . '.filter_date', 'today');
					$options = array(
						JHTML::_('select.option', '', '- ' . JText::_('All dates') . ' -', 'value', 'text')
					);
					$selector_data = JHTML::_(
									'teamtime.dateselector2', $options, 'filter_date',
									'class="inputbox" size="1" onchange="set_filter_date();"', 'value', 'text', $filter_date);
					$lists['select_date'] = $selector_data["select_date"];

					$filter_sproject = $mainframe->getUserState($option . '.filter_sproject', '');
					$filter_stodo = $mainframe->getUserState($option . '.filter_stodo', '');
				}

				break;

			case 'loaddescription':
				$project = & $this->get('project');
				$this->assignRef('project', $project);
				break;			

			case 'loadteamlog':
				$other_logs = & $this->get('otherlogs');
				$this->assignRef('other_logs', $other_logs);
				break;
		}

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);

		$this->assignRef('lists', $lists);

		$this->assignRef('filter_sproject', $filter_sproject);
		$this->assignRef('filter_stodo', $filter_stodo);

		parent::display($tpl);
	}

}