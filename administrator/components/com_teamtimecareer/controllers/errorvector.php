<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TeamtimecareerControllerErrorvector extends Core_Joomla_Controller {

	public function display() {
		// set the default view
		$view = JRequest::getCmd('view');
		if (empty($view)) {
			JRequest::setVar('view', 'errorvectors');
		}

		parent::display();
	}

}