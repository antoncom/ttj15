<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/*
  Class: ProjectModelProject
  The Model Class for Project
 */

class ProjectModelProject extends JModel {

	/**
	 * Table
	 *
	 * @var array
	 */
	var $_table = 'project';

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
	 * Project report
	 *
	 * @var Report
	 */
	var $_report = null;

	/**
	 * Constructor
	 *
	 */
	function __construct() {
		parent::__construct();

		$array = JRequest::getVar('cid', array(0), '', 'array');
		$edit = JRequest::getVar('edit', true);
		$report = JRequest::getVar('report', false);

		if ($edit || $report) {
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
	 * Method to set the state of an Project
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function storeState($id, $state) {
		$row = & $this->getTable($this->_table);

		// store state to database
		if (!$row->setProjectState($id, $state)) {
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

	function getMaxRate($projectId, $isDynamicRate = 0) {
		$result = 0;

		// calculate price for dynamic rate
		if ($isDynamicRate) {
			$query = "select * from #__teamlog_task as a
        where a.project_id = " . (int) $projectId;

			$this->_db->setQuery($query);
			$rows = $this->_db->loadObjectList();

			$values = array();
			foreach ($rows as $row) {
				// check price by dotu
				$tmp_rate = TeamTime::_("Dotu_getTargetPrice", null, array(
						"task_id" => $row->id
						), true);
				if ($tmp_rate === null) {
					$tmp_rate = $row->rate;
				}

				$values[] = $tmp_rate;
			}

			$result = max($values);
		}

		// calculate price for static rate
		else {
			$query = "select max(a.rate) as task_rate, max(b.rate) as type_rate
        from #__teamlog_task as a
        left join #__teamlog_type as b on a.type_id = b.id
        where a.project_id = " . (int) $projectId;

			$this->_db->setQuery($query);
			$row = $this->_db->loadObject();

			$result = max($row->task_rate, $row->type_rate);
		}

		return $result;
	}

	function getMinRate($projectId, $isDynamicRate = 0) {
		$result = 0;

		// calculate price for dynamic rate
		if ($isDynamicRate) {
			$query = "select * from #__teamlog_task as a
        where a.project_id = " . (int) $projectId;

			$this->_db->setQuery($query);
			$rows = $this->_db->loadObjectList();

			$values = array();
			foreach ($rows as $row) {
				// check price by dotu
				$tmp_rate = TeamTime::_("Dotu_getTargetPrice", null, array(
						"task_id" => $row->id
						), true);
				if ($tmp_rate === null) {
					$tmp_rate = $row->rate;
				}

				$values[] = $tmp_rate;
			}

			$result = min($values);
		}

		// calculate price for static rate
		else {
			$query = "select min(a.rate) as task_rate, min(b.rate) as type_rate
        from #__teamlog_task as a
        left join #__teamlog_type as b on a.type_id = b.id
        where a.project_id = " . (int) $projectId;

			$this->_db->setQuery($query);
			$row = $this->_db->loadObject();

			$result = min($row->task_rate, $row->type_rate);
		}

		return $result;
	}

}