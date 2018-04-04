<?php
/**
* @version		1.5.0
* @package		AceSearch Library
* @subpackage	HandlerLis
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted Access');

class JElementHandlerList extends JElement {

	var $_name = 'HandlerList';

	function fetchElement($name, $value, &$node, $control_name) {
		// Base name of the HTML control
		$class = ($node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="inputbox"');
		
        $extension = AcesearchUtility::getExtensionFromRequest();
		
		$options = AcesearchUtility::getHandlerList($extension);

		return JHTML::_('select.genericlist', $options, ''.$control_name.'['.$name.']', $class, 'value', 'text', $value, $control_name.$name);
	}
}