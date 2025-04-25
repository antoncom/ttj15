<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TeamlogWuser extends YObject {

    /*
       Variable: id
         Primary key.
    */
	var $id	= null;

    /*
       Variable: name
         Name.
    */
	var $name	= null;

  /*
    	Function: __construct
    	  Constructor.

		Parameters:
	      id - Type id.
 	*/
	function __construct($id = 0) {
		// load task if it exists
		if (!empty($id)) {
			$table =& JTable::getInstance('wuser', 'Table');
			if ($table->load($id)) {
				$this->bind($table);
			}
		}
	}
}

class TableWuser extends JTable {

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
		parent::__construct('#__users', 'id', $db);
	}

	function loadObjects($result) {
		$objects = array();
		if ($result != "") {
			foreach ($result as $row) {
				$object =& new TeamlogWuser();
				$object->bind($row);
				$objects[] = $object;
			}
		}

		return $objects;
	}
	
	function getUsers() {
		$query = " SELECT * "
			. " FROM ".$this->_tbl
			;
		$this->_db->setQuery($query);

		$result = $this->_db->loadAssocList();
		$return = $this->loadObjects($result);
		return $return;
	}
}

