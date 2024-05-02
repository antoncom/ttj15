<?php

class TeamtimebpmViewSpaces extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$user = & JFactory::getUser();
		$config = & JFactory::getConfig();

		// get request vars		
		JHTML::script('space-list.js', URL_MEDIA_COMPONENT_ASSETS . "js/teamtimebpm/");
		JHTML::script('editable-tags.js', URL_MEDIA_COMPONENT_ASSETS . "js/teamtimebpm/");
		JHTML::script('follow.js', URL_MEDIA_COMPONENT_ASSETS . "js/teamtimebpm/");

		// set toolbar items
		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . JText::_('Spaces'), TEAMLOG_ICON);
		//JToolBarHelper::deleteList();
		//JToolBarHelper::editListX();
		JToolBarHelper::addNewX();

		JHTML::_('behavior.tooltip');

		$from_period = JRequest::getVar('from_period', '?');
		$until_period = JRequest::getVar('until_period', '?');

		// get request vars
		$option = JRequest::getCmd('option');
		$controller = JRequest::getWord('controller');

		$name = $this->get('name');
		$filter_order = $mainframe->getUserStateFromRequest($option . $name . '.filter_order',
				'filter_order', 'a.name', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option . $name . '.filter_order_Dir',
				'filter_order_Dir', '', 'word');
		$search = $mainframe->getUserStateFromRequest($option . $name . '.search', 'search', '', 'string');
		if ($search == JText::_("BPM FILTER DEFAULT")) {
			$search = "";
		}
		$search = JString::strtolower($search);

		$filter_archived = $mainframe->getUserStateFromRequest($option . $name . '.filter_archived',
				'filter_archived', 'active', 'string');
		$options = array();
		$options[] = JHTML::_('select.option', "active", JText::_('Active'));
		$options[] = JHTML::_('select.option', "archived", JText::_('Archived'));
		$lists['select_archived'] = JHTML::_(
						'select.genericlist', $options, 'archived', 'class="inputbox auto-submit"', 'value', 'text',
						$filter_archived);

		$mSpace = new TeamtimebpmModelSpace();

		// get data from the model
		if ($filter_order == "a.tags") {
			$lists["is_grouped"] = true;
			$items = & $this->get('groupedData');
		}
		else {
			$lists["is_grouped"] = false;
			$items = & $this->get('data');
		}

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
		$this->assignRef('mSpace', $mSpace);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('total', $total);

		$this->assignRef('from_period', $from_period);
		$this->assignRef('until_period', $until_period);
		$this->assignRef('date_presets', $date_presets);
		$this->assignRef('date_select', $date_select);
		$this->assignRef('period', JRequest::getVar('period', ''));

		parent::display($tpl);
	}

}