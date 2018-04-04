<?php

class TeamtimeControllerUser extends Core_Joomla_EditController {

	public function __construct($default = array()) {
		parent::__construct($default);

		$this->registerTask('cancel', 'cancel');

		$this->viewEdit = 'user';
		$this->viewList = 'users';
		$this->acl = new TeamTime_Acl();
	}

	public function display() {
		$mainframe =& JFactory::getApplication();
		
		$isSetBackurl = JRequest::getVar('backurl', '', 'get');
		if ($isSetBackurl) {
			$mainframe->setUserState('com_teamtimeformals_backurl', 1);
		}
		else {
			$mainframe->setUserState('com_teamtimeformals_backurl', 0);
		}

		parent::display();
	}

	protected function checkPost($post) {
		return true;
	}

	private function getRedirectUrl($defaultUrl) {
		$mainframe =& JFactory::getApplication();
		
		$isSetBackurl = $mainframe->getUserState('com_teamtimeformals_backurl');
		if ($isSetBackurl) {
			$url = TeamTime::helper()->getFormals()->getVariablesUrl();
		}
		else {
			$url = $defaultUrl;
		}

		$mainframe->setUserState('com_teamtimeformals_backurl', 0);

		return $url;
	}

	public function setRedirect($link, $msg = "") {
		if (in_array($this->getTask(), array("cancel", "apply", "save"))) {
			$link = $this->getRedirectUrl($link);
		}

		parent::setRedirect($link, $msg);
	}

	public function cancel() {
		$option = JRequest::getCmd('option');
		$controller = JRequest::getCmd('controller');

		$link = $this->getRedirectUrl("");
		if ($link == "") {
			$link = 'index.php?option=' . $option . '&controller=' . $controller;
		}

		$this->setRedirect($link);
	}

}