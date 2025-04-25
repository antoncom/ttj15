<?php

class VariableViewVariables extends JView {

	function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$user = & JFactory::getUser();
		$config = & JFactory::getConfig();

		// set toolbar items
		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . JText::_('Formals Variables'),
				TEAMLOG_ICON);
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();

		JHTML::_('behavior.tooltip');

		// get request vars		
		$controller = JRequest::getWord('controller');
		$name = $this->get('name');
		$filter_order = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_order', 'filter_order', 'a.id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_order_Dir', 'filter_order_Dir', '', 'word');
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

		$filter_project = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_project', 'filter_project', '', 'cmd');

		$filter_using = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_using', 'filter_using', '', 'cmd');

		// project select
		$lists['select_project'] = JHTML::_(
						'teamtimeformals.variables_filter', $options, 'filter_project',
						'class="inputbox auto-submit"', 'value', 'text', $filter_project,
						$filter_using);

		$has_variables = $filter_project != "" &&
				TeamTime::helper()->getFormals()->hasVariables($filter_project, $filter_using);

		$edit_variables = "";
		if ($has_variables) {
			if ($filter_using == "0") {
				$edit_variables = '<a href="index.php?option=com_teamtime&controller=project&task=edit&cid[]=' .
						$filter_project . '&backurl=1">' . JText::_("Edit the data for") . '</a>';
			}
			else if ($filter_using == "1") {
				$edit_variables = '<a href="index.php?option=com_teamtime&controller=user&task=edit&cid[]=' .
						$filter_project . '&backurl=1">' . JText::_("Edit the data for") . '</a>';
			}
		}

		// get using options
		$doctypeModel = new DoctypeModelDoctype();
		$options = array();
		$options[] = JHTML::_('select.option', "",
						"- " . JText::_('All destinations') . " -");
		foreach ($doctypeModel->getUsings() as $k => $v) {
			$options[] = JHTML::_('select.option', $v[0], $v[1]);
		}
		$lists['select_using'] = JHTML::_(
						'select.genericlist', $options, 'filter_using', 'class="inputbox"',
						'value', 'text', $filter_using);

		// set template vars
		$this->assignRef('doctypeModel', $doctypeModel);
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);

		$this->assignRef('filter_project', $filter_project);
		$this->assignRef('edit_variables', $edit_variables);

		parent::display($tpl);
	}

}