<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

JHTML::script('jquery-1.7.1.min.js',
		'components/com_teamtimecreport/assets/js/libs/jquery/');
JHTML::script('jquery.noconflict.js',
		'components/com_teamtimecreport/assets/js/');

JHTML::script('jquery.mousewheel-3.0.6.pack.js',
		'components/com_teamtimecreport/assets/js/libs/jquery/');
JHTML::script('jquery.fancybox.js',
		'components/com_teamtimecreport/assets/js/libs/jquery/fancybox/');
JHTML::stylesheet('jquery.fancybox.css',
		'components/com_teamtimecreport/assets/js/libs/jquery/fancybox/');
JHTML::stylesheet('jquery.fancybox-buttons.css',
		'components/com_teamtimecreport/assets/js/libs/jquery/fancybox/helpers/');
JHTML::script('jquery.fancybox-buttons.js',
		'components/com_teamtimecreport/assets/js/libs/jquery/fancybox/helpers/');
JHTML::stylesheet('jquery.fancybox-thumbs.css',
		'components/com_teamtimecreport/assets/js/libs/jquery/fancybox/helpers/');
JHTML::script('jquery.fancybox-thumbs.js',
		'components/com_teamtimecreport/assets/js/libs/jquery/fancybox/helpers/');

JHTML::script('highslide-with-html.js',
		"components/com_teamtimecreport/assets/highslide/");
JHTML::stylesheet('highslide.css',
		"components/com_teamtimecreport/assets/highslide/");

// load mootools
JHTML::_('behavior.mootools');

require_once(JPATH_BASE . DS . '/administrator/components/com_teamtimecreport/teamtime_helpers.php');

require_once(JPATH_COMPONENT . DS . 'controller.php' );

// Require specific controller if requested
if ($controller = JRequest::getWord('controller')) {
	$path = JPATH_COMPONENT . DS . 'controllers' . DS . $controller . '.php';
	if (file_exists($path)) {
		require_once $path;
	}
	else {
		$controller = '';
	}
}

// Create the controller
$classname = 'TeamlogCreportController' . $controller;
$controller = new $classname( );

// Perform the Request task
$controller->execute(JRequest::getWord('task'));

// Redirect if set by the controller
$controller->redirect();