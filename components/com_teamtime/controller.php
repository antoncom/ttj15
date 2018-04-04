<?php

class TeamlogController extends JController {

	public function display() {
		// set defaults
		JRequest::setVar('layout', 'default');
		JRequest::setVar('view', 'log');

		$mProject = new TeamtimeModelProject();
		$mTodo = new TeamtimeModelTodo();

		switch ($this->getTask()) {
			case 'updatestate':
				$this->updateState();
				break;

			case 'addlog':
				$this->addLog();
				break;

			case 'startlog':
				$this->startLog();
				break;

			case 'stoplog':
				$this->stopLog();
				break;

			case 'removelog':
				$this->removeLog();
				break;

			case 'updatetodos':
				$this->updateTodos();
				break;

			case 'loadtasks':
				JRequest::setVar('layout', 'tasks');
				break;

			case 'loadprojects':
				JRequest::setVar('layout', 'loadprojects');
				break;

			case 'loadtimer':
				JRequest::setVar('layout', 'durationselect');
				break;

			case 'loadtodos':
				JRequest::setVar('layout', 'todos');
				break;

			case 'loadtodo':
				JRequest::setVar('layout', 'todo');
				$mTodo->showTodo();
				jexit();
				break;

			case 'setproject':
				JRequest::setVar('layout', 'projects');
				break;

			case 'loaddescription':
				if ($_REQUEST["project_id"]) {
					$mProject->showProjectDescription();
					jexit();
				}
				else {
					JRequest::setVar('layout', 'description');
				}
				break;

			case 'loadteamlog':
				JRequest::setVar('layout', 'teamtime');
				break;

			case 'inputcomment':
				JRequest::setVar('layout', 'inputcomment');
				break;

			case "set_pause":
				$this->set_pause();
				break;

			case "reset_pause":
				$this->reset_pause();
				break;

			case "check_pause":
				$this->check_pause();
				break;

			case "logs_client":
				$this->logs_client();
				jexit();

			case 'load_project_description':
				$mProject->showProjectDescription();
				jexit();
				break;

			case 'load_task_description':
				$this->showTaskDescription();
				break;

			case 'set_filter_period':
				$this->set_filter_period();
				break;

			case 'set_filter_state':
				$this->set_filter_state();
				break;

			case 'set_filter_date':
				$this->set_filter_date();
				break;

			case 'set_filter_stodo':
				$this->set_filter_stodo();
				break;

			case 'set_filter_sproject':
				$this->set_filter_sproject();
				break;
		}

		parent::display();
	}

	function logs_client() {
		JRequest::setVar('layout', 'default');
		JRequest::setVar('view', 'client');

		parent::display();
	}

	function set_pause() {
		$user = new TeamtimeModelUser();
		$user->setPause();

		jexit();
	}

	function check_pause() {
		$user = new TeamtimeModelUser();
		print $user->checkPause();

		jexit();
	}

	function reset_pause() {
		$user = new TeamtimeModelUser();
		$user->resetPause();

		jexit();
	}

	function updateState() {
		$currentUser = & JFactory::getUser();
		$user = new YUser($currentUser->id);

		$date = & JFactory::getDate();
		$post = JRequest::get('post');
		$msg = 'SUCCESS';

		if ($post['description'] != $user->getStateDescription()) {
			$user->state_description = $post['description'];
			$user->state_modified = $date->toMySQL();
			if (!$user->save()) {
				$msg = 'FAILED';
			}
		}

		jexit($msg);
	}

	function addLog() {
		// check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$currentUser = & JFactory::getUser();
		$user = new YUser($currentUser->id);

		$post = JRequest::get('post');
		$hours = JRequest::getInt('hours');
		$minutes = JRequest::getInt('minutes');
		$msg = '';

		$log = new Log();
		$log->user_id = $user->id;
		$log->duration = ($hours * 60) + $minutes;

		// validate post data
		$validation = true;
		if (!isset($post['description']) || $post['description'] == "") {
			JError::raiseWarning(0, JText::_('Please enter a valid description'));
			$validation = false;
		}
		if (!isset($post['project_id']) || $post['project_id'] == "") {
			JError::raiseWarning(0, JText::_('Please select a project'));
			$validation = false;
		}
		if (!isset($post['task_id']) || $post['task_id'] == "") {
			JError::raiseWarning(0, JText::_('Please select a task'));
			$validation = false;
		}
		if ($log->duration == 0) {
			JError::raiseWarning(0, JText::_('Please set a task duration'));
			$validation = false;
		}

		if ($validation) {
			$post['description'] = $_REQUEST['description'];
			// bind post data
			if (!$log->bind($post)) {
				JError::raiseWarning(0, 'Post data bind failed!');
				return false;
			}

			// save log
			if (!$log->save()) {
				JError::raiseWarning(0, 'Save failed!');
				return false;
			}

			// reset user state description
			$date = JFactory::getDate();
			$user->state_description = '';
			$user->state_modified = $date->toMySQL();
			if (!$user->save()) {
				JError::raiseWarning(0, 'Save user failed!');
				return false;
			}

			$msg = JText::_('Added log entry successfully');
		}

		$link = JRoute::_('index.php?option=com_teamtime&view=log', false);
		$this->setRedirect($link, $msg);
	}

