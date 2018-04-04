<?php

class TeamtimeformalsViewFormal extends JView {

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
		JToolBarHelper::title(
				JText::_('FORMALS DOCUMENT') . ': <small><small>[ '
				. $text . ' ]</small></small>', TEAMLOG_ICON);
		JToolBarHelper::save();
		JToolBarHelper::apply();
		$edit ? JToolBarHelper::cancel('cancel', 'Close') : JToolBarHelper::cancel();

		$fromPeriod = $mainframe->getUserStateFromRequest(
				$option . '.from_period', 'from_period', '?', 'string');
		$untilPeriod = $mainframe->getUserStateFromRequest(
				$option . '.until_period', 'until_period', '?', 'string');
		$assignementId = $mainframe->getUserStateFromRequest(
				$option . '.filter_assignement', 'project_id', 0, 'int');
		$doctypeId = $mainframe->getUserStateFromRequest(
				$option . '.filter_doctype_id', 'doctype_id', '');

		// get data from the model
		$item = & $this->get('data');

		$mFormal = new TeamtimeformalsModelFormal();

		// project select
		$options = array(JHTML::_(
					'select.option', '- ' . JText::_('Using in') . ' -', '', 'text', 'value'));
		$lists['select_project'] = JHTML::_(
						'select.genericlist', $options, 'project_id', 'class="inputbox"', 'value', 'text', "", "");

		if (!TeamTime::helper()->getBpmn() instanceof TeamTime_Helpers_Base) {
			$options = array(JHTML::_(
						'select.option', '- ' . JText::_('Select process') . ' -', '', 'text', 'value'));
			$lists['select_process'] = JHTML::_(
							'select.genericlist', $options, 'process_id', 'size="10" multiple class="inputbox"', 'value',
							'text', "", "");
		}

		// template select
		$options = JHTML::_('select.option', '', '- ' . JText::_('Select Template') . ' -');

		if ($edit) {
			$lists['select_template'] = JHTML::_(
							'teamtimeformals.templateslist', $options, 'doctype_id', 'class="inputbox"', 'value', 'text',
							$item->doctype_id, false, true);

			$doctype = $mFormal->getDoctype($item->doctype_id);
			$usingIn = $doctype->using_in;
			if ($usingIn == "project") {
				$projectName = JHTML::_(
								'teamtimeformals.getdbtitle', '#__teamtime_project', 'name', $item->project_id);
			}
			else if ($usingIn == "user") {
				$projectName = JHTML::_(
								'teamtimeformals.getdbtitle', '#__users', 'name', $item->project_id);
			}
			$templateName = JText::_(JHTML::_(
									'teamtimeformals.getdbtitle', '#__teamtimeformals_template', 'name', $item->doctype_id));
		}
		else {
			$lists['select_template'] = JHTML::_(
							'teamtimeformals.templateslist', $options, 'doctype_id', 'class="inputbox"', 'value', 'text',
							$doctypeId, false, true);
			$projectName = "";
			$templateName = "";
		}

		list($lists['select_date'], $dateSelect, $datePresets, $fromPeriod, $untilPeriod) =
				$helperBase->getDateSelect($fromPeriod, $untilPeriod, array("addJScode" => true));

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('lists', $lists);
		$this->assignRef('controller', $controller);
		$this->assignRef('item', $item);

		$this->assignRef('from_period', $fromPeriod);
		$this->assignRef('until_period', $untilPeriod);
		$this->assignRef('date_presets', $datePresets);

		$this->assignRef('is_edit', $edit);
		$this->assignRef('project_name', $projectName);
		$this->assignRef('template_name', $templateName);

		parent::display($tpl);
	}

}