<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class WuserViewWusers extends JView {

  function display($tpl = null) {
    global $mainframe;

    $db = & JFactory::getDBO();
    $user = & JFactory::getUser();
    $config = & JFactory::getConfig();

    // set toolbar items
    JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . JText::_('Users'), TEAMLOG_ICON);
    //JToolBarHelper::deleteList();
    //JToolBarHelper::editListX();
    //JToolBarHelper::addNewX();
		
		// get request vars
    $option = JRequest::getCmd('option');
    $controller = JRequest::getWord('controller');
    $name = $this->get('name');

    JHTML::_('behavior.tooltip');

    $from_period = JRequest::getVar('from_period', '?');
    $until_period = JRequest::getVar('until_period', '?');

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
    $date_select = array();
    $options = array(JHTML::_('select.option', '', '- ' . JText::_('Select Period') . ' -', 'text', 'value'));
    foreach ($date_presets as $datename => $value) {
      $options[] = JHTML::_('select.option', $datename, JText::_($value['name']), 'text', 'value');
      if ($value['from'] == $from_period && $value['until'] == $until_period) {
        $select = $datename;
        $date_select = $value;
      }
    }
    $lists['select_date'] = JHTML::_(
            'select.genericlist', $options, 'period', 'class="inputbox" size="1"', 'text', 'value', $select);

		$mainframe->setUserState($option . $name . '.from_period', $from_period);
    $mainframe->setUserState($option . $name . '.until_period', $until_period);

    $filter_order = $mainframe->getUserStateFromRequest($option . $name . '.filter_order', 'filter_order', 'a.id', 'cmd');
    $filter_order_Dir = $mainframe->getUserStateFromRequest($option . $name . '.filter_order_Dir', 'filter_order_Dir', '', 'word');
    $search = $mainframe->getUserStateFromRequest($option . $name . '.search', 'search', '', 'string');
    $search = JString::strtolower($search);

    // get data from the model
    $items = & $this->get('data');
    $total = & $this->get('total');
    $pagination = & $this->get('pagination');

    //init hours_fact
    if ($from_period && $until_period)
      $where_hours_fact = ' and date >= ' . $db->Quote($db->getEscaped("$from_period 00:00:00", true), false) .
          ' and date <= ' . $db->Quote($db->getEscaped("$until_period 23:59:59", true), false);
    else
      $where_hours_fact = "";

    if ($from_period && $until_period)
      $where_hours_fact2 = ' and created >= ' . $db->Quote($db->getEscaped("$from_period 00:00:00", true), false) .
          ' and created <= ' . $db->Quote($db->getEscaped("$until_period 23:59:59", true), false);
    else
      $where_hours_fact2 = "";

    foreach ($items as $i => $item) {
      $sql = "select sum(duration)/60 as sfact from #__teamlog_log
				where user_id = {$item->id} $where_hours_fact";
      $db->setQuery($sql);

      $row = $db->loadObject();
      $items[$i]->sfact = $row->sfact;

      $sql = "select sum(hours_plan) as splan from #__teamlog_todo
				where user_id = {$item->id} $where_hours_fact2";
      $db->setQuery($sql);

      $row = $db->loadObject();
      $items[$i]->splan = $row->splan;
    }

    // table ordering
    $lists['order_Dir'] = $filter_order_Dir;
    $lists['order'] = $filter_order;

    // search filter
    $lists['search'] = $search;

    $has_dotu_prices = false;
    foreach ($items as $i => $item) {
      $tmp = Teamtime::_("get_maxprice_teamtimedotu", null, $item->id);
      if ($tmp !== null) {
        $has_dotu_prices = true;
      }

      $items[$i]->dotu_price = $tmp;
    }

    // set template vars
    $this->assignRef('user', $user);
    $this->assignRef('option', $option);
    $this->assignRef('controller', $controller);
    $this->assignRef('lists', $lists);
    $this->assignRef('items', $items);
    $this->assignRef('pagination', $pagination);

    $this->assignRef('from_period', $from_period);
    $this->assignRef('until_period', $until_period);
    $this->assignRef('date_presets', $date_presets);
    $this->assignRef('date_select', $date_select);
    $this->assignRef('period', JRequest::getVar('period', ''));

    $this->assignRef('has_dotu_prices', $has_dotu_prices);

    parent::display($tpl);
  }

}