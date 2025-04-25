<?php
/**
* @version		1.5.0
* @package		AceSearch
* @subpackage	AceSearch
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

//No Permision
defined('_JEXEC') or die('Restricted access');

// Includes
require_once(JPATH_COMPONENT.'/controller.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_acesearch/library/loader.php');

if (!AcesearchUtility::checkPlugin()) {
	return;
}

$controller = new AceSearchController();

// Perform the Request task
$controller->execute(JRequest::getWord('task'));

$module = JRequest::getInt('module');
if(empty($module)) {
AcesearchUtility::getPlugin();
}
// Redirect if set by the controller
$controller->redirect();