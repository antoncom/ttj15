<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TeamtimebpmViewProcessdiagrampage extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		JRequest::setVar('hidemainmenu', 1);

		$id = JRequest::getVar("id", 0);

		$mProcess = new TeamtimebpmModelProcess();
		$mProcess->setId($id);
		$process = $mProcess->getData();

		JHTML::script('process-diagrampage.js', URL_MEDIA_COMPONENT_ASSETS . "js/teamtimebpm/");
		JHTML::stylesheet('diagrampage.css', URL_MEDIA_COMPONENT_ASSETS . "css/");

		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . $process->name, TEAMLOG_ICON);

		JToolBarHelper::custom('playprocess', 'playprocess', 'playprocess', JText::_('Playback'));
		JToolBarHelper::custom('saveprocess', 'saveprocess', 'saveprocess', JText::_('Save'));
		JToolBarHelper::custom('exitprocess', 'exitprocess', 'exitprocess', JText::_('End Edit'));

		$controller = JRequest::getWord('controller');

		$frameUrl = JURI::base() .
				"index.php?option=com_teamtimebpm&controller=process&view=processdiagram" .
				"&id=" . $id . "&tmpl=component";

		$options = array();
		$options[] = JHTML::_('select.option', "status", JText::_('Show Status'));
		$options[] = JHTML::_('select.option', "plan", JText::_('Show Hours Plan'));
		$options[] = JHTML::_('select.option', "time", JText::_('Show Time'));
		$options[] = JHTML::_('select.option', "price", JText::_('Show Price'));
		$options[] = JHTML::_('select.option', "performer", JText::_('Show Performer'));
		$options[] = JHTML::_('select.option', "date", JText::_('Show Date'));
		$options[] = JHTML::_('select.option', "", JText::_('Show None'));

		$lists["select_show"] = JHTML::_(
						'select.genericlist', $options, 'filter_show', 'class="inputbox"', 'value', 'text', "status");
		$lists["select_show_fullscreen"] = JHTML::_(
						'select.genericlist', $options, 'filter_show_fullscreen', 'class="inputbox"', 'value', 'text',
						"status");

		$lists["order"] = "";
		$lists["order_Dir"] = "";

		$this->assignRef('frameUrl', $frameUrl);
		$this->assignRef('process', $process);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);

		parent::display($tpl);
	}

}