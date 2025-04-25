<?php defined('_JEXEC') or die('Restricted access');

JHTML::_('script', 'dotab.js', 'administrator/components/com_hsconfig/js/' );
JHTML::_('script', 'ssscreenshot.js', 'administrator/components/com_hsconfig/js/' );
JHTML::_('script', 'opexample.js', 'administrator/components/com_hsconfig/js/' );
JHTML::_('stylesheet', 'ssscreenshot.css', 'administrator/components/com_hsconfig/css/' );
JHTML::_('stylesheet', 'opexample.css', 'administrator/components/com_hsconfig/css/' );

if ($this->hsconfig->id == 0)
{?>
<script language="javascript" type="text/javascript">
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	if (getSelectedValue('adminForm','cid') < 1) {
		alert( "<?php echo JText::_( 'Please select an article' ).'.'; ?>" , true);
	} else {
		submitform( pressbutton );
	}
}
</script>
<?php
}
else
{?>
<script language="javascript" type="text/javascript">
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	submitform( pressbutton );
}
</script>
<?php
}
?>
<script language="javascript" type="text/javascript">
var dotab = new doTab();
var ssscreenshot = new SsScreenshot();
var opexample = new OpExample();
window.addEvent('domready', function() {
	dotab.init();
	ssscreenshot.init( { base: '<?php echo JURI::root(true); ?>' });
	opexample.init( { base: '<?php echo JURI::root(true); ?>' });
    });
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
    <div id="config-document">
		<div class="configuration">
		<?php if($this->hsconfig->id == 0)
		{
			$arraylist[]	= JHTML::_('select.option',  '0', JText::_( 'Select Article' ), 'id', 'title' );
			$arraylist		= array_merge( $arraylist, $this->articlelist );
			echo JHTML::_('select.genericlist', $arraylist, 'cid', 'class="inputbox" size="1"','id', 'title' );
		}
		else
		{
			if ($this->hsconfig->id == -1)
			{
				echo JText::_('Site Configuration');
			}
			else
			{
				echo $this->hsconfig->title;
			}
		}
		?>
		</div>
		<?php
			require_once(dirname(__FILE__).DS.'navigation.php');
		?>
        <div id="page-general">
            <table class="noshow">
			<tr>
			<td align="left" class="key">
                    <fieldset class="adminform">
                        <legend><?php echo JText::_( 'General Settings' ); ?></legend>
                      <?php if($output = $this->params->render('params', 'general')) :
                                  echo $output;
                            else :
                                  echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('No Parameters')."</div>";
                             endif;?>
                     </fieldset>
                    </td>
                </tr>
            </table>
        </div>
        <div id="page-popup">
            <table class="noshow">
			<tr>
			<td align="left" class="key">
                    <fieldset class="adminform">
                        <legend><?php echo JText::_( 'Content and Positioning' ); ?></legend>
                      <?php if($output = $this->params->render('params', 'popupcontent')) :
								echo $output;
							else :
								echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('No Parameters')."</div>";
							endif;?>
                     </fieldset>
                    </td>
                    <td>
                     <fieldset class="adminform">
                        <legend><?php echo JText::_( 'Behavior' ); ?></legend>
                      <?php if($output = $this->params->render('params', 'popupbehavior')) :
								echo $output;
							else :
								echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('No Parameters')."</div>";
							endif;?>
                     </fieldset>
                    </td>
                </tr>
            </table>
        </div>
        <div id="page-overlays">
            <table width="100%" class="noshow" cellspacing="1">
			<tr>
			<td align="left" class="key">
	        <fieldset class="adminform">
            <legend><?php echo JText::_( 'Caption Overlay' ); ?></legend>
                        <?php if($output = $this->params->render('params', 'captionoverlay')) :
								echo $output;
							else :
								echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('No Parameters')."</div>";
							endif;?>
			</fieldset>
            <fieldset class="adminform">
            <legend><?php echo JText::_( 'Overlay Presets' ); ?></legend>
                        <?php if($output = $this->params->render('params', 'overlaypresets')) :
							echo $output;
						else :
							echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('No Parameters')."</div>";
						endif;?>
 			</fieldset>
            <fieldset class="adminform">
            <legend><?php echo JText::_( 'Custom Overlay Settings' ); ?></legend>
                        <?php if($output = $this->params->render('params', 'customoverlay')) :
							echo $output;
						else :
							echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('No Parameters')."</div>";
						endif;?>
 			</fieldset>
   			</td>
			<td class="key">
	        <fieldset class="adminform">
            <legend><?php echo JText::_( 'Heading Overlay' ); ?></legend>
                        <?php if($output = $this->params->render('params', 'headingoverlay')) :
							echo $output;
						else :
							echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('No Parameters')."</div>";
						endif;?>
			</fieldset>
	   		<fieldset class="adminform">
			<legend><?php echo JText::_('Overlay Preset Example'); ?></legend>
				<table class="noshow" cellspacing="1" >
				<tr>
				<td width="40%" align="left" class="key">
				<div id="overlayexample" >
					<img id="overlayexample_image" src="<?php echo JURI::root(true); ?>/administrator/components/com_hsconfig/presets/overlay/none-selected.jpg" alt="Overlay example" style="visibility: visible;display:block" />
					<?php echo HsConfigsViewHsConfig::renderOverlayExampleInfos(); ?>
				</div>
				</td>
				</tr>
				</table>
			</fieldset>
   			</td>
			</tr>
			</table>
        <fieldset class="adminform">
            <legend><?php echo JText::_( 'Custom Overlay HTML' ); ?></legend>
            <table width="100%" cellspacing="1">
				<tr>
					<td align="left"><textarea name="overlayhtml" cols="120" rows="10" id="overlayhtml" ><?php echo htmlspecialchars($this->hsconfig->overlayhtml, ENT_COMPAT, 'UTF-8');?></textarea>
           			</td>
				</tr>
			</table>
		</fieldset>
		</div>
        <div id="page-slideshow">
            <table width="100%" class="noshow">
			<tr>
			<td class="key">
	   		<fieldset class="adminform">
            <legend><?php echo JText::_( 'Slideshow Presets' ); ?></legend>
                        <?php if($output = $this->params->render('params', 'slideshowpresets')) :
							echo $output;
						else :
							echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('No Parameters')."</div>";
						endif;?>
            </fieldset>
	   		<fieldset class="adminform">
			<legend><?php echo JText::_('Screenshot'); ?></legend>
				<table class="noshow" cellspacing="1" >
				<tr>
				<td width="40%" align="left" class="key">
				<div id="slideshowscreenshot">
					<img id="slideshowscreenshot_image" src="<?php echo JURI::root(true); ?>/administrator/components/com_hsconfig/presets/slideshow/none-selected.jpg" alt="Screenshot" style="visibility: visible;display:block" />
					<?php echo HsConfigsViewHsConfig::renderScreenshotInfos(); ?>
				</div>
				</td>
				</tr>
				</table>
			</fieldset>
			</td>
			<td class="key">
	   		<fieldset class="adminform">
            <legend><?php echo JText::_( 'Slideshow Settings' ); ?></legend>
                        <?php if($output = $this->params->render('params', 'slideshow')) :
							echo $output;
						else :
							echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('No Parameters')."</div>";
						endif;?>
            </fieldset>
	   		<fieldset class="adminform">
            <legend><?php echo JText::_( 'Thumbstrip Settings' ); ?></legend>
                        <?php if($output = $this->params->render('params', 'thumbstrip')) :
						echo $output;
					else :
						echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('No Parameters')."</div>";
					endif;?>
            </fieldset>
			</td>
			</tr>
			</table>
		</div>
        <div id="page-csslink">
            <table width="100%" class="noshow" cellspacing="1">
			<tr>
			<td align="left" class="css_value">
	        <fieldset class="adminform">
            <legend><?php echo JText::_( 'Highslide Cascading Style Sheet Settings' ); ?></legend>
				<textarea name="css" cols="120" rows="40" id="css" ><?php echo $this->hsconfig->css;?></textarea>
			</fieldset>
   			</td>
			</tr>
			</table>
		</div>
        <div id="page-html">
            <table class="noshow" cellspacing="1">
			<tr>
			<td align="left" class="key">
            <fieldset class="adminform">
            	<legend><?php echo JText::_( 'HTML Extensions' ); ?></legend>
                	<?php if($output = $this->params->render('params', 'htmlextensions')) :
						echo $output;
					else :
						echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('No Parameters')."</div>";
					endif;?>
            </fieldset>
			</td>
			<td class="key">
            <fieldset class="adminform">
            	<legend><?php echo JText::_( 'Flash' ); ?></legend>
                	<?php if($output = $this->params->render('params', 'flash')) :
						echo $output;
					else :
						echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('No Parameters')."</div>";
					endif;?>
            </fieldset>
			</td>
			</tr>
			</table>
            <table width="100%" class="noshow" cellspacing="1">
			<tr>
			<td>
	        <fieldset class="adminform">
            <legend><?php echo JText::_( 'Skin' ); ?></legend>
                	<?php if($output = $this->params->render('params', 'htmlskin')) :
						echo $output;
					else :
						echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('No Parameters')."</div>";
					endif;?>
				<fieldset>
            	<legend><?php echo JText::_( 'Controls' ); ?></legend>
				<textarea name="skincontrols" cols="120" rows="20" id="skincontrols" ><?php echo htmlspecialchars($this->hsconfig->skincontrols, ENT_COMPAT, 'UTF-8');?></textarea>
				</fieldset>
				<fieldset>
            	<legend><?php echo JText::_( 'Content' ); ?></legend>
				<textarea name="skincontent" cols="120" rows="20" id="skincontent" ><?php echo htmlspecialchars($this->hsconfig->skincontent, ENT_COMPAT, 'UTF-8');?></textarea>
				</fieldset>
			</fieldset>
   			</td>
			</tr>
			</table>
		</div>
        <div id="page-language">
            <table class="noshow">
			<tr>
			<td align="left" class="key">
	   		<fieldset class="adminform">
            <legend><?php echo JText::_( 'Language Strings' ); ?>
				<span class="error hasTip" title="<?php echo JText::_( 'Warning' );?>::<?php echo JText::_( 'WARNLANGUAGEOVERRIDE' ); ?>">
					<?php echo HsConfigsViewHsConfig::WarningIcon(); ?>
				</span>
			</legend>
                        <?php if($output = $this->params->render('params', 'languagestrings')) :
						echo $output;
					else :
						echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('No Parameters')."</div>";
					endif;?>
            </fieldset>
			</td>
			</tr>
			</table>
		</div>
    </div>


<div class="clr"></div>

<input type="hidden" name="option" value="com_hsconfig" />
<input type="hidden" name="id" value="<?php echo $this->hsconfig->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="hsconfig" />
</form>