	function startLog() {
		$db = & JFactory::getDBO();

		// check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$currentUser = & JFactory::getUser();
		$user = new YUser($currentUser->id);
		$msg = '';
		$post = JRequest::get('post');
		$mTodo = new TeamtimeModelTodo();

		if (isset($post["todo_id"])) {
			error_log($post["todo_id"]);
			// autoopen started todo if closed
			$todo = $mTodo->getById($post["todo_id"]);
			if ($todo->state == TODO_STATE_CLOSED) {
				$todo->state = TODO_STATE_OPEN;
				$mTodo->store($todo);
			}
		}

		$log = new Log();
		$log->user_id = $user->id;

		// validate post data
		$validation = true;
		if (!isset($post['project_id']) || $post['project_id'] == "") {
			JError::raiseWarning(0, JText::_('Please select a client'));
			$validation = false;
		}

		if (!isset($post['task_id']) || $post['task_id'] == "") {
			JError::raiseWarning(0, JText::_('Please select a task'));
			$validation = false;
		}

		if ($validation) {
			// get type_id
			$task = new Task($post['task_id']);
			$post['type_id'] = $task->type_id;

			$ids = JRequest::getVar('todo');

			$model = $this->getModel('Log');
			$todos = $model->getUserTodos();
			foreach ($todos as $todo) {
				$state = 0;
				if ($todo->state == 1 && $todo->project_id != $post['project_id']
						&& $todo->task_id != $post['task_id']) {
					$this->clearTodos();
				}
			}

			// for repeated todo
			$res = $mTodo->create_copy_forlog($post["todo_id"]);
			// if not null - update current todo id for log
			if ($res != null) {
				$post["todo_id"] = $res;
			}

			// bind post data
			if (!$log->bind($post)) {
				JError::raiseWarning(0, 'Post data bind failed!');
				return false;
			}

			// save log
			if (!$log->save()) {
				JError::raiseWarning(0, 'Save failed!');
				return false;
			}

			// reset user state description
			$date = JFactory::getDate();
			$user->state_description = '';
			$user->state_modified = $date->toMySQL();
			if (!$user->save()) {
				JError::raiseWarning(0, 'Save user failed!');
				return false;
			}

			$msg = JText::_('Added log entry successfully');
		}

		$link = JRoute::_('index.php?option=com_teamtime&view=log&status=started', false);
		$this->setRedirect($link, $msg);
	}

	public function stopLog() {
		// check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$user = & JFactory::getUser();
		$teamtimeConfig = TeamTime::getConfig();
		$mLog = new TeamtimeModelLog();

		$post = JRequest::get('post');
		$msg = '';

		$unclog = $mLog->getUncompletedLog($user->id);
		$logId = $unclog[0]->id;

		$hours = JRequest::getInt('hours');
		$minutes = JRequest::getInt('minutes');
		$money = JRequest::getInt('money');

		if (empty($logId)) {
			JError::raiseWarning(0, 'Log id empty!');
			return false;
		}

		$log = new Log($logId);
		$date = & JFactory::getDate();
		$log->ended = $date->toMySQL();
		$log->user_id = $user->id;
		$log->duration = ($hours * 60) + $minutes;
		$log->money = $money;

		// validate post data
		$validation = true;
		if (!isset($post['description']) || $post['description'] == "") {
			JError::raiseWarning(0, JText::_('Please enter a valid description'));
			$validation = false;
		}

		// validation for autotodos
		if ($teamtimeConfig->use_autotodos && !$log->todo_id) {
			$post['autotodo_text'] = isset($_POST['autotodo_text']) ? $_POST['autotodo_text'] : "";

			if (!isset($post['autotodo_text'])
					|| mb_strlen(strip_tags($post['autotodo_text'])) < 10) {
				JError::raiseWarning(0, JText::_('Please enter a valid text for autotodo'));
				$validation = false;
			}
		}

		if ($validation) {
			$post['description'] = $_REQUEST['description'];

			// bind post data
			if (!$log->bind($post)) {
				JError::raiseWarning(0, 'Post data bind failed!');
				return false;
			}

			// save log
			if (!$log->save()) {
				JError::raiseWarning(0, 'Save failed!');
				return false;
			}

			// autocreating todo should before other processings for log
			if ($teamtimeConfig->use_autotodos && !$log->todo_id) {
				$mTodo = new TeamtimeModelTodo();
				$autoTodo = $mTodo->createAutoTodoForLog($log, $post);

				// update todo_id for log by created auto todo
				if ($autoTodo) {
					$log->todo_id = $autoTodo->id;
					$log->save();
				}
			}

			TeamTime::helper()->getDotu()->onSaveLog($log, $post);

			// reset user state description
			$date = JFactory::getDate();
			/*
			  $user->state_description = '';
			  $user->state_modified = $date->toMySQL();
			  if (!$user->save()) {
			  JError::raiseWarning(0, 'Save user failed!');
			  return false;
			  } */

			$this->clearTodos();
			$msg = JText::_('Stop log entry successfully');

			$todo = new Todo($log->todo_id);
			// set time sum for current todo
			$todo->hours_fact = $todo->getLogsSumm();

			// set fact time as plan for autotodo
			if ($todo->is_autotodo) {
				$todo->hours_plan = $todo->hours_fact;
			}

			if (isset($post['close_todo']) && $post['close_todo']) {
				// set state closed
				$todo->state = 2;
				// reset autotodo flag
				$todo->is_autotodo = 0;
			}
			$todo->save();

			TeamTime::helper()->getBpmn()->sendReport($todo, $post);
		}

		$link = JRoute::_('index.php?option=com_teamtime&view=log&status=stopped', false);
		$this->setRedirect($link, $msg);
	}

