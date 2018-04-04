<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TeamtimebpmViewProcessimport extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		JRequest::setVar('hidemainmenu', 1);

		$id = JRequest::getVar("process_id", 0);

		$mSpace = new TeamtimebpmModelSpace();
		$mProcess = new TeamtimebpmModelProcess();
		$mProcess->setId($id);
		$process = $mProcess->getData();

		JHTML::script('process-import.js', URL_MEDIA_COMPONENT_ASSETS . "js/teamtimebpm/");

		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . JText::_('Save process as template'), TEAMLOG_ICON);
	
		$controller = JRequest::getWord('controller');

		$defaultOption = new stdClass();
		$defaultOption->value = "";
		$defaultOption->text = "- " . JText::_("SELECT BPM SPACE") . " -";
		$options = $mSpace->getOptionsList();
		array_unshift($options, $defaultOption);
		$lists['select_spaces'] = JHTML::_(
						'select.genericlist', $options, 'space_id', 'class="inputbox"', 'value', 'text',
						$process->space_id);

		$lists["order"] = "";
		$lists["order_Dir"] = "";

		$this->assignRef('process', $process);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);

		parent::display($tpl);
	}

}