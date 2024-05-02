<?php

class TeamtimebpmViewTemplatediagrampage extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		JRequest::setVar('hidemainmenu', 1);

		$id = JRequest::getVar("id", 0);

		$mTemplate = new TeamtimebpmModelTemplate();
		$mTemplate->setId($id);
		$template = $mTemplate->getData();

		JHTML::script('template-diagrampage.js', URL_MEDIA_COMPONENT_ASSETS . "js/teamtimebpm/");
		JHTML::stylesheet('diagrampage.css', URL_MEDIA_COMPONENT_ASSETS . "css/");

		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . $template->name, TEAMLOG_ICON);

		JToolBarHelper::custom('savetemplate', 'savetemplate', 'savetemplate', JText::_('Save'));
		JToolBarHelper::custom('exittemplate', 'exittemplate', 'exittemplate', JText::_('End Edit'));

		$controller = JRequest::getWord('controller');

		$frameUrl = JURI::base() .
				"index.php?option=com_teamtimebpm&controller=process&view=processdiagram" .
				"&id=" . $id . "&tmpl=component&is_template=1";

		$options = array();
		$options[] = JHTML::_('select.option', "status", JText::_('Show Status'));
		$options[] = JHTML::_('select.option', "plan", JText::_('Show Hours Plan'));
		$options[] = JHTML::_('select.option', "time", JText::_('Show Time'));
		$options[] = JHTML::_('select.option', "price", JText::_('Show Price'));
		$options[] = JHTML::_('select.option', "performer", JText::_('Show Performer'));
		$options[] = JHTML::_('select.option', "date", JText::_('Show Date'));
		$options[] = JHTML::_('select.option', "", JText::_('Show None'));

		$lists["select_show"] = JHTML::_(
						'select.genericlist', $options, 'filter_show', 'class="inputbox"', 'value', 'text', "status");

		$lists["order"] = "";
		$lists["order_Dir"] = "";

		$this->assignRef('frameUrl', $frameUrl);
		$this->assignRef('template', $template);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);

		parent::display($tpl);
	}

}