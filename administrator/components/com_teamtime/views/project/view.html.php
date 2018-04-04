<?php

class TeamtimeViewProject extends JView {

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
		JToolBarHelper::title(JText::_('Project') . ': <small><small>[ '
				. $text . ' ]</small></small>', TEAMLOG_ICON);
		JToolBarHelper::save();
		JToolBarHelper::apply();
		$edit ? JToolBarHelper::cancel('cancel', 'Close') : JToolBarHelper::cancel();

		// get data from the model
		$item = & $this->get('data');

		// state select
		$options = JHTML::_('select.option', '', '- ' . JText::_('Select State') . ' -');
		$lists['select_state'] = JHTML::_(
						'teamtime.projectstatelist', $options, 'state', 'class="inputbox"', 'value', 'text',
						$item->state);

		$model = new TeamtimeModelProject();
		$userIds = $model->getUsersIds($item->id);

		$lists['select_users'] = $helperBase->getUserSelect($userIds,
				array(
			"autosubmit" => false,
			"attrs" => 'size="10" multiple',
			"fieldId" => "users[]",
			"firstOptions" => JHTML::_('select.option', '', '- ' . JText::_('Select User') . ' -')
				));

		$options = array(
			JHTML::_('select.option', '0', JText::_("Fixed price")),
			JHTML::_('select.option', '1', JText::_("Multiplier of man-hour price"))
		);
		$lists['radio_rate'] = JHTML::_(
						'select.radiolist', $options, 'dynamic_rate', 'class="inputbox"', 'value', 'text',
						$item->dynamic_rate);

		$item->maxRate = $model->getMaxRate($item->id, $item->dynamic_rate);
		$item->minRate = $model->getMinRate($item->id, $item->dynamic_rate);

		$confData = TeamTime::getConfig();

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);
		$this->assignRef('item', $item);

		$this->assignRef('conf_data', $confData);

		parent::display($tpl);
	}

}