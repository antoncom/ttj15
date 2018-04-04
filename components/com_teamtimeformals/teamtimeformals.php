<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . '/administrator/components/com_teamtime/library/TeamTime/init.php');

require_once(JPATH_COMPONENT . DS . 'controller.php' );

// Require specific controller if requested
$controller = JRequest::getWord('controller');
if ($controller != "") {
	$path = JPATH_COMPONENT . DS . 'controllers' . DS . $controller . '.php';
	if (file_exists($path)) {
		require_once $path;
	}
	else {
		$controller = '';
	}
}

// Create the controller
$classname = 'TeamlogFormalController' . $controller;
$controller = new $classname();

// Perform the Request task
$controller->execute(JRequest::getWord('task'));

// Redirect if set by the controller
$controller->redirect();