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
 * @version     $Id: controller.php  $
 */
	
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');
$l['b']	= 'BASIC_CONFIG';
$l['h']		= 'HELP';
$task= JRequest::getVar( 'task', '', '', 'string', JREQUEST_ALLOWRAW );
if ($task == '' || $task == 'basic' || $task == 'cancel') {
	JSubMenuHelper::addEntry(JText::_($l['b']), 'index.php?option=com_jsecurelite', true);
	JSubMenuHelper::addEntry(JText::_($l['h']), 'index.php?option=com_jsecurelite&task=help');
}

if ($task == 'help') {
	JSubMenuHelper::addEntry(JText::_($l['b']), 'index.php?option=com_jsecurelite');
	JSubMenuHelper::addEntry(JText::_($l['h']), 'index.php?option=com_jsecurelite&task=help', true);
}

class jsecureliteControllerjsecurelite extends JController
{
	function display(){
        $view   = $this->getView('basic','html');
	 	$view->display();
	}

   function help(){
	 	$view   = $this->getView('help','html');
	 	$view->display();
	}
	
	function saveBasic(){

		$view   = $this->getView('basic','html');
	 	$view->save();
 	
	}

	function getVersion(){
  	$params = &JComponentHelper::getParams( 'com_jsecurelite' );
	$versionPresent = $params->get( 'version' );
	echo $versionPresent;
  	exit;
 	}

}