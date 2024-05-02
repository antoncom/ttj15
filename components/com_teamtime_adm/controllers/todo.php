<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/*
  Class: TodoController
  The controller class for Todo
 */

class TodoController extends JController {
	/*
	  Function: Constructor

	  Parameters:

	  $default -

	  Returns:

	  See Also:

	 */

	function __construct($default = array()) {
		parent::__construct($default);

		$this->registerTask('apply', 'save');
		$this->registerTask('preview', 'display');
		$this->registerTask('edit', 'display');
		$this->registerTask('add', 'display');
	}

	function display() {

		switch ($this->getTask()) {
			case 'add':
				JRequest::setVar('hidemainmenu', 1);
				JRequest::setVar('view', 'todo');
				JRequest::setVar('edit', false);
				JRequest::setVar('report', false);
				break;
			case 'edit':
				JRequest::setVar('hidemainmenu', 1);
				JRequest::setVar('view', 'todo');
				JRequest::setVar('edit', true);
				JRequest::setVar('report', false);
				break;
			case 'report':
				JRequest::setVar('hidemainmenu', 1);
				JRequest::setVar('view', 'todo');
				JRequest::setVar('edit', false);
				JRequest::setVar('report', true);
				break;
		}

		// set the default view
		$view = JRequest::getCmd('view');
		if (empty($view)) {
			JRequest::setVar('view', 'todos');
		}

		parent::display();
	}

	function setState() {
		global $option;

		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		$cid = JRequest::getVar('state_change_id', array(), 'post', 'array');

		$id = (isset($cid[0])) ? (int) $cid[0] : null;
		$state = JRequest::getVar('state' . $id, 0);

		$model = $this->getModel();

		if ($model->storeState($id, $state)) {
			$msg = JText::_('State Changed');
		}
		else {
			$msg = JText::_('Error Changing State');
		}

		$link = 'index.php?option=' . $option . '&controller=' . $this->getName();
		$this->setRedirect($link, $msg);
	}

	function save() {
		global $option;

		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		$mydesk = JRequest::getVar('description', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post = JRequest::get('post');

		//set create date from params box
		if (isset($post["details"]["created"]))
			$post["created"] = $post["details"]["created"];

		$post['description'] = $mydesk;

		if (!isset($post["is_parent"])) {
			$post["is_parent"] = 0;
		}

		// set type_id from task
		$model = $this->getModel("Task", "TaskModel");
		$model->setId($post['task_id']);
		$model->getData();
		$post["type_id"] = $model->_data->type_id;

		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		$post['id'] = (int) $cid[0];

		$model = $this->getModel();

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
				$link = 'index.php?option=' . $option . '&controller=' . $this->getName() .
						'&view=type&task=edit&cid[]=' . $item->id;
				break;
			case 'save':
			default:
				$link = 'index.php?option=' . $option . '&controller=' . $this->getName();
				break;
		}

		$this->setRedirect($link, $msg);

		//send mail if checked
		if (isset($post["sendmail"])) {
			$model->notifyUserByEmail($post);
		}
	}

	function checkPost($post) {

//	print_r($post);
//	exit;


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

	function remove() {
		global $option;

		// Check for request forgeries
		JRequest::checkToken() or die('Invalid Token');

		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('Select an item to delete'));
		}

		$model = $this->getModel();

		if (!$model->delete($cid)) {
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$msg = JText::_('Todo Deleted');
		$link = 'index.php?option=' . $option . '&controller=' . $this->getName();

		$this->setRedirect($link, $msg);
	}

	function check_project_for_user() {
		$params = JRequest::get('get');

		print TeamTime::checkProjectForUser($params);

		exit();
	}

	function enable_project_for_user() {
		$params = JRequest::get('get');

		print TeamTime::enableProjectForUser($params);

		exit();
	}

	function check_repeat_event() {
		$db = & JFactory::getDBO();

		$params = JRequest::get('get');
		$todo_id = (int) $params["todo_id"];

		$todo = new TodoModelTodo();
		$res = $todo->get_repeat_params($todo_id);

		print $res ? "1" : "0";

		exit();
	}

	function remove_all_repeat_todo() {
		$params = JRequest::get('get');

		$todo = new TodoModelTodo();
		$res = $todo->delete(array((int) $params["id"]));

		print $res ? "Succefully" : "1";

		exit();
	}

	function remove_rest_repeat_todo() {
		$params = JRequest::get('get');
		$id = (int) $params["id"];

		$todo = new TodoModelTodo();
		$res = $todo->trunc_repeat_dates($id,
				array(
				"end_date" => date("Y-m-d H:i:s", strtotime($params["current_date"] . " -1 day"
						))));

		print $res ? "Succefully" : "1";

		exit();
	}

	function remove_only_repeat_todo() {
		$params = JRequest::get('get');
		$id = (int) $params["id"];

		$todo = new TodoModelTodo();
		$todo->create_repeat_copy($id,
				array(
				"start_date" => date("Y-m-d H:i:s", strtotime($params["current_date"] . " +1 day"))));

		$res = $todo->trunc_repeat_dates($id,
				array(
				"end_date" => date("Y-m-d H:i:s", strtotime($params["current_date"] . " -1 day"))));

		print $res ? "Succefully" : "1";

		exit();
	}

	function edit_all_repeat_todo() {
		$res = 1;

		print $res ? "Succefully" : "1";

		exit();
	}

	function edit_rest_repeat_todo() {
		$this->remove_rest_repeat_todo();
	}

	function edit_only_repeat_todo() {
		$this->remove_only_repeat_todo();
	}

	function exclude_repeat_todo() {
		$post = JRequest::get('post');
		$id = $post["todo_id"];

		$todo = new TodoModelTodo();
		$todo->exclude_repeated_todo($id,
				TeamTime::php2MySqlTime(
						TeamTime::js2PhpTime($post["old_date"])),
				TeamTime::php2MySqlTime(
						TeamTime::js2PhpTime($post["new_date"]))
		);
	}

	function get_list_todos() {
		$params = JRequest::get('get');
		$project_id = (int) $params["project_id"];
		$current_todo_id = (int) $params["current_id"];
		$parent_todo_id = (int) $params["todo_id"] ? (int) $params["todo_id"] : "-";

		$options = JHTML::_('select.option', '', '- ' . JText::_('INCLUDED TO TEAM TODO') . ' -', 'value',
						'text');
		print JHTML::_('teamlog.todolist', $project_id, $current_todo_id, $options, 'curtodoid',
						'class="inputbox"' . (isset($params["nosize"]) ? '' : ' size=16'), 'value', 'text',
						$parent_todo_id);

		jexit();
	}

}