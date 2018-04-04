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

class AcesearchControllersearch extends AcesearchController{

	function view() {
		$model =& $this->getModel('Search');
		$view =& $this->getView ('Search', 'html');
		$view->setModel($model , true );
		$view->view();
	}
	
	function advancedsearch() {
		$model =& $this->getModel('Search');
		$view =& $this->getView ('Search', 'html');
		$view->setModel($model , true );
		$view->view('advanced');
	}	
}