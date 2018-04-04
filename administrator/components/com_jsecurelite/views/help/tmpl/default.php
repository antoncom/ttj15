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
 * @version     $Id: default.php  $
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <TITLE> Readme </TITLE>
 </HEAD>

 <BODY>
	<form action="index.php" method="post" name="adminForm">
	<div style='width: 90%; text-align: left;'>
		<p><h2>Drawback:</h2></p>    
		<p>Joomla has one drawback, any web user can easily know the site is created in Joomla! by typing the URL to access the administration area (i.e. www.site name.com/administration). This makes hackers hack the site easily once they crack id and password for Joomla!. </p>    
		<p><h2>Instructions</h2></p>

		<p>jSecure Lite  prevents access to administration (back end) login page without appropriate access key.</p>

		<p><h2>Important! </h2></p>

		<p>In order for jSecure Lite to work the jSecure Lite <b>plugin</b> must be enabled. Go to Extensions>Plugin Manager and look for the "<b>System - jSecure Lite plugin</b>". Make sure this plug in is enabled.</p>

		<p><h2>Basic Configuration:</h2></p>

		<p>The basic configuration will hide your administrator URL from public access. This is all most people need.</p>

		<ol>
			<li>Set "enable" to "yes".</li>
			<li>Set the "Pass Key" to "URL" This will hide the administrator URL.</li>
			<li><p>In the "Key" field enter the key that will be part of your new administrator URL. For example, if you enter "test" into the key field, then the administrator URL will be http://www.yourwebsite/administrator/?test. Please note that you cannot have a key that is only numbers.
			<p>If you do not enter a key, but enable the jSecure Lite component, then the URL to access the administrator area is /?jSecure (http://www.yourwebsite/administrator/?jSecure).</li>
			<li>Set the "Redirect Options" field. By default, if someone tries to access you /administrator URL without the correct key, they will be redirected to the home page of your Joomla site. You can also set up a "Custom Path" is you would like the user to be redirected somewhere else, such as a 404 error page.</li>
		</ol>
		 
		
		<br>
		<p>
			<strong>License:</strong> This is free software and you may redistribute it under the GPL. jSecure Lite comes with absolutely no warranty. Use at your own risk. For details, see the license at http://www.gnu.org/licenses/gpl.txt Other licenses can be found in LICENSES folder.
		</p>
	</div>
	<input type="hidden" name="option" value="com_jsecurelite"/>
	<input type="hidden" name="task" value=""/>
	</form>
 </BODY>
</HTML>