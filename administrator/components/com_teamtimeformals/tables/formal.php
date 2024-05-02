<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableFormal extends JTable {

	/** @var int primary key */
	var $id					= null;
	/** @var string */
	var $name			= null;
	
	var $project_id	= null;
  var $doctype_id	= null;
  var $price	= null;
  var $content	= null;

	var $created = null;
	
	/**
	 * @param database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct('#__teamtimeformals_formal', 'id', $db);
	}

	function loadObjects($result) {
		$objects = array();
		if ($result != "") {
			foreach ($result as $row) {
				$object =& new Formal();
				$object->bind($row);
				$objects[] = $object;
			}
		}

		return $objects;
	}
	
	function getFormals() {
		error_log("FORMALS");


		$query = " SELECT * "
			. " FROM ".$this->_tbl
			;
		$this->_db->setQuery($query);

		$result = $this->_db->loadAssocList();
		$return = $this->loadObjects($result);

		error_log(print_r($result, true));
		error_log(print_r($return, true));

		return $return;
	}
}