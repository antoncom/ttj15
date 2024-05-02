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

class AcesearchControllerStatistics extends AcesearchController {
	
	function __construct() {
		parent::__construct('statistics', 'search_results');
	}
	
	function view(){
		$model =& $this->getModel('Statistics');
		$view  =& $this->getView('Statistics', 'html');
		$view->setModel($model , true);
		$view->view();
	}
}