<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
   Class: TableTask
   The Table Class for Task. Manages the database operations.
*/
class TableTask extends JTable {

	/** @var int primary key */
	var $id					= null;
	/** @var int project id */
	var $project_id			= null;
	/** @var int */
	var $name				= null;
	/** @var string */
	var $description		= null;
	/** @var int */
	var $state				= null;
	/** @var int type id */
	var $type_id			= null;
	/** @var int */
	var $rate				= null;

	/**
	 * @param database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct('#__teamtime_task', 'id', $db);
	}

	function setTaskState($id, $state) {
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

	function setTaskRate($id) {
		$query = " UPDATE " . $this->_tbl . " t0 "
		. " SET rate=("
		. " select if (t1.rate=0,p1.rate,t1.rate) as rate "
		. " from #__teamtime_type AS t1, #__teamtime_project AS p1 "
		. " where t1.id = t0.type_id and p1.id = t0.project_id "
		. ") WHERE t0.rate=0 ";
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
		$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}	
	
	function loadObjects($result) {
		$objects = array();
		if ($result != "") {
			foreach ($result as $row) {
				$object =& new Task();
				$object->bind($row);
				$objects[] = $object;
			}
		}

		return $objects;
	}

	function getTasks($project_id) {
		$query = " SELECT * "
			. " FROM ".$this->_tbl
			. " WHERE project_id=".$project_id
			. " OR project_id=0"
			. " AND state=".TASK_STATE_OPEN
			;
		$this->_db->setQuery($query);

		$result = $this->_db->loadAssocList();
		$return = $this->loadObjects($result);
		return $return;
	}

	
}
