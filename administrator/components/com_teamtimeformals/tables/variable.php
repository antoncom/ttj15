<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableVariable extends JTable {

	/** @var int primary key */
	var $id = null;

	/** @var int */
	var $name = null;

	/** @var string */
	var $tagname = null;
	var $xsize = null;
	var $ysize = null;
	var $description = null;
	var $defaultval = null;
	var $using_in = null;

	/**
	 * @param database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct('#__teamtimeformals_variable', 'id', $db);
	}

	function loadObjects($result) {
		$objects = array();
		foreach ($result as $row) {
			$object = & new Variable();
			$object->bind($row);
			$objects[] = $object;
		}

		return $objects;
	}

}