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
class AcesearchControllerUpgrade extends AcesearchController {

	// Main constructer
	function __construct() {
		parent::__construct('upgrade');
	}
	
	// Upgrade
    function upgrade() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Upgrade
		$this->_model->upgrade();
		
		// Return
		$this->setRedirect('index.php?option=com_acesearch&controller=upgrade&task=view');
    }
}
?>