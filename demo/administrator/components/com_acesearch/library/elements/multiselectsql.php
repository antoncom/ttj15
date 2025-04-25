<?php
/**
* @version		1.5.0
* @package		AceSearch Library
* @subpackage	MultiSelectSQL
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted Access');

class JElementMultiSelectSQL extends JElement {

	var	$_name = 'MultiSelectSQL';

	function fetchElement($name, $value, &$node, $control_name) {
		// Base name of the HTML control
		$ctrl = $control_name .'['. $name .']';

		// Construct the various argument calls that are supported
		$attribs = ' ';
		$attribs .= 'size="7"';
		$attribs .= 'class="inputbox"';
		$attribs .= ' multiple="multiple"';
		$ctrl .= '[]';
		
		$db	= & JFactory::getDBO();
		$db->setQuery($node->attributes('db_query'));
		
		$key = ($node->attributes('db_id') ? $node->attributes('db_id') : 'id');
		$val = ($node->attributes('db_name') ? $node->attributes('db_name') : $name);
		
		$rows = array();
		
		if ($node->attributes('default') == 'alll') {
			$rows[0] = new stdClass();
			$rows[0]->id = 'alll';
			$rows[0]->name = JText::_('- All -');
		}
		
		$apps = array_merge($rows, $db->loadObjectList());
		
		return JHTML::_('select.genericlist', $apps, $ctrl, $attribs, $key, $val, $value, $control_name.$name);
	}
}