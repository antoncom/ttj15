<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/*
  Class: TodoViewTodo
  The View Class for Todo
 */

class TodoViewTodo extends JView {

  function display($tpl = null) {
    global $mainframe;

    $db = & JFactory::getDBO();
    $user = & JFactory::getUser();

    // get request vars
    $option = JRequest::getCmd('option');
    $controller = JRequest::getWord('controller');
    $edit = JRequest::getVar('edit', true);

    // set toolbar items
    $text = $edit ? JText::_('Edit') : JText::_('New');
    JToolBarHelper::title(
        JText::_('Todo') . ': <small><small>[ ' . $text . ' ]</small></small>', TEAMLOG_ICON);
    JToolBarHelper::save();
    JToolBarHelper::apply();
    $edit ? JToolBarHelper::cancel('cancel', 'Close') : JToolBarHelper::cancel();

    // get data from the model
    $item = & $this->get('data');

    if (!$edit) {
      $item->description = $this->get('defaultdescription');
      //$item->hourly_rate = "default";
    }

    // user select
    $options = JHTML::_('select.option', '', '- ' . JText::_('Select User') . ' -');
    $lists['select_user'] = JHTML::_('teamlog.userlist', $options, 'user_id', 'class="inputbox"', 'value', 'text', $item->user_id);

    // state select
    $options = JHTML::_('select.option', '', '- ' . JText::_('Select State') . ' -');
    $lists['select_state'] = JHTML::_('teamlog.todostatelist', $options, 'state', 'class="inputbox"', 'value', 'text', $item->state == null ? '0' : $item->state);

    // project select
    $options = JHTML::_('select.option', '', '- ' . JText::_('Global (All Projects)') . ' -');
    $lists['select_project'] = JHTML::_('teamlog.projectlist', $options, 'project_id', 'class="inputbox" size=15', 'value', 'text', $item->project_id);

    // type select
    $options = JHTML::_('select.option', '', '- ' . JText::_('Select Type') . ' -');
    $lists['select_type'] = JHTML::_('teamlog.typelist', $options, 'type_id', 'class="inputbox"  size=15', 'value', 'text', $item->type_id);

    // task select
    $options = JHTML::_('select.option', '', '- ' . JText::_('Select Task') . ' -');
    $lists['select_task'] = JHTML::_('teamlog.tasklist', $item->project_id, $options, 'curtaskid', 'class="inputbox" size=15', 'value', 'text', ($item->task_id ? $item->task_id : '-'));

    // todo select
    $todo_model = new TodoModelTodo();
    $item->parent_id = $todo_model->getParentTodo($item->id);

    if ($item->project_id != "") {
      $options = JHTML::_('select.option', '', '- ' . JText::_('INCLUDED TO TEAM TODO') . ' -', 'value', 'text', true);
      $lists['select_todo'] = JHTML::_('teamlog.todolist', $item->project_id, $item->id, $options, 'curtodoid', 'class="inputbox" size=16', 'value', 'text', $item->parent_id ? $item->parent_id : "-");
    }
    else {
      $lists['select_todo'] = JHTML::_('select.genericlist', array(JHTML::_('select.option', '', '- ' . JText::_("Select project first") . ' -')), 'curtodoid', 'class="inputbox" size=16', 'value', 'text', '');
    }

    // goals select
    $lists['select_goals'] = TeamTime::_(
        "targetvector_selector_by_todo", "", $item, 'Select vector of goals');

    // set template vars
    $this->assignRef('user', $user);
    $this->assignRef('option', $option);
    $this->assignRef('controller', $controller);
    $this->assignRef('lists', $lists);
    $this->assignRef('item', $item);

    parent::display($tpl);
  }

}