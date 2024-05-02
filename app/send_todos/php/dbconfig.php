<?php

// Set flag that this is a parent file
define( '_JEXEC', 1 );

define('JPATH_BASE', realpath(dirname(__FILE__)."/../../../") );
define( 'DS', DIRECTORY_SEPARATOR );

//var_dump(JPATH_BASE);

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

//error_reporting(E_ALL ^ E_NOTICE);
//ini_set("display_errors", "off");
//ini_set("log_errors", 1);
//ini_set("error_log", "error_log");

//$mainframe =& JFactory::getApplication('site');

$mainframe =& JFactory::getApplication('administrator');
$mainframe->initialise(array(
	'language' => $mainframe->getUserState( "application.lang", 'lang' )
));

JPluginHelper::importPlugin('system');
$mainframe->triggerEvent('onAfterInitialise');

define('JPATH_COMPONENT', JPATH_BASE.'/administrator/components/com_teamlog' );

JHTML::addIncludePath(JPATH_COMPONENT.'/helpers');
JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');

//JHTML::script('default.js', '/administrator/components/com_teamlog/assets/js/');
//JHTML::stylesheet('default.css', '/administrator/components/com_teamlog/assets/css/');

require_once(JPATH_COMPONENT.DS.'classes'.DS.'factory.php');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'date.php');

require_once(dirname(__FILE__)."/Services/JSON.php");

jimport( 'joomla.application.component.model' );

require_once(JPATH_COMPONENT."/models/task.php");
require_once(JPATH_COMPONENT."/models/todo.php");

$lang =& JFactory::getLanguage();
$lang->load("com_teamlog", JPATH_BASE."/administrator");

class DBConnection{

	function getConnection(){
		if(substr($_SERVER["HTTP_HOST"], 0, 9) == "localhost"){
			$user = "root";
			$password = "root";
			$db = "teamlogdb";
		}
		else{
			$user = "teamlogdbu";
			$password = "tL3845915009";
			$db = "teamlogdb";
		}

	  //change to your database server/user name/password
		mysql_connect("localhost", $user, $password) or
			die("Could not connect: " . mysql_error());

		//change to your database name
		mysql_select_db($db) or
			die("Could not select database: " . mysql_error());
	}
	
}

if(!defined("is_cron")){
	// check for user - admin
	$user =& JFactory::getUser();

	if($user->usertype == "Super Administrator" || $user->usertype == "Administrator"){
		//...
	}
	else{
		print "...";
		exit();
	}
}

