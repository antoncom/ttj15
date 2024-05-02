<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// include table
require_once(dirname(dirname(__FILE__)).DS.'tables'.DS.'formal.php');

class Formal extends YObject {

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

	var $project_id	= null;
  var $doctype_id	= null;
  var $price	= null;
  var $content	= null;

	var $created = null;

	function __construct($id = 0) {
		// load task if it exists
		if (!empty($id)) {
			$table =& JTable::getInstance('formal', 'Table');
			if ($table->load($id)) {
				$this->bind($table);
			}		
		}
	}
}