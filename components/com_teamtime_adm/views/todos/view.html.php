<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/*
  Class: TodoViewTodos
  The View Class for Todos
 */

class TodoViewTodos extends JView {

	function checkTaskItem($query, $value) {
		$db = & JFactory::getDBO();

		$db->setQuery($query);
		$result = $db->loadObjectList();

		foreach ($result as $row) {
			if ($row->value == $value) {
				return true;
			}
		}

		return false;
	}

	function display($tpl = null) {
		global $mainframe, $option;

		$db = & JFactory::getDBO();
		$user = & JFactory::getUser();
		$config = & JFactory::getConfig();

		// set toolbar items
		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . JText::_('Todos'), TEAMLOG_ICON);
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();

		JHTML::_('behavior.tooltip');

		$filter_user_id = $mainframe->getUserStateFromRequest(
				$option . '.filter_user_id', 'filter_user_id', '', 'int');
		$from_period = $mainframe->getUserStateFromRequest(
				$option . '.from_period', 'from_period', '', 'string');
		$until_period = $mainframe->getUserStateFromRequest(
				$option . '.until_period', 'until_period', '', 'string');

		// set date presets
		$date = JFactory::getDate();
		$date = $date->toUnix();
		$date = mktime(0, 0, 0, date('n', $date), date('j', $date), date('Y', $date));
		$monday = (date('w', $date) == 1) ? $date : strtotime('last Monday', $date);

		$date_presets['last_month'] = array(
				'name' => 'Last Month',
				'from' => date('Y-m-d', mktime(0, 0, 0, date('n', $date) - 1, 1, date('Y', $date))),
				'until' => date('Y-m-d', mktime(0, 0, 0, date('n', $date), 0, date('Y', $date))));

		$date_presets['last30'] = array(
				'name' => 'Last 30 days',
				'from' => date('Y-m-d', strtotime('-29 day', $date)),
				'until' => date('Y-m-d', $date));

		$date_presets['last_week'] = array(
				'name' => 'Last Week',
				'from' => date('Y-m-d', strtotime('-7 day', $monday)),
				'until' => date('Y-m-d', strtotime('-1 day', $monday)));

		$date_presets['last30'] = array(
				'name' => 'Last 30 days',
				'from' => date('Y-m-d', strtotime('-29 day', $date)),
				'until' => date('Y-m-d', $date));
		$date_presets['today'] = array(
				'name' => 'Today',
				'from' => date('Y-m-d', $date),
				'until' => date('Y-m-d', $date));
		$date_presets['week'] = array(
				'name' => 'This Week',
				'from' => date('Y-m-d', $monday),
				'until' => date('Y-m-d', strtotime('+6 day', $monday)));
		$date_presets['month'] = array(
				'name' => 'This Month',
				'from' => date('Y-m-d', mktime(0, 0, 0, date('n', $date), 1, date('Y', $date))),
				'until' => date('Y-m-d', mktime(0, 0, 0, date('n', $date) + 1, 0, date('Y', $date))));
		$date_presets['year'] = array(
				'name' => 'This Year',
				'from' => date('Y-m-d', mktime(0, 0, 0, 1, 1, date('Y', $date))),
				'until' => date('Y-m-d', mktime(0, 0, 0, 12, 31, date('Y', $date))));

		$date_presets['next_week'] = array(
				'name' => 'Next Week',
				'from' => date('Y-m-d', strtotime('+7 day', $monday)),
				'until' => date('Y-m-d', strtotime('+13 day', $monday)));
		$date_presets['next_month'] = array(
				'name' => 'Next Month',
				'from' => date('Y-m-d', mktime(0, 0, 0, date('n', $date) + 1, 1, date('Y', $date))),
				'until' => date('Y-m-d', mktime(0, 0, 0, date('n', $date) + 2, 0, date('Y', $date))));

		// set period
		$tzoffset = $config->getValue('config.offset');
		$from = JFactory::getDate($from_period, $tzoffset);
		$until = JFactory::getDate($until_period, $tzoffset);

		// check period - set to defaults if no value is set or dates cannot be parsed
		if ($from->_date === false || $until->_date === false) {
			if ($from_period != '?' && $until_period != '?') {
				JError::raiseNotice(500, JText::_('Please enter a valid date format (YYYY-MM-DD)'));
			}
			$from_period = $date_presets['last30']['from'];
			$until_period = $date_presets['last30']['until'];
			$from = JFactory::getDate($from_period, $tzoffset);
			$until = JFactory::getDate($until_period, $tzoffset);
		}
		else {
			if ($from->toUnix() > $until->toUnix()) {
				list($from_period, $until_period) = array($until_period, $from_period);
				list($from, $until) = array($until, $from);
			}
		}

