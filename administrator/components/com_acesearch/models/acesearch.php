<?php
/**
* @version		1.5.0
* @package		AceSearch
* @subpackage	AceSearch
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

//No Permision
defined('_JEXEC') or die('Restricted access');

class AcesearchModelAcesearch extends AcesearchModel {

	function __construct(){
		parent::__construct('acesearch');
	}
	
	function saveDownloadID() {
		$download_id = trim(JRequest::getVar('download_id', '', 'post', 'string'));
		
		if (strlen($download_id) == 32) {
			$AcesearchConfig = AcesearchFactory::getConfig();
			$AcesearchConfig->download_id = $download_id;
			
			AcesearchUtility::storeConfig($AcesearchConfig);
		}
	}
	
	// Check info
	function getInfo() {
		static $info;
		
		if (!isset($info)) {
			$info = array();
			
			if ($this->AcesearchConfig->version_checker == 1){
				$info['version_installed'] = AcesearchUtility::getXmlText(JPATH_ACESEARCH_ADMIN.DS.'acesearch.xml', 'version');
				$version_info = AcesearchCache::getRemoteInfo();
				
				$info['version_latest'] = $version_info['acesearch'];
				
				// Set the version status
				$info['version_status'] = version_compare($info['version_installed'], $info['version_latest']);
				$info['version_enabled'] = 1;
			}
			else {
				$info['version_status'] = 0;
				$info['version_enabled'] = 0;
			}
			
			$info['download_id'] = $this->AcesearchConfig->download_id;

			$info['extensions'] = AceDatabase::loadResult("SELECT COUNT(*) FROM #__acesearch_extensions");
			$info['keywords'] = AceDatabase::loadResult("SELECT COUNT(*) FROM #__acesearch_filters");
			$info['filters'] = AceDatabase::loadResult("SELECT COUNT(*) FROM #__acesearch_search_results");
		}
		
		return $info;
	}
	
	function getStats() {
		$count= array();
		
		$count['extensions'] = AceDatabase::loadResult("SELECT COUNT(*) FROM #__acesearch_extensions");
		$count['statistics'] = AceDatabase::loadResult("SELECT COUNT(*) FROM #__acesearch_search_results");
		$count['filters'] = AceDatabase::loadResult("SELECT COUNT(*) FROM #__acesearch_filters");
		
		return $count;
	}
}