<?php

class Core_Joomla_Manager extends JModel {

	public $_table = '';
	public $_id = null;
	public $_data = null;

	public function __construct() {
		parent::__construct();

		$array = JRequest::getVar('cid', array(0), '', 'array');
		$edit = JRequest::getVar('edit', true);

		if ($edit) {
			$this->setId((int) $array[0]);
		}
	}

	public function setId($id) {
		$this->_id = $id;
		$this->_data = null;
	}

	public function &getData() {
		if (empty($this->_data)) {
			$row = & $this->getTable($this->_table);

			// load the row from the db table
			if ($this->_id) {
				$row->load($this->_id);
			}

			// set defaults, if new
			if ($row->id == 0) {
				
			}

			$this->_data = & $row;
		}

		return $this->_data;
	}

	public function getById($id) {
		$this->setId($id);
		$row = $this->getData();

		return $row;
	}

	public function store($data) {
		$row = & $this->getTable($this->_table);

		// bind the form fields
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// init additional fields
		$fields = get_object_vars($row);
		$datenow = & JFactory::getDate();
		$user = & JFactory::getUser();

		if (array_key_exists("modified", $fields)) {
			$row->modified = $datenow->toMySQL();
		}
		if (array_key_exists("modified_by", $fields)) {
			$row->modified_by = $user->get("id");
		}

		// check if model item data is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// store model item to the database
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$this->_data = $row;

		return true;
	}

	public function delete($cid = array()) {
		$table = & $this->getTable($this->_table);

		if (count($cid)) {
			JArrayHelper::toInteger($cid);
			$cids = implode(',', $cid);
			$query = 'DELETE FROM ' . $table->getTableName()
					. ' WHERE id IN (' . $cids . ')';
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			else {
				//foreach ($cid as $id) {
				// remove some data for each selected $id
				//}
			}
		}

		return true;
	}

}
