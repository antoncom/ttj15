<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/*
   Class: LogViewLog
   The View Class for Log
*/
class LogViewLog extends JView {

	function display($tpl = null) {
		global $mainframe;

		$db   =& JFactory::getDBO();
		$user =& JFactory::getUser();

		// get request vars
		$option       = JRequest::getCmd('option');
		$controller   = JRequest::getWord('controller');
		$edit         = JRequest::getVar('edit', true);

		// set toolbar items
		$text = $edit ? JText::_('Edit') : JText::_('New');
		JToolBarHelper::title(JText::_('Log').': <small><small>[ '.$text.' ]</small></small>', TEAMLOG_ICON);
		JToolBarHelper::save();
		JToolBarHelper::apply();
		$edit ?	JToolBarHelper::cancel('cancel', 'Close') : JToolBarHelper::cancel();

		// get data from the model
		$item =& $this->get('data');

		// user select
		$options = JHTML::_('select.option', '', '- '.JText::_('Select User').' -');		
		$lists['select_user'] = JHTML::_('teamlog.userlist', $options, 'user_id', 'class="inputbox"', 'value', 'text', $item->user_id);

		// project select
		$options = JHTML::_('select.option', '', '- '.JText::_('Select Project').' -');		
		$lists['select_project'] = JHTML::_('teamlog.projectlist', $options, 'project_id', 'class="inputbox" onchange="getTasks(\''.JRoute::_('index.php?option='.$option.'&controller='.$controller.'&view=log&format=raw').'\')"', 'value', 'text', $item->project_id);	

		// task select
		$options = JHTML::_('select.option', '', '- '.JText::_('Select Task').' -');
		$lists['select_task'] = JHTML::_('teamlog.tasklist', $item->project_id, $options, 'task_id', 'class="inputbox"', 'value', 'text', ($item->task_id ? $item->task_id : '-'));

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);
		$this->assignRef('item', $item);

		parent::display($tpl);
	}

}