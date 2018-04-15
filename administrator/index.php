<?php
/**
* @version		$Id: index.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Set flag that this is a parent file
define( '_JEXEC', 1 );

define('JPATH_BASE', dirname(__FILE__) );

define('DS', DIRECTORY_SEPARATOR);

require_once( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once( JPATH_BASE .DS.'includes'.DS.'framework.php' );
require_once( JPATH_BASE .DS.'includes'.DS.'helper.php' );
require_once( JPATH_BASE .DS.'includes'.DS.'toolbar.php' );

JDEBUG ? $_PROFILER->mark( 'afterLoad' ) : null;


jimport('joomla.plugin.helper');

/**
 * CREATE THE APPLICATION
 *
 * NOTE :
 */
$mainframe =& JFactory::getApplication('administrator');

// ant addon
$userM = JFactory::getUser();

$notLoggedYet = !(isset($userM->id) && $userM->id > 0);
$allowedReferer = ['localhost:8000', 'telemetry.mediapublish.ru'];
if($notLoggedYet)	{
	if ((strpos($_SERVER['HTTP_REFERER'], $allowedReferer[0]) != false
		|| strpos($_SERVER['HTTP_REFERER'], $allowedReferer[1]) != false))	{

		$u = JRequest::getVar('username', '', 'GET', 'username');
		$p = JRequest::getVar('passwd', '', 'GET', 'passwd');

		if(strlen($u) > 0 && strlen($p) > 0)	{
			$credentials = array();
			$credentials['username'] = JRequest::getVar('username', '', 'GET', 'username');
			$credentials['password'] = JRequest::getVar('passwd', '', 'GET', 'passwd');

			//perform the login action
			$error = $mainframe->login($credentials);
			$userM = JFactory::getUser();
		}
	}
	else{
		error_log('====LOGIN ATTEMPT FROM: ' . $_SERVER['HTTP_REFERER'], 3, "/home/mediapub/teamlog.teamtime.info/docs/logs/my-errors.log");
	}
}



// end of 'ant addon'




/**
 * INITIALISE THE APPLICATION
 *
 * NOTE :
 */
$mainframe->initialise(array(
	'language' => $mainframe->getUserState( "application.lang", 'lang' )
));

JPluginHelper::importPlugin('system');

// trigger the onAfterInitialise events
JDEBUG ? $_PROFILER->mark('afterInitialise') : null;
$mainframe->triggerEvent('onAfterInitialise');

/**
 * ROUTE THE APPLICATION
 *
 * NOTE :
 */
$mainframe->route();

// trigger the onAfterRoute events
JDEBUG ? $_PROFILER->mark('afterRoute') : null;
$mainframe->triggerEvent('onAfterRoute');

/**
 * DISPATCH THE APPLICATION
 *
 * NOTE :
 */
$option = JAdministratorHelper::findOption();
$mainframe->dispatch($option);

// trigger the onAfterDispatch events
JDEBUG ? $_PROFILER->mark('afterDispatch') : null;
$mainframe->triggerEvent('onAfterDispatch');

/**
 * RENDER THE APPLICATION
 *
 * NOTE :
 */
$mainframe->render();

// trigger the onAfterRender events
JDEBUG ? $_PROFILER->mark( 'afterRender' ) : null;
$mainframe->triggerEvent( 'onAfterRender' );

/**
 * RETURN THE RESPONSE
 */
echo JResponse::toString($mainframe->getCfg('gzip'));
?>