<?php

require_once(dirname(__FILE__) . "/Bootstrap.php");
require_once(dirname(__FILE__) . "/EventHandlers.php");
require_once(dirname(__FILE__) . "/Helpers.php");

// add Dotu event handlers
TeamTime_EventDispatcher::add(new TeamTime_EventHandlers_Dotu());

// add Dotu helpers
TeamTime_HelpersDispatcher::add(new TeamTime_Helpers_Dotu());