<?php

class TeamtimecareerViewTargetvectors extends JView {

	public function display($tpl = null) {
		$mainframe = & JFactory::getApplication();
		$option = JRequest::getCmd('option');
		
		$controller = JRequest::getWord('controller');

		// set toolbar items
		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . JText::_('Vector of goals'), TEAMLOG_ICON);
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();
		//JToolBarHelper::preferences($option, 280);

		JHTML::_('behavior.tooltip');

		// get request vars
		$name = $this->get('name');
		$filterOrder = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_order', 'filter_order', 'a.ordering', 'cmd');
		$filterOrderDir = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$search = $mainframe->getUserStateFromRequest(
				$option . $name . '.search', 'search', '', 'string');
		$search = JString::strtolower($search);

		$filterGoalsOnly = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_goalsonly', 'filter_goalsonly', 1, 'int');

		$options = array();
		$options[] = JHTML::_(
						'select.option', '', '- ' . JText::_('All') . ' -', 'value', 'text');
		$options[] = JHTML::_(
						'select.option', '1', JText::_('Goals only'), 'value', 'text');
		$lists["select_goalsonly"] = JHTML::_(
						'select.genericlist', $options, 'filter_goalsonly', 'class="inputbox auto-submit"', 'value',
						'text', $filterGoalsOnly);

		// get data from the model
		$items = & $this->get('data');
		$total = & $this->get('total');
		$pagination = & $this->get('pagination');

		// table ordering
		$lists['order_Dir'] = $filterOrderDir;
		$lists['order'] = $filterOrder;

		$ordering = ($lists['order'] == 'a.ordering');

		// search filter
		$lists['search'] = $search;

		// level limit filter
		$filter_limit = $mainframe->getUserStateFromRequest(
				$option . '.filter_limit', 'filter_limit', 10, 'int');
		$lists['levellist'] = JHTML::_('select.integerlist', 1, 20, 1, 'filter_limit',
						'size="1" onchange="document.adminForm.submit();"', $filter_limit);

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('ordering', $ordering);

		parent::display($tpl);
	}

}