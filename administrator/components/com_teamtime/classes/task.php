<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// include table
require_once(dirname(dirname(__FILE__)).DS.'tables'.DS.'task.php');

define('TASK_STATE_OPEN', 0);
define('TASK_STATE_CLOSED', 1);

/*
   Class: Task
   Task related attributes and functions.
*/
class Task extends YObject {

    /*
       Variable: id
         Primary key.
    */
	var $id	= null;

    /*
       Variable: project id
         Project id.
    */
	var $project_id	= null;

    /*
       Variable: type id
         Task type id.
    */
	var $type_id = null;

    /*
       Variable: name
         Task name.
    */
	var $name = null;

    /*
       Variable: description
         Task description.
    */
	var $description = null;

    /*
       Variable: duration
         Task state.
    */
	var $state = null;

    /*
       Variable: rate
         Rate.
    */
	var $rate = null;
	
	/*
    	Function: __construct
    	  Constructor.

		Parameters:
	      id - Task id.
 	*/
	function __construct($id = 0) {
		// load task if it exists
		if (!empty($id)) {
			$table =& JTable::getInstance('task', 'Table');
			if ($table->load($id)) {
				$this->bind($table);
			}
		}
	}

	/*
    	Function: getstates
    	  Get task states as array.

	   Returns:
	      Array.
 	*/	
	function getStates() {
		$states = array(
			TASK_STATE_OPEN => JText::_('Open'),
			TASK_STATE_CLOSED => JText::_('Closed'));
			
		return $states;
	}

}