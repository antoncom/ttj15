<?php

class TeamtimebpmViewSpace extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$helperBase = TeamTime::helper()->getBase();
		$user = & JFactory::getUser();

		// get request vars		
		$controller = JRequest::getWord('controller');
		$edit = JRequest::getVar('edit', true);

		// set toolbar items
		$text = $edit ? JText::_('Edit') : JText::_('New');
		JToolBarHelper::title(JText::_('Space') . ': <small><small>[ ' . $text . ' ]</small></small>',
				TEAMLOG_ICON);
		JToolBarHelper::save();
		JToolBarHelper::apply();
		$edit ? JToolBarHelper::cancel('cancel', 'Close') : JToolBarHelper::cancel();

		// get data from the model
		$item = & $this->get('data');

		$mSpace = new TeamtimebpmModelSpace();

		if ($item->modified_by) {
			$item->user_name = $mSpace->getModifiedUserName($item->modified_by);
		}

		$filter_archived = $item->archived != "" ? $item->archived : "active";

		$options = array();
		$options[] = JHTML::_('select.option', "active", JText::_('Active'));
		$options[] = JHTML::_('select.option', "archived", JText::_('Archived'));
		$lists['select_archived'] = JHTML::_(
						'select.genericlist', $options, 'archived', 'class="inputbox"', 'value', 'text',
						$filter_archived);

		$projectIds = $mSpace->getProjectsIds($item->id);
		$lists['select_projects'] = $helperBase->getProjectSelect($projectIds,
				array(
			"showClosed" => false,
			"autosubmit" => false,
			"attrs" => 'size="10" multiple',
			"fieldId" => "projects[]",
			"firstOptions" => JHTML::_('select.option', '', '- ' . JText::_('Select Project') . ' -')
				));

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);
		$this->assignRef('item', $item);

		parent::display($tpl);
	}

}