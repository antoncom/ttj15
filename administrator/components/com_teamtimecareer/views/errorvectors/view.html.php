<?php

class TeamtimecareerViewErrorvectors extends JView {

	public function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');
		
		$helperBase = TeamTime::helper()->getBase();

		$controller = JRequest::getWord('controller');

		// set toolbar items
		JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . JText::_('Vector of errors'), TEAMLOG_ICON);

		JHTML::_('behavior.tooltip');

		$errorvectorm = new TeamtimecareerModelErrorvectors();

		// get request vars
		$name = $this->get('name');
		$filterUserId = $mainframe->getUserStateFromRequest(
				$option . '.filter_user_id', 'filter_user_id', '', 'int');

		$lists['select_user'] = $helperBase->getUserSelect($filterUserId,
				array("fieldId" => 'filter_user_id',
			"firstOptions" => JHTML::_('select.option', '', '- ' . JText::_('All users') . ' -')));

		$showtargets = JRequest::getVar('showtargets');
		$options = array();
		$options[] = JHTML::_('select.option', '', " - " . JText::_('User targets'), 'text', 'value');
		$options[] = JHTML::_('select.option', '1', " - " . JText::_('All targets'), 'text', 'value');
		$lists["select_showtargets"] = JHTML::_(
						'select.genericlist', $options, 'showtargets', 'class="inputbox auto-submit" size="1"',
						'text', 'value', $showtargets);
		$showtargets = $showtargets == "1" ? false : true;

		JRequest::setVar('filter_targets', $showtargets);

		// get data from the model
		$errorvectorm->setState("filter_targets", JRequest::getVar('filter_targets'));
		$items = $errorvectorm->getData();

		$helperDotu = TeamTime::helper()->getDotu();

		$html = file_get_contents(JPATH_ADMINISTRATOR
				. "/components/com_teamtimecareer/assets/templates/usererrorvector.html");
		foreach ($items as $userData) {
			$errorvectorContent .= $helperDotu->renderErrorVectorContent(
					$userData, $html, true, $showtargets);
		}

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('errorvector_content', $errorvectorContent);

		parent::display($tpl);
	}

}