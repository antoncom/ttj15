<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

$user = & JFactory::getUser();
if ($user->guest) {
	JError::raiseWarning(500, JText::_('ALERTNOTAUTH'));
}
else {
	// custom initialisation here
	set_include_path(implode(PATH_SEPARATOR,
					array(
				realpath(JPATH_COMPONENT_ADMINISTRATOR . "/library"),
				get_include_path(),
			)));

	require_once(JPATH_ADMINISTRATOR .
			'/components/com_teamtime/library/TeamTime/init.php');

	JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

	define("TEAMTIMEATTACHMENTS_ASSETS",
			"components/com_teamtimeattachments/assets/");
	define("URl_TEAMTIMEATTACHMENTS_ASSETS",
			JURI::base() .
			"components/com_teamtimeattachments/assets/");

	// add helpers, css, js
	JHTML::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers');

	JHTML::stylesheet('default.css', TEAMTIMEATTACHMENTS_ASSETS . 'css/');

	JHTML::script('default.js', TEAMTIMEATTACHMENTS_ASSETS . 'js/');

	// get request vars
	$controller = JRequest::getWord('controller');
	if ($controller == "") {
		$controller = "attachments";
	}
	$task = JRequest::getCmd('task');

	require_once(JPATH_COMPONENT . '/controllers/' . $controller . '.php');

	// custom init
	// ...
	//require_once(JPATH_COMPONENT . '/models/viewedit.php');

	$className = 'TeamtimeAttachmentsController' . $controller;
	$controller = new $className();

	// perform the request task
	$controller->execute($task);
	$controller->redirect();
}