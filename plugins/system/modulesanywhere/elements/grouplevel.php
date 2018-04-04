<?php
/**
 * Element: Group Level
 * Displays a select box of backend group levels
 *
 * @package    NoNumber! Elements
 * @version    v1.0.0
 *
 * @author     Peter van Westen <peter@nonumber.nl>
 * @link       http://www.nonumber.nl
 * @copyright  Copyright (C) 2009 NoNumber! All Rights Reserved
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// Ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Group Level Element
 *
 * Available extra parameters:
 * root				The user group to use as root (default = USERS)
 * showroot			Show the root in the list
 * multiple			Multiple options can be selected
 * notregistered	Add an option for 'Not Registered' users
 */
class JElementGroupLevel extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'GroupLevel';

	function fetchElement( $name, $value, &$node, $control_name )
	{
		$_root			= $this->def( $node->attributes( 'root' ), 'USERS' );
		$_showroot		= $node->attributes( 'showroot' );
		if ( strtoupper( $_root ) == 'USERS' && $_showroot == '' ) { $_showroot = 0; }
		$_multiple		= $node->attributes( 'multiple' );
		$_notregistered	= $node->attributes( 'notregistered' );

		$_control = $control_name.'['.$name.']';
		$_attribs = 'class="inputbox"';

		$_acl		=& JFactory::getACL();
		$_options	= $_acl->get_group_children_tree( null, $_root, $_showroot );
		if ( $_notregistered ) {
			$_no_user			= '';
			$_no_user->value	= 0;
			$_no_user->text		= '&nbsp; '.JText::_( 'Not Registered' );
			$_no_user->disable	= '';
			array_unshift( $_options, $_no_user );
		}

		if ( $_multiple ) {
			if( !is_array( $value ) ) {
				$value = explode( ',', $value );
			}

			$_attribs .= ' multiple="multiple"';
			$_control .= '[]';

			if ( in_array( 29, $value ) ) {
				$value[] = 18;
				$value[] = 19;
				$value[] = 20;
				$value[] = 21;
			}
			if ( in_array( 30, $value ) ) {
				$value[] = 23;
				$value[] = 24;
				$value[] = 25;
			}
		}

		return JHTML::_( 'select.genericlist', $_options, $_control, $_attribs, 'value', 'text', $value, $control_name.$name );
	}

	function def( $val, $default )
	{
		return ( $val == '' ) ? $default : $val;
	}
}