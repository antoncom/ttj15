<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
  Class: TableProject
  The Table Class for Project. Manages the database operations.
 */

class TableProject extends JTable {

	/** @var int primary key */
	var $id = null;
	/** @var int */
	var $name = null;
	/** @var string */
	var $description = null;
	/** @var int */
	var $state = null;
	/** @var int */
	var $rate = null;
	
	var $dynamic_rate = null;

	/**
	 * @param database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct('#__teamlog_project', 'id', $db);
	}

	function loadObjects($result) {
		$objects = array();
		foreach ($result as $row) {
			$object = & new Project();
			$object->bind($row);
			$objects[] = $object;
		}

		return $objects;
	}

	function getActiveProjects($from_frontend = false) {
		$user = & YFactory::getUser();
		$current_user = $user->id;
		$user = & JFactory::getUser();
		if (!$from_frontend)
		/* $user->usertype == "Super Administrator" || $user->usertype == "Administrator" */
			$filter_projects = "";
		else
			$filter_projects = " and (" .
				// projects - enabled for all
				" id not in (select project_id from #__teamlog_project_user group by project_id) or " .
				// projects - enabled for current user
				"	id in (SELECT project_id FROM #__teamlog_project_user
					WHERE user_id = {$current_user} group by project_id) ) ";

		$query = " SELECT * "
			. " FROM " . $this->_tbl
			. " WHERE state=" . PROJECT_STATE_OPEN
			. $filter_projects
			. " ORDER BY name";
		$this->_db->setQuery($query);

		//print "<!-- test: "; var_dump($query); print " -->";

		$result = $this->_db->loadAssocList();
		return $this->loadObjects($result);
	}

	function setProjectState($id, $state) {
		$query = " UPDATE " . $this->_tbl
			. " SET state=" . $state
			. " WHERE id=" . $id;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	function setProjectRate($id, $rate) {
		$query = " UPDATE " . $this->_tbl
			. " SET rate=" . $rate
			. " WHERE id=" . $id;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

}