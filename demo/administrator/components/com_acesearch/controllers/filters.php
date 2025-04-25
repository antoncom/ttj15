<?php
/**
* @version		1.5.0
* @package		AceSearch
* @subpackage	AceSearch
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

//No Permision
defined( '_JEXEC' ) or die( 'Restricted access' );

class AcesearchControllerFilters extends AcesearchController {
	
	function __construct() {
		parent::__construct('filters');
	}
	
	function changeExtension() {
		$categories = array();
		
		$extension = JRequest::getCmd('ext');
		$extensions = AcesearchCache::getExtensions(1);
		
		$categories[] = JHTML::_('select.option', '', JText::_('COM_ACESEARCH_ALL_CATEGORIES'));
		
		if (!empty($extension) && isset($extensions[$extension])) {
			$params = new JParameter($extensions[$extension]->params);
			
			require_once(JPATH_ACESEARCH_ADMIN.'/extensions/'.$extension.'.php');
			
			$class_name = 'AceSearch_'.$extension;
			$acesearch_ext = new $class_name($extensions[$extension], $params);
			
			$rows = $acesearch_ext->getCategoryList('1');
			foreach($rows as $row){
				$categories[] = JHTML::_('select.option', $row->id, $row->name);
			}
			
			$id = JRequest::getInt('catid');
			?>
			<strong><?php echo JText::_("COM_ACESEARCH_FILTERS_CATEGORY"); ?></strong>
			<br />
			<?php
			echo JHTML::_('select.genericlist', $categories, 'category', 'class="inputbox" size="10" style="width:150px"', 'value', 'text', $id).'<br/>';
		}
	}
	
	function changeUser() {
		$users = "";
		
		$extension = JRequest::getCmd('ext', '');
		$extensions = AcesearchCache::getExtensions(1);
	
		if (!empty($extension) && isset($extensions[$extension])) {
			$user = JRequest::getString('usr', '');
			
			$params = new JParameter($extensions[$extension]->params);
			
			require_once(JPATH_ACESEARCH_ADMIN.'/extensions/'.$extension.'.php');
			
			$class_name = 'AceSearch_'.$extension;
			$acesearch_ext = new $class_name($extensions[$extension],$params);
			
			$users = $acesearch_ext->getUser($user);
		}
		
		if (!empty($users)){
			?>
			<strong><?php echo JText::_("COM_ACESEARCH_EXTENSIONS_VIEW_AUTHOR") . " (".JText::_("COM_ACESEARCH_FILTERS_USERNAME").") "; ?></strong><br />
			<?php
			echo $users;
		}
	}
}