		// simpledate select
		$select = '';
		$options = array(
				JHTML::_('select.option', '', '- ' . JText::_('Select Period') . ' -', 'text', 'value')
		);
		foreach ($date_presets as $name => $value) {
			$options[] = JHTML::_('select.option', $name, JText::_($value['name']), 'text', 'value');
			if ($value['from'] == $from_period && $value['until'] == $until_period) {
				$select = $name;
			}
		}
		$lists['select_date'] = JHTML::_(
						'select.genericlist', $options, 'period', 'class="inputbox" size="1"', 'text', 'value',
						$select);

		// get request vars
		$controller = JRequest::getWord('controller');
		$name = $this->get('name');
		$filter_order = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_order', 'filter_order', 'a.id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_order_Dir', 'filter_order_Dir', '', 'word');
		$filter_state = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_state', 'filter_state', '', 'cmd');
		$search = $mainframe->getUserStateFromRequest(
				$option . $name . '.search', 'search', '', 'string');
		$search = JString::strtolower($search);

		$type_id = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_type_id', 'type_id', '', 'string');
		$task_id = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_task_id', 'task_id', '', 'string');
		$project_id = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_project_id', 'project_id', '', 'string');

		$project_filter = $project_id > 0 ?
				" and a.project_id = {$project_id} " : "";
		$query = 'SELECT b.id AS value, b.name AS text
			FROM #__teamlog_log AS a
			LEFT JOIN #__teamlog_type AS b ON a.type_id = b.id where b.id '
				. $project_filter
				. ' GROUP BY b.name ORDER BY b.name';
		if (!$this->checkTaskItem($query, $type_id)) {
			$type_id = "";
		}
		$options = JHTML::_('select.option', '', JText::_('All types'));
		$lists['select_type'] = JHTML::_(
						'teamlog.querylist', $query, $options, 'type_id', 'class="inputbox auto-submit"', 'value',
						'text', $type_id);

		$type_filter = $type_id != "" ?
				" and b.type_id = {$type_id}" : "";
		$query = 'SELECT b.name AS value, b.name AS text
			FROM #__teamlog_log AS a
			LEFT JOIN #__teamlog_task AS b ON a.task_id = b.id  where b.id '
				. $project_filter
				. $type_filter
				. ' GROUP BY b.name ORDER BY b.name';
		if (!$this->checkTaskItem($query, $task_id))
			$task_id = "";

		$options = JHTML::_('select.option', '', JText::_('All tasks'));
		$lists['select_task'] = JHTML::_(
						'teamlog.querylist', $query, $options, 'task_id', 'class="inputbox auto-submit"', 'value',
						'text', $task_id);

		$query = 'SELECT b.id AS value, b.name AS text'
				. ' FROM #__teamlog_log AS a '
				. ' LEFT JOIN #__teamlog_project AS b ON a.project_id = b.id'
				. ' GROUP BY b.id'
				. ' ORDER BY b.name';
		$options = JHTML::_('select.option', '', '- ' . JText::_('All Projects') . ' -');
		$lists['select_project'] = JHTML::_(
						'teamlog.querylist', $query, $options, 'project_id', 'class="inputbox auto-submit" size="1"',
						'value', 'text', $project_id);

		// get data from the model
		$items = & $this->get('data');
		$total = & $this->get('total');
		$pagination = & $this->get('pagination');

		$total_hours = & $this->get('totalhours');

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		// search filter
		$lists['search'] = $search;

		// user select
		$query = 'SELECT b.id AS value, b.name AS text
			FROM #__teamlog_todo AS a
			LEFT JOIN #__users AS b ON a.user_id = b.id
			where b.block = 0
			GROUP BY b.id
			ORDER BY b.name';
		$options = JHTML::_('select.option', '', '- ' . JText::_('Select User') . ' -');
		$lists['select_user'] = JHTML::_(
						'teamlog.querylist', $query, $options, 'filter_user_id', 'class="inputbox auto-submit"',
						'value', 'text', $filter_user_id);

		// state select
		$options = JHTML::_('select.option', '', '- ' . JText::_('Select State') . ' -');
		$lists['select_state'] = JHTML::_(
						'teamlog.todostatelist', $options, 'filter_state', 'class="inputbox auto-submit"', 'value',
						'text', $filter_state);

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
		$this->assignRef('total_hours', $total_hours);

		$this->assignRef('from_period', $from_period);
		$this->assignRef('until_period', $until_period);
		$this->assignRef('date_presets', $date_presets);

		parent::display($tpl);
	}

}