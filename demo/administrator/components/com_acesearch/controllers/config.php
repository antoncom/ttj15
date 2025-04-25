<?php
/**
* @version		1.5.0
* @package		AceSearch
* @subpackage	AceSearch
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

//No Permision
defined('_JEXEC') or die('Restricted Access');

// Controller Class
class AcesearchControllerConfig extends AcesearchController {
	
	// Main constructer
 	function __construct() {
		parent::__construct('config');
	}
	
	// Save changes
	function save() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		$this->_model->save();
		$this->setRedirect('index.php?option=com_acesearch', JText::_('COM_ACESEARCH_CONFIG_SAVED'));
	}
	
	// Apply changes
	function apply() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		$this->_model->save();
		
		$this->setRedirect('index.php?option=com_acesearch&controller=config&task=edit', JText::_('COM_ACESEARCH_CONFIG_SAVED'));
	}
	
	// Cancel saving changes
	function cancel() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		$this->setRedirect('index.php?option=com_acesearch', JText::_('COM_ACESEARCH_CONFIG_NOT_SAVED'));
	
	}
}