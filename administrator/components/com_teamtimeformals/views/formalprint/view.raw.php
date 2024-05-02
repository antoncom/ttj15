<?php

class TeamtimeformalsViewFormalprint extends JView {

	public function display($tpl = null) {
		$user = & JFactory::getUser();

		// get request vars
		$option = JRequest::getCmd('option');
		$controller = JRequest::getWord('controller');

		// get data from the model
		$item = & $this->get('data');

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('item', $item);

		parent::display($tpl);
	}

}