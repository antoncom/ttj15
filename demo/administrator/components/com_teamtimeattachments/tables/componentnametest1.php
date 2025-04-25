<?php

class TableComponentnameTest1 extends JTable {

	var $id = null;
	var $name = null;
	var $description = null;
	var $modified = null;
	var $modified_by = null;

	function __construct(&$db) {
		parent::__construct('#__componentname_test1', 'id', $db);
	}

}