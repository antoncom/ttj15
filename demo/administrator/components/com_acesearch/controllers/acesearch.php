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

class AcesearchControllerAcesearch extends AcesearchController {

	// Main constructer
    function __construct() {
        parent::__construct('acesearch');
    }
	
	function saveDownloadID() {
		// Check token
		JRequest::checkToken() or jexit('Invalid Token');
		
		$model = $this->getModel('Acesearch');
		$msg = $model->saveDownloadID();
        
        $this->setRedirect('index.php?option=com_acesearch', $msg);
    }
}