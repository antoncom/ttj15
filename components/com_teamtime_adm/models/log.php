<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

/*
   Class: LogModelLog
   The Model Class for Log
*/
class LogModelLog extends JModel {

	/**
	 * Table
	 *
	 * @var array
	 */
	var $_table = 'log';

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
		$edit  = JRequest::getVar('edit', true);

		if ($edit) {
			$this->setId((int)$array[0]);
		}
	}

	/**
	 * Method to set the model item identifier
	 *
	 * @access	public
	 * @param	int identifier
	 */
	function setId($id) {
		$this->_id		= $id;
		$this->_data	= null;
	}

	/**
	 * Method to get model item data
	 *
	 */
	function &getData() {
		if (empty($this->_data)) {
			$row =& $this->getTable($this->_table);

			// load the row from the db table
			if ($this->_id) {
				 $row->load($this->_id);
			}

			// set defaults, if new
			if ($row->id == 0) {
			}

			$this->_data =& $row;
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
		$row =& $this->getTable($this->_table);

		// Initialize variables
		$db			= & JFactory::getDBO();
		$user		= & JFactory::getUser();

		$nullDate	= $db->getNullDate();

		$details	= JRequest::getVar( 'details', null, 'post', 'array' );

		// bind the detail fields
		if (!$row->bind($details)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// bind the form fields
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// sanitise id field
		$row->id = (int) $row->id;

		// Are we saving from an item edit?
		if ($row->id) {
			$datenow =& JFactory::getDate();
			$row->modified 		= $datenow->toMySQL();
		}

		$row->user_id 	= $row->user_id ? $row->user_id : $user->get('id');

		if ($row->created && strlen(trim( $row->created )) <= 10) {
			$row->created 	.= ' 00:00:00';
		}



		$config =& JFactory::getConfig();
		$tzoffset = $config->getValue('config.offset');
		$date =& JFactory::getDate($row->created, $tzoffset);
		$row->created = $date->toMySQL();
		

		if ($row->date && strlen(trim( $row->date )) <= 10) {
			$row->date 	.= ' 00:00:00';
		}

		$config =& JFactory::getConfig();
		$tzoffset = $config->getValue('config.offset');
		$date =& JFactory::getDate($row->date, $tzoffset);
		$row->date = $date->toMySQL();

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
	 * Method to set the state of an Log
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function storeState($id, $state) {
		$row =& $this->getTable($this->_table);

		// store state to database
		if (!$row->setLogState($id, $state)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Method to remove a model item
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function delete($cid = array()) {
		$table =& $this->getTable($this->_table);

		if (count($cid)) {
			JArrayHelper::toInteger($cid);
			$cids = implode(',', $cid);
			$query = 'DELETE FROM '.$table->getTableName()
				.' WHERE id IN ('.$cids.')';
			$this->_db->setQuery($query);
			if(!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return true;
	}
}