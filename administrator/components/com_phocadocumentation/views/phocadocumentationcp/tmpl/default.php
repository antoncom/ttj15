<?php defined('_JEXEC') or die('Restricted access');?>

<form action="index.php" method="post" name="adminForm">
<table class="adminform">
	<tr>
		<td width="55%" valign="top">
			<div id="cpanel">
	<?php

	$link = 'index.php?option=com_phocadocumentation&view=phocadocumentations';
	echo PhocaDocumentationHelperControlPanel::quickIconButton( $link, 'icon-48-doc.png', JText::_( 'Documentation' ) );
	
	$link = 'index.php?option=com_sections&scope=content';
	echo PhocaDocumentationHelperControlPanel::quickIconButton( $link, 'icon-48-sec.png', JText::_( 'Sections' ) );
	
	$link = 'index.php?option=com_categories&section=com_content';
	echo PhocaDocumentationHelperControlPanel::quickIconButton( $link, 'icon-48-cat.png', JText::_( 'Categories' ) );
	
	$link = 'index.php?option=com_phocadocumentation&view=phocadocumentationin';
	echo PhocaDocumentationHelperControlPanel::quickIconButton( $link, 'icon-48-info.png', JText::_( 'Info' ) );
	?>
			
			<div style="clear:both">&nbsp;</div>
			<p>&nbsp;</p>
			<div style="text-align:center;padding:0;margin:0;border:0">
				<iframe style="padding:0;margin:0;border:0" src="http://www.phoca.cz/adv/phocadocumentation" noresize="noresize" frameborder="0" border="0" cellspacing="0" scrolling="no" width="500" marginwidth="0" marginheight="0" height="125">
				<a href="http://www.phoca.cz/adv/phocadocumentation" target="_blank">Phoca Documentation</a>
				</iframe> 
			</div>
			
			
			</div>
		</td>
		
		<td width="45%" valign="top">
			<div style="300px;border:1px solid #ccc;background:#fff;margin:15px;padding:15px">
			<div style="float:right;margin:10px;">
				<?php
					echo JHTML::_('image.site',  'logo-phoca.png', '/components/com_phocadocumentation/assets/images/', NULL, NULL, 'Phoca.cz' )
				?>
			</div>
			
			<h3><?php echo JText::_('Version');?></h3>
			<p><?php echo $this->version ;?></p>

			<h3><?php echo JText::_('Copyright');?></h3>
			<p>© 2007-2008 Jan Pavelka</p>
			<p><a href="http://www.phoca.cz/" target="_blank">www.phoca.cz</a></p>

			<h3><?php echo JText::_('License');?></h3>
			<p><a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GPLv2</a></p>
			<p>&nbsp;</p>
			
			
			<div id="pg-update"><a href="http://www.phoca.cz/version/index.php?phocadocumentation=<?php echo $this->version ;?>" target="_blank"><?php echo JText::_('Check for update'); ?></a></div>
			
			
			</div>
		</td>
	</tr>
</table>

<input type="hidden" name="option" value="com_phocadocumentation" />
<input type="hidden" name="view" value="phocadocumentationcp" />
<input type="hidden" name="<?php echo JUtility::getToken(); ?>" value="1" />
</form>