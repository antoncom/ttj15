<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TeamtimecareerControllerStatevector extends Core_Joomla_EditController {

	public function __construct($default = array()) {
		parent::__construct($default);

		$this->viewEdit = 'statevector';
		$this->viewList = 'statevectors';
		//$this->acl = new TeamTime_Acl();
	}

	public function checkPost($post) {
		return true;
	}

	public function save() {
		global $option;

		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		$post = JRequest::get('post');
		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		$post['id'] = (int) $cid[0];
		$post['num'] = (int) $post['indication'] * (int) $post['num'];
		// for saving html
		$post["description"] = JRequest::getVar('description', '', 'post', 'string', JREQUEST_ALLOWHTML);

		$model = $this->getModel();

		$msg = "";
		if ($this->checkPost($post)) {
			if ($model->store($post)) {
				$msg = JText::_('Item saved');
			}
			else {
				$msg = JText::_('Error saving item');
			}
		}

		switch ($this->_task) {
			case 'apply':
				$item = & $model->getData();
				$link = 'index.php?option=' . $option . '&controller=' . $this->getName() .
						'&view=type&task=edit&cid[]=' . $item->id;
				break;
			case 'save':
			default:
				$link = 'index.php?option=' . $option . '&controller=' . $this->getName();
				break;
		}

		$this->setRedirect($link, $msg);
	}

}