<?php

class TeamtimeViewUser extends JView {

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
		JToolBarHelper::title(
				JText::_('User') . ': <small><small>[ ' . $text . ' ]</small></small>', TEAMLOG_ICON);
		JToolBarHelper::save();
		JToolBarHelper::apply();
		$edit ? JToolBarHelper::cancel('cancel', 'Close') : JToolBarHelper::cancel();

		// get data from the model
		$item = & $this->get('data');
		
		$model = new TeamtimeModelUser();
		$projectIds = $model->getProjectsIds($item->id);
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