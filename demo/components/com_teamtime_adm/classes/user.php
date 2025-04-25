<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// include table
require_once(dirname(dirname(__FILE__)) . DS . 'tables' . DS . 'userdata.php');

/*
  Class: YUser
  Extends component specific user attributes and functions.
 */

class YUser extends JUser {
	/*
	  Variable: state description
	  Current user state description.
	 */

	var $state_description = null;

	/*
	  Variable: state modified
	  Last time the user state description was modified.
	 */
	var $state_modified = null;

	/*
	  Function: Construtor
	  User object constructor.
	 */

	function __construct($identifier = 0) {
		parent::__construct($identifier);

		// load user data
		$table = & JTable::getInstance('userdata', 'Table');
		if ($table->load($identifier)) {
			$this->state_description = $table->state_description;
			$this->state_modified = $table->state_modified;
		}
	}

	/*
	  Function: getStateDescription
	  Returns the users current state description.
	 */

	function getStateDescription() {
		return $this->state_description;
	}

	/*
	  Function: getStateModified
	  Returns the state modification date.
	 */

	function getStateModified() {
		return $this->state_modified;
	}

	/*
	  Function: getLogs
	  Returns a user log entries.
	 */

	function getLogs($limit = 0, $project_id = -1) {
		$table = & JTable::getInstance('log', 'Table');
		return $table->getUserLogs($this->id, $limit, $project_id);
	}

	function getUncompletedLog($limit = 0) {
		$table = & JTable::getInstance('log', 'Table');
		return $table->getUncompletedLog($this->id, $limit);
	}

	/*
	  Function: getWeekLogs
	  Returns a user log entries from last week.
	 */

	function getWeekLogs() {
		$table = & JTable::getInstance('log', 'Table');
		return $table->getUserWeekLogs($this->id);
	}

	/*
	  Function: getTodos
	  Returns a user todo entries.
	 */

	function getTodos($params = array()) {
		$table = & JTable::getInstance('todo', 'Table');
		return $table->getUserTodos($this->id, null, $params);
	}

	/*
	  Function: getTodo
	  Returns a user todo item.
	 */

	function getTodo($todo_id = 0) {
		$table = & JTable::getInstance('todo', 'Table');
		return $table->getUserTodo($this->id, $todo_id);
	}

	/*
	  Function: getInstance
	  Override. Returns a user object instance.
	 */

	function &getInstance($id = 0) {
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

	/*
	  Function: save
	  Override. Method to save the User object to the database.
	 */

	function save($updateOnly = false) {

		// save JUser
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

	function set_pause() {
		$user = & YFactory::getUser();
		$unclog = $user->getUncompletedLog();

		$date = & JFactory::getDate();
		$sdate = $date->toMySQL();

		$db = & JFactory::getDBO();
		$query = "update #__teamlog_log set datepause=" . $db->Quote($sdate) .
			" where id = " . $unclog[0]->id;
		$db->Execute($query);
	}

	function check_pause() {
		$user = & YFactory::getUser();
		$unclog = $user->getUncompletedLog();

		if ($unclog[0]->id) {
			$db = & JFactory::getDBO();
			$query = "select datepause > 0 from #__teamlog_log
				where id = " . $unclog[0]->id;
			$res = $db->GetCol($query);

			return $res[0];
		}

		return false;
	}

	function reset_pause() {
		$user = & YFactory::getUser();
		$unclog = $user->getUncompletedLog();

		$db = & JFactory::getDBO();

		$date = & JFactory::getDate();
		$NOW = $db->Quote($date->toMySQL());

		$query = "update #__teamlog_log
			set
				sumpause = sumpause +
					unix_timestamp(str_to_date($NOW, '%Y-%m-%d %H:%i:%s')) - unix_timestamp(datepause),
				created = addtime(created,
					timediff(str_to_date($NOW, '%Y-%m-%d %H:%i:%s'), datepause)),
				date = created,
				modified = created,
				datepause = 0
			where id = " . $unclog[0]->id;
		$db->Execute($query);
	}

}