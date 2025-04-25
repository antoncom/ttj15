<?php

require_once(dirname(__FILE__) . "/EventHandlers.php");
require_once(dirname(__FILE__) . "/Helpers.php");

// add Formals event handlers
TeamTime_EventDispatcher::add(new TeamTime_EventHandlers_Formals());

// add Formals helpers
TeamTime_HelpersDispatcher::add(new TeamTime_Helpers_Formals());