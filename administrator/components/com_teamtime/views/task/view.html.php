<?php

class TeamtimeViewTask extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$helperBase = TeamTime::helper()->getBase();
		$user = & JFactory::getUser();

		// get request vars		
		$controller = JRequest::getWord('controller');

		JHTML::script('task-details.js', "administrator/components/" . $option . "/assets/js/teamtime/");

		$edit = JRequest::getVar('edit', true);
		// set toolbar items
		$text = $edit ? JText::_('Edit') : JText::_('New');
		JToolBarHelper::title(
				JText::_('Task') . ': <small><small>[ ' . $text . ' ]</small></small>', TEAMLOG_ICON);
		JToolBarHelper::save();
		JToolBarHelper::apply();
		$edit ? JToolBarHelper::cancel('cancel', 'Close') : JToolBarHelper::cancel();

		// get data from the model
		$item = & $this->get('data');

		// set default state for new task
		if (!$edit) {
			$item->state = 0;
		}

		// state select
		$options = JHTML::_('select.option', '', '- ' . JText::_('Select State') . ' -');
		$lists['select_state'] = JHTML::_('teamtime.taskstatelist', $options, 'state', 'class="inputbox"',
						'value', 'text', $item->state);

		$lists['select_project'] = $helperBase->getProjectSelect($item->project_id,
				array(
			"autosubmit" => false,
			"attrs" => 'multiple="multiple" onclick="getSelPro(this);"'
				));

		// type select
		$projectFilter = $helperBase->getProjectFilter(null);
		list($lists['select_type']) = $helperBase->getTypesSelect($item->type_id, $projectFilter,
				array(
			"autosubmit" => false,
			"firstOptions" => JHTML::_('select.option', '', '- ' . JText::_('Select Type') . ' -')
				));

		// goals select
		$lists['select_goals'] = TeamTime::helper()->getDotu()
				->targetVectorSelector($item);

		// dotu price
		$elements['dotu_price'] = TeamTime::helper()->getDotu()->userHourPrice($item);

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);
		$this->assignRef('elements', $elements);
		$this->assignRef('item', $item);

		parent::display($tpl);
	}

}