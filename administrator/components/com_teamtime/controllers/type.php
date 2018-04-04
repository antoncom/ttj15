<?php

class TeamtimeControllerType extends Core_Joomla_EditController {

	private $typeUsings = array();

	public function __construct($default = array()) {
		parent::__construct($default);

		$this->viewEdit = 'type';
		$this->viewList = 'types';
		$this->acl = new TeamTime_Acl();
	}

	protected function checkPost($post) {
		if (!isset($post['name']) || $post['name'] == "") {
			JError::raiseWarning(0, JText::_('Error Saving: Please enter a valid name'));
			return false;
		}
		return true;
	}

	private function checkTypeUsings() {
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);
		$this->typeUsings = TeamTime::helper()->getBase()->getTypeUsings($cid);
	}

	public function setRedirect($link, $msg) {
		if (sizeof($this->typeUsings) > 0) {
			$msg .= "<p>" . JText::_('Type Usings') . ":<p>" . implode("<p>", $this->typeUsings);
		}

		parent::setRedirect($link, $msg);
	}

	public function remove() {
		$this->checkTypeUsings();
		parent::remove();
	}

}