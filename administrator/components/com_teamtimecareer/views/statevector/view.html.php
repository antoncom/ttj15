<?php

class TeamtimecareerViewStatevector extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$helperBase = TeamTime::helper()->getBase();
		
		// get request vars		
		$controller = JRequest::getWord('controller');
		$edit = JRequest::getVar('edit', true);

		// get data from the model
		$item = & $this->get('data');

		$tmodel = new TeamtimecareerModelTargetvector();
		$tmodel->setId($item->target_id);
		$targetData = $tmodel->getData();

		if ($targetData->is_skill) {
			$smodel = new TeamtimecareerModelStatevector();
			$item->id = null;
			$item->target_id = $targetData->parent_id;
			$item->num = 0;
			$item->description = "";

			$edit = false;
		}

		// set toolbar items
		$text = $edit ? JText::_('Edit') : JText::_('New');
		JToolBarHelper::title(
				JText::_('State vector') . ': <small><small>[ ' . $text . ' ]</small></small>', TEAMLOG_ICON);
		JToolBarHelper::save();
		JToolBarHelper::apply();
		$edit ? JToolBarHelper::cancel('cancel', 'Close') : JToolBarHelper::cancel();

		$skillsForTargets = $tmodel->getSkillsForTargets();

		$options = JHTML::_(
						'select.option', '0', '- ' . JText::_('All goals') . ' -', 'value', 'text', false);
		$lists['select_goal'] = JHTML::_(
						'teamtimecareer.goalslist', 0, $options, 'target_id', 'class="inputbox" size=16', 'value',
						'text', $item->target_id ? $item->target_id : "-");

		$lists['select_user'] = $helperBase->getUserSelect($item->user_id,
				array("autosubmit"=>false,
					"attrs"=>'size=16',
			"firstOptions" => JHTML::_('select.option', '', '- ' . JText::_('All users') . ' -')));
		
		$options = array();
		$options[] = JHTML::_('select.option', '-1', JText::_('Minus'));
		$options[] = JHTML::_('select.option', '1', JText::_('Plus'));

		$sign = $item->num >= 0 ? 1 : -1;
		$lists['select_sign'] = JHTML::_(
						'select.radiolist', $options, 'indication', 'class="inputbox" ', 'value', 'text', $sign,
						'indication');

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('item', $item);
		$this->assignRef('lists', $lists);
		$this->assignRef('edit', $edit);
		$this->assignRef('skillsForTargets', $skillsForTargets);

		parent::display($tpl);
	}

}