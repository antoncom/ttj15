<?php
/**
 * Element: Title
 * Displays a title with a bunch of extras, like: description, image, versioncheck
 *
 * @package    NoNumber! Elements
 * @version    v1.0.5
 *
 * @author     Peter van Westen <peter@nonumber.nl>
 * @link       http://www.nonumber.nl
 * @copyright  Copyright (C) 2009 NoNumber! All Rights Reserved
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// Ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Title Element
 *
 * Available extra parameters:
 * title			The title
 * description		The description
 * message_type		none, message, notice, error?
 * image			Image (and path) to show on the right
 * show_apply		Show an apply tick image on the right (only if the image is not set)
 * url				The main url
 * download_url		The url of the download location
 * help_url			The url of the help page
 * version_url		The url to the new version folder (default = [url]/versions/)
 * version_path		The path to version folder
 * version_file		The filename of the current version file
 */
class JElementTitle extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'Title';

	function fetchTooltip( $label, $description, &$node, $control_name, $name )
	{
		$_nostyle		= $node->attributes( 'nostyle' );
		if ( $_nostyle ) {
			return JElement::fetchTooltip( $label, '', $node, $control_name, $name );
		}
		return;
	}

	function fetchElement( $name, $value, &$node, $control_name )
	{
		$_title			= $node->attributes( 'label' );
		$_description	= $node->attributes( 'description' );
		$_message_type	= $node->attributes( 'message_type' );
		$_image			= $node->attributes( 'image' );
		$_image_w		= $node->attributes( 'image_w' );
		$_image_h		= $node->attributes( 'image_h' );
		$_show_apply	= $node->attributes( 'show_apply' );
		$_nostyle		= $node->attributes( 'nostyle' );
		$_toggle		= $node->attributes( 'toggle' );

		if ( $_nostyle ) {
			return JText::_( $_description );
		}

		// The main url
		$_url			= $node->attributes( 'url' );
		$_download		= $node->attributes( 'download_url' );
		$_help			= $node->attributes( 'help_url' );
		$_version_url	= $this->def( $node->attributes( 'version_url' ), $_url.'/versions/' );
		$_version_path	= $node->attributes( 'version_path' );
		$_version_file	= $node->attributes( 'version_file' );

		$_msg = '';

		if ( $_title ) {
			$_title = html_entity_decoder( JText::_( $_title ) );
		}

		$_user = JFactory::getUser();
		if( $_version_file && ( $_user->usertype == 'Super Administrator' || $_user->usertype == 'Administrator' ) ) {
			// Import library dependencies
			require_once( dirname( __FILE__ ).DS.'version_check.php' );

			$_msg = NoNumberVersionCheck::setMessage( $_version_file, $_version_path, $_version_url, $_download );
			$_version = NoNumberVersionCheck::getVersions( $_version_file, $_version_path, $_version_url );
			$_current_version = $_version['0'];
			if ( $_current_version ) {
				if ( $_title ) {
					$_title .= ' v'.$_current_version;
				} else {
					$_title = 'Version '.$_current_version;
				}
			}
		}

		if ( $_url ) {
			$_url = '<a href="'.$_url.'" target="_blank" title="'.$_title.'">';
		}

		if ( $_image ) {
			$_image = $_url.'<img src="'.$_image.'" border="0" style="float:right;margin-left:10px" alt=""';
			if ( $_image_w ) {
				$_image .= ' width="'.$_image_w.'"';
			}
			if ( $_image_h ) {
				$_image .= ' height="'.$_image_h.'"';
			}
			$_image .= ' />';
			if ( $_url ) { $_image .= '</a>'; }
		}

		if ( $_url ) { $_title = $_url.$_title.'</a>'; }
		if ( $_description ) { $_description = html_entity_decoder( JText::_( $_description ) ); }
		if ( $_help ) { $_help = '<a href="'.$_help.'" target="_blank" title="'.JText::_( 'Help...' ).'">'.JText::_( 'Help...' ).'</a>'; }

		// Include extra language file
		$_lang = JFactory::getLanguage();
		$_lang = str_replace( '_', '-', $_lang->_lang );

		$_include_file = 'language.'.$_lang.'.inc.php';
		if ( !file_exists( dirname( __FILE__ ).DS.$_include_file ) ) {
			$_include_file = 'language.en-GB.inc.php';
		}
		if ( file_exists( dirname( __FILE__ ).DS.$_include_file ) ) {
			include( dirname( __FILE__ ).DS.$_include_file );
		}

		$html = '';
		if ( $_image ) { $html .= $_image; }
		if ( $_show_apply ) {
			$_apply_button = '<a href="#" onclick="submitbutton( \'apply\' );" title="'.JText::_( 'Apply' ).'"><img align="right" border="0" alt="'.JText::_( 'Apply' ).'" src="images/tick.png"/></a>';
			$html .= $_apply_button;
		}

		if ( $_toggle && $_description ) {
			$_el = 'document.getElementById( \''.$control_name.$name.'description\' )';
			$_onclick =
				'if( this.innerHTML == \''.JText::_( JText::_( 'Show' ).' '.$_title ).'\' ){'.
					$_el.'.style.display = \'block\';'.
					'this.innerHTML = \''.JText::_( JText::_( 'Hide' ).' '.$_title ).'\';'.
				'}else{'.
					$_el.'.style.display = \'none\';'.
					'this.innerHTML = \''.JText::_( JText::_( 'Show' ).' '.$_title ).'\';'.
				'}'.
				'this.blur();return false;';
			$html .= '<div class="button2-left" style="margin:0px 0px 5px 0px;"><div class="blank"><a href="javascript://;" onclick="'.$_onclick.'">'.JText::_( JText::_( 'Show' ).' '.$_title ).'</a></div></div>'."\n";
			$html .= '<br clear="all" />';
			$html .= '<div id="'.$control_name.$name.'description" style="display:none;">';
		} else if ( $_title ) {
			$html .= '<h4 style="margin: 0px;">'.$_title.'</h4>';
		}
		if ( $_description ) { $html .= $_description; }
		if ( $_help ) { $html .= '<p>'.$_help.'</p>'; }
		if ( $_toggle && $_description ) {
			$html .= '</div>';
		}
		if ( $_message_type ) {
			$html = '<dl id="system-message"><dd class="'.$_message_type.'"><ul><li>'.html_entity_decoder( $html ).'</li></ul></dd></dl>';
		} else {
			$html = '<div class="panel"><div style="padding: 2px 5px;">'.$html.'<div style="clear: both;"></div></div></div>';
		}

		if ( $_msg ) { $html = $_msg.$html; }

		return $html;
	}

	function def( $val, $default )
	{
		return ( $val == '' ) ? $default : $val;
	}
}

if( !function_exists( 'html_entity_decoder' ) )
{
	function html_entity_decoder( $given_html, $quote_style = ENT_QUOTES, $charset = 'UTF-8' ) {
		if( phpversion() < '5.0.0' ) {
			$trans_table = array_flip( get_html_translation_table( HTML_SPECIALCHARS, $quote_style ) );
			$trans_table['&#39;'] = "'";
			return ( strtr( $given_html, $trans_table ) );
		}else {
			return html_entity_decode( $given_html, $quote_style, $charset );
		}
	}
}