<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class DoctypeViewDoctype extends JView {

	function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$user = & JFactory::getUser();

		// get request vars		
		$controller = JRequest::getWord('controller');
		$edit = JRequest::getVar('edit', true);

		// set toolbar items
		$text = $edit ? JText::_('Edit') : JText::_('New');
		JToolBarHelper::title(JText::_('TEMPLATE TYPE') . ': <small><small>[ ' . $text . ' ]</small></small>', TEAMLOG_ICON);
		JToolBarHelper::save();
		JToolBarHelper::apply();
		$edit ? JToolBarHelper::cancel('cancel', 'Close') : JToolBarHelper::cancel();

		// get data from the model
		$item = & $this->get('data');

		$doctypeModel = new DoctypeModelDoctype();
		$options = array();
		foreach ($doctypeModel->getUsings() as $k => $v) {
			$options[] = JHTML::_('select.option', $k, $v[1]);
		}
		$lists['select_using'] = JHTML::_(
										'select.genericlist', $options, 'using_in', 'class="inputbox"', 'value', 'text', $item->using_in);

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('item', $item);
		$this->assignRef('lists', $lists);

		parent::display($tpl);
	}

}