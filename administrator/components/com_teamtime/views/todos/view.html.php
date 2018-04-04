<?php

class TeamtimeViewTodos extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$helperBase = TeamTime::helper()->getBase();
		$user = & JFactory::getUser();

		// set toolbar items
		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . JText::_('Todos'), TEAMLOG_ICON);
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();

		JHTML::_('behavior.tooltip');

		$filterUserId = $mainframe->getUserStateFromRequest(
				$option . '.filter_user_id', 'filter_user_id', '', 'int');
		$fromPeriod = $mainframe->getUserStateFromRequest(
				$option . '.from_period', 'from_period', '', 'string');
		$untilPeriod = $mainframe->getUserStateFromRequest(
				$option . '.until_period', 'until_period', '', 'string');

		list($lists['select_date'], $dateSelect, $datePresets) = $helperBase->getDateSelect(
				$fromPeriod, $untilPeriod);

		// get request vars
		$controller = JRequest::getWord('controller');
		$name = $this->get('name');
		$filterOrder = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_order', 'filter_order', 'a.id', 'cmd');
		$filterOrderDir = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_order_Dir', 'filter_order_Dir', '', 'word');
		$filterState = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_state', 'filter_state', '', 'cmd');
		$search = $mainframe->getUserStateFromRequest(
				$option . $name . '.search', 'search', '', 'string');
		$search = JString::strtolower($search);

		$typeId = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_type_id', 'type_id', '', 'string');
		$taskId = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_task_id', 'task_id', '', 'string');
		$projectId = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_project_id', 'project_id', '', 'string');

		$projectFilter = $helperBase->getProjectFilter($projectId);
		list($lists['select_type'], $typeId) = $helperBase->getTypesSelect($typeId, $projectFilter);
		list($lists['select_task'], $taskId) = $helperBase->getTasksSelect($typeId, $taskId,
				$projectFilter);

		$lists['select_user'] = $helperBase->getUserSelect($filterUserId,
				array("fieldId" => "filter_user_id"));

		$lists['select_project'] = $helperBase->getProjectSelect($projectId);

		// get data from the model
		$items = & $this->get('data');
		$total = & $this->get('total');
		$pagination = & $this->get('pagination');

		$totalHours = & $this->get('totalhours');

		// table ordering
		$lists['order_Dir'] = $filterOrderDir;
		$lists['order'] = $filterOrder;

		// search filter
		$lists['search'] = $search;

		// state select
		$lists['select_state'] = $helperBase->getStateSelect($filterState);

		// level limit filter
		$filterLimit = $mainframe->getUserStateFromRequest(
				$option . '.filter_limit', 'filter_limit', 10, 'int');
		$lists['levellist'] = JHTML::_('select.integerlist', 1, 20, 1, 'filter_limit',
						'size="1" onchange="document.adminForm.submit();"', $filterLimit);

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('total_hours', $totalHours);

		$this->assignRef('from_period', $fromPeriod);
		$this->assignRef('until_period', $untilPeriod);
		$this->assignRef('date_presets', $datePresets);

		parent::display($tpl);
	}

}