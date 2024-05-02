<?php

if (isset($_SERVER["HTTP_HOST"])) {
	die("Start only from cron or command line.");
}

$_SERVER["HTTP_HOST"] = "localhost";

// Set flag that this is a parent file
define('_JEXEC', 1);

define('JPATH_BASE', realpath(dirname(__FILE__) . "/../../../administrator/"));
define('JPATH_COMPONENT', realpath(dirname(__FILE__)));
define('DS', DIRECTORY_SEPARATOR);

require_once(JPATH_BASE . DS . 'includes' . DS . 'defines.php' );
require_once(JPATH_BASE . DS . 'includes' . DS . 'framework.php' );

// custom init here
require_once(JPATH_BASE . '/components/com_teamtime/library/TeamTime/init.php');
require_once(JPATH_BASE . '/components/com_teamtime/library/TeamTime/Cronjob.php');
require_once(dirname(__FILE__) . "/library/TeamTime/CronjobTodoSender.php");

$mainframe = & JFactory::getApplication('administrator');
$mainframe->initialise(array(
	'language' => $mainframe->getUserState("application.lang", "lang")
));

$lang = & JFactory::getLanguage();
$lang->load("com_teamtimecalendar", JPATH_BASE);

// custom actions here
$cronjob = new TeamTime_CronjobTodoSender("TeamTime_CronjobTodoSender");
$cronjob->start();
