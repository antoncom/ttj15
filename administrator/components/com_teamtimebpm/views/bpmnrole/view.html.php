<?php

class BpmnRoleViewBpmnRole extends JView {

	function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$user = & JFactory::getUser();

		// get request vars
		$controller = JRequest::getWord('controller');
		$edit = JRequest::getVar('edit', true);

		// set toolbar items
		$text = $edit ? JText::_('Edit') : JText::_('New');
		JToolBarHelper::title(JText::_('Role') . ': <small><small>[ ' . $text . ' ]</small></small>',
				TEAMLOG_ICON);
		JToolBarHelper::save();
		JToolBarHelper::apply();
		$edit ? JToolBarHelper::cancel('cancel', 'Close') : JToolBarHelper::cancel();

		// get data from the model
		$item = & $this->get('data');

		if (TeamTime::addonExists("com_teamtimecareer")) {
			$options = JHTML::_('select.option', '0', '- ' . JText::_('Select vector of goal') . ' -',
							'value', 'text', false);
			$lists['select_target'] = JHTML::_('teamtimecareer.getparenttargets', $options, 'target_id',
							'class="inputbox"', 'value', 'text', $item->target_id);
		}
		
		// user select
		$options = JHTML::_('select.option', '', '- ' . JText::_('User') . ' -');
		$lists['select_user'] = JHTML::_('teamtime.userlist', $options, 'user_id',
						'style="width:150px" class="inputbox"', 'value', 'text', $item->user_id);

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);
		$this->assignRef('item', $item);

		parent::display($tpl);
	}

}