<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/*
   Class: TypeViewType
   The View Class for Type
*/
class TypeViewType extends JView {

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
		JToolBarHelper::title(JText::_('Type').': <small><small>[ '.$text.' ]</small></small>', TEAMLOG_ICON);
		JToolBarHelper::save();
		JToolBarHelper::apply();
		$edit ?	JToolBarHelper::cancel('cancel', 'Close') : JToolBarHelper::cancel();

		// get data from the model
		$item =& $this->get('data');

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('item', $item);

		parent::display($tpl);
	}

}