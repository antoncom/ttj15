<?php

class TemplateViewTemplates extends JView {

	function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$user = & JFactory::getUser();
		$config = & JFactory::getConfig();

		// set toolbar items
		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . JText::_('Formals Templates'),
				TEAMLOG_ICON);
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();

		JHTML::_('behavior.tooltip');
		
		$filter_using = $mainframe->getUserStateFromRequest(
				$option . '.filter_using', 'filter_using', '', 'cmd');
		
		$doctypeModel = new DoctypeModelDoctype();
		$options = array();
		$options[] = JHTML::_('select.option', "",
						"- " . JText::_('All destinations') . " -");
		foreach ($doctypeModel->getUsings() as $k => $v) {
			$options[] = JHTML::_('select.option', $k, $v[1]);
		}
		$lists['select_using'] = JHTML::_(
						'select.genericlist', $options, 'filter_using', 'class="inputbox"',
						'value', 'text', $filter_using);

		$name = $this->get('name');		
		// get request vars		
		$controller = JRequest::getWord('controller');		
		$filter_order = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_order', 'filter_order', 'a.id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_order_Dir', 'filter_order_Dir', '', 'word');
		$filter_type = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_type', 'filter_type', '', 'cmd');
		$search = $mainframe->getUserStateFromRequest(
				$option . $name . '.search', 'search', '', 'string');
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

		// type select
		$options = JHTML::_(
						'select.option', '', '- ' . JText::_('Select Template Type') . ' -');
		$lists['select_type'] = JHTML::_(
						'teamtimeformals.typeslist', $options, 'filter_type',
						'class="inputbox auto-submit"', 'value', 'text', $filter_type, false, true);

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);

		/* $this->assignRef('from_period', $from_period);
		  $this->assignRef('until_period', $until_period);
		  $this->assignRef('date_presets', $date_presets);
		  $this->assignRef('date_select', $date_select);
		  $this->assignRef('period', JRequest::getVar('period', '')); */

		parent::display($tpl);
	}

}