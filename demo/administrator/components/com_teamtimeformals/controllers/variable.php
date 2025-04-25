<?php

class VariableController extends Core_Joomla_EditController {

	public function __construct($default = array()) {
		parent::__construct($default);

		$this->viewEdit = 'variable';
		$this->viewList = 'variables';
	}

	public function display() {
		if ($this->getTask() == 'report') {
			JRequest::setVar('hidemainmenu', 1);
			JRequest::setVar('view', $this->viewEdit);
			JRequest::setVar('edit', false);
			JRequest::setVar('report', true);
		}

		$cid = JRequest::getVar('cid', array(0), 'get', 'array');
		JRequest::setVar('projects', $this->getProjectIds($cid[0]));
		JRequest::setVar('users', $this->getUserIds($cid[0]));

		parent::display();
	}

	private function saveProjectIds($variableId, $projectIds) {
		$db = & JFactory::getDBO();

		$db->Execute("delete from #__teamtimeformals_variable_project
			where variable_id = " . (int) $variableId);

		if (sizeof($projectIds) > 0 && $projectIds[0] !== "") {
			foreach ($projectIds as $id) {
				$db->Execute("insert into #__teamtimeformals_variable_project
					values(" . (int) $id . ", " . (int) $variableId . ")");
			}
		}
	}

	private function saveUserIds($variableId, $userIds) {
		$db = & JFactory::getDBO();

		$db->Execute("delete from #__teamtimeformals_variable_user
			where variable_id = " . (int) $variableId);

		if (sizeof($userIds) > 0 && $userIds[0] !== "") {
			foreach ($userIds as $id) {
				$db->Execute("insert into #__teamtimeformals_variable_user
					values(" . (int) $id . ", " . (int) $variableId . ")");
			}
		}
	}

	private function getProjectIds($variableId) {
		$db = & JFactory::getDBO();

		$db->setQuery("select * from #__teamtimeformals_variable_project
			where variable_id = " . (int) $variableId);
		$res = array();
		foreach ($db->loadObjectList() as $row) {
			$res[] = $row->project_id;
		}
		return $res;
	}

	private function getUserIds($variableId) {
		$db = & JFactory::getDBO();

		$db->setQuery("select * from #__teamtimeformals_variable_user
			where variable_id = " . (int) $variableId);
		$res = array();
		foreach ($db->loadObjectList() as $row) {
			$res[] = $row->user_id;
		}
		return $res;
	}

	public function save() {
		$option = JRequest::getCmd('option');
		$controller = JRequest::getCmd('controller');

		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		$post = JRequest::get('post');
		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		$post['id'] = (int) $cid[0];
		$post['description'] = JRequest::getVar('description', '', 'post', 'string', JREQUEST_ALLOWHTML);
		$projectIds = JRequest::getVar('projects', array(0), 'post', 'array');
		$userIds = JRequest::getVar('users', array(0), 'post', 'array');

		$model = $this->getModel($this->viewEdit);

		$msg = "";
		if ($this->checkPost($post)) {
			if ($model->store($post)) {
				$this->saveProjectIds($model->_data->id, $projectIds);
				$this->saveUserIds($model->_data->id, $userIds);

				$msg = JText::_('Variable Saved');
			}
			else {
				$msg = JText::_('Error Saving Variable');
			}
		}

		switch ($this->_task) {
			case 'apply':
				$item = & $model->getData();
				$link = 'index.php?option=' . $option . '&controller=' . $controller .
						'&view=type&task=edit&cid[]=' . $item->id;
				break;
			case 'save':
			default:
				$link = 'index.php?option=' . $option . '&controller=' . $controller;
				break;
		}

		$this->setRedirect($link, $msg);
	}

	//
	// ajax actions
	//

	public function loadUsings() {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');
		$name = "variables";

		$filterProject = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_project', 'filter_project', '', 'cmd');

		$filterUsing = $mainframe->getUserStateFromRequest(
				$option . $name . '.filter_using', 'filter_using', '', 'cmd');

		print JHTML::_(
						'teamtimeformals.variables_filter', $options, 'filter_project',
						'class="inputbox auto-submit"', 'value', 'text', $filterProject, $filterUsing, true);

		jexit();
	}

}