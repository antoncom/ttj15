<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// include table
require_once(dirname(dirname(__FILE__)).DS.'tables'.DS.'type.php');

/*
   Class: Type
   Type related attributes and functions.
*/
class Type extends YObject {

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
       Variable: rate
         Rate.
    */
	var $rate	= null;


	/*
    	Function: __construct
    	  Constructor.

		Parameters:
	      id - Type id.
 	*/
	function __construct($id = 0) {
		// load task if it exists
		if (!empty($id)) {
			$table =& JTable::getInstance('type', 'Table');
			if ($table->load($id)) {
				$this->bind($table);
			}
		}
	}
}