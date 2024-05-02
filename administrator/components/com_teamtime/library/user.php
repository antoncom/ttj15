<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// include table
require_once(dirname(dirname(__FILE__)) . DS . 'tables' . DS . 'userdata.php');

class YUser extends JUser {

	public $state_description = null;
	public $state_modified = null;

	public function __construct($identifier = 0) {
		parent::__construct($identifier);

		// load user data
		$table = & JTable::getInstance('userdata', 'Table');
		if ($table->load($identifier)) {
			$this->state_description = $table->state_description;
			$this->state_modified = $table->state_modified;
		}
	}

	public function getStateDescription() {
		return $this->state_description;
	}

	public function getStateModified() {
		return $this->state_modified;
	}

	public function getLogs($limit = 0, $projectId = -1) {
		$table = & JTable::getInstance('log', 'Table');

		return $table->getUserLogs($this->id, $limit, $projectId);
	}

	public function getWeekLogs() {
		$table = & JTable::getInstance('log', 'Table');
		return $table->getUserWeekLogs($this->id);
	}

	public function getTodos($params = array()) {
		$table = & JTable::getInstance('teamtimetodo', 'Table');

		return $table->getUserTodos($this->id, null, $params);
	}

	public function getTodo($todoId = 0) {
		$table = & JTable::getInstance('teamtimetodo', 'Table');

		return $table->getUserTodo($this->id, $todoId);
	}

	public function &getInstance($id = 0) {
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}

		// Find the user id
		if (!is_numeric($id)) {
			jimport('joomla.user.helper');
			if (!$id = JUserHelper::getUserId($id)) {
				JError::raiseWarning('SOME_ERROR_CODE', 'User::_load: User ' . $id . ' does not exist');
				return false;
			}
		}

		if (empty($instances[$id])) {
			$user = new YUser($id);
			$instances[$id] = $user;
		}

		return $instances[$id];
	}

	public function save($updateOnly = false) {
		if (!parent::save($updateOnly)) {
			return false;
		}

		$table = & JTable::getInstance('userdata', 'Table');

		// insert if user data dont exists
		if (!$table->load($this->id)) {
			$table->user_id = $this->id;
			$db = & JFactory::getDBO();
			$db->insertObject($table->getTableName(), $table, $table->getKeyName());
		}

		// bind user data
		$table->state_description = $this->state_description;
		$table->state_modified = $this->state_modified;

		// check and store user data
		if (!$table->check() || !$table->store()) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

}