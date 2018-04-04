<?php

class TeamlogViewAttachments extends JView {

	function display($tpl = null) {
		$mainframe = & JFactory::getApplication();
		$option = JRequest::getCmd('option');

		parent::display($tpl);
	}

}