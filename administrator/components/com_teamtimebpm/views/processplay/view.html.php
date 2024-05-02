<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TeamtimebpmViewProcessplay extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		JHTML::_('behavior.tooltip');
		JHTML::script('process-play.js', URL_MEDIA_COMPONENT_ASSETS . "js/teamtimebpm/");
		JHTML::stylesheet('operations.css', URL_MEDIA_COMPONENT_ASSETS . "css/");

		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE, TEAMLOG_ICON);
		
		$controller = JRequest::getWord('controller');

		$id = JRequest::getVar("id", 0);

		$mProcess = new TeamtimebpmModelProcess();
		$mProcess->setId($id);
		$process = $mProcess->getData();

		$mSpace = new TeamtimebpmModelSpace();
		$mSpace->setId($process->space_id);
		$space = $mSpace->getData();
		$process->space_name = $space->name;

		list($todos, $todosStat) = $mProcess->getBlocks($process->id, true, true);

		$this->assignRef('process', $process);
		$this->assignRef('todos', $todos);
		$this->assignRef('todosStat', $todosStat);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);

		parent::display($tpl);
	}

}