<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/*
  Class: LogViewLogs
  The View Class for Logs
 */

class LogViewLogs extends JView {

	function display($tpl = null) {
		global $mainframe, $option;

		$db = & JFactory::getDBO();
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
		$filter_order = $mainframe->getUserStateFromRequest($option . $name . '.filter_order', 'filter_order', 'a.id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option . $name . '.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$filter_user_id = $mainframe->getUserStateFromRequest($option . '.filter_user_id', 'filter_user_id', '', 'int');

		$filter_project_id = $mainframe->getUserStateFromRequest($option . $name . '.filter_project_id', 'filter_project_id', '', 'int');
		$search = $mainframe->getUserStateFromRequest($option . $name . '.search', 'search', '', 'string');
		$search = JString::strtolower($search);

		// get data from the model
		$items = & $this->get('data');
		$total = & $this->get('total');
		$pagination = & $this->get('pagination');

		// user select
		$query = 'SELECT b.id AS value, b.name AS text'
			. ' FROM #__teamlog_log AS a '
			. ' LEFT JOIN #__users AS b ON a.user_id = b.id'
			. ' where b.block = 0'
			. ' GROUP BY b.id'
			. ' ORDER BY b.name';
		$options = JHTML::_('select.option', '', '- ' . JText::_('Select User') . ' -');
		$lists['select_user'] = JHTML::_('teamlog.querylist', $query, $options, 'filter_user_id', 'class="inputbox auto-submit"', 'value', 'text', $filter_user_id);

		// project select
		$query = 'SELECT b.id AS value, b.name AS text'
			. ' FROM #__teamlog_log AS a '
			. ' LEFT JOIN #__teamlog_project AS b ON a.project_id = b.id'
			. ' GROUP BY b.id'
			. ' ORDER BY b.name';
		$options = JHTML::_('select.option', '', '- ' . JText::_('Select Project') . ' -');
		$lists['select_project'] = JHTML::_('teamlog.querylist', $query, $options, 'filter_project_id', 'class="inputbox auto-submit"', 'value', 'text', $filter_project_id);

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

		parent::display($tpl);
	}

}