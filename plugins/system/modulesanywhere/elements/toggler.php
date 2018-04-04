<?php
/**
 * Element: Toggler
 * Adds slide in and out functionality to elements based on an elements value
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
 * Toggler Element
 *
 * To use this, make a start xml param tag with the param and value set
 * And an end xml param tag without the param and value set
 * Everything between those tags will be included in the slide
 *
 * Available extra parameters:
 * param			The name of the reference parameter
 * value			a comma seperated list of value on which to show the elements
 */
class JElementToggler extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'Toggler';

	function fetchTooltip( $label, $description, &$node, $control_name, $name )
	{
		return;
	}

	function fetchElement( $name, $value, &$node, $control_name )
	{
		$_param			= $node->attributes( 'param', '' );
		$_value			= $node->attributes( 'value', '' );
		$_nofx			= $node->attributes( 'nofx' );

		$file_root	= str_replace( array( '\\', '/' ), DS, dirname( __FILE__ ) );
		$file_root = explode( DS, $file_root );
		foreach ( $file_root as $folder ) {
			if ( !in_array( $folder, array( 'administrator', 'components', 'modules', 'plugins', 'templates' ) ) ) {
				array_shift ( $file_root );
			} else {
				break;
			}
		}
		$file_root = implode( '/', $file_root );

		$_document	=& JFactory::getDocument();
		$_document->addScript( JURI::root(true).'/'.$file_root.'/toggler.js' );
		
		if ( $_param != '' ) {
			$_set_groups = explode( '|', $_param );
			$_set_values = explode( '|', $_value );
			$_ids = array();
			foreach ( $_set_groups as $_i => $group ) {
				$_count = $_i;
				if ( $_count >= count( $_set_values ) ) {
					$_count = 0;
				}
				$_values = explode( ',', $_set_values[$_count] );
				foreach ( $_values as $_val ) {
					$_ids[] = $group.'.'.$_val;
				}
			}
			$html = '<div id="'.implode( ' ', $_ids ).'" class="toggler';
			if ( $_nofx ) {
				$html .= ' toggler_nofx';
			}
			$html .= '">';
			$html .= '<table width="100%" class="paramlist admintable" cellspacing="1">';
			$html .= '<tr><td style="padding: 0px;" colspan="2">';
		} else {
			$html = '</td></tr></table>';
			$html .= '</div>';
		}

		return $html;
	}
}