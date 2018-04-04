<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// include table
require_once(dirname(dirname(__FILE__)).DS.'tables'.DS.'variable.php');

//define('PROJECT_STATE_OPEN', 0);
//define('PROJECT_STATE_CLOSED', 1);

/*
   Class: Project
   Project related attributes and functions.
*/
class Variable extends YObject {

    /*
       Variable: id
         Primary key.
    */
	var $id	= null;

    /*
       Variable: name
         Project name.
    */
	var $name = null;
	var $tagname = null;

	var $xsize = null;
	var $ysize = null;

    /*
       Variable: description
         Logs description.
    */
	var $description = null;
	var $defaultval = null;
	var $using_in = null;

	function __construct($id = 0) {
		if (!empty($id)) {
			$table =& JTable::getInstance('variable', 'Table');
			if ($table->load($id)) {
				$this->bind($table);
			}
		}
	}	
}