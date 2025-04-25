<?php

class TeamtimeControllerTodo extends Core_Joomla_EditController {

	public function __construct($default = array()) {
		parent::__construct($default);

		$this->viewEdit = 'todo';
		$this->viewList = 'todos';
		$this->acl = new TeamTime_Acl();
	}

	public function display() {
		if ($this->getTask() == 'report') {
			JRequest::setVar('hidemainmenu', 1);
			JRequest::setVar('view', $this->viewEdit);
			JRequest::setVar('edit', false);
			JRequest::setVar('report', true);
		}

		parent::display();
	}

	protected function checkPost($post) {
		if (!isset($post['user_id']) || $post['user_id'] == "") {
			JError::raiseWarning(0, JText::_('Error Saving: Please select a user'));
			return false;
		}
		if (!isset($post['description']) || $post['description'] == "") {
			JError::raiseWarning(0, JText::_('Error Saving: Please enter a valid description'));
			return false;
		}
		return true;
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

	public function save() {
		if (!$this->isAllowed()) {
			return;
		}

		$option = JRequest::getCmd('option');
		$controller = JRequest::getCmd('controller');
		$view = JRequest::getVar('view');

		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		$mydesk = JRequest::getVar('description', '', 'post', 'string', JREQUEST_ALLOWHTML);
		$post = JRequest::get('post');

		// set create date from params box
		// TODO !!!move to model
		if (isset($post["details"]["created"])) {
			$post["created"] = $post["details"]["created"];
		}

		$post['description'] = $mydesk;

		// TODO !!!move to model
		if (!isset($post["is_parent"])) {
			$post["is_parent"] = 0;
		}

		// set type_id from task
		// TODO !!!move to model
		$model = new TeamtimeModelTask();
		$task = $model->getById($post['task_id']);
		$post["type_id"] = $task->type_id;

		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		$post['id'] = (int) $cid[0];

		$model = $this->getModel($this->viewEdit);
		$msg = "";
		if ($this->checkPost($post)) {
			if ($model->store($post)) {
				$msg = JText::_('Todo Saved');
			}
			else {
				$msg = JText::_('Error Saving Todo');
			}
		}

		switch ($this->_task) {
			case 'apply':
				$item = & $model->getData();
				$link = 'index.php?option=' . $option . '&controller=' . $controller .
						'&view=' . $view . '&task=edit&cid[]=' . $item->id;
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

	public function check_project_for_user() {
		$params = JRequest::get('get');
		print TeamTime::helper()->getBase()->checkProjectForUser($params);

		jexit();
	}

	public function enable_project_for_user() {
		$params = JRequest::get('get');
		print TeamTime::helper()->getBase()->enableProjectForUser($params);

		jexit();
	}

	public function check_repeat_event() {
		$db = & JFactory::getDBO();
		$params = JRequest::get('get');
		$todo_id = (int) $params["todo_id"];
		$todo = new TeamtimeModelTodo();
		$res = $todo->get_repeat_params($todo_id);

		print $res ? "1" : "0";

		jexit();
	}

	public function remove_all_repeat_todo() {
		$params = JRequest::get('get');
		$todo = new TeamtimeModelTodo();
		$res = $todo->delete(array((int) $params["id"]));
		print $res ? "Succefully" : "1";

		jexit();
	}

	public function remove_rest_repeat_todo() {
		$params = JRequest::get('get');
		$id = (int) $params["id"];
		$todo = new TeamtimeModelTodo();
		$res = $todo->trunc_repeat_dates($id,
				array("end_date" => date("Y-m-d H:i:s", strtotime($params["current_date"] . " -1 day"))));
		print $res ? "Succefully" : "1";

		jexit();
	}

	public function remove_only_repeat_todo() {
		$params = JRequest::get('get');
		$id = (int) $params["id"];
		$todo = new TeamtimeModelTodo();
		$todo->create_repeat_copy($id,
				array("start_date" => date("Y-m-d H:i:s", strtotime($params["current_date"] . " +1 day"))));
		$res = $todo->trunc_repeat_dates($id,
				array("end_date" => date("Y-m-d H:i:s", strtotime($params["current_date"] . " -1 day"))));
		print $res ? "Succefully" : "1";

		jexit();
	}

	public function edit_all_repeat_todo() {
		$res = 1;
		print $res ? "Succefully" : "1";

		jexit();
	}

	public function edit_rest_repeat_todo() {
		$this->remove_rest_repeat_todo();
	}

	public function edit_only_repeat_todo() {
		$this->remove_only_repeat_todo();
	}

	public function exclude_repeat_todo() {
		$post = JRequest::get('post');
		$id = $post["todo_id"];
		$todo = new TeamtimeModelTodo();
		$todo->exclude_repeated_todo($id,
				TeamTime_DateTools::php2MySqlTime(
						TeamTime_DateTools::js2PhpTime($post["old_date"])),
				TeamTime_DateTools::php2MySqlTime(
						TeamTime_DateTools::js2PhpTime($post["new_date"]))
		);
	}

	public function get_list_todos() {
		$params = JRequest::get('get');
		$projectId = (int) $params["project_id"];
		$currentTodoId = (int) $params["current_id"];
		$parentTodoId = (int) $params["todo_id"] ? (int) $params["todo_id"] : "-";
		$options = JHTML::_('select.option', '', '- ' . JText::_('INCLUDED TO TEAM TODO') . ' -', 'value',
						'text');
		print JHTML::_('teamtime.todolist', $projectId, $currentTodoId, $options, 'curtodoid',
						'class="inputbox"' . (isset($params["nosize"]) ? '' : ' size=16'), 'value', 'text',
						$parentTodoId);

		jexit();
	}

}