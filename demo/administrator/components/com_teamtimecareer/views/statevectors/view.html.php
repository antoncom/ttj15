<?php

class TeamtimecareerViewStatevectors extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$helperBase = TeamTime::helper()->getBase();
		
		$controller = JRequest::getWord('controller');

		// set toolbar items
		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . JText::_('State vector'), TEAMLOG_ICON);
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();
		//JToolBarHelper::preferences($option, 280);

		JHTML::_('behavior.tooltip');

		// get request vars
		$name = $this->get('name');
		$filterOrder = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_order', 'filter_order', 'a.id', 'cmd');
		$filterOrderDir = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$search = $mainframe->getUserStateFromRequest(
				$option . $name . '.search', 'search', '', 'string');
		$search = JString::strtolower($search);

		$filterUserId = $mainframe->getUserStateFromRequest(
				$option . '.filter_user_id', 'filter_user_id', '', 'int');
		$filterTargetId = $mainframe->getUserStateFromRequest(
				$option . '.filter_target_id', 'filter_target_id', '', 'int');
		$filterType = $mainframe->getUserStateFromRequest(
				$option . '.filter_type', 'filter_type', '', 'string');

		$lists['select_user'] = $helperBase->getUserSelect($filterUserId,
				array("fieldId" => "filter_user_id",
			"firstOptions" => JHTML::_('select.option', '', '- ' . JText::_('All users') . ' -')));

		$options = JHTML::_(
						'select.option', '0', '- ' . JText::_('All goals') . ' -', 'value', 'text', false);
		$lists['select_target'] = JHTML::_(
						'teamtimecareer.goalslist', 0, $options, 'filter_target_id', 'class="inputbox auto-submit"',
						'value', 'text', $filterTargetId);

		$options = array();
		$options[] = JHTML::_(
						'select.option', '', '- ' . JText::_('All experience') . ' -', 'value', 'text');
		$options[] = JHTML::_(
						'select.option', 'indicators', JText::_('Indicators'), 'value', 'text');
		$options[] = JHTML::_(
						'select.option', 'logs', JText::_('Reports/Orders'), 'value', 'text');
		$lists["select_type"] = JHTML::_(
						'select.genericlist', $options, 'filter_type', 'class="inputbox auto-submit"', 'value',
						'text', $filterType);

		// get data from the model
		$items = & $this->get('data');
		$total = & $this->get('total');
		$totalStat = & $this->get('totalstat');
		$pagination = & $this->get('pagination');

		// table ordering
		$lists['order_Dir'] = $filterOrderDir;
		$lists['order'] = $filterOrder;

		// search filter
		$lists['search'] = $search;

		// date selector
		$fromPeriod = $mainframe->getUserStateFromRequest(
				$option . '.from_period', 'from_period', '', 'string');
		$untilPeriod = $mainframe->getUserStateFromRequest(
				$option . '.until_period', 'until_period', '', 'string');
		$selectorData = JHTML::_('teamtimecareer.dateselector', $fromPeriod, $untilPeriod);
		$fromPeriod = $selectorData["from_period"];
		$untilPeriod = $selectorData["until_period"];
		$datePresets = $selectorData["date_presets"];
		$lists['select_date'] = $selectorData["select_date"];

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);

		$this->assignRef('from_period', $fromPeriod);
		$this->assignRef('until_period', $untilPeriod);
		$this->assignRef('date_presets', $datePresets);
		$this->assignRef('total_stat', $totalStat);


		parent::display($tpl);
	}

}