<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class VariableModelVariable extends JModel {

	/**
	 * Table
	 *
	 * @var array
	 */
	var $_table = 'variable';

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

	/*
	  function storeState($id, $state) {
	  $row =& $this->getTable($this->_table);

	  // store state to database
	  if (!$row->setProjectState($id, $state)) {
	  $this->setError($this->_db->getErrorMsg());
	  return false;
	  }
	  return true;
	  } */

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

	function getVariablesDefault($names_filter = array()) {
		$result = array();

		// set filter by variable names
		if (sizeof($names_filter) > 0) {
			$filter = array();
			foreach ($names_filter as $n) {
				$filter[] = "b.tagname = " . $this->_db->Quote($n);
			}
			$filter = "where " . implode(" or ", $filter);
		}
		else {
			$filter = "";
		}

		// get variables list
		$query = "select * from #__teamtimeformals_variable as b
      {$filter}";

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();

		if (!$rows) {
			return $result;
		}

		foreach ($rows as $row) {
			$result[$row->id] = $row;
		}

		return $result;
	}

	function getVariablesByProject($project_id, $names_filter = array()) {
		$variables_default = $this->getVariablesDefault($names_filter);

		if (sizeof($variables_default) > 0) {
			$ids = implode(",", array_keys($variables_default));
			$query = "select * from #__teamtimeformals_formaldata as a
        where a.project_id = {$project_id} and a.variable_id in ({$ids})
        order by a.mdate desc";

			$this->_db->setQuery($query);
			$rows = $this->_db->loadObjectList();
		}

		$variables_names = array();
		$variables = array();

		foreach ($variables_default as $id => $var) {
			// set flag - default value
			$variables_default[$id]->is_default = true;

			$variables_names[$var->tagname] = $var->name;
			$variables[$var->tagname] = $var->defaultval;
		}

		foreach ($rows as $row) {
			// set last variable value
			if ($variables_default[$row->variable_id]->is_default) {
				$variables_default[$row->variable_id]->is_default = false;
				$variables[$variables_default[$row->variable_id]->tagname] = $row->content;
			}
		}

		return array($variables_names, $variables);
	}

	function getVariablesByUser($user_id, $names_filter = array()) {
		$variables_default = $this->getVariablesDefault($names_filter);

		if (sizeof($variables_default) > 0) {
			$ids = implode(",", array_keys($variables_default));
			$query = "select * from #__teamtimeformals_formaldata as a
        where a.user_id = {$user_id} and a.variable_id in ({$ids})
        order by a.mdate desc";

			$this->_db->setQuery($query);
			$rows = $this->_db->loadObjectList();
		}

		$variables_names = array();
		$variables = array();
		$current_tax_variables = array();

		foreach ($variables_default as $id => $var) {
			// set flag - default value
			$variables_default[$id]->is_default = true;

			$variables_names[$var->tagname] = $var->name;
			$variables[$var->tagname] = $var->defaultval;
		}

		foreach ($rows as $row) {
			// set last variable value
			if ($variables_default[$row->variable_id]->is_default) {
				$variables_default[$row->variable_id]->is_default = false;
				$variables[$var->tagname] = $row->content;
				//???
				//$variables[$variables_default[$row->variable_id]->tagname] = $row->content;
			}
		}

		/*
		  TODO init current tax variables by date

		  // for user_tax variables - store as array with key by date
		  if (substr($row->tagname, 0, 9) == "user_tax_") {
		  if (trim($row->content) != "") {
		  $v = $row->content;
		  }
		  else {
		  $v = $row->defaultval;function getVariablesByUser($user_id, $names_filter = array()) {
		  }

		  if (!isset($variables[$row->tagname])) {
		  $variables[$row->tagname] = array();
		  }

		  if ($v != "") {
		  $variables[$row->tagname][$row->mdate] = $v;
		  }
		  }

		  // for other variables - use only last variable value
		  else {
		  if (isset($variables[$row->tagname])) {
		  continue;
		  }

		  if (trim($row->content) != "") {
		  $variables[$row->tagname] = $row->content;
		  }
		  else {
		  $variables[$row->tagname] = $row->defaultval;
		  }
		  }
		 */

		return array($variables_names, $variables, $current_tax_variables);
	}

	function getUserTaxData($user_variables, $money, $exclude = array()) {
		$user_tax_data = array();

		// calc money without tax
		$current_tax = 0;
		foreach ($user_variables[1] as $tagname => $perc) {
			if (!in_array($tagname, $exclude)) {
				$current_tax += $perc / 100;
			}
		}
		$tmp_result = $money / (1 + $current_tax);

		// calc money without exluded tax
		$result = $tmp_result;
		foreach ($exclude as $tagname) {
			$result -= $tmp_result * ($user_variables[1][$tagname] / 100);
		}

		// get tax money values
		foreach ($user_variables[1] as $tagname => $perc) {
			$user_tax_data[$tagname] = $perc * ($tmp_result / 100);
		}

		return array($result, $user_tax_data);
	}

	function setVariablesForUser($user_id, $variables) {
		$mdate = $this->_db->Quote(date("Y-m-01"));

		foreach ($variables as $variable_id => $content) {

			if (trim($content) != "") {
				$content = $this->_db->Quote($content);
			}
			else {
				// set default value
				$this->setId($variable_id);
				$data = $this->getData();
				$content = $data->defaultval;
			}

			$query = "insert into `#__teamtimeformals_formaldata`
        (variable_id, user_id, content, mdate)
        values({$variable_id}, {$user_id}, {$content}, $mdate)
        ON DUPLICATE KEY UPDATE content = {$content}";

			$this->_db->Execute($query);
		}
	}

	function setVariablesForProject($project_id, $variables) {
		$mdate = $this->_db->Quote(date("Y-m-01"));

		foreach ($variables as $variable_id => $content) {

			if (trim($content) != "") {
				$content = $this->_db->Quote($content);
			}
			else {
				// set default value
				$this->setId($variable_id);
				$data = $this->getData();
				$content = $data->defaultval;
			}

			$query = "insert into `#__teamtimeformals_formaldata`
        (variable_id, project_id, content, mdate)
        values({$variable_id}, {$project_id}, {$content}, {$mdate})
        ON DUPLICATE KEY UPDATE content = {$content}";

			$this->_db->Execute($query);
		}
	}

	function canNotifyClientByEmail($projectId) {
		list($variables, $variablesData) = $this->getVariablesByProject(
				$projectId, array("client_email", "requirements_template_name"));

		return trim($variablesData["requirements_template_name"]) != ""
				&& trim($variablesData["client_email"]) != "";
	}

}