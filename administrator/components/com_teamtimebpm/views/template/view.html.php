<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TeamtimebpmViewTemplate extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$helperBase = TeamTime::helper()->getBase();
		$user = & JFactory::getUser();

		// get request vars		
		$controller = JRequest::getWord('controller');
		$edit = JRequest::getVar('edit', true);

		// set toolbar items
		$text = $edit ? JText::_('Edit') : JText::_('New');
		JToolBarHelper::title(JText::_('Template') . ': <small><small>[ ' . $text . ' ]</small></small>',
				TEAMLOG_ICON);
		JToolBarHelper::save();
		JToolBarHelper::apply();
		$edit ? JToolBarHelper::cancel('cancel', 'Close') : JToolBarHelper::cancel();

		// get data from the model
		$item = & $this->get('data');

		$mTemplate = new TeamtimebpmModelTemplate();
		$mSpace = new TeamtimebpmModelSpace();

		if ($item->modified_by) {
			$item->user_name = $mTemplate->getModifiedUserName($item->modified_by);
		}

		$filter_archived = $item->archived != "" ? $item->archived : "active";

		$options = array();
		$options[] = JHTML::_('select.option', "active", JText::_('Active'));
		$options[] = JHTML::_('select.option', "archived", JText::_('Archived'));
		$lists['select_archived'] = JHTML::_(
						'select.genericlist', $options, 'archived', 'class="inputbox"', 'value', 'text',
						$filter_archived);

		$defaultOption = new stdClass();
		$defaultOption->value = "";
		$defaultOption->text = "- " . JText::_("Select space") . " -";
		$options = $mSpace->getOptionsList();
		array_unshift($options, $defaultOption);
		$lists['select_spaces'] = JHTML::_(
						'select.genericlist', $options, 'space_id', 'class="inputbox"', 'value', 'text',
						$item->space_id);

		$lists['select_project'] = $helperBase->getProjectSelect($item->project_id,
				array(
			"showClosed" => false,
			"autosubmit" => false,
			"firstOptions" => JHTML::_('select.option', "", "- " . JText::_('Select project') . " -"),
			"type" => "space",
			"spaceId" => $item->space_id
				));

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);
		$this->assignRef('item', $item);

		parent::display($tpl);
	}

}