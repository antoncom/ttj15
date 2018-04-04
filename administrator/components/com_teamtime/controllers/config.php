<?php

class TeamtimeControllerConfig extends Core_Joomla_Controller {

	public function __construct($default = array()) {
		parent::__construct($default);

		$this->registerTask('apply', 'save');

		$this->acl = new TeamTime_Acl();
	}

	protected function isAllowed() {
		if ($this->acl == null) {
			return true;
		}

		if (!$this->acl->isSuperAdmin()) {
			JError::raiseWarning(0, JText::_('Access denied'));
			return false;
		}

		return true;
	}

	public function display() {
		if (!$this->isAllowed()) {
			return;
		}

		// set the default view
		$view = JRequest::getCmd('view');
		if (empty($view)) {
			JRequest::setVar('view', 'config');
		}

		parent::display();
	}

	public function cancel() {
		$option = JRequest::getCmd('option');
		$url = JRoute::_('index.php?option=' . $option . '&controller=cpanel', false);

		$this->setRedirect($url);
	}

	public function save() {
		if (!$this->isAllowed()) {
			return;
		}

		$option = JRequest::getCmd('option');
		$controller = JRequest::getCmd('controller');
		$task = $this->getTask();

		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		$params = JRequest::getVar('params');
		TeamTime::helper()->getBase()->saveConfig($params);

		$msg = JText::_('Successfully Saved changes to Teamlog configuration');

		switch ($task) {
			case 'apply':
				$link = 'index.php?option=' . $option . '&controller=' . $controller;
				break;

			case 'save':
			default:
				$link = 'index.php?option=' . $option . '&controller=cpanel';
				break;
		}

		$this->setRedirect($link, $msg);
	}

}