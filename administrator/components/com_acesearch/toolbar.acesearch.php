<?php
/**
* @version		1.5.0
* @package		AceSearch
* @subpackage	AceSearch
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

$controller	= JRequest::getCmd('controller', 'acesearch');

JHTML::_('behavior.switcher');

// Load submenus
$views = array(''											=> JText::_('COM_ACESEARCH_COMMON_PANEL'),
				'&controller=config&task=edit'				=> JText::_('COM_ACESEARCH_CPANEL_CONFIGURATION'),
				'&controller=extensions&task=view'			=> JText::_('COM_ACESEARCH_CPANEL_EXTENSIONS'),
				'&controller=filters&task=view'				=> JText::_('COM_ACESEARCH_CPANEL_FILTERS'),
				'&controller=statistics&task=view'			=> JText::_('COM_ACESEARCH_CPANEL_STATISTICS'),
				'&controller=search&task=view'				=> JText::_('COM_ACESEARCH_CPANEL_SEARCH'),
				'&controller=css&task=edit'					=> JText::_('CSS'),
				'&controller=support&task=support'			=> JText::_('COM_ACESEARCH_SUPPORT')
				);	

foreach($views as $key => $val) {
	$active	= ($controller == $key);
	
	if ($key == '') {
		$img = 'acesearch.png';
	}
	else {
		$a = explode('&', $key);
		$c = explode('=', $a[1]);

		$img = 'icon-16-as-'.$c[1].'.png';
	}
	
	JSubMenuHelper::addEntry('<img src="components/com_acesearch/assets/images/'.$img.'" style="margin-right: 2px;" align="absmiddle" />'.$val, 'index.php?option=com_acesearch'.$key, $active);
}
?>