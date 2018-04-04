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
 * @version     $Id: toolbar.jsecurelite.html.php  $
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
class TOOLBAR_jsecurelite {

 	function _help(){
		JToolBarHelper::title( JText::_( 'jSecure Lite Help' ), 'generic.png' );
	} 
	function _DEFAULT(){
		JToolBarHelper::title( JText::_( 'jSecure Lite' ), 'generic.png' );
		JToolBarHelper::save();
		JToolBarHelper::custom( 'help', 'help.png', 'help.png', 'Help', false, false );
	
	}
	
}

?>