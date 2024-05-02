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

// Model Class
class AcesearchModelCSS extends AcesearchModel {
	
	// Main constructer
	function __construct() {
        parent::__construct('css');
    }
	
	function save() {
		$filecontent = JRequest::getVar('filecontent', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$file = JPATH_ACESEARCH.'/assets/css/acesearch.css';

		// Try to make the css file writeable
		if (JPath::isOwner($file) && !JPath::setPermissions($file, '0755')) {
			JError::raiseNotice('SOME_ERROR_CODE', JText::_('Could not make the css file writable'));
		}

		jimport('joomla.filesystem.file');
		$return = JFile::write(JPATH_ACESEARCH.'/assets/css/acesearch.css', $filecontent);

		// Try to make the css file unwriteable
		if (JPath::isOwner($file) && !JPath::setPermissions($file, '0555')) {
			JError::raiseNotice('SOME_ERROR_CODE', JText::_('Could not make the css file unwritable'));
		}
		
		return $return;
	}
}