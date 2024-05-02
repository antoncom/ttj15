<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');// no direct access


class JElementPhocaDocSMod extends JElement
{
	var	$_name = 'PhocaDocSMod';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$class 		= ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="inputbox"' );
		
		
		$db = &JFactory::getDBO();

		$query =  " SELECT s.id as value, s.title as text"
				. " FROM #__sections AS s"
				. " GROUP BY s.id"
				. " ORDER BY s.ordering";
		$db->setQuery( $query );
		
		$optionsS = $db->loadObjectList( );
		

		
		// Multiple
		$ctrl	= $control_name .'['. $name .']';
		$attribs	= ' ';
		if ($v = $node->attributes('size')) {
			$attribs	.= 'size="'.$v.'"';
		}
		if ($v = $node->attributes('class')) {
			$attribs	.= 'class="'.$v.'"';
		} else {
			$attribs	.= 'class="inputbox"';
		}
		if ($m = $node->attributes('multiple'))
		{
			$attribs	.= 'multiple="multiple"';
			$ctrl		.= '[]';
			//$value		= implode( '|', )
		}
		return JHTML::_('select.genericlist', $optionsS, $ctrl, $attribs, 'value', 'text', $value, $control_name.$name );
	}

}
