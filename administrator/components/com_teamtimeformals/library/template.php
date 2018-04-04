<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// include table
require_once(dirname(dirname(__FILE__)).DS.'tables'.DS.'template.php');

//define('PROJECT_STATE_OPEN', 0);
//define('PROJECT_STATE_CLOSED', 1);

class Template extends YObject {

	var $id	= null;

	var $name = null;
	var $type = null;
	var $description = null;

	function __construct($id = 0) {
		if (!empty($id)) {
			$table =& JTable::getInstance('template', 'Table');
			if ($table->load($id)) {
				$this->bind($table);
			}
		}
	}	
}