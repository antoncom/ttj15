<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableTemplate extends JTable {

	/** @var int primary key */
	var $id = null;

	/** @var int */
	var $name = null;

	/** @var string */
	var $type = null;
	var $description = null;

	/**
	 * @param database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct('#__teamtimeformals_template', 'id', $db);
	}

	function loadObjects($result) {
		$objects = array();
		foreach ($result as $row) {
			$object = & new Template();
			$object->bind($row);
			$objects[] = $object;
		}

		return $objects;
	}

}