<?php

class TeamtimecareerViewTargetvector extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		// get request vars		
		$controller = JRequest::getWord('controller');
		$edit = JRequest::getVar('edit', true);

		// set toolbar items
		$text = $edit ? JText::_('Edit') : JText::_('New');
		JToolBarHelper::title(
				JText::_('Goal') . ': <small><small>[ ' . $text . ' ]</small></small>', TEAMLOG_ICON);
		JToolBarHelper::save();
		JToolBarHelper::apply();
		$edit ? JToolBarHelper::cancel('cancel', 'Close') : JToolBarHelper::cancel();

		// get data from the model
		$item = & $this->get('data');

		$skillItems = null;
		if (!$item->is_skill) {
			$skillItems = $this->get('skills');
		}
		$hasChildren = sizeof($this->get('children')) > 0;

		$options = JHTML::_(
						'select.option', '0', '- ' . JText::_('Included to goal') . ' -', 'value', 'text', false);
		$lists['select_goal'] = JHTML::_(
						'teamtimecareer.goalslist', $item->id, $options, 'parent_id', 'class="inputbox" size=16',
						'value', 'text', $item->parent_id ? $item->parent_id : "-");

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('item', $item);
		$this->assignRef('skillItems', $skillItems);
		$this->assignRef('hasChildren', $hasChildren);
		$this->assignRef('lists', $lists);

		parent::display($tpl);
	}

}