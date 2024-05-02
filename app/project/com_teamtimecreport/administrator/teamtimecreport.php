<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// set defines
define('TEAMLOG_ICON', 'teamtime.png');
define('TEAMLOG_TOOLBAR_TITLE', JText::_('TeamTime CReport').' - ');

/*
// add jhtml path, css, js
JHTML::script('jquery.min.js', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/');
JHTML::script('jquery.noconflict.js', 'components/com_teamtime/assets/js/');

JHTML::addIncludePath(JPATH_BASE.'/components/com_teamtime/helpers');
JHTML::addIncludePath(JPATH_BASE.'/components/com_teamtimeformals/helpers');
JHTML::script('default.js', 'administrator/components/com_teamtime/assets/js/');
JHTML::stylesheet('default.css', 'administrator/components/com_teamtime/assets/css/');
JHTML::stylesheet('default.css', 'administrator/components/com_teamtimeformals/assets/css/');
*/

require_once(JPATH_COMPONENT.DS.'teamtime_helpers.php');

//get request vars
$controller = JRequest::getWord('controller');
$task       = JRequest::getCmd('task');

//component menu
JSubMenuHelper::addEntry(JText::_("Config"),
	"index.php?option=com_teamtimecreport&controller=config",
		$controller == 'config');

//load controller
require_once(JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');

$classname  = $controller.'Controller';
$controller = new $classname();

//perform the request task
$controller->execute($task);
$controller->redirect();