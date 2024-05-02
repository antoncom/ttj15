<?php

class TeamtimebpmViewTemplateimport extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		JRequest::setVar('hidemainmenu', 1);

		$id = JRequest::getVar("template_id", 0);

		$mSpace = new TeamtimebpmModelSpace();
		$mTemplate = new TeamtimebpmModelTemplate();
		$mTemplate->setId($id);
		$template = $mTemplate->getData();

		JHTML::script('template-import.js', URL_MEDIA_COMPONENT_ASSETS . "js/teamtimebpm/");

		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . JText::_('Import template'), TEAMLOG_ICON);

		$controller = JRequest::getWord('controller');

		$defaultOption = new stdClass();
		$defaultOption->value = "";
		$defaultOption->text = "- " . JText::_("Select default template destination") . " -";
		$options = $mSpace->getOptionsList();
		array_unshift($options, $defaultOption);
		$lists['select_spaces'] = JHTML::_(
						'select.genericlist', $options, 'space_id', 'class="inputbox"', 'value', 'text',
						$template->space_id);

		$lists["order"] = "";
		$lists["order_Dir"] = "";

		$this->assignRef('template', $template);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);

		parent::display($tpl);
	}

}