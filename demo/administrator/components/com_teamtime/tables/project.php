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
		parent::__construct('#__teamtime_project', 'id', $db);
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