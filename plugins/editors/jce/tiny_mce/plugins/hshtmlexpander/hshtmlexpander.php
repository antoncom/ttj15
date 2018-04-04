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

$HsHtmlExpander =& JContentEditorPlugin::getInstance();

$HsHtmlExpander->checkPlugin() or die( 'Restricted access' );
// Load Plugin Parameters
$params	= $HsHtmlExpander->getPluginParams();

// Load Languages
$HsHtmlExpander->loadLanguages();

// Set javascript file array
$HsHtmlExpander->script( array(
	'tiny_mce_popup',
), 'tiny_mce' );
$HsHtmlExpander->script( array(
	'tiny_mce_utils',
	'mootools',
	'jce',
	'plugin',
	'window'
) );
$HsHtmlExpander->script( array( 'hshtmlexpander' ), 'plugins' );
// Set css file array
$HsHtmlExpander->css( array( 'hshtmlexpander' ), 'plugins' );
$HsHtmlExpander->css( array( 'plugin', 'tree' ) );
$HsHtmlExpander->css( array(
	'window',
	'dialog'
), 'skins' );

// Process any XHR requests
$HsHtmlExpander->processXHR( true );

$HsHtmlExpander->_debug = false;
$version .= $HsHtmlExpander->_debug ? ' - debug' : '';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $HsHtmlExpander->getLanguageTag();?>" lang="<?php echo $HsHtmlExpander->getLanguageTag();?>" dir="<?php echo $HsHtmlExpander->getLanguageDir();?>" >
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo JText::_('PLUGIN TITLE');?> : <?php echo $version;?></title>
<?php
$HsHtmlExpander->printScripts();
$HsHtmlExpander->printCss();

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
		function initHsHtmlExpander(){
			return new HsHtmlExpander({
				lang: '<?php echo $HsHtmlExpander->getLanguage(); ?>',
				alerts: <?php echo $HsHtmlExpander->getAlerts();?>,
				params: {
					'defaults': {
						'targetlist': "<?php echo $params->get('target', 'default');?>"
					}
				}
			});
		}
	</script>
