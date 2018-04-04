<?php
/**
* @version		1.5.0
* @package		Acesearch Library
* @subpackage	Controller
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class AcesearchController extends JController {
	
	public $_mainframe;
	public $_option;
	public $_context;
	public $_table;
	public $_model;
	
	function __construct($context = '', $table = '') 	{
		
		parent::__construct();
		
		$this->_mainframe =& JFactory::getApplication();
		if ($this->_mainframe->isAdmin()) {
			$this->_option = JAdministratorHelper::findOption();
		} else {
			$this->_option = JRequest::getCmd('option');
		}
		$this->_context = $context;
		
		$this->_table = $table;
		if ($this->_table == '') {
			$this->_table = $this->_context;
		}
		
		$this->_model =& $this->getModel($context);
		$this->AcesearchConfig = AcesearchFactory::getConfig();
		
		// Register tasks
		$this->registerTask('add', 'edit');
	}
	
	function display() {
		parent::display();
	}

	// Display
	function view() {
		$view = $this->getView(ucfirst($this->_context), 'html');
		$view->setModel($this->_model, true);
		
		$view->view();
	}
	
	// Edit
	function edit() {
		JRequest::setVar('hidemainmenu', 1);
		
		$view = $this->getView(ucfirst($this->_context), 'edit');
		$view->setModel($this->_model, true);
		$view->edit('edit');
	}
	
	function route($msg = ""){
		if ($msg != "") {
			parent::setRedirect('index.php?option='.$this->_option.'&controller='.$this->_context.'&task=view', $msg);
		} else {
			parent::setRedirect('index.php?option='.$this->_option.'&controller='.$this->_context.'&task=view');
		}
	}
	
	// Delete
	function remove() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Action
		if (!self::deleteRecord($this->_table, $this->_model)) {
			$msg = JText::_('COM_ACESEARCH_COMMON_RECORDS_DELETED');
		} else {
			$msg = JText::_('COM_ACESEARCH_COMMON_RECORDS_DELETED_NOT');
		}
		
		// Return
		self::route($msg);
	}
	
	function deleteRecord($table, $model, $where = true) {
		if ($where === true) {
			$where = self::_getWhere($model);
		}
		
		if (!AceDatabase::query("DELETE FROM #__acesearch_{$table}{$where}")) {
			return false;
		}

		return true;
    }
	
	function _getWhere($model, $prefix = "") {
        $where = '';
		
        $sel = JRequest::getVar('selection', 'selected', 'post');
        if ($sel == 'selected') {
            $where = self::_buildSelectedWhere($prefix);
        } elseif ($sel == 'filtered') {
            $where = $model->_buildViewWhere($prefix);
        }
        
        return $where;
    }
	
	// Get the id's of selected records
	function _buildSelectedWhere($prefix = "") {
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);
		
		$where = '';
		if(count($cid) > 0){
			$where = " WHERE {$prefix}id IN (".implode(", ", $cid).")";
		}

		return $where;
	}
	// Delete
	function delete() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Action
		if (!self::deleteRecord($this->_table, $this->_model)) {
			$msg = JText::_('COM_ACESEARCH_COMMON_RECORDS_DELETED_NOT');
		} else {
			$msg = JText::_('COM_ACESEARCH_COMMON_RECORDS_DELETED');
		}
		
		// Return
		self::route($msg);
	}
	
	// Publish
	function publish() {
		// Check token
		//JRequest::checkToken() or jexit('Invalid Token');
		
		// Action
		self::updateField($this->_table, 'published', 1, $this->_model);
		
		// Return
		self::route();
	}
	
	// Unpublish
	function unpublish() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Action
		self::updateField($this->_table, 'published', 0, $this->_model);
		
		// Return
		self::route();
	}
	
   	// Save changed record
	function saveRecord($post, $table, $id = 0) {
		// Get row
		$row = AcesearchFactory::getTable($table);
		
		// Bind the form fields to the table
		if (!$row->bind($post)) {
			return JError::raiseWarning(500, $row->getError());
		}

		// Make sure the record is valid
		if (!$row->check()) {
			return JError::raiseWarning(500, $row->getError());
		}
		
		// Save record
		if (!$row->store()) {
			return JError::raiseWarning(500, $row->getError());
		}
		
		return true;
	}
	
	// Save changes
	function editSave() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get post
		$post = JRequest::get('post');
		
		// Save record
		$table = 'Acesearch' . ucfirst($this->_context);
		if (!self::saveRecord($post, $table, $post['id'])) {
			return JError::raiseWarning(500, JText::_('COM_ACESEARCH_COMMON_RECORD_SAVED_NOT'));
		} else {
			if ($post['modal'] == '1') {
				// Display message
				JFactory::getApplication()->enqueueMessage(JText::_('COM_ACESEARCH_COMMON_RECORD_SAVED'));
			} else {
				// Return
				self::route(JText::_('COM_ACESEARCH_COMMON_RECORD_SAVED'));
			}
		}
	}
	
	// Apply changes
	function editApply() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get post
		$post = JRequest::get('post');
		
		// Save record
		$table = 'acesearch' . ucfirst($this->_context);
		if (!self::saveRecord($post, $table, $post['id'])) {
			return JError::raiseWarning(500, JText::_('COM_ACESEARCH_COMMON_RECORD_SAVED_NOT'));
		} else {
			if ($post['modal'] == '1') {
				// Return
				$this->setRedirect('index.php?option='.$this->_option.'&controller='.$this->_context.'&task=edit&cid[]='.$post['id'].'&tmpl=component', JText::_('COM_ACESEARCH_COMMON_RECORD_SAVED'));
			} else {
				// Return
				$this->setRedirect('index.php?option='.$this->_option.'&controller='.$this->_context.'&task=edit&cid[]='.$post['id'], JText::_('COM_ACESEARCH_COMMON_RECORD_SAVED'));
			}
		}
	}
	
	// Cancel changes
	function editCancel() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Get vars
		$modal = JRequest::getVar('modal', 0, 'method', 'int');
		
		if ($modal == '1') {
			// Display message
			JFactory::getApplication()->enqueueMessage(JText::_('COM_ACESEARCH_COMMON_RECORD_SAVED_NOT'));
		} else {
			// Return
			self::route(JText::_('COM_ACESEARCH_COMMON_RECORD_SAVED_NOT'));
		}
	}
	
	// Update field
	function updateField($table, $field, $value, $model, $where = true) {
		if ($where === true) {
			$where = self::_getWhere($model);
		}
		
		if (!AceDatabase::query("UPDATE #__acesearch_{$table} SET {$field} = '{$value}' {$where}")) {
			return false;
		}

		return true;
	}
	
	// Update param
	function updateParam($table, $table_m, $field, $param, $value, $model, $where = true) {
		if (!$ids = self::_getIDs($table, $model, $where)) {
			return;
		}
		
		$row = AcesearchFactory::getTable($table_m);
		
		if (!empty($ids) && is_array($ids)) {
			foreach ($ids as $index => $id) {
				if (!$row->load($id)) {
					continue;
				}
				
				$params = new JParameter($row->$field);
				$params->set($param, $value);
				
				$row->$field = $params->toString();
				
				if (!$row->check()) {
					continue;
				}
				
				if (!$row->store()) {
					continue;
				}
			}
		}
	}
	
	// Get IDs
	function _getIDs($table, $model, $where = true) {
		if ($where === true) {
			$where = self::_getWhere($model);
		}
		
		if (!$ids = AceDatabase::loadResultArray("SELECT id FROM #__acesearch_{$table} {$where}")) {
			return false;
		}
		
		return $ids;
	}
}