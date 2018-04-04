<?php
/**
 * jSecure Lite components for Joomla!
 * jSecure Lite extention prevents access to administration (back end)
 * login page without appropriate access key.
 *
 * @author      $Author: Ajay Lulia $
 * @copyright   Joomla Service Provider - 2012
 * @package     jSecure Lite 1.0
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     $Id: admin.jsecurelite.php  $
 */
// no direct access
defined('_JEXEC') or die('Restricted Access');

// Require the base controller
require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'controller.php');

$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::base()."components/com_jsecurelite/css/jsecurelite.css");

// Create the controller
$controller    = new jsecureliteControllerjsecurelite();	

$controller->execute(JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();

?>