</head>
<body lang="<?php echo $HsHtmlExpander->getLanguage();?>" id="HsHtmlExpander">
	<form onsubmit="insertAction();return false;" action="#">
	<div class="tabs">
		<ul>
			<li id="expander_tab" class="current"><span><a href="javascript:mcTabs.displayTab('expander_tab','expander_panel');" onmousedown="return false;"><?php echo JText::_('EXPANDER TAB');?></a></span></li>
			<li id="options_tab"><span><a href="javascript:mcTabs.displayTab('options_tab','options_panel');" onmousedown="return false;"><?php echo JText::_('OPTIONS TAB');?></a></span></li>
			<li id="html_tab"><span><a href="javascript:mcTabs.displayTab('html_tab','html_panel');" onmousedown="return false;"><?php echo JText::_('HTML TAB');?></a></span></li>
			<li id="flash_tab"><span><a href="javascript:mcTabs.displayTab('flash_tab','flash_panel');" onmousedown="return false;"><?php echo JText::_('FLASH TAB');?></a></span></li>
			<li id="caption_tab"><span><a href="javascript:mcTabs.displayTab('caption_tab','caption_panel');" onmousedown="return false;"><?php echo JText::_('CAPTION TAB');?></a></span></li>
			<li id="heading_tab"><span><a href="javascript:mcTabs.displayTab('heading_tab','heading_panel');" onmousedown="return false;"><?php echo JText::_('HEADING TAB');?></a></span></li>
			<li id="overlay_tab"><span><a href="javascript:mcTabs.displayTab('overlay_tab','overlay_panel');" onmousedown="return false;"><?php echo JText::_('OVERLAY TAB');?></a></span></li>
		</ul>
	</div>
	<div class="panel_wrapper" style="border-bottom:0px;">
		<div id="expander_panel" class="panel current">
			<fieldset>
				<legend><?php echo JText::_('GENERAL');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td class="nowrap"><label id="hreflabel" for="href" class="hastip" title="<?php echo JText::_('URL DESC');?>"><?php echo JText::_('URL');?></label></td>
						<td><input id="href" type="text" value="" size="150" /></td>
                        <td id="hrefbrowsercontainer">&nbsp;</td>
					</tr>
					<tr>
						<td class="nowrap"><label id="titlelabel" for="title" class="hastip" title="<?php echo JText::_('TITLE DESC');?>"><?php echo JText::_('TITLE');?></label></td>
						<td colspan="3"><input id="title" name="title" type="text" value="" /></td>
					</tr>
					<tr>
						<td class="column1"><label id="idlabel" for="id" class="hastip" title="<?php echo JText::_('ID DESC');?>"><?php echo JText::_('ID');?></label></td>
						<td><input id="id" type="text" value="" onchange="return HsHtmlExpanderDialog.mirrorValue( this, 'expanderid' );"/></td>
					</tr>
					<tr>
						<td><label id="stylelabel" for="style" class="hastip" title="<?php echo JText::_('STYLE DESC');?>"><?php echo JText::_('STYLE');?></label></td>
						<td><input type="text" id="style" value="" /></td>
					</tr>
					<tr>
						<td class="column1"><label id="objecttypelabel" for="objecttype" class="hastip" title="<?php echo JText::_('OBJECTTYPE DESC');?>"><?php echo JText::_('OBJECTTYPE');?></label></td>
						<td><select id="objecttype" name="objecttype" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="ajax">Ajax</option>
								<option value="iframe">IFrame</option>
								<option value="swf">Flash</option>
							</select>
						</td>
					</tr>
				</table>
			</fieldset>
				<label id="unobtrusivelabel" for="unobtrusive" class="hastip" title="<?php echo JText::_('UNOBTRUSIVE DESC');?>"><?php echo JText::_('UNOBTRUSIVE');?></label>
				<input type="checkbox" id="unobtrusive" onclick="return HsHtmlExpanderDialog.setTabs();"/>
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
						<td><label id="targetxlabel" for="targetx" class="hastip" title="<?php echo JText::_('TARGETX DESC');?>"><?php echo JText::_('TARGETX');?></label></td>
						<td><input type="text" id="targetx" value="" />
						<label id="targetylabel" for="targety" class="hastip" title="<?php echo JText::_('TARGETY DESC');?>"><?php echo JText::_('TARGETY');?></label>
						<input type="text" id="targety" value="" /></td>
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
		<div id="html_panel" class="panel">
			<fieldset>
				<legend><?php echo JText::_('GENERAL');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td class="column1"><label id="contentidlabel" for="contentid" class="hastip" title="<?php echo JText::_('CONTENTID DESC');?>"><?php echo JText::_('CONTENTID');?></label></td>
						<td><input id="contentid" type="text" value="" /></td>
					</tr>
					<tr>
						<td><label id="contentstylelabel" for="contentstyle" class="hastip" title="<?php echo JText::_('STYLE DESC');?>"><?php echo JText::_('STYLE');?></label></td>
						<td><input type="text" id="contentstyle" value="" /></td>
					</tr>
					<tr>
						<td><label id="widthlabel" for="width" class="hastip" title="<?php echo JText::_('WIDTH DESC');?>"><?php echo JText::_('WIDTH');?></label></td>
						<td><input type="text" id="width" value="" /></td>
					</tr>
					<tr>
						<td><label id="heightlabel" for="height" class="hastip" title="<?php echo JText::_('HEIGHT DESC');?>"><?php echo JText::_('HEIGHT');?></label></td>
						<td><input type="text" id="height" value="" /></td>
					</tr>
					<tr>
						<td class="column1"><label id="allowwidthreductionlabel" for="allowwidthreduction" class="hastip" title="<?php echo JText::_('ALLOWWIDTHREDUCTION DESC');?>"><?php echo JText::_('ALLOWWIDTHREDUCTION');?></label></td>
						<td><select id="allowwidthreduction" name="allowwidthreduction" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="true">Yes</option>
								<option value="false">No</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="allowheightreductionlabel" for="allowheightreduction" class="hastip" title="<?php echo JText::_('ALLOWHEIGHTREDUCTION DESC');?>"><?php echo JText::_('ALLOWHEIGHTREDUCTION');?></label></td>
						<td><select id="allowheightreduction" name="allowheightreduction" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="true">Yes</option>
								<option value="false">No</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="preservecontentlabel" for="preservecontent" class="hastip" title="<?php echo JText::_('PRESERVECONTENT DESC');?>"><?php echo JText::_('PRESERVECONTENT');?></label></td>
						<td><select id="preservecontent" name="preservecontent" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="true">Yes</option>
								<option value="false">No</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="cacheajaxlabel" for="cacheajax" class="hastip" title="<?php echo JText::_('CACHEAJAX DESC');?>"><?php echo JText::_('CACHEAJAX');?></label></td>
						<td><select id="cacheajax" name="cacheajax" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="true">Yes</option>
								<option value="false">No</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="objectloadtimelabel" for="objectloadtime" class="hastip" title="<?php echo JText::_('OBJECTLOADTIME DESC');?>"><?php echo JText::_('OBJECTLOADTIME');?></label></td>
						<td><select id="objectloadtime" name="objectloadtime" >
								<option value=""><?php echo JText::_('NOT SET');?></option>
								<option value="before">Before</option>
								<option value="after">After</option>
							</select>
						</td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('CONTENTTEXT');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td><textarea id="content"  rows="20" cols="80" ></textarea></td>
					</tr>
				</table>
			</fieldset>
		</div>
		<div id="flash_panel" class="panel">
			<fieldset>
				<legend><?php echo JText::_('GENERAL');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td><label id="objectwidthlabel" for="objectwidth" class="hastip" title="<?php echo JText::_('OBJECTWIDTH DESC');?>"><?php echo JText::_('OBJECTWIDTH');?></label></td>
						<td><input type="text" id="objectwidth" value="" /></td>
					</tr>
					<tr>
						<td><label id="objectheightlabel" for="objectheight" class="hastip" title="<?php echo JText::_('OBJECTHEIGHT DESC');?>"><?php echo JText::_('OBJECTHEIGHT');?></label></td>
						<td><input type="text" id="objectheight" value="" /></td>
					</tr>
					<tr>
						<td class="column1"><label id="swfversionlabel" for="swfversion" class="hastip" title="<?php echo JText::_('SWFVERSION DESC');?>"><?php echo JText::_('SWFVERSION');?></label></td>
						<td><input id="swfversion" type="text" value="" /></td>
					</tr>
					<tr>
						<td class="nowrap"><label id="swfexpressinstallurllabel" for="swfexpressinstallurl" class="hastip" title="<?php echo JText::_('SWFEXPRESSINSTALLURL DESC');?>"><?php echo JText::_('SWFEXPRESSINSTALLURL');?></label></td>
						<td><input id="swfexpressinstallurl" type="text" value="" size="150" /></td>
                        <td id="swfexpressinstallurlbrowsercontainer">&nbsp;</td>
					</tr>
					<tr>
						<td class="column1"><label id="swfflashvarslabel" for="swfflashvars" class="hastip" title="<?php echo JText::_('SWFFLASHVARS DESC');?>"><?php echo JText::_('SWFFLASHVARS');?></label></td>
						<td><input id="swfflashvars" type="text" value="" /></td>
					</tr>
					<tr>
						<td class="column1"><label id="swfparamslabel" for="swfparams" class="hastip" title="<?php echo JText::_('SWFPARAMS DESC');?>"><?php echo JText::_('SWFPARAMS');?></label></td>
						<td><input id="swfparams" type="text" value="" /></td>
					</tr>
					<tr>
						<td class="column1"><label id="swfattributeslabel" for="swfattributes" class="hastip" title="<?php echo JText::_('SWFATTRIBUTES DESC');?>"><?php echo JText::_('SWFATTRIBUTES');?></label></td>
						<td><input id="swfattributes" type="text" value="" /></td>
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
						<td class="column1"><label id="expanderidlabel" for="expanderid" class="hastip" title="<?php echo JText::_('EXPANDERID DESC');?>"><?php echo JText::_('EXPANDERID');?></label></td>
						<td><input id="expanderid" type="text" value="" onchange="return HsHtmlExpanderDialog.mirrorValue( this, 'id' );"/></td>
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
				<input type="button" id="insert" name="insert" value="<?php echo JText::_('Insert');?>" onclick="HsHtmlExpanderDialog.insert();" />
			</div>
			<div style="float: right">
				<input type="button" class="button" id="help" name="help" value="<?php echo JText::_('Help');?>" onclick="HsHtmlExpander.openHelp();" />
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