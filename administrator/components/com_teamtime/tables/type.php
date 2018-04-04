<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
   Class: TableType
   The Table Class for Type. Manages the database operations.
*/
class TableType extends JTable {

	/** @var int primary key */
	var $id					= null;
	/** @var string */
	var $name			= null;
	/** @var int */
	var $rate			= null;

	/**
	 * @param database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct('#__teamtime_type', 'id', $db);
	}

	function loadObjects($result) {
		$objects = array();
		if ($result != "") {
			foreach ($result as $row) {
				$object =& new Type();
				$object->bind($row);
				$objects[] = $object;
			}
		}

		return $objects;
	}
	
	function getTypes() {
		$query = " SELECT * "
			. " FROM ".$this->_tbl
			;
		$this->_db->setQuery($query);

		$result = $this->_db->loadAssocList();
		$return = $this->loadObjects($result);
		return $return;
	}
}