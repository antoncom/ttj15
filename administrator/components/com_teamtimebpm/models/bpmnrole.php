<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class BpmnRoleModelBpmnRole extends JModel {

	/**
	 * Table
	 *
	 * @var array
	 */
	var $_table = 'bpmnrole';

	/**
	 * Model item id
	 *
	 * @var int
	 */
	var $_id = null;

	/**
	 * Model item data
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Constructor
	 *
	 */
	function __construct() {
		parent::__construct();

		$array = JRequest::getVar('cid', array(0), '', 'array');
		$edit = JRequest::getVar('edit', true);

		if ($edit) {
			$this->setId((int) $array[0]);
		}
	}

	/**
	 * Method to set the model item identifier
	 *
	 * @access	public
	 * @param	int identifier
	 */
	function setId($id) {
		$this->_id = $id;
		$this->_data = null;
	}

	/**
	 * Method to get model item data
	 *
	 */
	function &getData() {
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

	/**
	 * Method to store the model item
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function store($data) {
		$row = & $this->getTable($this->_table);

		// bind the form fields
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
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

	/**
	 * Method to remove a model item
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function delete($cid = array()) {
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
		}

		return true;
	}

	function getRoles() {
		$table = & $this->getTable($this->_table);

		$query = 'select id, name from ' . $table->getTableName()
				. ' order by name';
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();

		return $result;
	}

	function getUserIdByRole($role) {
		$table = & $this->getTable($this->_table);

		$query = "select * from " . $table->getTableName() .
				" where name = " . $this->_db->Quote($role);

		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();

		return $row ? $row->user_id : "";
	}

}