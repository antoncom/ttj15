<?php 
/**
 * jSecure Lite plugin for Joomla!
 * jSecure Lite extention prevents access to administration (back end)
 * login page without appropriate access key.
 *
 * @author      $Author: Ajay Lulia $
 * @copyright   Joomla Service Provider - 2012
 * @package     jSecure Lite 1.0
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     $Id: jsecurelite.php  $
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');
JPlugin::loadLanguage( 'plg_system_jsecurelite' );
require_once('jsecurelite/jsecurelite.class.php');

$basepath     = JPATH_ADMINISTRATOR .'/components/com_jsecurelite';
$configFile	  = $basepath.'/params.php';
				
require_once($configFile);		

class plgSystemJSecurelite extends JPlugin {
	
	function plgSystemCanonicalization(& $subject, $config) {
		parent :: __construct($subject, $config);
	}
	
	function onAfterDispatch() {
		global $mainframe;
		
		if (!$mainframe->isAdmin()) {
			return true; // Dont run in admin
		}
		
		$config        = new JConfig();
		$JSecureliteConfig = new JSecureliteConfig();
		$app           =& JFactory::getApplication();
		$path          = '';
		$path         .= $JSecureliteConfig->options == 1 ? JURI::root().$JSecureliteConfig->custom_path : JURI::root();
		$jsecure 	   =  new jsecurelite();
		$publish       = $JSecureliteConfig->publish;
		if(!$publish){			
			return true;
		}

		$session    =& JFactory::getSession();
		$checkedKey = $session->get('jSecureAuthentication');

		if(!empty($checkedKey)){			
			return true;
		}
		
		$submit       = JRequest::getVar('submit', '');
		$passkey      = $JSecureliteConfig->key;

		if($submit == 'submit'){			
			$resultFormAction = jsecurelite::formAction($JSecureliteConfig);
			
			if(!empty($resultFormAction)){
				$session->set('jSecureAuthentication', 1);
				return true;
			} else {
				$app->redirect($path);
			}
		}
		$task        = $JSecureliteConfig->passkeytype;
		switch($task){
			case 'form':
				jsecurelite::displayForm();
			exit;
			break;

			case 'url':
			default:
				$resultUrlKey = jsecurelite::checkUrlKey($JSecureliteConfig);
				if(!empty($resultUrlKey)){
					 $session->set('jSecureAuthentication', 1);
					 $link = JURI::root()."administrator/index.php?option=com_login";
					 $app->redirect($link);
				} else {
					$app->redirect($path);
				}
			break;
		}
	}
}
?>