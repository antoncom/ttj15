<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class DoctypeModelDoctype extends JModel {

	/**
	 * Table
	 *
	 * @var array
	 */
	var $_table = 'doctype';

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

	function getUsings() {
		static $usings = null;
		if ($usings == null) {
			$usings = array(
					"project" => array(0, JText::_("Formal Select Projects")),
					"user" => array(1, JText::_("Formal Select Users")),
					"system" => array(2, JText::_("Formal Select System"))
			);
		}

		return $usings;
	}

	function getUsingType($usingIndex) {
		$types = array_keys($this->getUsings());

		return $types[$usingIndex];
	}

	function getUsingIndex($usingType) {
		$usings = $this->getUsings();

		return $usings[$usingType][0];
	}

	function getUsingLabel($usingType) {
		$usings = $this->getUsings();

		return $usings[$usingType][1];
	}

}