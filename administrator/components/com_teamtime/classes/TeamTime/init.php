<?php

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

if (!class_exists("Services_JSON")) {
	require_once("Services/JSON.php");
}
require_once("HTML/Template/IT.php");

require_once("Zend/File/Transfer.php");

require_once(dirname(dirname(__FILE__)) . '/Calendar/Event.php');
require_once(dirname(dirname(__FILE__)) . '/factory.php');

require_once(dirname(__FILE__) . "/Undefined.php");
require_once(dirname(__FILE__) . "/EventDispatcher.php");
require_once(dirname(__FILE__) . "/HelpersDispatcher.php");

require_once(dirname(__FILE__) . "/EventHandlers.php");
require_once(dirname(__FILE__) . "/Helpers.php");

// add Base event handlers
TeamTime_EventDispatcher::add(new TeamTime_EventHandlers_Base());

// add Base helpers
TeamTime_HelpersDispatcher::add(new TeamTime_Helpers_Base());

TeamTime::init();