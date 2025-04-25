<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// get request vars
$option = JRequest::getWord('option');
$controller = JRequest::getWord('controller');
$task = JRequest::getCmd('task');

$enabledTasks = in_array($task,
				array("logs_client", "loadtasks",
			"loadprojects", "load_description", "loadReport")) ||
		in_array($controller, array("api", "report"));

$user = & JFactory::getUser();
if ($user->guest && !$enabledTasks) {
	JError::raiseWarning(500, JText::_('ALERTNOTAUTH'));
}
else {
	require_once(JPATH_ADMINISTRATOR . '/components/com_teamtime/library/TeamTime/init.php');

	if (JRequest::getVar('view') == "reports") {
		$controller = "reports";
	}

	TeamTime::helper()->getBase()->addJavaScript(array(
		"option" => $option,
		"controller" => $controller), true);

	require_once(JPATH_COMPONENT . DS . 'controller.php');
	require_once(JPATH_COMPONENT . DS . 'controllers' . DS . 'report.php');
	require_once(JPATH_COMPONENT . DS . 'controllers' . DS . 'reports.php');
	$className = 'TeamlogController' . $controller;
	if (!class_exists($className)) {
		require_once(JPATH_COMPONENT . '/controllers/' . $controller . '.php');
		$className = 'TeamtimeController' . $controller;
	}
	$controller = new $className();
	$controller->execute($task);
	$controller->redirect();
}