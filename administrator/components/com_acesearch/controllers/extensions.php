<?php
/**
* @version		1.5.0
* @package		AceSearch
* @subpackage	AceSearch
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

//No Permision
defined( '_JEXEC' ) or die( 'Restricted access' );

class AcesearchControllerExtensions extends AcesearchController {
	
	// Main constructer
	function __construct() 	{
		parent::__construct('extensions');
	}
	
	// Display
	function view() {
		$this->_model->checkComponents();
		
		$view = $this->getView(ucfirst($this->_context), 'html');
		$view->setModel($this->_model, true);
		$view->view();
	}
	
	// Uninstall extensions
	function uninstall() {

		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Uninstall selected extensions
		$this->_model->uninstall();

		// Return
		parent::route(JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_REMOVED'));
	}
	
	// Save changes
	function save() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Save
		$this->_model->save();
		
		// Return
		parent::route(JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_SAVED'));
	}
	
	
	// Install a new extension
	function installUpgrade() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		if(!$this->_model->installUpgrade()){
			JError::raiseWarning('1001', JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_NOT_INSTALLED'));
		} else {
			parent::route(JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_INSTALLED'));
		}
	}
	
	function accesspublic() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Action
		parent::updateParam($this->_table, 'AcesearchExtensions', 'params', 'access', '0', $this->_model);
		
		// Return
		parent::route();
	}
	
	function accessregistered () {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Action
		parent::updateParam($this->_table, 'AcesearchExtensions', 'params', 'access', '1', $this->_model);
		
		// Return
		parent::route();
	}

	function accessspecial () {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Action
		parent::updateParam($this->_table, 'AcesearchExtensions', 'params', 'access', '2', $this->_model);
		
		// Return
		parent::route();
	}

}