<?php

class TemplateViewTemplate extends JView {

	function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$user =& JFactory::getUser();

		// get request vars
		$controller   = JRequest::getWord('controller');
		$edit         = JRequest::getVar('edit', true);

		//$project_ids = JRequest::getVar('projects');

		// set toolbar items
		$text = $edit ? JText::_('Edit') : JText::_('New');
		JToolBarHelper::title(JText::_('Formals template').': <small><small>[ '.$text.' ]</small></small>', TEAMLOG_ICON);
		JToolBarHelper::save();
		JToolBarHelper::apply();
		$edit ?	JToolBarHelper::cancel('cancel', 'Close') : JToolBarHelper::cancel();

		// get data from the model
		$item =& $this->get('data');

		if(!$edit){
			
		}

		// type select
		$options = JHTML::_('select.option', '', '- '.JText::_('SELECT TEMPLATE TYPE').' -');
		$lists['select_type'] = JHTML::_('teamtimeformals.typeslist', $options,
			'type', 'class="inputbox"', 'value', 'text', $item->type, false, true);
		
		/*$options = array(
			JHTML::_('select.option', '', '- '.JText::_('Select Project').' -'),
			JHTML::_('select.option', '0', '- '.JText::_('All Projects').' -')
		);
		$lists['select_projects'] = JHTML::_('teamtime.projectlist',
			$options, 'projects[]', 'size="10" multiple class="inputbox"', 'value', 'text',
			$project_ids);
		*/
		
		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);		
		$this->assignRef('item', $item);

		parent::display($tpl);
	}
}