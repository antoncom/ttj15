<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// load related classes
require_once(dirname(__FILE__).DS.'object.php');
require_once(dirname(__FILE__).DS.'user.php');
require_once(dirname(__FILE__).DS.'log.php');
require_once(dirname(__FILE__).DS.'project.php');
require_once(dirname(__FILE__).DS.'task.php');
require_once(dirname(__FILE__).DS.'todo.php');
require_once(dirname(__FILE__).DS.'type.php');

/*
   Class: YFactory
   The Factory Class. Provides global access to application objects.
*/
class YFactory extends JFactory {

 	/*
    	Function: getUser
    	Override. Returns a user object instance.
 	*/
	function &getUser($id = null) {
		jimport('joomla.user.user');

		if(is_null($id)) {
			$session =& YFactory::getSession();
			$juser =& $session->get('user');
			$id = is_a($juser, 'JUser') ? $juser->id : 0;
		}

		$instance =& YUser::getInstance($id);
		return $instance;
	}

}