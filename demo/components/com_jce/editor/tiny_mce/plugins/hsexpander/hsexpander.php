<?php
/**
* @version $Id: link.php 2008-02-20 Ryan Demmer $
* @package JCE
* @copyright Copyright (C) 2006-2007 Ryan Demmer. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

$version = "1.5.0";

require_once( JCE_LIBRARIES .DS. 'classes' .DS. 'editor.php' );
require_once( JCE_LIBRARIES .DS. 'classes' .DS. 'plugin.php' );
require_once( JCE_LIBRARIES .DS. 'classes' .DS. 'utils.php' );

$HsExpander =& JContentEditorPlugin::getInstance();

$HsExpander->checkPlugin() or die( 'Restricted access' );
// Load Plugin Parameters
$params	= $HsExpander->getPluginParams();

// Load Languages
$HsExpander->loadLanguages();

// Set javascript file array
$HsExpander->script( array(
	'tiny_mce_popup',
), 'tiny_mce' );
$HsExpander->script( array(
	'tiny_mce_utils',
	'mootools',
	'jce',
	'plugin',
	'window'
) );
$HsExpander->script( array( 'hsexpander' ), 'plugins' );
// Set css file array
$HsExpander->css( array( 'hsexpander' ), 'plugins' );
$HsExpander->css( array( 'plugin', 'tree' ) );
$HsExpander->css( array(
	'window',
	'dialog'
), 'skins' );

// Process any XHR requests
$HsExpander->processXHR( true );

$HsExpander->_debug = false;
$version .= $HsExpander->_debug ? ' - debug' : '';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $HsExpander->getLanguageTag();?>" lang="<?php echo $HsExpander->getLanguageTag();?>" dir="<?php echo $HsExpander->getLanguageDir();?>" >
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo JText::_('PLUGIN TITLE');?> : <?php echo $version;?></title>
<?php
$HsExpander->printScripts();
$HsExpander->printCss();

jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );

// path to images directory
$path		= JPATH_ROOT.DS."plugins".DS."content".DS."highslide".DS."graphics".DS."outlines";
$filter		= ".png";
$files		= JFolder::files($path, $filter);

$options = "";

if ( is_array($files) )
{
	foreach ($files as $file)
	{
		$file = JFile::stripExt( $file );
		$options .= '<option value="'.$file.'">'.$file."</option>\n";
	}
}

?>
	<script type="text/javascript">
		function initHsExpander(){
			return new HsExpander({
				lang: '<?php echo $HsExpander->getLanguage(); ?>',
				alerts: <?php echo $HsExpander->getAlerts();?>,
				params: {
					'defaults': {
						'targetlist': "<?php echo $params->get('target', 'default');?>"
					}
				}
			});
		}
	</script>
    <?php echo $HsExpander->printHead();?>
</head>
<body lang="<?php echo $HsExpander->getLanguage();?>" id="HsExpander">
	<form onsubmit="insertAction();return false;" action="#">
	<div class="tabs">
		<ul>
			<li id="expander_tab" class="current"><span><a href="javascript:mcTabs.displayTab('expander_tab','expander_panel');" onmousedown="return false;"><?php echo JText::_('EXPANDER TAB');?></a></span></li>
			<li id="options_tab"><span><a href="javascript:mcTabs.displayTab('options_tab','options_panel');" onmousedown="return false;"><?php echo JText::_('OPTIONS TAB');?></a></span></li>
			<li id="caption_tab"><span><a href="javascript:mcTabs.displayTab('caption_tab','caption_panel');" onmousedown="return false;"><?php echo JText::_('CAPTION TAB');?></a></span></li>
			<li id="heading_tab"><span><a href="javascript:mcTabs.displayTab('heading_tab','heading_panel');" onmousedown="return false;"><?php echo JText::_('HEADING TAB');?></a></span></li>
			<li id="overlay_tab"><span><a href="javascript:mcTabs.displayTab('overlay_tab','overlay_panel');" onmousedown="return false;"><?php echo JText::_('OVERLAY TAB');?></a></span></li>
		</ul>
	</div>
	<div class="panel_wrapper" style="border-bottom:0px;">
		<div id="expander_panel" class="panel current">
			<fieldset>
				<legend><?php echo JText::_('POPUP IMAGE');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td class="nowrap"><label id="hreflabel" for="href" class="hastip" title="<?php echo JText::_('POPUPURL DESC');?>"><?php echo JText::_('POPUPURL');?></label></td>
						<td><input id="href" type="text" value="" size="150" /></td>
                        <td id="hrefbrowsercontainer">&nbsp;</td>
					</tr>
					<tr>
						<td class="nowrap"><label id="titlelabel" for="title" class="hastip" title="<?php echo JText::_('POPUPTITLE DESC');?>"><?php echo JText::_('POPUPTITLE');?></label></td>
						<td colspan="3"><input id="title" name="title" type="text" value="" /></td>
					</tr>
					<tr>
						<td class="column1"><label id="idlabel" for="id" class="hastip" title="<?php echo JText::_('ID DESC');?>"><?php echo JText::_('ID');?></label></td>
						<td><input id="id" type="text" value="" /></td>
					</tr>
					<tr>
						<td><label id="stylelabel" for="style" class="hastip" title="<?php echo JText::_('STYLE DESC');?>"><?php echo JText::_('Style');?></label></td>
						<td><input type="text" id="style" value="" /></td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('THUMBNAIL IMAGE');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td class="nowrap"><label id="srclabel" for="src" class="hastip" title="<?php echo JText::_('THUMBURL DESC');?>"><?php echo JText::_('THUMBURL');?></label></td>
						<td><input id="src" type="text" value="" size="150" /></td>
                        <td id="srcbrowsercontainer">&nbsp;</td>
					</tr>
					<tr>
						<td class="nowrap"><label id="imgtitlelabel" for="imgtitle" class="hastip" title="<?php echo JText::_('THUMBTITLE DESC');?>"><?php echo JText::_('THUMBTITLE');?></label></td>
						<td colspan="3"><input id="imgtitle" name="imgtitle" type="text" value="" /></td>
					</tr>
					<tr>
						<td><label id="altlabel" for="alt" class="hastip" title="<?php echo JText::_('THUMBALT DESC');?>"><?php echo JText::_('THUMBALT');?></label></td>
						<td><input type="text" id="alt" value="" /></td>
					</tr>
					<tr>
						<td><label id="imgclasslabel" for="imgclass" class="hastip" title="<?php echo JText::_('CLASS DESC');?>"><?php echo JText::_('CLASS');?></label></td>
						<td><input id="imgclass" type="text" value="" /></td>
					</tr>
					<tr>
						<td><label id="imgidlabel" for="imgid" class="hastip" title="<?php echo JText::_('ID DESC');?>"><?php echo JText::_('ID');?></label></td>
						<td><input id="imgid" type="text" value="" onchange="return HsExpanderDialog.mirrorValue( this, 'thumbid' );"/></td>
					</tr>
					<tr>
						<td><label id="imgstylelabel" for="imgstyle" class="hastip" title="<?php echo JText::_('STYLE DESC');?>"><?php echo JText::_('Style');?></label></td>
						<td><input type="text" id="imgstyle" value="" /></td>
					</tr>
					<tr>
						<td><label id="widthlabel" for="width" class="hastip" title="<?php echo JText::_('WIDTH DESC');?>"><?php echo JText::_('WIDTH');?></label></td>
						<td><input type="text" id="width" value="" /></td>
					</tr>
					<tr>
						<td><label id="heightlabel" for="height" class="hastip" title="<?php echo JText::_('HEIGHT DESC');?>"><?php echo JText::_('HEIGHT');?></label></td>
						<td><input type="text" id="height" value="" /></td>
					</tr>
				</table>
			</fieldset>
				<label id="unobtrusivelabel" for="unobtrusive" class="hastip" title="<?php echo JText::_('UNOBTRUSIVE DESC');?>"><?php echo JText::_('UNOBTRUSIVE');?></label>
				<input type="checkbox" id="unobtrusive" onclick="return HsExpanderDialog.setTabs();"/>
		</div>
		<div id="options_panel" class="panel">
			<fieldset>
				<legend><?php echo JText::_('INLINEOPTS');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td><label id="wrapperclasslabel" for="wrapperclass" class="hastip" title="<?php echo JText::_('WRAPPERCLASS DESC');?>"><?php echo JText::_('WRAPPERCLASS');?></label></td>
						<td><input type="text" id="wrapperclass" value="" /></td>
					</tr>
					<tr>
						<td><label id="slideshowgrouplabel" for="slideshowgroup" class="hastip" title="<?php echo JText::_('SLIDESHOWGROUP DESC');?>"><?php echo JText::_('SLIDESHOWGROUP');?></label></td>
						<td><input type="text" id="slideshowgroup" value="" /></td>
					</tr>
					<tr>
						<td><label id="thumbnailidlabel" for="thumbnailid" class="hastip" title="<?php echo JText::_('THUMBNAILID DESC');?>"><?php echo JText::_('THUMBNAILID');?></label></td>
						<td><input type="text" id="thumbnailid" value="" /></td>
					</tr>
					<tr>
						<td class="column1"><label id="autoplaylabel" for="autoplay" class="hastip" title="<?php echo JText::_('AUTOPLAY DESC');?>"><?php echo JText::_('AUTOPLAY');?></label></td>
						<td><select id="autoplay" name="autoplay" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="true">Yes</option>
								<option value="false">No</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label id="targetxlabel" for="targetx" class="hastip" title="<?php echo JText::_('TARGETX DESC');?>"><?php echo JText::_('TARGETX');?></label></td>
						<td><input type="text" id="targetx" value="" />
						<label id="targetylabel" for="targety" class="hastip" title="<?php echo JText::_('TARGETY DESC');?>"><?php echo JText::_('TARGETY');?></label>
						<input type="text" id="targety" value="" /></td>
					</tr>
					<tr>
						<td class="column1"><label id="useboxlabel" for="usebox" class="hastip" title="<?php echo JText::_('USEBOX DESC');?>"><?php echo JText::_('USEBOX');?></label></td>
						<td><select id="usebox" name="usebox" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="true">Yes</option>
								<option value="false">No</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label id="cbwidthlabel" for="cbwidth" class="hastip" title="<?php echo JText::_('CBWIDTH DESC');?>"><?php echo JText::_('CBWIDTH');?></label></td>
						<td><input type="text" id="cbwidth" value="" />
						<label id="cbheightlabel" for="cbheight" class="hastip" title="<?php echo JText::_('CBHEIGHT DESC');?>"><?php echo JText::_('CBHEIGHT');?></label>
						<input type="text" id="cbheight" value=""/></td>
					</tr>
					<tr>
						<td><label id="minwidthlabel" for="minwidth" class="hastip" title="<?php echo JText::_('MINWIDTH DESC');?>"><?php echo JText::_('MINWIDTH');?></label></td>
						<td><input type="text" id="minwidth" value="" />
						<label id="minheightlabel" for="minheight" class="hastip" title="<?php echo JText::_('MINHEIGHT DESC');?>"><?php echo JText::_('MINHEIGHT');?></label>
						<input type="text" id="minheight" value=""/></td>
					</tr>
					<tr>
						<td class="column1"><label id="outlinetypelabel" for="outlinetype" class="hastip" title="<?php echo JText::_('OUTLINETYPE DESC');?>"><?php echo JText::_('OUTLINETYPE');?></label></td>
						<td><select id="outlinetype" name="outlinetype" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<?php echo $options; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="outlinewhileanimatinglabel" for="outlinewhileanimating" class="hastip" title="<?php echo JText::_('OUTLINEWHILEANIMATING DESC');?>"><?php echo JText::_('OUTLINEWHILEANIMATING');?></label></td>
						<td><select id="outlinewhileanimating" name="outlinewhileanimating" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="true">Yes</option>
								<option value="false">No</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="allowsizereductionlabel" for="allowsizereduction" class="hastip" title="<?php echo JText::_('ALLOWSIZEREDUCTION DESC');?>"><?php echo JText::_('ALLOWSIZEREDUCTION');?></label></td>
						<td><select id="allowsizereduction" name="allowsizereduction" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="true">Yes</option>
								<option value="false">No</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="alignlabel" for="align" class="hastip" title="<?php echo JText::_('ALIGN DESC');?>"><?php echo JText::_('ALIGN');?></label></td>
						<td><select id="align" name="align" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="auto">Auto</option>
								<option value="center">Center</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="anchorlabel" for="anchor" class="hastip" title="<?php echo JText::_('ANCHOR DESC');?>"><?php echo JText::_('ANCHOR');?></label></td>
						<td><select id="anchor" name="anchor" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="auto">Auto</option>
								<option value="top">Top</option>
								<option value="top right">Top, right</option>
								<option value="top left">Top, left</option>
								<option value="bottom">Bottom</option>
								<option value="bottom right">Bottom, right</option>
								<option value="bottom left">Bottom, left</option>
								<option value="right">Right</option>
								<option value="left">Left</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="easinglabel" for="easing" class="hastip" title="<?php echo JText::_('EASING DESC');?>"><?php echo JText::_('EASING');?></label></td>
						<td><select id="easing" name="easing" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="easeInQuad">EaseInQuad</option>
								<option value="linearTween">LinearTween</option>
								<option value="easeOutQuad">EaseOutQuad</option>
								<option value="easeInOutQuad">EaseInOutQuad</option>
								<option value="easeInCubic">EaseInCubic</option>
								<option value="easeOutCubic">EaseOutCubic</option>
								<option value="easeInOutCubic">EaseInOutCubic</option>
								<option value="easeInQuart">EaseInQuart</option>
								<option value="easeOutQuart">EaseOutQuart</option>
								<option value="easeInOutQuart">EaseInOutQuart</option>
								<option value="easeInQuint">EaseInQuint</option>
								<option value="easeOutQuint">EaseOutQuint</option>
								<option value="easeInOutQuint">EaseInOutQuint</option>
								<option value="easeInSine">EaseInSine</option>
								<option value="easeOutSine">EaseOutSine</option>
								<option value="easeInOutSine">EaseInOutSine</option>
								<option value="easeInExpo">EaseInExpo</option>
								<option value="easeOutExpo">EaseOutExpo</option>
								<option value="easeInOutExpo">EaseInOutExpo</option>
								<option value="easeInCirc">EaseInCirc</option>
								<option value="easeOutCirc">EaseOutCirc</option>
								<option value="easeInOutCirc">EaseInOutCirc</option>
								<option value="easeInElastic">EaseInElastic</option>
								<option value="easeOutElastic">EaseOutElastic</option>
								<option value="easeInOutElastic">EaseInOutElastic</option>
								<option value="easeInBack">EaseInBack</option>
								<option value="easeOutBack">EaseOutBack</option>
								<option value="easeInOutBack">EaseInOutBack</option>
								<option value="easeInBounce">EaseInBounce</option>
								<option value="easeOutBounce">EaseOutBounce</option>
								<option value="easeInOutBounce">EaseInOutBounce</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="easingcloselabel" for="easingclose" class="hastip" title="<?php echo JText::_('EASINGCLOSE DESC');?>"><?php echo JText::_('EASINGCLOSE');?></label></td>
						<td><select id="easingclose" name="easingclose" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="easeInQuad">EaseInQuad</option>
								<option value="linearTween">LinearTween</option>
								<option value="easeOutQuad">EaseOutQuad</option>
								<option value="easeInOutQuad">EaseInOutQuad</option>
								<option value="easeInCubic">EaseInCubic</option>
								<option value="easeOutCubic">EaseOutCubic</option>
								<option value="easeInOutCubic">EaseInOutCubic</option>
								<option value="easeInQuart">EaseInQuart</option>
								<option value="easeOutQuart">EaseOutQuart</option>
								<option value="easeInOutQuart">EaseInOutQuart</option>
								<option value="easeInQuint">EaseInQuint</option>
								<option value="easeOutQuint">EaseOutQuint</option>
								<option value="easeInOutQuint">EaseInOutQuint</option>
								<option value="easeInSine">EaseInSine</option>
								<option value="easeOutSine">EaseOutSine</option>
								<option value="easeInOutSine">EaseInOutSine</option>
								<option value="easeInExpo">EaseInExpo</option>
								<option value="easeOutExpo">EaseOutExpo</option>
								<option value="easeInOutExpo">EaseInOutExpo</option>
								<option value="easeInCirc">EaseInCirc</option>
								<option value="easeOutCirc">EaseOutCirc</option>
								<option value="easeInOutCirc">EaseInOutCirc</option>
								<option value="easeInElastic">EaseInElastic</option>
								<option value="easeOutElastic">EaseOutElastic</option>
								<option value="easeInOutElastic">EaseInOutElastic</option>
								<option value="easeInBack">EaseInBack</option>
								<option value="easeOutBack">EaseOutBack</option>
								<option value="easeInOutBack">EaseInOutBack</option>
								<option value="easeInBounce">EaseInBounce</option>
								<option value="easeOutBounce">EaseOutBounce</option>
								<option value="easeInOutBounce">EaseInOutBounce</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="fadeinoutlabel" for="fadeinout" class="hastip" title="<?php echo JText::_('FADEINOUT DESC');?>"><?php echo JText::_('FADEINOUT');?></label></td>
						<td><select id="fadeinout" name="fadeinout" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="true">Yes</option>
								<option value="false">No</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="openonhoverlabel" for="openonhover" class="hastip" title="<?php echo JText::_('OPENONHOVER DESC');?>"><?php echo JText::_('OPENONHOVER');?></label></td>
						<td><input class="checkbox" type="checkbox" id="openonhover" name="openonhover"/></td>
					</tr>
					<tr>
						<td class="column1"><label id="dragbyheadinglabel" for="dragbyheading" class="hastip" title="<?php echo JText::_('DRAGBYHEADING DESC');?>"><?php echo JText::_('DRAGBYHEADING');?></label></td>
						<td><select id="dragbyheading" name="dragbyheading">
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="true">Yes</option>
								<option value="false">No</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label id="dimmingopacitylabel" for="dimmingopacity" class="hastip" title="<?php echo JText::_('DIMMINGOPACITY DESC');?>"><?php echo JText::_('DIMMINGOPACITY');?></label></td>
						<td><input type="text" id="dimmingopacity" value="" /></td>
					</tr>
					<tr>
						<td class="column1"><label id="numberpositionlabel" for="numberposition" class="hastip" title="<?php echo JText::_('NUMBERPOSITION DESC');?>"><?php echo JText::_('NUMBERPOSITION');?></label></td>
						<td><select id="numberposition" name="numberposition" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="caption">Caption</option>
								<option value="heading">Heading</option>
								<option value="null">None</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="nowrap"><label id="psrclabel" for="psrc" class="hastip" title="<?php echo JText::_('SRC DESC');?>"><?php echo JText::_('SRC');?></label></td>
						<td><input id="psrc" type="text" value="" size="150" /></td>
                        <td id="psrcbrowsercontainer">&nbsp;</td>
					</tr>
					<tr>
					<td class="column1"><label id="crvpositionlabel" for="crvposition" class="hastip" title="<?php echo JText::_('CRVPOSITION DESC');?>"><?php echo JText::_('CRVPOSITION');?></label></td>
						<td><select id="crvposition" name="crvposition" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="above">Above</option>
								<option value="top">Top</option>
								<option value="middle">Middle</option>
								<option value="bottom">Bottom</option>
								<option value="below">Below</option>
							</select>
						</td>
					</tr>
					<tr>
					<td class="column1"><label id="crhpositionlabel" for="crhposition" class="hastip" title="<?php echo JText::_('CRHPOSITION DESC');?>"><?php echo JText::_('CRHPOSITION');?></label></td>
						<td><select id="crhposition" name="crhposition" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="leftpanel">Left Panel</option>
								<option value="left">Left</option>
								<option value="center">Center</option>
								<option value="right">Right</option>
								<option value="rightpanel">Right Panel</option>
							</select>
						</td>
					</tr>
					<tr>
					<td class="column1"><label id="transitionslabel" for="transitions" class="hastip" title="<?php echo JText::_('TRANSITIONS DESC');?>"><?php echo JText::_('TRANSITIONS');?></label></td>
						<td><select id="transitions" name="transitions" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
            					<option value="'fade'">Fade</option>
								<option value="'fade', 'crossfade'">Fade, Crossfade</option>
            					<option value="'fade', 'expand'">Fade, Expand</option>
<!--            				<option value="'crossfade'">Crossfade</option> -->
<!--            				<option value="'crossfade', 'fade'">Crossfade, Expand</option> -->
<!--            				<option value="'crossfade', 'expand'">Crossfade, Expand</option> -->
            					<option value="'expand'">Expand</option>
            					<option value="'expand', 'fade'">Expand, Fade</option>
            					<option value="'expand', 'crossfade'">Expand, Crossfade</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label id="captiontextlabel" for="captiontext" class="hastip" title="<?php echo JText::_('CAPTIONTEXT DESC');?>"><?php echo JText::_('CAPTIONTEXT');?></label></td>
						<td><input type="text" id="captiontext" value="" /></td>
					</tr>
					<tr>
						<td><label id="headingtextlabel" for="headingtext" class="hastip" title="<?php echo JText::_('HEADINGTEXT DESC');?>"><?php echo JText::_('HEADINGTEXT');?></label></td>
						<td><input type="text" id="headingtext" value="" /></td>
					</tr>
				</table>
			</fieldset>
		</div>
		<div id="caption_panel" class="panel">
			<fieldset>
				<legend><?php echo JText::_('GENERAL');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td class="column1"><label id="captionidlabel" for="captionid" class="hastip" title="<?php echo JText::_('CAPTIONID DESC');?>"><?php echo JText::_('CAPTIONID');?></label></td>
						<td><input id="captionid" type="text" value="" /></td>
					</tr>
					<tr>
						<td><label id="captionstylelabel" for="captionstyle" class="hastip" title="<?php echo JText::_('STYLE DESC');?>"><?php echo JText::_('STYLE');?></label></td>
						<td><input type="text" id="captionstyle" value="" /></td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('CAPTIONHTMLTEXT');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td><textarea id="caption" rows="15" cols="80" ></textarea></td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('CAPTIONOVERLAY');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td class="column1"><label id="coenableoverlaylabel" for="coenableoverlay" class="hastip" title="<?php echo JText::_('COENABLEOVERLAY DESC');?>"><?php echo JText::_('COENABLEOVERLAY');?></label></td>
						<td><input class="checkbox" type="checkbox" id="coenableoverlay" name="coenableoverlay"/></td>
					</tr>
					<tr>
						<td class="column1"><label id="cofadelabel" for="cofade" class="hastip" title="<?php echo JText::_('COFADE DESC');?>"><?php echo JText::_('COFADE');?></label></td>
						<td><select id="cofade" name="cofade" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="0">No</option>
								<option value="1">Yes</option>
								<option value="2">Not in IE</option>
							</select>
						</td>
					</tr>
					<tr>
					<td class="column1"><label id="covpositionlabel" for="covposition" class="hastip" title="<?php echo JText::_('COVPOSITION DESC');?>"><?php echo JText::_('COVPOSITION');?></label></td>
						<td><select id="covposition" name="covposition" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="above">Above</option>
								<option value="top">Top</option>
								<option value="middle">Middle</option>
								<option value="bottom">Bottom</option>
								<option value="below">Below</option>
							</select>
						</td>
					</tr>
					<tr>
					<td class="column1"><label id="cohpositionlabel" for="cohposition" class="hastip" title="<?php echo JText::_('COHPOSITION DESC');?>"><?php echo JText::_('COHPOSITION');?></label></td>
						<td><select id="cohposition" name="cohposition" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="leftpanel">Left Panel</option>
								<option value="left">Left</option>
								<option value="center">Center</option>
								<option value="right">Right</option>
								<option value="rightpanel">Right Panel</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label id="cooffsetxlabel" for="cooffsetx" class="hastip" title="<?php echo JText::_('COOFFSETX DESC');?>"><?php echo JText::_('COOFFSETX');?></label></td>
						<td><input type="text" id="cooffsetx" value="" /></td>
					</tr>
					<tr>
						<td><label id="cooffsetylabel" for="cooffsety" class="hastip" title="<?php echo JText::_('COOFFSETY DESC');?>"><?php echo JText::_('COOFFSETY');?></label></td>
						<td><input type="text" id="cooffsety" value="" /></td>
					</tr>
					<tr>
					<td class="column1"><label id="corelativetolabel" for="corelativeto" class="hastip" title="<?php echo JText::_('CORELATIVETO DESC');?>"><?php echo JText::_('CORELATIVETO');?></label></td>
						<td><select id="corelativeto" name="corelativeto" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="viewport">Viewport</option>
								<option value="expander">Expander</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="cohideonmouseoutlabel" for="cohideonmouseout" class="hastip" title="<?php echo JText::_('COHIDEONMOUSEOUT DESC');?>"><?php echo JText::_('COHIDEONMOUSEOUT');?></label></td>
						<td><select id="cohideonmouseout" name="cohideonmouseout" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="true">Yes</option>
								<option value="false">No</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label id="coopacitylabel" for="coopacity" class="hastip" title="<?php echo JText::_('COOPACITY DESC');?>"><?php echo JText::_('COOPACITY');?></label></td>
						<td><input type="text" id="coopacity" value="" /></td>
					</tr>
					<tr>
						<td><label id="cowidthlabel" for="cowidth" class="hastip" title="<?php echo JText::_('COWIDTH DESC');?>"><?php echo JText::_('COWIDTH');?></label></td>
						<td><input type="text" id="cowidth" value="" /></td>
					</tr>
					<tr>
						<td><label id="coclassnamelabel" for="coclassname" class="hastip" title="<?php echo JText::_('COCLASSNAME DESC');?>"><?php echo JText::_('COCLASSNAME');?></label></td>
						<td><input type="text" id="coclassname" value="" /></td>
					</tr>
				</table>
			</fieldset>
		</div>
		<div id="heading_panel" class="panel">
			<fieldset>
				<legend><?php echo JText::_('GENERAL');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td class="column1"><label id="headingidlabel" for="headingid" class="hastip" title="<?php echo JText::_('HEADINGID DESC');?>"><?php echo JText::_('HEADINGID');?></label></td>
						<td><input id="headingid" type="text" value="" /></td>
					</tr>
					<tr>
						<td><label id="headingstylelabel" for="headingstyle" class="hastip" title="<?php echo JText::_('STYLE DESC');?>"><?php echo JText::_('STYLE');?></label></td>
						<td><input type="text" id="headingstyle" value="" /></td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('HEADINGHTMLTEXT');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td><textarea id="heading" rows="15"  cols="80" ></textarea></td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('HEADINGOVERLAY');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td class="column1"><label id="hoenableoverlaylabel" for="hoenableoverlay" class="hastip" title="<?php echo JText::_('HOENABLEOVERLAY DESC');?>"><?php echo JText::_('HOENABLEOVERLAY');?></label></td>
						<td><input class="checkbox" type="checkbox" id="hoenableoverlay" name="hoenableoverlay"/></td>
					</tr>
					<tr>
						<td class="column1"><label id="hofadelabel" for="hofade" class="hastip" title="<?php echo JText::_('HOFADE DESC');?>"><?php echo JText::_('HOFADE');?></label></td>
						<td><select id="hofade" name="hofade" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="0">No</option>
								<option value="1">Yes</option>
								<option value="2">Not in IE</option>
							</select>
						</td>
					</tr>
					<tr>
					<td class="column1"><label id="hovpositionlabel" for="hovposition" class="hastip" title="<?php echo JText::_('HOVPOSITION DESC');?>"><?php echo JText::_('HOVPOSITION');?></label></td>
						<td><select id="hovposition" name="hovposition" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="above">Above</option>
								<option value="top">Top</option>
								<option value="middle">Middle</option>
								<option value="bottom">Bottom</option>
								<option value="below">Below</option>
							</select>
						</td>
					</tr>
					<tr>
					<td class="column1"><label id="hohpositionlabel" for="hohposition" class="hastip" title="<?php echo JText::_('HOHPOSITION DESC');?>"><?php echo JText::_('HOHPOSITION');?></label></td>
						<td><select id="hohposition" name="hohposition" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="leftpanel">Left Panel</option>
								<option value="left">Left</option>
								<option value="center">Center</option>
								<option value="right">Right</option>
								<option value="rightpanel">Right Panel</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label id="hooffsetxlabel" for="hooffsetx" class="hastip" title="<?php echo JText::_('HOOFFSETX DESC');?>"><?php echo JText::_('HOOFFSETX');?></label></td>
						<td><input type="text" id="hooffsetx" value="" /></td>
					</tr>
					<tr>
						<td><label id="hooffsetylabel" for="hooffsety" class="hastip" title="<?php echo JText::_('HOOFFSETY DESC');?>"><?php echo JText::_('HOOFFSETY');?></label></td>
						<td><input type="text" id="hooffsety" value="" /></td>
					</tr>
					<tr>
					<td class="column1"><label id="horelativetolabel" for="horelativeto" class="hastip" title="<?php echo JText::_('HORELATIVETO DESC');?>"><?php echo JText::_('HORELATIVETO');?></label></td>
						<td><select id="horelativeto" name="horelativeto" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="viewport">Viewport</option>
								<option value="expander">Expander</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="hohideonmouseoutlabel" for="hohideonmouseout" class="hastip" title="<?php echo JText::_('HOHIDEONMOUSEOUT DESC');?>"><?php echo JText::_('HOHIDEONMOUSEOUT');?></label></td>
						<td><select id="hohideonmouseout" name="hohideonmouseout" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="true">Yes</option>
								<option value="false">No</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label id="hoopacitylabel" for="hoopacity" class="hastip" title="<?php echo JText::_('HOOPACITY DESC');?>"><?php echo JText::_('HOOPACITY');?></label></td>
						<td><input type="text" id="hoopacity" value="" /></td>
					</tr>
					<tr>
						<td><label id="howidthlabel" for="howidth" class="hastip" title="<?php echo JText::_('HOWIDTH DESC');?>"><?php echo JText::_('HOWIDTH');?></label></td>
						<td><input type="text" id="howidth" value="" /></td>
					</tr>
					<tr>
						<td><label id="hoclassnamelabel" for="hoclassname" class="hastip" title="<?php echo JText::_('HOCLASSNAME DESC');?>"><?php echo JText::_('HOCLASSNAME');?></label></td>
						<td><input type="text" id="hoclassname" value="" /></td>
					</tr>
				</table>
			</fieldset>
		</div>
		<div id="overlay_panel" class="panel">
			<fieldset>
				<legend><?php echo JText::_('OVERLAY');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td class="column1"><label id="thumbidlabel" for="thumbid" class="hastip" title="<?php echo JText::_('THUMBID DESC');?>"><?php echo JText::_('THUMBID');?></label></td>
						<td><input id="thumbid" type="text" value="" onchange="return HsExpanderDialog.mirrorValue( this, 'imgid' );"/></td>
					</tr>
					<tr>
						<td class="column1"><label id="overlayidlabel" for="overlayid" class="hastip" title="<?php echo JText::_('OVERLAYID DESC');?>"><?php echo JText::_('OVERLAYID');?></label></td>
						<td><input id="overlayid" type="text" value="" /></td>
					</tr>
					<tr>
						<td><label id="overlaystylelabel" for="overlaystyle" class="hastip" title="<?php echo JText::_('STYLE DESC');?>"><?php echo JText::_('STYLE');?></label></td>
						<td><input type="text" id="overlaystyle" value="" /></td>
					</tr>
					<tr>
						<td class="column1"><label id="ovfadelabel" for="ovfade" class="hastip" title="<?php echo JText::_('OVFADE DESC');?>"><?php echo JText::_('OVFADE');?></label></td>
						<td><select id="ovfade" name="ovfade" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="0">No</option>
								<option value="1">Yes</option>
								<option value="2">Not in IE</option>
							</select>
						</td>
					</tr>
					<tr>
					<td class="column1"><label id="ovvpositionlabel" for="ovvposition" class="hastip" title="<?php echo JText::_('OVVPOSITION DESC');?>"><?php echo JText::_('OVVPOSITION');?></label></td>
						<td><select id="ovvposition" name="ovvposition" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="above">Above</option>
								<option value="top">Top</option>
								<option value="middle">Middle</option>
								<option value="bottom">Bottom</option>
								<option value="below">Below</option>
							</select>
						</td>
					</tr>
					<tr>
					<td class="column1"><label id="ovhpositionlabel" for="ovhposition" class="hastip" title="<?php echo JText::_('OVHPOSITION DESC');?>"><?php echo JText::_('OVHPOSITION');?></label></td>
						<td><select id="ovhposition" name="ovhposition" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="leftpanel">Left Panel</option>
								<option value="left">Left</option>
								<option value="center">Center</option>
								<option value="right">Right</option>
								<option value="rightpanel">Right Panel</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label id="ovoffsetxlabel" for="ovoffsetx" class="hastip" title="<?php echo JText::_('OVOFFSETX DESC');?>"><?php echo JText::_('OVOFFSETX');?></label></td>
						<td><input type="text" id="ovoffsetx" value="" /></td>
					</tr>
					<tr>
						<td><label id="ovooffsetylabel" for="ovoffsety" class="hastip" title="<?php echo JText::_('OVOFFSETY DESC');?>"><?php echo JText::_('OVOFFSETY');?></label></td>
						<td><input type="text" id="ovoffsety" value="" /></td>
					</tr>
					<tr>
					<td class="column1"><label id="ovrelativetolabel" for="ovrelativeto" class="hastip" title="<?php echo JText::_('OVRELATIVETO DESC');?>"><?php echo JText::_('OVRELATIVETO');?></label></td>
						<td><select id="ovrelativeto" name="ovrelativeto" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="viewport">Viewport</option>
								<option value="expander">Expander</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="ovhideonmouseoutlabel" for="ovhideonmouseout" class="hastip" title="<?php echo JText::_('OVHIDEONMOUSEOUT DESC');?>"><?php echo JText::_('OVHIDEONMOUSEOUT');?></label></td>
						<td><select id="ovhideonmouseout" name="ovhideonmouseout" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="true">Yes</option>
								<option value="false">No</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label id="ovopacitylabel" for="ovopacity" class="hastip" title="<?php echo JText::_('OVOPACITY DESC');?>"><?php echo JText::_('OVOPACITY');?></label></td>
						<td><input type="text" id="ovopacity" value="" /></td>
					</tr>
					<tr>
						<td><label id="ovwidthlabel" for="ovwidth" class="hastip" title="<?php echo JText::_('OVWIDTH DESC');?>"><?php echo JText::_('OVWIDTH');?></label></td>
						<td><input type="text" id="ovwidth" value="" /></td>
					</tr>
					<tr>
						<td><label id="ovclassnamelabel" for="ovclassname" class="hastip" title="<?php echo JText::_('OVCLASSNAME DESC');?>"><?php echo JText::_('OVCLASSNAME');?></label></td>
						<td><input type="text" id="ovclassname" value="" /></td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('OVERLAYHTMLTEXT');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td><textarea id="overlay" rows="16"  cols="80" ></textarea></td>
					</tr>
				</table>
			</fieldset>
		</div>
		</div>
		<div class="mceActionPanel">
			<div style="float: left">
				<input type="button" id="insert" name="insert" value="<?php echo JText::_('Insert');?>" onclick="HsExpanderDialog.insert();" />
			</div>
			<div style="float: right">
				<input type="button" class="button" id="help" name="help" value="<?php echo JText::_('Help');?>" onclick="HsExpander.openHelp();" />
				<input type="button" id="cancel" name="cancel" value="<?php echo JText::_('Cancel');?>" onclick="tinyMCEPopup.close();" />
			</div>
		</div>
		<div id="hidden_elements">
			<input type="hidden" id="onclick" value="" />
			<input type="hidden" id="onmouseover" value="" />
		</div>
    </form>
</body>
</html>