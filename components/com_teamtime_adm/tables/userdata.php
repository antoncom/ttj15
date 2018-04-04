<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
   Class: TableUserdata
   The Table Class for User. Manages components specific user data.
*/
class TableUserdata extends JTable {

	/** @var int user id */
	var $user_id           = null;
	/** @var string */
	var $state_description = null;
	/** @var date */
	var $state_modified    = null;

	/**
	 * @param database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct('#__teamlog_userdata', 'user_id', $db);
	}

}