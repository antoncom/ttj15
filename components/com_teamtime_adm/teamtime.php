<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// set for debug
//error_reporting(E_ALL ^ E_DEPRECATED);
//ini_set("display_errors", "off");
//ini_set("log_errors", 1);
//ini_set("error_log", JPATH_ROOT . "/tmp/error_log");

require_once( dirname(__FILE__) . DS . 'teamtime_helpers.php');

// set defines
define('TEAMLOG_ICON', 'teamtime.png');
define('TEAMLOG_TOOLBAR_TITLE', JText::_('TeamTime Accounting') . ' - ');

// add jhtml path, css, js
JHTML::script('jquery.min.js', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/');
JHTML::script('jquery.autoNumeric-1.6.2.js', 'administrator/components/com_teamtime/assets/js/');
JHTML::script('jquery.noconflict.js', 'components/com_teamtime/assets/js/');

JHTML::script('highslide-with-html.js', "components/com_teamtime/assets/highslide/");
JHTML::stylesheet('highslide.css', "components/com_teamtime/assets/highslide/");

JHTML::addIncludePath(dirname(__FILE__) . '/helpers');
JHTML::stylesheet('default.css', 'administrator/components/com_teamtime/assets/css/');

// get request vars
$controller = JRequest::getWord('controller');
$task = JRequest::getCmd('task');

// validate controller
$controllers = array(
		'type', 'project', 'task',
		'log', 'report', 'todo',
		'wuser', 'cpanel', 'config');

if (!in_array($controller, $controllers)) {
	$user = & JFactory::getUser();
	if ($user->usertype == "Super Administrator" || $user->usertype == "Administrator") {
		$controller = 'cpanel';
	}
	else {
		$controller = 'report';
	}
}

// component menu
if ($controller == 'cpanel' || $controller == 'config') {
	foreach (TeamTime::addonsList() as $name) {
		TeamTime::_("addon_menuitem_{$name}", $controller);
	}
}
else {
	JSubMenuHelper::addEntry(
			JText::_("Control Panel"), "index.php?option=com_teamtime&controller=cpanel",
			$controller == "cpanel"
			|| $controller == 'config');
	JSubMenuHelper::addEntry(
			JText::_("Reports"), "index.php?option=com_teamtime&controller=report", $controller == "report");
	JSubMenuHelper::addEntry(
			JText::_("Todos"), "index.php?option=com_teamtime&controller=todo", $controller == "todo");
	JSubMenuHelper::addEntry(
			JText::_("Logs"), "index.php?option=com_teamtime&controller=log", $controller == "log");
	JSubMenuHelper::addEntry(
			JText::_("Projects"), "index.php?option=com_teamtime&controller=project",
			$controller == "project");
	JSubMenuHelper::addEntry(
			JText::_("Tasks"), "index.php?option=com_teamtime&controller=task", $controller == "task");
	JSubMenuHelper::addEntry(
			JText::_("Types"), "index.php?option=com_teamtime&controller=type", $controller == "type");
	JSubMenuHelper::addEntry(
			JText::_("Users"), "index.php?option=com_teamtime&controller=wuser", $controller == "wuser");
}

// set the table directory
JTable::addIncludePath(JPATH_COMPONENT . DS . 'tables');

// load controller
require_once(JPATH_COMPONENT . DS . 'controllers' . DS . $controller . '.php');

require_once( dirname(__FILE__) . DS . 'helper.php' );

// load classes & helper
require_once(JPATH_COMPONENT . DS . 'classes' . DS . 'factory.php');
require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'date.php');

require_once(JPATH_COMPONENT . DS . 'models' . DS . 'todo.php');

foreach ($controllers as $con) {
	$helper_path = JPATH_COMPONENT . DS . 'helpers' . DS . $con . '.php';

	if (file_exists($helper_path)) {
		require_once($helper_path);
	}
}

$classname = $controller . 'Controller';
$controller = new $classname();

// perform the request task
$controller->execute($task);
$controller->redirect();