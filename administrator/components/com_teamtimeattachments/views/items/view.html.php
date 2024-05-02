<?php

jimport('joomla.application.component.view');

class ComponentnameViewItems extends JView {

	function display($tpl = null) {
		$mainframe = & JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$user = & JFactory::getUser();
		$config = & JFactory::getConfig();

		// set toolbar items
		JToolBarHelper::title(COMPONENTNAME_TOOLBAR_TITLE . JText::_('Page list'), COMPONENTNAME_ICON);
		//JToolBarHelper::deleteList();
		//JToolBarHelper::editListX();
		JToolBarHelper::addNewX();

		JHTML::_('behavior.tooltip');

		// get request vars		
		$controller = JRequest::getWord('controller');

		$name = $this->get('name');
		$filter_order = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_order', 'filter_order', 'a.name', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$search = $mainframe->getUserStateFromRequest(
				$option . $name . '.search', 'search', '', 'string');
		$search = JString::strtolower($search);

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
		$this->assignRef('total', $total);

		parent::display($tpl);
	}

}