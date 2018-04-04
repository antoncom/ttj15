<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
   Class: YObject
   Extends JObject related attributes and functions.
*/
class YObject extends JObject {

	/*
    	Function: bind
    	  Binds a named array/hash to this object.
		  Can be overloaded/supplemented by the child class.
		
		Parameters:
	      from   - An associative array or object.
	      ignore - An array or space separated list of fields not to bind.
	
	   Returns:
	      Boolean.	
 	*/
	function bind($from, $ignore=array()) {
		$fromArray	= is_array($from);
		$fromObject	= is_object($from);

		if (!$fromArray && !$fromObject) {
			$this->setError(get_class( $this ).'::bind failed. Invalid from argument');
			return false;
		}
		
		if (!is_array( $ignore )) {
			$ignore = explode( ' ', $ignore );
		}
		
		foreach ($this->getProperties() as $k => $v) {
			// internal attributes of an object are ignored
			if (!in_array($k, $ignore)) {
				if ($fromArray && isset($from[$k])) {
					$this->$k = $from[$k];
				} else if ($fromObject && isset($from->$k)) {
					$this->$k = $from->$k;
				}
			}
		}
		
		return true;
	}

}