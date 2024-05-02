<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ADMINISTRATOR . '/components/com_teamtime/library/TeamTime/init.php');

$bootstrap = new TeamTime_Bootstrap_Formals();
$bootstrap->run();