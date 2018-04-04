<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableTeamtimebpmSpace extends Core_Joomla_Model {

	public $id = null;
	public $name = null;
	public $description = null;
	public $tags = null;
	public $modified = null;
	public $modified_by = null;
	public $archived = null;

	public function __construct(&$db) {
		parent::__construct('#__teamtimebpm_space', 'id', $db);
	}

}