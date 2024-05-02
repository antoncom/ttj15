<?php

class DoctypeViewDoctypes extends JView {

	function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$user =& JFactory::getUser();
		$config   =& JFactory::getConfig();

		// set toolbar items
		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE.JText::_('FORMALS TYPES'), TEAMLOG_ICON);
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();

		JHTML::_('behavior.tooltip');

		// get request vars
		$option           = JRequest::getCmd('option');
		$controller       = JRequest::getWord('controller');
		$name			  = $this->get('name');
		$filter_order	  = $mainframe->getUserStateFromRequest($option.$name.'.filter_order', 'filter_order', 'a.id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option.$name.'.filter_order_Dir', 'filter_order_Dir', '', 'word');
		$filter_type	  = $mainframe->getUserStateFromRequest(
			$option.$name.'.filter_type', 'filter_type', '', 'cmd');
		$search	          = $mainframe->getUserStateFromRequest($option.$name.'.search', 'search', '', 'string');
		$search			  = JString::strtolower($search);

		// get data from the model
		$items		=& $this->get('data');
		$total		=& $this->get('total');
		$pagination =& $this->get('pagination');

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search']= $search;

		// type select
		$options = JHTML::_('select.option', '', '- '.JText::_('Select Generator').' -');
		$lists['select_type'] = JHTML::_('teamtimeformals.generatortypeslist', $options,
			'filter_type', 'class="inputbox auto-submit"', 'value', 'text', $filter_type, false, false);
		
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