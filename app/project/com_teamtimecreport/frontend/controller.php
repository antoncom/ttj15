<?php

jimport('joomla.application.component.controller');

class TeamlogCreportController extends JController {

	/**
	 * Display the view
	 */
	function display() {
		// set defaults
		JRequest::setVar('layout', 'default');
		JRequest::setVar('view', 'creport');
		
		parent::display();
	}

	
}