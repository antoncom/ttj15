<?php

class TeamtimeControllerLog extends Core_Joomla_EditController {

	public function __construct($default = array()) {
		parent::__construct($default);

		$this->viewEdit = 'log';
		$this->viewList = 'logs';
		$this->acl = new TeamTime_Acl();
	}

	protected function checkPost($post) {
		if (!isset($post['description']) || $post['description'] == "") {
			JError::raiseWarning(0, JText::_('Error Saving: Please enter a valid description'));
			return false;
		}

		if (!isset($post['project_id']) || $post['project_id'] == "") {
			JError::raiseWarning(0, JText::_('Error Saving: Please select a project'));
			return false;
		}

		if (!isset($post['task_id']) || $post['task_id'] == "" || $post['task_id'] < 0) {
			JError::raiseWarning(0, JText::_('Error Saving: Please select a task'));
			return false;
		}
		return true;
	}

	//
	// ajax actions
	//

	public function loadtasks() {
		$helperBase = TeamTime::helper()->getBase();
		$projectId = JRequest::getVar('project_id');

		print $helperBase->getTypesTasksSelect($projectId);

		jexit();
	}

}