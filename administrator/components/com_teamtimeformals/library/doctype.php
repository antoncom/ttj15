<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// include table
require_once(dirname(dirname(__FILE__)).DS.'tables'.DS.'doctype.php');

class Doctype extends YObject {

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

	var $generator	= null;
	var $using_in			= null;

	function __construct($id = 0) {
		// load task if it exists
		if (!empty($id)) {
			$table =& JTable::getInstance('doctype', 'Table');
			if ($table->load($id)) {
				$this->bind($table);
			}
		}
	}
}