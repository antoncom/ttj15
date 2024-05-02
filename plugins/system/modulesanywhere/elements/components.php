<?php
/**
 * Element: Components
 * Displays a list of components with check boxes
 *
 * @package    NoNumber! Elements
 * @version    v1.0.1
 *
 * @author     Peter van Westen <peter@nonumber.nl>
 * @link       http://www.nonumber.nl
 * @copyright  Copyright (C) 2009 NoNumber! All Rights Reserved
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// Ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Restricted access' );

 /**
 * Components Element
 */
class JElementComponents extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'Components';

	function fetchElement( $name, $value, &$node, $control_name )
	{
		$_frontend			= $this->def( $node->attributes( 'frontend' ), 1 );
		$_admin				= $this->def( $node->attributes( 'admin' ), 1 );
		$_show_content		= $this->def( $node->attributes( 'show_content' ), 0 );

		$_components = JElementComponents::getComponents( $_frontend, $_admin, $_show_content );

		// place a dummy hidden checkbox item in the list, to be able to deselect all (and still have a default)
		$list = "\n".'<input type="hidden" id="'.$control_name.$name.'x" name="'.$control_name.'['.$name.']'.'[]" value="x" checked="checked" />';
		if ( count( $_components ) ) {
			foreach ( $_components as $component ) {
				if ( ! is_array( $value ) ) $value = explode( ',', $value );
				$checked = ( in_array( $component->option, $value ) ) ? ' checked="checked"' : '';
				$list .= "\n".'<input type="checkbox" id="'.$control_name.$name.$component->option.'" name="'.$control_name.'['.$name.']'.'[]" value="'.$component->option.'"'.$checked.' />';
				$list .= $component->name.'<br />';
			}
		} else {
			$list .= JText::_( 'Component Not Found' );
		}

		return $list;
	}

	function getComponents( $_frontend = 1, $_admin = 1, $_show_content = 0 )
	{
		$_db   =& JFactory::getDBO();

		if ( !$_frontend && !$_admin ) {
			$_query = 'SELECT '.$_db->NameQuote( 'option' ).', name'.
				' FROM #__components'.
				' WHERE enabled = 1'.
				' AND parent = 0';
				if ( !$_show_content ) {
					$_query .= ' AND '.$_db->NameQuote( 'option' ).' <> "com_content"';
				}
				$_query .= ' ORDER BY name';
			$_db->setQuery( $_query );
			$components = $_db->loadObjectList();
		} else {
			if ( $_frontend ) {
				// component subs
				$_query = 'SELECT parent'.
					' FROM #__components'.
					' WHERE enabled = 1'.
					' AND parent != 0';
					' AND link != ""';
					' ORDER BY ordering, name';
				$_db->setQuery( $_query );
				$_subcomponents = $_db->loadResultArray();
				$_subcomponents = array_unique( $_subcomponents );

				// main components
				$_query = 'SELECT id'.
					' FROM #__components'.
					' WHERE enabled = 1'.
					' AND parent = 0'.
					' AND ( link != ""';
					if ( count( $_subcomponents ) ) {
						$_query .= ' OR id IN ( '.implode( ',', $_subcomponents ).' )';
					}
				$_query .= ' )';
				$_query .= ' ORDER BY ordering, name';
				$_db->setQuery( $_query );
				$_component_ids = $_db->loadResultArray();
			}

			if ( $_admin ) {
				// component subs
				$_query = 'SELECT parent'.
					' FROM #__components'.
					' WHERE enabled = 1'.
					' AND parent != 0';
					' AND admin_menu_link != ""';
				$_db->setQuery( $_query );
				$_subcomponents = $_db->loadResultArray();
				$_subcomponents = array_unique( $_subcomponents );

				// main components
				$_query = 'SELECT id'.
					' FROM #__components'.
					' WHERE enabled = 1'.
					' AND parent = 0'.
					' AND ( admin_menu_link != ""';
					if ( count( $_subcomponents ) ) {
						$_query .= ' OR id IN ( '.implode( ',', $_subcomponents ).' )';
					}
				$_query .= ' )';
				$_db->setQuery( $_query );
				if ( $_frontend ) {
					$_component_ids = array_intersect( $_component_ids, $_db->loadResultArray() );
				} else {
					$_component_ids = $_db->loadResultArray();
				}
			}

			$_component_ids = array_unique( $_component_ids );
			$_query = 'SELECT '.$_db->NameQuote( 'option' ).', name' .
				' FROM #__components' .
				' WHERE enabled = 1' .
				' AND parent = 0';
				if ( count( $_component_ids ) ) {
					$_query .= ' AND id IN ( '.implode( ',', $_component_ids ).' )';
				}
				if ( !$_show_content ) {
					$_query .= ' AND '.$_db->NameQuote( 'option' ).' <> "com_content"';
				}
			$_query .= ' ORDER BY name';
			$_db->setQuery( $_query );
			$components = $_db->loadObjectList();
		}

		return $components;
	}

	function getComponentsArray( $_frontend = 1, $_admin = 1, $_show_content = 0 )
	{
		$_components = JElementComponents::getComponents( $_frontend, $_admin, $_show_content );
		$components = array();
		foreach ( $_components as $component ) {
			$components[] = $component->option;
		}
		return $components;
	}

	function def( $val, $default )
	{
		return ( $val == '' ) ? $default : $val;
	}
}