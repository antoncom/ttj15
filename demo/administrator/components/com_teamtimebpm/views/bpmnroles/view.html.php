<?php

class BpmnRoleViewBpmnRoles extends JView {

	function display($tpl = null) {
		$mainframe = & JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$user = & JFactory::getUser();
		$config = & JFactory::getConfig();

		// set toolbar items
		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . JText::_('Roles'), TEAMLOG_ICON);
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();

		JHTML::_('behavior.tooltip');

		$from_period = JRequest::getVar('from_period', '?');
		$until_period = JRequest::getVar('until_period', '?');

		// get request vars
		$option = JRequest::getCmd('option');
		$controller = JRequest::getWord('controller');

		$name = $this->get('name');
		$filter_order = $mainframe->getUserStateFromRequest($option . $name . '.filter_order',
				'filter_order', 'a.id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option . $name . '.filter_order_Dir',
				'filter_order_Dir', '', 'word');
		$search = $mainframe->getUserStateFromRequest($option . $name . '.search', 'search', '', 'string');
		$search = JString::strtolower($search);

		// get data from the model
		$items = & $this->get('data');
		$total = & $this->get('total');
		$pagination = & $this->get('pagination');

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		// search filter
		$lists['search'] = $search;

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);

		$this->assignRef('from_period', $from_period);
		$this->assignRef('until_period', $until_period);
		$this->assignRef('date_presets', $date_presets);
		$this->assignRef('date_select', $date_select);
		$this->assignRef('period', JRequest::getVar('period', ''));

		parent::display($tpl);
	}

}