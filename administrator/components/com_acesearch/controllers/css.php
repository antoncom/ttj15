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

class AcesearchControllerCSS extends AcesearchController {

	// Main constructer
    function __construct() {
        parent::__construct('css');
    }
	
	function edit() {
		$view =& $this->getView ('CSS', 'html');
		JRequest::setVar('hidemainmenu', 1);
		$view->view();
	}
	
	function save() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		if ($this->_model->save()) {
			$this->setRedirect('index.php?option=com_acesearch',JText::_('COM_ACESEARCH_FILE_SAVED'));
		}
		else {
			$this->setRedirect('index.php?option=com_acesearch',JText::_('COM_ACESEARCH_FILE_NOT_SAVED'));
		}
	}
	
	function apply() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		if ($this->_model->save()) {
			$this->setRedirect('index.php?option=com_acesearch&controller=css&task=edit', JText::_('COM_ACESEARCH_FILE_SAVED'));
		}
		else {
			$this->setRedirect('index.php?option=com_acesearch&controller=css&task=edit', JText::_('COM_ACESEARCH_FILE_NOT_SAVED'));
		}
	}	
	
	// Cancel saving changes
	function cancel() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		$this->setRedirect('index.php?option=com_acesearch',JText::_('COM_ACESEARCH_FILE_NOT_SAVED'));
	}
}