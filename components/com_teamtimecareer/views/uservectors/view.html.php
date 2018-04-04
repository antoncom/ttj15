<?php

class UservectorViewUservectors extends JView {

	function display($tpl = null) {
		$mainframe = & JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$controller = JRequest::getWord('controller');
		$user = & JFactory::getUser();

		// get request vars
		$name = $this->get('name');

		if (JRequest::getVar('submit2')) {
			$showtargets = JRequest::getVar('showtargets2');
		}
		else {
			$showtargets = JRequest::getVar('showtargets');
		}

		$options = array();
		$options[] = JHTML::_('select.option', '', JText::_('Show only my targets'), 'text', 'value');
		$options[] = JHTML::_('select.option', '1', JText::_('Show all targets'), 'text', 'value');

		$lists["select_showtargets"] = JHTML::_(
						'select.genericlist', $options, 'showtargets', 'class="inputbox" size="1"', 'text', 'value',
						$showtargets);
		$lists["select_showtargets2"] = JHTML::_(
						'select.genericlist', $options, 'showtargets2', 'class="inputbox" size="1"', 'text', 'value',
						$showtargets);

		$html = file_get_contents(JPATH_ADMINISTRATOR
				. "/components/com_teamtimecareer/assets/templates/usererrorvector.html");
		$showtargets = $showtargets == "1" ? false : true;

		JRequest::setVar('filter_targets', $showtargets);
		// get data from the model
		$errorm = new TeamtimecareerModelErrorvectors();
		$user_data = $errorm->getUserData($user->id);

		$helperDotu = TeamTime::helper()->getDotu();

		$errorvector_content = $helperDotu->renderErrorVectorContent(
				$user_data, $html, false, $showtargets);

		// set template vars
		$this->assignRef('user', $user);
		$this->assignRef('option', $option);
		$this->assignRef('controller', $controller);
		$this->assignRef('lists', $lists);
		//$this->assignRef('items', $items);
		//$this->assignRef('pagination', $pagination);
		$this->assignRef('errorvector_content', $errorvector_content);

		parent::display($tpl);
	}

}