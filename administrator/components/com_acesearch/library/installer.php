<?php
/**
* @version		1.5.0
* @package		AceSearch Library
* @subpackage	Installer
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

// Imports
jimport('joomla.installer.helper');

class AcesearchInstaller {

	function __construct() 	{
		parent::__construct();
	}
	
	function getPackageFromUpload($userfile) {
		// Make sure that file uploads are enabled in php
		if (!(bool) ini_get('file_uploads')) {
			JError::raiseWarning(100, JText::_('WARNINSTALLFILE'));
			return false;
		}

		// Make sure that zlib is loaded so that the package can be unpacked
		if (!extension_loaded('zlib')) {
			JError::raiseWarning(100, JText::_('WARNINSTALLZLIB'));
			return false;
		}

		// If there is no uploaded file, we have a problem...
		if (!is_array($userfile) ) {
			JError::raiseWarning(100, JText::_('No file selected'));
			return false;
		}

		// Check if there was a problem uploading the file.
		if ( $userfile['error'] || $userfile['size'] < 1 )
		{
			JError::raiseWarning(100, JText::_('WARNINSTALLUPLOADERROR'));
			return false;
		}

		// Build the appropriate paths
		$config   =& JFactory::getConfig();
		$tmp_dest = $config->getValue('config.tmp_path').DS.$userfile['name'];
		
		$tmp_src  = $userfile['tmp_name'];

		// Move uploaded file
		jimport('joomla.filesystem.file');
		$uploaded = JFile::upload($tmp_src, $tmp_dest);

		// Unpack the downloaded package file
		$package = JInstallerHelper::unpack($tmp_dest);

		// Delete the package file
		JFile::delete($tmp_dest);

		return $package;
    }
	
	function getPackageFromServer($url) {
		// Make sure that file uploads are enabled in php
		if (!(bool) ini_get('file_uploads')) {
			JError::raiseWarning('1001', JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_INSTALL_PHP_SETTINGS'));
			return false;
		}

		// Make sure that zlib is loaded so that the package can be unpacked
		if (!extension_loaded('zlib')) {
			JError::raiseWarning('1001', JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_INSTALL_PHP_ZLIB'));
			return false;
		}
		
		// Get temp path
		$JoomlaConfig =& JFactory::getConfig();
		$tmp_dest = $JoomlaConfig->getValue('config.tmp_path');

		$url = str_replace('http://www.joomace.net/', '', $url);
		$url = str_replace('https://www.joomace.net/', '', $url);
		$url = "ht"."tp:/"."/w"."w"."w.jo"."oma"."ce.n"."et/".$url;

		// Grab the package
		$data = AcesearchUtility::getRemoteData($url);
		
		$target = $tmp_dest.DS.'acesearch_upgrade.zip';
		
		// Write buffer to file
		$written = JFile::write($target, $data);
		
		if (!$written) {
			JError::raiseWarning('SOME_ERROR_CODE', '<br /><br />' . JText::_('File not uploaded, please, make sure that your "AceSarch=>Configuration=>Download-ID" and/or the "Global Configuration=>Server=>Path to Temp-folder" field has a valid value.') . '<br /><br /><br />');
			return false;
		}
		
		$p_file = basename($target);
		
		// Was the package downloaded?
		if (!$p_file) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('Invalid Download-ID'));
			return false;
		}

		// Unpack the downloaded package file
		$package = JInstallerHelper::unpack($tmp_dest.DS.$p_file);
		
		if (!$package) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('An error occured, please, make sure that your "AceSarch=>Configuration=>Download-ID" and/or the "Global Configuration=>Server=>Path to Temp-folder" field has a valid value.'));
			return false;
		}
		
		// Delete the package file
		JFile::delete($tmp_dest.DS.$p_file);
		
		return $package;
	}
}