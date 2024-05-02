<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// include table
require_once(dirname(dirname(__FILE__)).DS.'tables'.DS.'log.php');

/*
   Class: Log
   Log related attributes and functions.
*/
class Log extends YObject {

    /*
       Variable: id
         Primary key.
    */
	var $id	= null;

    /*
       Variable: user_id
         Related user id.
    */
	var $user_id = null;


	var $type_id	= null;
    /*
       Variable: project_id
         Related project id.
    */
	var $project_id	= null;

    /*
       Variable: task_id
         Logs task id.
    */
	var $task_id = null;

    /*
       Variable: task_type
         Logs task type, to categorize tasks.
    */
	var $task_type = null;

    /*
       Variable: description
         Logs description.
    */
	var $description = null;

    /*
       Variable: duration
         Logs duration in minutes.
    */
	var $duration = null;

    /*
       Variable: date
         Logs date.
    */
	var $date = null;

    /*
       Variable: created
         Logs creation date.
    */
	var $created = null;
	var $ended = null;

    /*
       Variable: modified
         Logs last modification date.
    */
	var $modified = null;

	var $datepause			= null;
	var $sumpause			= null;
	var $money			= null;
	var $todo_id			= null;

	/*
    	Function: __construct
    	  Constructor.

		Parameters:
	      id - Log id.
 	*/
	function __construct($id = 0) {
		// load log if it exists
		if (!empty($id)) {
			$table =& JTable::getInstance('log', 'Table');
			if ($table->load($id)) {
				$this->bind($table);
			}
		} else {
			// init
			$date =& JFactory::getDate();
			$this->date     = $date->toMySQL();
			$this->created  = $date->toMySQL();
			$this->modified = $date->toMySQL();
		}
	}

	/*
    	Function: getDate
    	  Get formatted date.

		Parameters:
	      format - Date format.

	   Returns:
	      Date string or unix timestamp.
 	*/
	function getDate($format = 'Y-m-d H:i:s') {
		if ($format == null) {
			return strtotime($this->date);
		}

		return date($format, strtotime($this->date));
	}

	/*
    	Function: getDeltaText
    	  Get time delta of $date as text.

	   Returns:
	      Delta string.
 	*/
	function getDeltaText() {
		$date  =& JFactory::getDate();
		$delta =  $date->toUnix() - $this->getDate(null);

		if ($delta < 3600) {
			$delta = round($delta / 60);
			$unit = ($delta == 1)?JText::_('minute'):JText::_('minutes');
			$delta = $delta . ' ' . $unit;
		} elseif ($delta < 86400) {
			$delta = round($delta / 3600);
			$unit = ($delta == 1)?JText::_('hour'):JText::_('hours');
			$delta = JText::_('about'). ' ' . $delta . ' ' . $unit;
		} else {
			$delta = round($delta / 86400);
			$unit = ($delta == 1)?JText::_('day'):JText::_('days');
			$delta = JText::_('about'). ' ' . $delta . ' ' . $unit;
		}
		
		return $delta;
	}

	/*
    	Function: getDurationText
    	  Get Duration as string.

	   Returns:
	      Duration string.
 	*/
	function getDurationText(){
		$hours = intval($this->duration / 60);
		$hours = $hours > 0 ? $hours.JText::_('hr') : '';
		$mins  = ($this->duration % 60);
		$mins  = $mins > 0 ? ' '.$mins.JText::_('min') : '';
		return $hours.$mins;
	}
	
/****/
	function getTypeName() {
		$type = new Type($this->type_id);
		return $type->name;
	}
	

	/*
    	Function: getProjectName
    	  Get project name of log as text.

	   Returns:
	      Name string.
 	*/
	function getProjectName() {
		$project = new Project($this->project_id);
		return $project->name;
	}

	/*
    	Function: getTaskName
    	  Get task name of log as text.

	   Returns:
	      Name string.
 	*/
	function getTaskName() {
		$task = new Task($this->task_id);
		return $task->name;
	}

	/*
    	Function: save
    	  Save log entry to database.

	   Returns:
	      Boolean.
 	*/
	function save() {
		// load table object
		$table =& JTable::getInstance('log', 'Table');
		$table->bind($this->getProperties());

		// check object
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// store object
		if (!$result = $table->store()) {
			$this->setError($table->getError());
		}

		// set id
		if (empty($this->id)) {
			$this->id = $table->get('id');
		}

		return $result;
	}

	/*
    	Function: delete
    	  Delete log entry from database.

	   Returns:
	      Boolean.
 	*/
	function delete() {
		// load table object
		$table =& JTable::getInstance('log', 'Table');
		$table->bind($this->getProperties());

		// delete object
		if (!$result = $table->delete()) {
			$this->setError($table->getError());
		}

		return $result;
	}
	
}