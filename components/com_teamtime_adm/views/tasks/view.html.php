<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/*
  Class: TaskViewTasks
  The View Class for Tasks
 */

class TaskViewTasks extends JView {

  function display($tpl = null) {
    global $mainframe;

    $db = & JFactory::getDBO();
    $user = & JFactory::getUser();

    // set toolbar items
    JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . JText::_('Tasks'), TEAMLOG_ICON);
    JToolBarHelper::deleteList();
    JToolBarHelper::editListX();
    JToolBarHelper::addNewX();

    JHTML::_('behavior.tooltip');

    // get request vars
    $option = JRequest::getCmd('option');
    $controller = JRequest::getWord('controller');
    $name = $this->get('name');
    $filter_order = $mainframe->getUserStateFromRequest($option . $name . '.filter_order', 'filter_order', 'a.id', 'cmd');
    $filter_order_Dir = $mainframe->getUserStateFromRequest($option . $name . '.filter_order_Dir', 'filter_order_Dir', '', 'word');
    $filter_state = $mainframe->getUserStateFromRequest($option . $name . '.filter_state', 'filter_state', '', 'cmd');
    $search = $mainframe->getUserStateFromRequest($option . $name . '.search', 'search', '', 'string');
    $search = JString::strtolower($search);

    $filter_project = $mainframe->getUserStateFromRequest(
            $option . $name . '.filter_project', 'filter_project', '', 'cmd');
    $filter_type = $mainframe->getUserStateFromRequest(
            $option . $name . '.filter_type', 'filter_type', '', 'cmd');

    // get data from the model
    $items = & $this->get('data');
    $total = & $this->get('total');
    $pagination = & $this->get('pagination');

    // table ordering
    $lists['order_Dir'] = $filter_order_Dir;
    $lists['order'] = $filter_order;

    // search filter
    $lists['search'] = $search;

    // state select
    $options = JHTML::_('select.option', '', '- ' . JText::_('Select State') . ' -');
    $lists['select_state'] = JHTML::_('teamlog.taskstatelist', $options, 'filter_state', 'class="inputbox auto-submit"', 'value', 'text', $filter_state);

    $options = JHTML::_('select.option', '', '- Выберите проект -');
    $lists['select_project'] = JHTML::_('teamlog.projectlist', $options, 'filter_project', 'class="inputbox auto-submit"', 'value', 'text', $filter_project);

    $options = JHTML::_('select.option', '', '- Выберите тип -');
    $lists['select_type'] = JHTML::_('teamlog.typelist', $options, 'filter_type', 'class="inputbox auto-submit"', 'value', 'text', $filter_type);

    if (TeamTime::addonExists("com_teamtimedotu")) {
      $filter_target_id = $mainframe->getUserStateFromRequest(
              $option . '.filter_target_id', 'filter_target_id', '', 'int');
      $options = JHTML::_(
              'select.option', '0', '- ' . JText::_('Select vector of goal') . ' -', 'value', 'text', false);
      $lists['select_target'] = JHTML::_(
              'teamtimedotu.goalslist', 0, $options, 'filter_target_id', 'class="inputbox auto-submit"', 'value', 'text', $filter_target_id);
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