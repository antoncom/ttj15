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
class AcesearchModelPurge extends AcesearchModel {
	
	// Main constructer
	function __construct() {
        parent::__construct('purge');
    }
	
	// Clean Cache
    function cleanCache() {
		$cache = AcesearchFactory::getCache();
		$rt = false;
		
		// Get selections
		$cache_versions		= JRequest::getInt('cache_versions', 0, 'post');
		$cache_extensions	= JRequest::getInt('cache_extensions', 0, 'post');
		
		if ($cache_versions) {
			$cache->remove('versions');
			$rt = true;
		}
		
		if ($cache_extensions) {
			$cache->remove('extensions');
			$rt = true;
		}
		
		return $rt;
    }
    
	// Count cache
    function getCountCache() {
		$cache = AcesearchFactory::getCache();
		
		$count = array();
		$items = array('versions', 'extensions');
		
		foreach ($items as $item) {
			$contents = $cache->load($item);
			if (!empty($contents)) {
				$count[$item] = count($contents);
			} else {
				$count[$item] = 0;
			}
		}
		
		return $count;
    }
}