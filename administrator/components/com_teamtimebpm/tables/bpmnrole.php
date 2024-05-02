<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableBpmnRole extends JTable {

	var $id = null;
	var $name = null;
	var $rate = null;
	var $target_id = null;
	var $rate_from_dotu = null;
	var $user_id = null;

	function __construct(&$db) {
		parent::__construct('#__teamtimebpm_role', 'id', $db);
	}

}