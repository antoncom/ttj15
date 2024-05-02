<?php

class TeamtimeViewConfig extends JView {

	public function display($tpl = null) {
		JHTML::_('behavior.tooltip');

		JToolBarHelper::title(JText::_('Control panel'), 'config.png');
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel('cancel', JText::_('Close'));

		$config = TeamTime::getConfig();

		$this->assignRef('config', $config);

		parent::display($tpl);
	}

}