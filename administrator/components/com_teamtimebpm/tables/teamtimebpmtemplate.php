<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableTeamtimebpmTemplate extends Core_Joomla_Model {

	public $id = null;
	public $name = null;
	public $description = null;
	public $tags = null;
	public $modified = null;
	public $modified_by = null;
	public $archived = null;
	public $space_id = null;
	public $project_id = null;

	public function __construct(&$db) {
		parent::__construct('#__teamtimebpm_template', 'id', $db);
	}

}