<?php
/**
* @version		1.5.0
* @package		AceSearch
* @subpackage	AceSearch
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted Access');

class AcesearchViewConfig extends AcesearchView {

	function edit($tpl = null) {
		JToolBarHelper::title(JText::_('COM_ACESEARCH_CPANEL_CONFIGURATION'), 'acesearch');
		JToolBarHelper::custom('save', 'save1.png', 'save1.png', JText::_('Save'), false);
		JToolBarHelper::custom('apply', 'apply1.png', 'apply1.png', JText::_('Apply'), false);
		JToolBarHelper::custom('cancel', 'cancel1.png', 'cancel1.png', JText::_('Cancel'), false);
		JToolBarHelper::divider();
		$this->toolbar->appendButton('Popup', 'cache', JText::_('COM_ACESEARCH_COMMON_CLEAN_CACHE'), 'index.php?option=com_acesearch&amp;controller=purge&amp;task=cache&amp;tmpl=component', 300, 250);
		JToolBarHelper::divider();
		$this->toolbar->appendButton('Popup', 'help1', JText::_('Help'), 'http://www.joomace.net/support/docs/acesearch/user-manual/configuration?tmpl=component', 650, 500);
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/com_acesearch/assets/css/colorpicker.css');
		$document->addScript('components/com_acesearch/assets/js/jquery.js');
		$document->addScript('components/com_acesearch/assets/js/colorpicker.js');
		$document->addScript('components/com_acesearch/assets/js/eye.js');
		$document->addScript('components/com_acesearch/assets/js/layout.js?ver=1.0.2');
		// Get behaviors
  		JHTML::_('behavior.mootools');
		JHTML::_('behavior.tooltip');

		// Import JPane
		jimport('joomla.html.pane');
		$pane =& JPane::getInstance('Tabs');
		
		$this->assignRef('pane',	$pane);
		$this->assignRef('lists', 	$this->get('Lists'));
		
		parent::display($tpl);
	}
}