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
class AcesearchViewUpgrade extends AcesearchView {

	function view($tpl = null) {		
		// Toolbar
		JToolBarHelper::title(JText::_('COM_ACESEARCH_UPGRADE_TITLE'), 'acesearch');
		$this->toolbar->appendButton('Popup', 'help1', JText::_('Help'), 'http://www.joomace.net/support/docs/acesearch/installation-upgrading/upgrade?tmpl=component', 650, 500);
		
		$versions = array(2);
		$version_info = AcesearchCache::getRemoteInfo();
		$versions['latest'] = $version_info['acesearch'];
		$versions['installed'] = AcesearchUtility::getXmlText(JPATH_ACESEARCH_ADMIN.DS.'acesearch.xml', 'version');
		
		$this->assignRef('versions', $versions);
		
		parent::display($tpl);
	}
}