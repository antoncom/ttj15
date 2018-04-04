<?php
/**
 * jsecure Lite components for Joomla!
 * jsecure Lite extention prevents access to administration (back end)
 * login page without appropriate access key.
 *
 * @author      $Author: Ajay Lulia $
 * @copyright   Joomla Service Provider - 2012
 * @package     jSecure Lite 1.0
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     $Id: uninstall.jsecurelite.php  $
 */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

$database	= & JFactory::getDBO();
jimport('joomla.filesystem.file');

// remove system plugin
	$database->setQuery( "DELETE FROM `#__plugins` WHERE `element`= 'jsecurelite';");
	$database->query();

	JFile::delete( JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'jsecurelite.php' );
	JFile::delete( JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'jsecurelite.xml' );
	JFile::delete(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'404.html'); 
	JFile::delete(JPATH_ADMINISTRATOR.DS.'language'.DS.'en-GB'.DS.'en-GB.plg_system_jsecurelite.ini');

	JFile::delete(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'jsecurelite'.DS.'jsecurelite.class.php');
	JFile::delete(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'jsecurelite'.DS.'css'.DS.'jsecurelite.css');

    JFile::delete(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'jsecurelite'.DS.'css'.DS.'index.html');
	JFile::delete(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'jsecurelite'.DS.'index.html');

	rmdir(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'jsecurelite'.DS.'css');
	rmdir(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'jsecurelite');

	echo '<h3>jSecure Lite has been succesfully uninstalled.</h3>';