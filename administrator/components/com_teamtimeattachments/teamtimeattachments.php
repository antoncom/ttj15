<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

set_include_path(implode(PATH_SEPARATOR,
				array(
			realpath(JPATH_COMPONENT . "/library"),
			get_include_path(),
		)));

require_once(JPATH_ADMINISTRATOR .
		'/components/com_teamtime/library/TeamTime/init.php');

require_once('Core/EditController.php');
require_once('Core/Model.php');
//require_once('Core/ListModel.php');

JTable::addIncludePath(JPATH_COMPONENT . '/tables');

define('TEAMTIMEATTACHMENTS_ICON', 'teamtimeattachments.png');
define('TEAMTIMEATTACHMENTS_TOOLBAR_TITLE', JText::_('Component toolbar title') . ' - ');

define("TEAMTIMEATTACHMENTS_ASSETS", "administrator/components/com_teamtimeattachments/assets/");
define("URl_TEAMTIMEATTACHMENTS_ASSETS",
		JURI::base() .
		"components/com_teamtimeattachments/assets/");

// add helpers, css, js
JHTML::addIncludePath(JPATH_COMPONENT . '/helpers');

JHTML::stylesheet('default.css', TEAMTIMEATTACHMENTS_ASSETS . 'css/');

JHTML::script('default.js', TEAMTIMEATTACHMENTS_ASSETS . 'js/');

// get request vars
$controller = JRequest::getWord('controller');
if ($controller == "") {
	$controller = "item";
}
$task = JRequest::getCmd('task');

require_once(JPATH_COMPONENT . '/controllers/' . $controller . '.php');

// custom init
// ...
require_once(JPATH_COMPONENT . '/models/item.php');
require_once(JPATH_COMPONENT . '/models/items.php');

// init component menu
JSubMenuHelper::addEntry(JText::_("Items"),
		"index.php?option=com_teamtimeattachments&controller=item", $controller == "item");

$className = 'TeamtimeAttachmentsController' . $controller;
$controller = new $className();

// perform the request task
$controller->execute($task);
$controller->redirect();