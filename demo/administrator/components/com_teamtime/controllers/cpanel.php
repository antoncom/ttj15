<?php

class TeamtimeControllerCpanel extends Core_Joomla_Controller {

	public function display() {
		// set the default view
		$view = JRequest::getCmd('view');
		if (empty($view)) {
			JRequest::setVar('view', 'cpanel');
		}

		parent::display();
	}

}