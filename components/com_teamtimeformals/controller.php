<?php

jimport('joomla.application.component.controller');

class TeamlogFormalController extends JController {

	/**
	 * Display the view
	 */
	function display() {
		// set defaults
		JRequest::setVar('layout', 'default');
		JRequest::setVar('view', 'formal');

		parent::display();
	}
	
}