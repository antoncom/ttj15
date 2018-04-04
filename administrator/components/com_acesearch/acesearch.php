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

// Load AceSearch library
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_acesearch'.DS.'library'.DS.'loader.php');

$lang = JFactory::getLanguage();
$lang->load('com_acesearch' , JPATH_SITE);

JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');

if($controller = JRequest::getCmd('controller')){
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if(file_exists($path)) {
	    require_once($path);
	} else {
	    $controller = '';
	}
}

$classname = 'AcesearchController'.$controller;
$controller = new $classname();

// Perform the Request task
$controller->execute(JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
