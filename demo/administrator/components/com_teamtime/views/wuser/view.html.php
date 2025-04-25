<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/*
  Class: TodoViewTodo
  The View Class for Todo
 */

class WuserViewWuser extends JView {

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
        JText::_('User') . ': <small><small>[ ' . $text . ' ]</small></small>', TEAMLOG_ICON);
    JToolBarHelper::save();
    JToolBarHelper::apply();
    $edit ? JToolBarHelper::cancel('cancel', 'Close') : JToolBarHelper::cancel();

    $cid = JRequest::getVar('cid', array(0), 'get', 'array');
    $cid = (int) $cid[0];

    // get data from the model
    $db->setQuery("select * from #__users
			left join #__teamlog_userdata on #__users.id = #__teamlog_userdata.user_id
			where #__users.id = $cid");
    $item = $db->loadObject();

    // set template vars
    $this->assignRef('user', $user);
    $this->assignRef('option', $option);
    $this->assignRef('controller', $controller);
    $this->assignRef('lists', $lists);
    $this->assignRef('item', $item);
    
    parent::display($tpl);
  }

}