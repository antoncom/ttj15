<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TeamtimecareerControllerTargetvector extends Core_Joomla_EditController {

	public function __construct($default = array()) {
		parent::__construct($default);

		$this->viewEdit = 'targetvector';
		$this->viewList = 'targetvectors';
		//$this->acl = new TeamTime_Acl();
	}

	public function checkPost($post) {
		if (!isset($post['title']) || $post['title'] == "") {
			JError::raiseWarning(0, JText::_('Error Saving: Please enter a valid title'));
			return false;
		}

		return true;
	}

	//
	// ordering methods
	//
	
	public function orderup() {
		$option = JRequest::getCmd('option');
		$controller = JRequest::getCmd('controller');

		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		$link = 'index.php?option=' . $option . '&controller=' . $controller;

		if (isset($cid[0]) && $cid[0]) {
			$id = $cid[0];
		}
		else {
			$this->setRedirect($link, JText::_('No Items Selected'));
			return false;
		}

		$model = & $this->getModel($this->viewEdit);
		if ($model->orderItem($id, -1)) {
			$msg = JText::_('Item Moved Up');
		}
		else {
			$msg = $model->getError();
		}
		$this->setRedirect($link, $msg);
	}

	public function orderdown() {
		$option = JRequest::getCmd('option');
		$controller = JRequest::getCmd('controller');

		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		$link = 'index.php?option=' . $option . '&controller=' . $controller;

		if (isset($cid[0]) && $cid[0]) {
			$id = $cid[0];
		}
		else {
			$this->setRedirect($link, JText::_('No Items Selected'));
			return false;
		}

		$model = & $this->getModel($this->viewEdit);
		if ($model->orderItem($id, 1)) {
			$msg = JText::_('Item Moved Down');
		}
		else {
			$msg = $model->getError();
		}
		$this->setRedirect($link, $msg);
	}

	public function saveorder() {
		$option = JRequest::getCmd('option');
		$controller = JRequest::getCmd('controller');

		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		$link = 'index.php?option=' . $option . '&controller=' . $controller;

		$model = & $this->getModel($this->viewEdit);
		if ($model->setOrder($cid)) {
			$msg = JText::_('New ordering saved');
		}
		else {
			$msg = $model->getError();
		}
		$this->setRedirect($link, $msg);
	}

	//
	// ajax actions
	//

	public function selector_by_task() {
		$params = JRequest::get('get');
		print TeamTime::helper()->getDotu()
						->targetVectorSelector((int) $params["task_id"], false, 'Select vector of goals',
								(int) $params["user_id"], (int) $params["showskills"]);

		jexit();
	}

}