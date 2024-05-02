<?php

class TeamtimeControllerApi extends Core_Joomla_Controller {

	public function __construct($default = array()) {
		parent::__construct($default);

		//$this->acl = new TeamTime_Acl();
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

}