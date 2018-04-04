<?php

class TeamlogViewClient extends JView {

	function display($tpl = null) {
		$mainframe = & JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$user = & JFactory::getUser();

		// get request vars		
		$controller = JRequest::getWord('controller');

		// get data from the model
		$user_logs = & $this->get('userlogs');
		$other_logs = & $this->get('otherlogs');
		$projects = & $this->get('projects');
		$todos = & $this->get('usertodos');
		$todo = & $this->get('loadtodo');

		// set template vars
		$this->assignRef('user_logs', $user_logs);
		$this->assignRef('other_logs', $other_logs);
		$this->assignRef('todos', $todos);
//		$this->assignRef('todo', $todo);
		$this->assignRef('projects', $projects);
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);

		parent::display($tpl);
	}

}