<?php

class TeamtimebpmViewProcesslinkto extends JView {

	public function display($tpl = null) {
		$mainframe = & JFactory::getApplication();
		$option = JRequest::getCmd('option');

		JHTML::script('process-linkto.js', URL_MEDIA_COMPONENT_ASSETS . "js/teamtimebpm/");
		JHTML::stylesheet('link_to_process.css', URL_MEDIA_COMPONENT_ASSETS . "css/");

		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE, TEAMLOG_ICON);

		$controller = JRequest::getWord('controller');

		$spaceId = JRequest::getVar("space_id", 0);
		$processId = JRequest::getVar("process_id", 0);
		$mProcess = new TeamtimebpmModelProcess();
		if ($spaceId == 0) {
			$process = $mProcess->getById($processId);
			$spaceId = $process->space_id;
		}

		$search = JRequest::getVar("search", "");
		if ($search == JText::_("BPM PROCESS FILTER DEFAULT")) {
			$search = "";
		}
		$search = JString::strtolower($search);

		$lists['search'] = $search;

		$filter = array();
		if ($spaceId) {
			$filter["space_id"] = $spaceId;
		}
		if ($search != "") {
			$filter["name"] = $search;
		}
		$processes = $mProcess->getProcesses($filter);

		$mSpace = new TeamtimebpmModelSpace();

		$defaultOption = new stdClass();
		$defaultOption->value = "";
		$defaultOption->text = "- " . JText::_("Select space") . " -";
		$options = $mSpace->getOptionsList();
		array_unshift($options, $defaultOption);
		$lists['select_spaces'] = JHTML::_('select.genericlist', $options, 'space_id',
						'class="inputbox auto-submit"', 'value', 'text', $spaceId);

		$this->assignRef('processes', $processes);

		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);

		parent::display($tpl);
	}

}