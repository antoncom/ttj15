<?php

require_once(dirname(__FILE__) . "/Bootstrap.php");
require_once(dirname(__FILE__) . "/EventHandlers.php");
require_once(dirname(__FILE__) . "/Helpers.php");

// add Bpmn event handlers
TeamTime_EventDispatcher::add(new TeamTime_EventHandlers_Bpmn());

// add Bpmn helpers
TeamTime_HelpersDispatcher::add(new TeamTime_Helpers_Bpmn());