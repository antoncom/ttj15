<?php
/**
* @version		1.5.0
* @package		AceSearch Library
* @subpackage	Filters
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

//No Permision
defined('_JEXEC') or die('Restricted access');

class JElementAcesearchFilters extends JElement{

	var	$_name = 'AcesearchFilters';

	function fetchElement($name, $value, &$node, $control_name) {
		$filters = array();
		$filters[] = JHTML::_('select.option', '', JText::_('- - - - - -'));
		
		// Build the query.
		$db = &JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__acesearch_filters WHERE published = 1 ORDER BY title");
		$rows = $db->loadObjectList();
		
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$filters[] = JHTML::_('select.option', $row->id, $row->title);
			}
		}
		
		return JHTML::_('select.genericlist',  $filters, ''.$control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, $control_name.$name);
	}
}