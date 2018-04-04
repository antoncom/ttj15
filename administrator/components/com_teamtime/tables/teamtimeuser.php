<?php

class TeamtimeUserSettings extends YObject {

	public $id = null;
	public $name = null;

	public function __construct($id = 0) {
		// load task if it exists
		if (!empty($id)) {
			$table = & JTable::getInstance('teamtimeuser', 'Table');
			if ($table->load($id)) {
				$this->bind($table);
			}
		}
	}

}

class TableTeamtimeUser extends Core_Joomla_Model {

	public $id = null;
	public $name = null;
	public $rate = null;

	public function __construct(&$db) {
		parent::__construct('#__users', 'id', $db);
	}

	public function loadObjects($result) {
		$objects = array();
		if ($result != "") {
			foreach ($result as $row) {
				$object = & new TeamtimeUserSettings();
				$object->bind($row);
				$objects[] = $object;
			}
		}

		return $objects;
	}

	public function getUsers() {
		$query = " SELECT * FROM " . $this->_tbl;
		$this->_db->setQuery($query);

		$result = $this->_db->loadAssocList();
		$return = $this->loadObjects($result);

		return $return;
	}

}

