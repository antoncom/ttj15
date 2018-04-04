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

class AcesearchControllerAjax extends AcesearchController {

	function changeExtension(){
		$extension = JRequest::getCmd('ext');
		
		if (!empty($extension)) {
			echo AcesearchSearch::getAdvancedSearch($extension);
		}
	}
	
	function changeUser() {
		$users = "";
		
		$extension = JRequest::getCmd('ext', '-1');
		$id = JRequest::getInt('usr', '0');
		
		if ($extension != '-1') {
			$acesearch_ext =& AcesearchFactory::getExtension($extension);
			
			$users = $acesearch_ext->getUser($id);
		}
		
		if (!empty($users)) {
			?>
			<strong><?php echo JText::_("COM_ACESEARCH_EXTENSIONS_VIEW_AUTHOR"); ?>:</strong><br />
			<?php
			echo $users;
		}
	}
	
	function complete() {
		$return	= AcesearchSearch::getComplete();
		
		echo json_encode($return);
		
		JFactory::getApplication()->close();
	}
}