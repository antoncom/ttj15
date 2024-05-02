<?php

class TeamtimeViewLog extends JView {

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
		JToolBarHelper::title(JText::_('Log') . ': <small><small>[ ' . $text . ' ]</small></small>',
				TEAMLOG_ICON);
		JToolBarHelper::save();
		JToolBarHelper::apply();
		$edit ? JToolBarHelper::cancel('cancel', 'Close') : JToolBarHelper::cancel();

		// get data from the model
		$item = & $this->get('data');

		$lists['select_user'] = $helperBase->getUserSelect($item->user_id,
				array("autosubmit" => false,			
			"firstOptions" => JHTML::_('select.option', '', '- ' . JText::_('Select User') . ' -')));

		$lists['select_project'] = $helperBase->getProjectSelect($item->project_id,
				array("autosubmit" => false, "attrs" => "",
			"firstOptions" => JHTML::_('select.option', '', '- ' . JText::_('Select Project') . ' -')));

		$lists['select_task'] = $helperBase->getTypesTasksSelect($item->project_id, $item->task_id);

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);
		$this->assignRef('item', $item);

		parent::display($tpl);
	}

}