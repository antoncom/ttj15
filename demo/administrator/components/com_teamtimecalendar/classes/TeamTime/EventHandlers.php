<?php

class TeamTime_EventHandlers_Calendar {

	public function onInit() {
		// load language files for component
		$lang = & JFactory::getLanguage();
		$lang->load("com_teamtimecalendar", JPATH_BASE);
	}

}