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

// View Class
class AcesearchViewSupport extends AcesearchView {

	function display($tpl = null) {
		// Toolbar
		JToolBarHelper::title(JText::_('COM_ACESEARCH_SUPPORT'), 'acesearch');		
		JToolBarHelper::back(JText::_('Back'), 'index.php?option=com_acesearch');
		
		if (JRequest::getCmd('task', '') == 'translators') {
			$this->document->setCharset('iso-8859-9');
		}
		
		parent::display($tpl);
	}
}