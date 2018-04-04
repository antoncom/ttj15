<?php

class TeamtimeformalsViewFormals extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$user = & JFactory::getUser();
				
		// set toolbar items
		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . JText::_('FORMALS DOCUMENTS'),
				TEAMLOG_ICON);
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();
		JToolBarHelper::custom('print', 'print', 'print', JText::_('Print Document'));

		JHTML::_('behavior.tooltip');

		// get request vars
		$option = JRequest::getCmd('option');
		$controller = JRequest::getWord('controller');
		$name = $this->get('name');
		$filterOrder = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_order', 'filter_order', 'a.id', 'cmd');
		$filterOrderDir = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$filterType = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_type', 'filter_type', '', 'cmd');

		$filterProject = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_project', 'filter_project', '', 'cmd');
		$filterUsing = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_using', 'filter_using', '', 'cmd');
		
		$doctypeModel = new DoctypeModelDoctype();
		if (in_array($filterUsing, array_keys($doctypeModel->getUsings()))) {
			$filterUsing = $doctypeModel->getUsingIndex($filterUsing);
		}
		
		$search = $mainframe->getUserStateFromRequest(
				$option . $name . '.search', 'search', '', 'string');
		$search = JString::strtolower($search);

		// get data from the model
		$items = & $this->get('data');
		$mFormal = new TeamtimeformalsModelFormal();
		
		foreach ($items as $i => $row) {
			$doctype = $mFormal->getDoctype($row->doctype_id);
			$items[$i]->using_in = $doctype->using_in;
		}

		$total = & $this->get('total');
		$pagination = & $this->get('pagination');

		// table ordering
		$lists['order_Dir'] = $filterOrderDir;
		$lists['order'] = $filterOrder;

		// search filter
		$lists['search'] = $search;

		// type select		
		$options = array();
		$options[] = JHTML::_('select.option', '',
						'- ' . JText::_('Formals Template') . ' -');
		$lists['select_type'] = JHTML::_(
						'select.genericlist', $options, 'filter_type', 'class="inputbox auto-submit"',
						'value', 'text', $filterType);

		// project select
		$lists['select_project'] = JHTML::_(
						'teamtimeformals.variables_filter', $options, 'filter_project',
						'class="inputbox"', 'value', 'text', $filterProject, $filterUsing);

		// get using options		
		$options = array();
		$options[] = JHTML::_('select.option', "",
						"- " . JText::_('All destinations') . " -");
		foreach ($doctypeModel->getUsings() as $k => $v) {
			if ($k != "system") {
				$options[] = JHTML::_('select.option', $v[0], $v[1]);
			}
		}
		$lists['select_using'] = JHTML::_(
						'select.genericlist', $options, 'filter_using', 'class="inputbox"',
						'value', 'text', $filterUsing);

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);

		$this->assignRef('filter_using', $filterUsing);

		parent::display($tpl);
	}

}