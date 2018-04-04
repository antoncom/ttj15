<?php

require_once(dirname(__FILE__) . "/EventHandlers.php");
require_once(dirname(__FILE__) . "/Helpers.php");

// add Calendar event handlers
TeamTime_EventDispatcher::add(new TeamTime_EventHandlers_Calendar());

// add Calendar helpers
TeamTime_HelpersDispatcher::add(new TeamTime_Helpers_Calendar());