<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ADMINISTRATOR . '/components/com_teamtime/library/TeamTime/init.php');

JHTML::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_teamtimebpm/helpers');
require_once(JPATH_ADMINISTRATOR . '/components/com_teamtimebpm/helpers/teamtimebpm.php');

// add jhtml path, css, js
//JHTML::stylesheet('errorvectortable.css', "administrator/components/{$option}/assets/css/");

$user = & JFactory::getUser();
if ($user->guest) {
	JError::raiseWarning(500, JText::_('ALERTNOTAUTH'));
}
else {
	// get request vars
	$option = JRequest::getWord('option');
	$controller = JRequest::getWord('controller');

	//set default controller
	if ($controller == "") {
		$controller = "TeamtimebpmTest";
	}

	$task = JRequest::getCmd('task');

	// load controller
	require_once(JPATH_COMPONENT . "/controllers/{$controller}.php");

	// perform the request task
	$classname = $controller . 'Controller';
	$controller = new $classname();
	$controller->execute($task);
	$controller->redirect();
}