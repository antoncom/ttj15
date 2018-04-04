<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableDoctype extends JTable {

	/** @var int primary key */
	var $id					= null;
	/** @var string */
	var $name			= null;
	
	var $generator			= null;
	var $using_in			= null;

	/**
	 * @param database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct('#__teamtimeformals_type', 'id', $db);
	}

	function loadObjects($result) {
		$objects = array();
		if ($result != "") {
			foreach ($result as $row) {
				$object =& new Doctype();
				$object->bind($row);
				$objects[] = $object;
			}
		}

		return $objects;
	}
	
	function getDoctypes() {
		$query = " SELECT * "
			. " FROM ".$this->_tbl
			;
		$this->_db->setQuery($query);

		$result = $this->_db->loadAssocList();
		$return = $this->loadObjects($result);
		return $return;
	}
}