<?php

class TeamtimeViewTasks extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$helperBase = TeamTime::helper()->getBase();
		$user = & JFactory::getUser();

		// set toolbar items
		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . JText::_('Tasks'), TEAMLOG_ICON);
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();

		JHTML::_('behavior.tooltip');

		// get request vars		
		$controller = JRequest::getWord('controller');

		JHTML::script('task-list.js', "administrator/components/" . $option . "/assets/js/teamtime/");

		$name = $this->get('name');
		$filterOrder = $mainframe->getUserStateFromRequest($option . $name . '.filter_order',
				'filter_order', 'a.id', 'cmd');
		$filterOrderDir = $mainframe->getUserStateFromRequest($option . $name . '.filter_order_Dir',
				'filter_order_Dir', '', 'word');
		$filterState = $mainframe->getUserStateFromRequest($option . $name . '.filter_state',
				'filter_state', '', 'cmd');
		$search = $mainframe->getUserStateFromRequest($option . $name . '.search', 'search', '', 'string');
		$search = JString::strtolower($search);

		$filterProject = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_project', 'filter_project', '', 'cmd');
		$filterType = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_type', 'filter_type', '', 'cmd');

		// get data from the model
		$items = & $this->get('data');
		$total = & $this->get('total');
		$pagination = & $this->get('pagination');

		// table ordering
		$lists['order_Dir'] = $filterOrderDir;
		$lists['order'] = $filterOrder;

		// search filter
		$lists['search'] = $search;

		// state select
		$options = JHTML::_('select.option', '', '- ' . JText::_('Select State') . ' -');
		$lists['select_state'] = JHTML::_('teamtime.taskstatelist', $options, 'filter_state',
						'class="inputbox auto-submit"', 'value', 'text', $filterState);

		$lists['select_project'] = $helperBase->getProjectSelect($filterProject,
				array(
			"attrs" => "",
			"fieldId" => "filter_project",
			"firstOptions" => JHTML::_('select.option', '', '- ' . JText::_('Select Project') . ' -')
				));

		$projectFilter = $helperBase->getProjectFilter($filterProject);
		list($lists['select_type'], $typeId) = $helperBase->getTypesSelect($filterType, $projectFilter,
				array("fieldId" => "filter_type",
			"firstOptions" => JHTML::_('select.option', '', '- ' . JText::_('Select Type') . ' -')
				));

		if (TeamTime::addonExists("com_teamtimecareer")) {
			$filterTargetId = $mainframe->getUserStateFromRequest(
					$option . '.filter_target_id', 'filter_target_id', '', 'int');
			$options = JHTML::_(
							'select.option', '0', '- ' . JText::_('Select vector of goal') . ' -', 'value', 'text', false);
			$lists['select_target'] = JHTML::_(
							'teamtimecareer.goalslist', 0, $options, 'filter_target_id', 'class="inputbox auto-submit"',
							'value', 'text', $filterTargetId);
		}

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