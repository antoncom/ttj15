<?php
/**
* @version		1.5.0
* @package		AceSearch Library
* @subpackage	FieldList
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted Access');

class JElementFieldList extends JElement {

	var $_name = 'FieldList';

	function fetchElement($name, $value, &$node, $control_name) {
		$this->AcesearchConfig = AcesearchFactory::getConfig();
		
		// Base name of the HTML control
		$ctrl = $control_name .'['. $name .']';

		// Construct the various argument calls that are supported
		$attribs = ' ';
		$attribs .= 'size="7"';
		$attribs .= 'class="inputbox"';
		$attribs .= ' multiple="multiple"';
		$ctrl .= '[]';

		// Get rows
		$fields = array();
		
		$extension = AcesearchUtility::getExtensionFromRequest();
		$file = JPATH_ACESEARCH_ADMIN.'/extensions/'.$extension.'.php';
		
		
		if (file_exists($file)) {
			require_once($file);
			$items = AcesearchCache::getExtensions(1);
			
			if (!isset($items[$extension])) {
				$items= AceDatabase::loadObjectList("SELECT id, name, extension, params FROM #__acesearch_extensions ORDER BY ordering ASC, name ASC", 'extension');
			}
			
			$params = new JParameter($items[$extension]->params);
			$classname = 'AceSearch_'.$extension;
			$class = new $classname($items[$extension], $params);
			$rows = $class->getFieldList();

			if (!empty($rows)) {
				foreach ($rows as $row) {
					if(isset($row->id)) {
						$id = $row->id.'_'.$row->name;
					} else {
						$id = $row->name;
					}
					$fields[] = JHTML::_('select.option', $id, $row->name);
				}
			}
		}
		
		return JHTML::_('select.genericlist', $fields, $ctrl, $attribs, 'value', 'text', $value, $control_name.$name);
	}
}