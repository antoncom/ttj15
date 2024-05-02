<?php

class TeamtimeViewUsers extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$helperBase = TeamTime::helper()->getBase();
		$user = & JFactory::getUser();

		// set toolbar items
		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . JText::_('Users'), TEAMLOG_ICON);

		// get request vars	
		$controller = JRequest::getWord('controller');
		$name = $this->get('name');

		$filterOrder = $mainframe->getUserStateFromRequest($option . $name . '.filter_order',
				'filter_order', 'a.id', 'cmd');
		$filterOrderDir = $mainframe->getUserStateFromRequest($option . $name . '.filter_order_Dir',
				'filter_order_Dir', '', 'word');
		$search = $mainframe->getUserStateFromRequest($option . $name . '.search', 'search', '', 'string');
		$search = JString::strtolower($search);

		JHTML::_('behavior.tooltip');

		$fromPeriod = JRequest::getVar('from_period', '?');
		$untilPeriod = JRequest::getVar('until_period', '?');

		list($lists['select_date'], $dateSelect, $datePresets, $fromPeriod, $untilPeriod) =
				$helperBase->getDateSelect($fromPeriod, $untilPeriod);
		$mainframe->setUserState($option . $name . '.from_period', $fromPeriod);
		$mainframe->setUserState($option . $name . '.until_period', $untilPeriod);

		// get data from the model
		$items = & $this->get('data');
		$total = & $this->get('total');
		$pagination = & $this->get('pagination');

		$model = new TeamtimeModelUser();
		$items = $model->initHoursForUsers($items, $fromPeriod, $untilPeriod);

		$hasDotuPrices = false;
		list($items, $hasDotuPrices) = $model->initDotu($items, $hasDotuPrices);

		// table ordering
		$lists['order_Dir'] = $filterOrderDir;
		$lists['order'] = $filterOrder;

		// search filter
		$lists['search'] = $search;

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
		$this->assignRef('date_select', $dateSelect);		
		$this->assignRef('period', JRequest::getVar('period', ''));

		$this->assignRef('has_dotu_prices', $hasDotuPrices);

		parent::display($tpl);
	}

}