<?php
/**
 * Element include: Popup Page (start)
 * Include file to make a selfcontained page that has all the administrator variables and style
 *
 * @package    NoNumber! Elements
 * @version    1.0.3
 *
 * @author     Peter van Westen <peter@nonumber.nl>
 * @link       http://www.nonumber.nl
 * @copyright  Copyright (C) 2009 NoNumber! All Rights Reserved
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// Ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Restricted access' );

// Set the root paths
define( 'DS', DIRECTORY_SEPARATOR );

if ( !isset( $is_admin ) ) {
	$is_admin = 0;
}

$file_root	= str_replace( array( '\\', '/' ), DS, dirname( __FILE__ ) );
$file_root = explode( DS, $file_root );

foreach ( $file_root as $folder ) {
	if ( !in_array( $folder, array( 'administrator', 'components', 'modules', 'plugins', 'templates' ) ) ) {
		array_shift ( $file_root );
	} else {
		break;
	}
}

$root = '';
if ( $is_admin ) {
	$root = 'administrator';
}
$file_root = implode( DS, $file_root );
$root = str_replace( array( '\\', '/' ), DS, $root );

if ( !isset( $script_root ) ) {
	$script_root = $file_root;
}
$script_root = str_replace( '\\', '/', $script_root );

$_SERVER['PHP_SELF'] = str_replace( $script_root , str_replace( DS, '/', $root ), $_SERVER['PHP_SELF'] );
$_SERVER['SCRIPT_NAME'] = str_replace( $script_root , str_replace( DS, '/', $root ), $_SERVER['SCRIPT_NAME'] );

define( 'JPATH_BASE', dirname( str_replace( $file_root, $root, __FILE__ ) ) );
if ( $is_admin ) {

	// include all the administrator stuff
	require_once( JPATH_BASE .DS.'includes'.DS.'defines.php' );
	require_once( JPATH_BASE .DS.'includes'.DS.'framework.php' );
	require_once( JPATH_BASE .DS.'includes'.DS.'helper.php' );
	require_once( JPATH_BASE .DS.'includes'.DS.'toolbar.php' );

	// Set the template to component (so only the content)
	JRequest::SetVar( 'tmpl', 'component' );
	// this option returns an empty html body if no task is set
	JRequest::SetVar( 'option', 'com_admin' );


	// More admin stuff
	/* CREATE THE APPLICATION */
	$mainframe =& JFactory::getApplication( 'administrator' );
	/* INITIALISE THE APPLICATION */
	$mainframe->initialise( array(
		'language' => $mainframe->getUserState( "application.lang", 'lang' )
	) );
	/* ROUTE THE APPLICATION */
	$mainframe->route();
	/* DISPATCH THE APPLICATION */
	$option = JRequest::getCmd('option');
	$mainframe->dispatch( $option );
} else {
	// include all the administrator stuff
	require_once( JPATH_BASE .DS.'includes'.DS.'defines.php' );
	require_once( JPATH_BASE .DS.'includes'.DS.'framework.php' );

	// Set the template to component (so only the content)
	JRequest::SetVar( 'tmpl', 'component' );

	// More admin stuff
	/* CREATE THE APPLICATION */
	$mainframe =& JFactory::getApplication( 'site' );
	/* INITIALISE THE APPLICATION */
	$mainframe->initialise( array(
		'language' => $mainframe->getUserState( "application.lang", 'lang' )
	) );

	$mainframe->setTemplate( 'system' );
}

ob_start();