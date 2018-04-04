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

// View Class
class AcesearchViewExtensions extends AcesearchView{
	
	// Display extensions
	function view($tpl = null){
		// Toolbar
		JToolBarHelper::title(JText::_('COM_ACESEARCH_CPANEL_EXTENSIONS'), 'acesearch');
		$this->toolbar->appendButton('Confirm', JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_BTN_REMOVE_WARN'), 'uninstall', JText::_('Uninstall'), 'uninstall', true, false);
		JToolBarHelper::custom('save', 'save1.png', 'save1.png', JText::_('Save'), false);
		JToolBarHelper::divider();
		$this->toolbar->appendButton('Popup', 'cache', JText::_('COM_ACESEARCH_COMMON_CLEAN_CACHE'), 'index.php?option=com_acesearch&amp;controller=purge&amp;task=cache&amp;tmpl=component', 300, 250);
		JToolBarHelper::divider();
		$this->toolbar->appendButton('Popup', 'help1', JText::_('Help'), 'http://www.joomace.net/support/docs/acesearch/user-manual/extensions?tmpl=component', 650, 500);

		// Get behaviors
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.modal', 'a.modal', array('onClose'=>'\function(){location.reload(true);}'));
		
		$this->assignRef('lists',		$this->get('Lists'));
		$this->assignRef('info',		$this->get('Info'));		
		$this->assignRef('groups',		$this->get('Groups'));		
		$this->assignRef('items',		$this->get('Items'));
		$this->assignRef('pagination',	$this->get('Pagination'));

		parent::display($tpl);
	}
}