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
 * @version     $Id: jsecure.class.php  $
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class jsecurelite {
		

	
	function checkUrlKey($JSecureliteConfig){
		$my =& JFactory::getUser();
		if((preg_match("/administrator\/*index.?\.php$/i", $_SERVER['PHP_SELF']))) {
			if(!$my->id && $JSecureliteConfig->key != md5(base64_encode($_SERVER['QUERY_STRING']))) {
					
				return false;
			} else {
				return true;
		    }
		}
	}
	
	function formAction($JSecureliteConfig){
							
		$oriKey           = JRequest::getVar('passkey','');
		$userkey          = md5(base64_encode(JRequest::getVar('passkey','')));
		$passkey          = $JSecureliteConfig->key;
		if($userkey != $passkey){
			return false;
		} else {
		  	return true;
		}
	}	


	function displayForm(){
		$document =& JFactory::getDocument();
		$document->addStyleSheet(JURI::base()."plugins/system/jsecurelite/css/jsecurelite.css");
?>
		<link href='../plugins/system/jsecurelite/css/jsecurelite.css' rel='stylesheet' type='text/css' />
		<form name="key" action="index.php" method="POST" autocomplete="off">
		<table align="center" border="0">
		<tr>
			<td class="pad">
				<fieldset class="fieldset">
					<legend><?php echo JText::_( 'ADMINISTRATION_LOGIN' );?></legend>
					<table cellpadding="5" cellspacing="0" border="0" align="center" class="innerTable">
						<tr>
							<td><?php echo JText::_( 'ENTER_KEY_VALUE' );?></td>
						</tr>
						<tr>
							<td>
							<input type="text" name="passkey" />
							</td>
						</tr>
						<tr>
							<td align="right">
								<input type="submit" name="submit" value="submit"/>
							</td>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>
		</table>
		</form>
<?php
	}
}
?>