	function removeLog() {
		$log_id = JRequest::getInt('log_id');

		if (empty($log_id)) {
			JError::raiseWarning(0, 'Log id empty!');
			return false;
		}

		$log = new Log($log_id);

		if (!$log->delete()) {
			JError::raiseWarning(0, $log->getError());
			$link = JRoute::_('index.php?option=com_teamtime&view=log', false);
			$this->setRedirect($link);
		}
		else {
			$msg = JText::_('Log entry deleted successfully');
			$link = JRoute::_('index.php?option=com_teamtime&view=log', false);

			//update todo summ
			$todo = new Todo($log->todo_id);
			$todo->hours_fact = $todo->getLogsSumm();
			$todo->save();

			$this->setRedirect($link, $msg);
		}
	}

	function clearTodos() {
		// check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$user = & JFactory::getUser();
		$ids = JRequest::getVar('todo');

		$todo_model = new TeamtimeModelTodo();
		$todo_model->clear_selected_todos($user->id);
	}

	function updateTodos() {
		// check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$user = & JFactory::getUser();
		$ids = JRequest::getVar('todo');

		$todo_model = new TeamtimeModelTodo();
		$todo_model->set_selected_todos($user->id, $ids);
		jexit('SUCCESS');
	}

	function showTaskDescription() {
		//index.php?option=com_teamtime&controller=&view=log&format=raw&task=load_task_description&task_id=?
		$db = & JFactory::getDBO();

		$id = (int) JRequest::getVar('todo_id');
		if ($id > 0) {
			$db->setQuery("select * from #__teamtime_todo where id = $id");
			$result = $db->loadObject();
			$id = $result->task_id;
		}
		else
			$id = (int) JRequest::getVar('task_id');

		$db = & JFactory::getDBO();
		$db->setQuery("select * from #__teamtime_task where id = $id");
		$result = $db->loadObject();

		if (trim($result->description) != "") {
			print "<h3>" . $result->name . "</h3>";
			//print TeamTime::helper()->getBase()->convertTextLinks($result->description);
			print $result->description;
		}

		jexit();
	}

	function set_filter_period() {
		$mainframe = & JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$filter_period = $mainframe->getUserStateFromRequest(
				$option . '.filter_period', 'filter_period', '', 'string');
		jexit();
	}

	function set_filter_state() {
		$mainframe = & JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$filter_state = $mainframe->getUserStateFromRequest(
				$option . '.filter_state', 'filter_state', TODO_STATE_OPEN, 'string');
		jexit();
	}

	function set_filter_date() {
		$mainframe = & JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$filter_date = $mainframe->getUserStateFromRequest(
				$option . '.filter_date', 'filter_date', '');
		jexit();
	}

	function set_filter_stodo() {
		$mainframe = & JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$filter_stodo = $mainframe->getUserStateFromRequest(
				$option . '.filter_stodo', 'filter_stodo', '');
		jexit();
	}

	function set_filter_sproject() {
		$mainframe = & JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$filter_sproject = $mainframe->getUserStateFromRequest(
				$option . '.filter_sproject', 'filter_sproject', '');
		jexit();
	}

}