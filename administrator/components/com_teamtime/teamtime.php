<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(dirname(__FILE__) . '/library/TeamTime/init.php');

$bootstrap = new TeamTime_Bootstrap_Base();
$bootstrap->run();