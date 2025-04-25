<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.plugin.plugin');

class plgSystemJSecure extends JPlugin {
	
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @since 1.5
	 */
	function plgSystemCanonicalization(& $subject, $config) {
		parent :: __construct($subject, $config);
	}
	
	function onAfterDispatch() {
		// Register the needed session variables
		$session =& JFactory::getSession();

		$plugin =& JPluginHelper::getPlugin( 'system', 'jsecure' );
		
		$params = new JParameter($plugin->params);

		$my =& JFactory::getUser();
		$checkedKey = $session->get('jSecureAuthentication');

		if (empty($checkedKey)) {
			if((preg_match("/administrator.index.?\.php$/", $_SERVER['SCRIPT_NAME'])) && 
				!$my->id && $params->get('key') != $_SERVER['QUERY_STRING']) {

				$config =& JFactory::getConfig();				
				$path = $params->get('options') == 1 ? JURI::root(true).'/'.$params->get('custom_path') : JURI::root(true);
				$app =& JFactory::getApplication();
				$app->redirect($path);
			}  else {
				$session->set('jSecureAuthentication', 1);
			}
		}
	}
		
}

