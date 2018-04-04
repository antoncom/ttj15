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

class AcesearchViewStatistics extends AcesearchView {
	
	function view($tpl = null){
		JToolBarHelper::title(JText::_('COM_ACESEARCH_CPANEL_STATISTICS'), 'acesearch');
		$this->toolbar->appendButton('Confirm', JText::_('COM_ACESEARCH_COMMON_CONFIRM_DELETE'), 'uninstall', JText::_('Delete'), 'delete', true, false);
		JToolBarHelper::divider();
		$this->toolbar->appendButton('Popup', 'help1', JText::_('Help'), 'http://www.joomace.net/support/docs/acesearch/user-manual/statistics?tmpl=component', 650, 500);
		
		$this->assignRef('lists',			$this->get('Lists'));
		$this->assignRef('items',			$this->get('Items'));
		$this->assignRef('pagination',		$this->get('Pagination'));
		
		parent::display($tpl);
	}
}