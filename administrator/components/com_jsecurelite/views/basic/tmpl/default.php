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
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$JSecureliteConfig = $this->JSecureliteConfig;
$document =& JFactory::getDocument();
$document->addScript(JURI::base()."components/com_jsecurelite/js/basic.js");
$document->addScript(JURI::base()."components/com_jsecurelite/js/jquery1.8.min.js");
$document->addScriptDeclaration("window.onload = function(){showUpdates();}");
?>

<table width="100%"><tr><td width="65%" valign="top">
<form action="index.php" method="post" name="adminForm" onsubmit="return submitbutton();" style="padding:31px 0 0 0" autocomplete="off">
<fieldset class="adminform"><legend><?php echo JText::_('BASIC_CONFIG');?></legend>
	<table class="admintable">
	<tr valign="top">
		<td class="paramlist_key">
		<span class="editlinktip hasTip" title="Enable">
			<?php echo JText::_('ENABLE'); ?>
		</span>
		</td>
		<td class="paramlist_value">
			<select name="publish" id="publish" style="width:100px">
				<option value="0" <?php echo ($JSecureliteConfig->publish == 0)?"selected":''; ?>><?php echo JText::_('NO'); ?></option>
				<option value="1" <?php echo ($JSecureliteConfig->publish == 1)?"selected":''; ?>><?php echo JText::_('YES'); ?></option>
			</select>
		</td>
		<td class="paramlist_description">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('PUBLISHED_DESCRIPTION'); ?>">
					<img src="<?php echo JURI::root(); ?>includes/js/ThemeOffice/tooltip.png" border="0">
				</label>
			</span>	
		</td>			
	</tr>	
	<tr>
		<td class="paramlist_key">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('KEY_DESCRIPTION'); ?>">
					<?php echo JText::_('PASS_KEY'); ?>
				</label>
			</span>		
		</td>
		<td class="paramlist_value">
			<select name="passkeytype" style="width:100px">
				<?php
				$url  = $form = '';
				$url  = ($JSecureliteConfig->passkeytype == "url")? "selected" : "";
				$form = ($JSecureliteConfig->passkeytype == "form")? "selected" : "";
				if($form == '')
					$url = "selected";	 	
				?>
				<option value="url" <?php echo $url; ?>><?php echo JText::_('URL'); ?></option>
				<option value="form" <?php echo $form; ?>><?php echo JText::_('FORM'); ?></option>
			</select>
		</td>
		<td class="paramlist_description">
			<?php //echo JText::_('KEY_DESCRIPTION'); ?>		
		</td>			
	</tr>
	<tr>
		<td class="paramlist_key">
					<?php echo JText::_('KEY'); ?>
		</td>
		<td class="paramlist_value">
			<input type="password" name="key" value="" size="50" />
		</td>
		<td class="paramlist_description">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('KEY_DESCRIPTION'); ?>">
					<img src="<?php echo JURI::root(); ?>includes/js/ThemeOffice/tooltip.png" border="0">
				</label>
			</span>	
		</td>			
	</tr>
	<tr>
		<td class="paramlist_key">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('REDIRECT_OPTIONS_DESCRIPTION'); ?>">
					<?php echo JText::_('REDIRECT_OPTIONS'); ?>
				</label>
			</span>		
		</td>
		<td class="paramlist_value">
			<select name="options" id="options" style="width:150px" onchange="javascript: hideCustomPath(this);">
				<option value="0" <?php echo ($JSecureliteConfig->options == 0)?"selected":''; ?>><?php echo JText::_('REDIRECT_INDEX'); ?></option>
				<option value="1" <?php echo ($JSecureliteConfig->options == 1)?"selected":''; ?>><?php echo JText::_('CUSTOM_PATH'); ?></option>
			</select>
		</td>
		<td class="paramlist_description">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('REDIRECT_OPTIONS_DESCRIPTION'); ?>">
					<img src="<?php echo JURI::root(); ?>includes/js/ThemeOffice/tooltip.png" border="0">
				</label>
			</span>		
		</td>			
	</tr>
	<tr id="custom_path">
		<td class="paramlist_key">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('CUSTOM_PATH_DESCRIPTION'); ?>">
					<?php echo JText::_('CUSTOM_PATH'); ?>
				</label>
			</span>		
		</td>
		<td class="paramlist_value">
			<input name="custom_path" type="text" value="<?php echo $JSecureliteConfig->custom_path; ?>" size="50" />
		</td>
		<td class="paramlist_description">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('CUSTOM_PATH_DESCRIPTION'); ?>">
					<img src="<?php echo JURI::root(); ?>includes/js/ThemeOffice/tooltip.png" border="0">
				</label>
			</span>		
		</td>				
	</tr>
	</table>
</fieldset>

<input type="hidden" name="option" value="com_jsecurelite"/>
<input type="hidden" name="task" value="" />
</form>
</td>
<td align="right" valign="top"><a href="http://www.joomlaserviceprovider.com/component/docman/doc_details/2-jsecure-authentication.html" target="_block" class="btn1 blue-pill" >Get Premium Version</a>
<table cellpadding="4" cellspacing="0" border="1" class="adminform">
			
			<tr class="row0">
				<th colspan="2"  style="background-color:#FFF;">
						<div style="float:left;">
						<a href="http://www.joomlaserviceprovider.com" title="Joomla Service Provider" target="_blank"><img src="components/com_jsecurelite/images/logo.jpg" alt="Joomla Service Provider" border="none"/></a></div>
						<div style="text-align:center;margin-top:25px;"><h3><?php echo JText::_( 'jSecure Lite' ); ?></h3></div>
						
				</th>
			</tr>
			<tr class="row1">
				<td width="100"><?php echo JText::_( 'VERSION_TEXT' ); ?></td>
				<td><?php echo JText::_( 'VERSION_DESCRIPTION' ); ?></td>
			</tr>
			<tr class="row2">
				<td><?php echo JText::_( 'SUPPORT' ); ?></td>
				<td><a href="http://www.joomlaserviceprovider.com/component/kunena/38-jsecure-lite.html" target="_blank"><?php echo JText::_( 'JSECURE_AUTHENTICATION_FORUM' ); ?></a></td>
			</tr>
			<tr>
          <td><?php echo JText::_( 'UPDATES' ); ?></td>
         <td>
		 	<div id="image" name="image"><img src="components/com_jsecurelite/images/sh-ajax-loader-wide.gif" /></div>
		 	<div id="version"></div>
		  	<button id="chkupdates" class="btn1 grayLight" onclick="showUpdates();" href="#">Check For Update</button> 
		</td>
        </tr>
		
		<tr id="show_notes">
          <td><?php echo JText::_( 'NOTES' ); ?></td>
          <td><div id="notes"></div></td>
        </tr>
		</table>
	</td>
    </tr>
		</table>
</td>
</tr>

</table>

<script type="text/javascript">
	hideCustomPath(document.getElementById('options'));
</script>