<?php

// set for debug
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
ini_set("display_errors", "off");
ini_set("log_errors", 1);
ini_set("error_log", JPATH_ROOT . "/logs/error_log");

define('TODO_STATE_OPEN', 0);
define('TODO_STATE_DONE', 1);
define('TODO_STATE_CLOSED', 2);
define('TODO_STATE_PROJECT', 4);

set_include_path(implode(PATH_SEPARATOR,
				array(
			dirname(dirname(dirname(__FILE__))) . "/library/PEAR",
			dirname(dirname(dirname(__FILE__))) . "/library",
			get_include_path(),
		)));

// define application environment
if (file_exists(JPATH_ROOT . "/environment")) {
	define("APPLICATION_ENV", file_get_contents(JPATH_ROOT . "/environment"));
}
else {
	define("APPLICATION_ENV", "production");
}

// libs includes
require_once("HTML/Template/IT.php");
require_once("Zend/File/Transfer.php");

// joomla includes
jimport('joomla.database.table');
jimport('joomla.application.component.model');
jimport('joomla.application.component.view');
jimport('joomla.application.component.controller');

require_once(dirname(__FILE__) . "/../Core/Joomla/Model.php");
require_once(dirname(__FILE__) . "/../Core/Joomla/Manager.php");
require_once(dirname(__FILE__) . "/../Core/Joomla/ManagerList.php");
require_once(dirname(__FILE__) . '/../Core/Joomla/Controller.php');
require_once(dirname(__FILE__) . '/../Core/Joomla/EditController.php');

// core includes
require_once(dirname(__FILE__) . "/../TeamTime.php");
require_once(dirname(__FILE__) . '/DateTools.php');
require_once(dirname(__FILE__) . '/Acl.php');
require_once(dirname(__FILE__) . '/Calendar/Event.php');
require_once(dirname(__FILE__) . "/Undefined.php");
require_once(dirname(__FILE__) . "/EventDispatcher.php");
require_once(dirname(__FILE__) . "/HelpersDispatcher.php");

require_once(dirname(__FILE__) . "/Bootstrap.php");
require_once(dirname(__FILE__) . "/EventHandlers.php");
require_once(dirname(__FILE__) . "/Helpers.php");

// add Base event handlers
TeamTime_EventDispatcher::add(new TeamTime_EventHandlers_Base());

// add Base helpers
TeamTime_HelpersDispatcher::add(new TeamTime_Helpers_Base());

TeamTime::init();