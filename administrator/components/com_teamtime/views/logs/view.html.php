<?php

class TeamtimeViewLogs extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$helperBase = TeamTime::helper()->getBase();
		$user = & JFactory::getUser();

		// set toolbar items
		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . JText::_('Logs'), TEAMLOG_ICON);
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();

		JHTML::_('behavior.tooltip');

		// get request vars
		$controller = JRequest::getWord('controller');
		$name = $this->get('name');
		$filterOrder = $mainframe->getUserStateFromRequest($option . $name . '.filter_order',
				'filter_order', 'a.id', 'cmd');
		$filterOrderDir = $mainframe->getUserStateFromRequest($option . $name . '.filter_order_Dir',
				'filter_order_Dir', '', 'word');

		$search = $mainframe->getUserStateFromRequest($option . $name . '.search', 'search', '', 'string');
		$search = JString::strtolower($search);

		$filterUserId = $mainframe->getUserStateFromRequest($option . $name . '.filter_user_id',
				'filter_user_id', '', 'int');
		$filterProjectId = $mainframe->getUserStateFromRequest($option . $name . '.filter_project_id',
				'filter_project_id', '', 'int');

		// get data from the model
		$items = & $this->get('data');
		$total = & $this->get('total');
		$pagination = & $this->get('pagination');

		$lists['select_user'] = $helperBase->getUserSelect($filterUserId,
				array("fieldId" => "filter_user_id",			
			"firstOptions" => JHTML::_('select.option', '', '- ' . JText::_('Select User') . ' -')));

		$lists['select_project'] = $helperBase->getProjectSelect($filterProjectId,
				array(
			"firstOptions" => JHTML::_('select.option', '', '- ' . JText::_('Select Project') . ' -'),
			"fieldId" => "filter_project_id"));

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

		parent::display($tpl);
	}

}