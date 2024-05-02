<?php

class VariableViewVariable extends JView {

	function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$user = & JFactory::getUser();

		// get request vars		
		$controller = JRequest::getWord('controller');
		$edit = JRequest::getVar('edit', true);

		$project_ids = JRequest::getVar('projects');
		$user_ids = JRequest::getVar('users');

		// set toolbar items
		$text = $edit ? JText::_('Edit') : JText::_('New');
		JToolBarHelper::title(JText::_('Formal Variable') . ': <small><small>[ ' . $text . ' ]</small></small>', TEAMLOG_ICON);
		JToolBarHelper::save();
		JToolBarHelper::apply();
		$edit ? JToolBarHelper::cancel('cancel', 'Close') : JToolBarHelper::cancel();

		// get data from the model
		$item = & $this->get('data');

		if (!$edit) {
			$item->xsize = 30;
			$item->ysize = 1;
		}

		$options = array(
		  JHTML::_('select.option', '0', JText::_('Projects')),
		  JHTML::_('select.option', '1', JText::_('Users'))
		);
		$lists['select_using'] = JHTML::_('select.genericlist',
			$options, 'using_in', 'class="inputbox"', 'value', 'text',
		  $item->using_in);

		$options = array(
			JHTML::_('select.option', '', '- ' . JText::_('Nowhere') . ' -'),
			JHTML::_('select.option', '0', '- ' . JText::_('All Projects') . ' -')
		);
		$lists['select_projects'] = JHTML::_('teamtime.projectlist',
				$options, 'projects[]', 'size="10" multiple class="inputbox"', 'value', 'text',
				$project_ids);

		$options = array(
			JHTML::_('select.option', '', '- ' . JText::_('No one') . ' -'),
			JHTML::_('select.option', '0', '- ' . JText::_('All Users') . ' -')
		);
		$lists['select_users'] = JHTML::_('teamtime.userlist',
				$options, 'users[]', 'size="10" multiple class="inputbox"', 'value', 'text',
				$user_ids);

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);
		$this->assignRef('item', $item);

		parent::display($tpl);
	}

}