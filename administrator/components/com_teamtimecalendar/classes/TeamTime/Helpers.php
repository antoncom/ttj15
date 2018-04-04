<?php

class TeamTime_Helpers_Calendar {

	const ORDER_INDEX = 2;
	
	function __construct() {
		//...
	}

	public function getLink($s, $url) {
		if (JPATH_BASE == JPATH_ADMINISTRATOR) {
			return "<a
				href='index.php?option=com_teamtimecalendar&controller=calendar&" .
					$url . "'>" . $s . "</a>";
		}

		return $s;
	}

	public function getAddonButton() {
		$lang = & JFactory::getLanguage();
		$lang->load("com_teamtimecalendar", JPATH_BASE);

		return TeamTime::helper()->getBase()->quickiconButton(
						'index.php?option=com_teamtimecalendar&controller=calendar&view_type=month',
						'components/com_teamtimecalendar/assets/images/icon-48-calendar.png',
						JText::_('TeamTime Calendar'));
	}

	public function addonMenuItem($controller) {
		JSubMenuHelper::addEntry(JText::_("Calendar"),
				"index.php?option=com_teamtimecalendar&controller=calendar&view_type=month",
				$controller == "calendar");
	}

}