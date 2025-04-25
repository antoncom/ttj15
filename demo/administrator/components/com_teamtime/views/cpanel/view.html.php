<?php

class TeamtimeViewCpanel extends JView {

	public function display($tpl = null) {
		JToolBarHelper::title(JText::_('Control panel'), 'cpanel.png');

		JHTML::stylesheet('icons.css', 'administrator/components/com_teamtime/css/');

		$version = TeamTime::helper()->getBase()->getComponentVersion();

		$this->assignRef('component_version', $version);

		parent::display($tpl);
	}

}