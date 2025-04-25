<?php

exit();

// setup current session parameters
//session_name($_REQUEST["_sn"]);
//session_id($_REQUEST["_si"]);
//
// set flag that this is a parent file
define('_JEXEC', 1);

define('DS', DIRECTORY_SEPARATOR);

$basePath = realpath(dirname(__FILE__) . "/../../../../../../../../");

// init paths
define('JPATH_BASE', $basePath);
define('JPATH_COMPONENT', $basePath . "/components/com_teamtimeattachments");
define('JPATH_COMPONENT_ADMINISTRATOR',
		$basePath . "/administrator/components/com_teamtimeattachments");

// include joomla core
require_once(JPATH_BASE . DS . 'includes' . DS . 'defines.php' );
require_once(JPATH_BASE . DS . 'includes' . DS . 'framework.php' );

// set for debug
error_reporting(E_ALL ^ E_DEPRECATED);
ini_set("display_errors", "off");
ini_set("log_errors", 1);
ini_set("error_log", JPATH_ROOT . "/logs/error_log");

$mainframe = & JFactory::getApplication('site');
$mainframe->initialise(array(
	'language' => $mainframe->getUserState("application.lang", "lang")
));

$lang = & JFactory::getLanguage();
$lang->load("com_teamtimeattachments", JPATH_BASE);

jimport('joomla.database.table');
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

$user = & JFactory::getUser();
error_log("user: " . $user->name);
error_log($mainframe->getUserState("application.lang", "lang"));

//$session = & JFactory::getSession();
//error_log($session->getName() . ": " . $session->getId());

if ($user->guest) {
	JError::raiseWarning(500, JText::_('ALERTNOTAUTH'));

	error_log("not allowed");
}
else {
	// custom initialisation here
	set_include_path(implode(PATH_SEPARATOR,
					array(
				realpath(JPATH_COMPONENT_ADMINISTRATOR . "/library"),
				get_include_path(),
			)));

	require_once(JPATH_ADMINISTRATOR .
			'/components/com_teamtime/library/TeamTime/init.php');

	// get request vars
	$task = JRequest::getCmd('task');
	$controller = "attachments";

	error_log($task);

	require_once(JPATH_COMPONENT . '/controllers/' . $controller . '.php');

	$className = 'TeamtimeAttachmentsController' . $controller;
	$controller = new $className();

	// perform the request task
	$controller->execute($task);
	$controller->redirect();
}