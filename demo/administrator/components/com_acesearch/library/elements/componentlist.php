<?php
/**
* @version		1.5.0
* @package		AceSearch Library
* @subpackage	ComponentList
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

//No Permision
defined('_JEXEC') or die('Restricted access');

class JElementComponentList extends JElement{

	var	$_name = 'ComponentList';

	function fetchElement($name, $value, &$node, $control_name) {
		$components = array();
		$components[] = JHTML::_('select.option', '', JText::_('COM_ACESEARCH_MENU_COMPONENT_ALL'));
		
		$db =& JFactory::getDBO();
		$db->setQuery("SELECT name, extension FROM #__acesearch_extensions WHERE params NOT LIKE '%handler=0%' AND (client = 0 OR client = 2)");
		$rows = $db->loadObjectList();
		
		foreach($rows as $row) {
			$components[] = JHTML::_('select.option', $row->extension, $row->name);
		}
		
		return JHTML::_('select.genericlist', $components, ''.$control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, $control_name.$name);
	}
}