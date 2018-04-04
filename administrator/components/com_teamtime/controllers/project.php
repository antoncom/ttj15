<?php

class TeamtimeControllerProject extends Core_Joomla_EditController {

	public function __construct($default = array()) {
		parent::__construct($default);

		$this->registerTask('cancel', 'cancel');

		$this->viewEdit = 'project';
		$this->viewList = 'projects';
		$this->acl = new TeamTime_Acl();
	}

	public function display() {
		$mainframe = & JFactory::getApplication();

		$isSetBackurl = JRequest::getVar('backurl', '', 'get');
		if ($isSetBackurl) {
			$mainframe->setUserState('com_teamtimeformals_backurl', 1);
		}
		else {
			$mainframe->setUserState('com_teamtimeformals_backurl', 0);
		}

		if ($this->getTask() == 'report') {
			JRequest::setVar('hidemainmenu', 1);
			JRequest::setVar('view', $this->viewEdit);
			JRequest::setVar('edit', false);
			JRequest::setVar('report', true);
		}

		parent::display();
	}

	public function setState() {
		if (!$this->isAllowed()) {
			return;
		}

		$option = JRequest::getCmd('option');
		$controller = JRequest::getCmd('controller');

		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		$cid = JRequest::getVar('state_change_id', array(), 'post', 'array');

		$id = (isset($cid[0])) ? (int) $cid[0] : null;
		$state = JRequest::getVar('state' . $id, 0);

		$model = $this->getModel($this->viewEdit);
		if ($model->storeState($id, $state)) {
			$msg = JText::_('State Changed');
		}
		else {
			$msg = JText::_('Error Changing State');
		}

		$link = 'index.php?option=' . $option . '&controller=' . $controller;
		$this->setRedirect($link, $msg);
	}

	protected function checkPost($post) {
		if (!isset($post['name']) || $post['name'] == "") {
			JError::raiseWarning(0, JText::_('Error Saving: Please enter a valid name'));
			return false;
		}
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