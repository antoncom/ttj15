<?php
/**
* @version		1.5.0
* @package		AceSearch Library
* @subpackage	Cache
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted Access');

// Imports
jimport('joomla.cache.cache');

// Cache class
class AcesearchCache extends JCache {

	function __construct($lifetime) {
		$this->AcesearchConfig = AcesearchFactory::getConfig();
		
		$JoomlaConfig =& JFactory::getConfig();
		$options = array(
			'defaultgroup' 	=> 'com_acesearch',
			'lifetime' 		=> $lifetime,
			'language' 		=> 'en-GB',
			'storage'		=> $JoomlaConfig->getValue('config.cache_handler', 'file')
		);
		
		parent::__construct($options);
	}

    function load($id) {
        $content = parent::get($id);
        
        if ($content === false) {
            return false;
        }
        
        $cache = @unserialize($content);
		
		if ($cache === false || !is_array($cache)) {
            return false;
        }
		
		return $cache;
    }
	
	function save($content, $id) {
		// Store the cache string
		for ($i = 0; $i < 5; $i++) {
            if (parent::store(serialize($content), $id)) {
                return;
            }
        }
		
		parent::remove($id);
	}
	
	function getExtensions($filter = 0) {
		if (($filter == 1) || (JFactory::getApplication()->isSite())){
			$where = ' WHERE params NOT LIKE "%handler=0%" AND (client = 0 OR client = 2)';
			
			$aid =& JFactory::getUser()->get('aid');
			
			if ($aid == 0) {
				$where .= ' AND params LIKE "%access=0%" ';
			}
			elseif ($aid == 1) {
				$where .= ' AND (params LIKE "%access=0%" OR params LIKE "%access=1%")';
			}
		}
		elseif (JFactory::getApplication()->isAdmin()){
			$where = ' WHERE params LIKE "%handler=1%" AND (client = 1 OR client = 2)';
		}
		
		if ($this->AcesearchConfig->cache_extensions == 1) {
			$cache = AcesearchFactory::getCache();
			$cached_extensions = $cache->load('extensions');
		
			if (!empty($cached_extensions)) {
				return $cached_extensions;
			}
		}

        static $extensions;
		if (!isset($extensions)) {
			$fields = "id, name, extension, params";
			$extensions = AceDatabase::loadObjectList("SELECT {$fields} FROM #__acesearch_extensions {$where} ORDER BY ordering ASC, name ASC", 'extension');
		}
		
		if (!empty($extensions)) {
			if ($this->AcesearchConfig->cache_extensions == 1) {
				$cache->save($extensions, 'extensions');
			}
			
			return $extensions;
		}
		
		return false;
	}
	
	function getFilter($id){
		if (empty($id)) {
			return null;
		}
		
		static $cache = array();
		
		if (!isset($cache[$id])) {
			$id = intval($id);
			$cache[$id] = AceDatabase::loadObject("SELECT id, author, extension, category FROM #__acesearch_filters WHERE published = 1 AND id = {$id}");
		}
		
		return $cache[$id];
	}
	
	function getRemoteInfo() {
		// Get config object
		if (!isset($this->AcesearchConfig)) {
			$this->AcesearchConfig = AcesearchFactory::getConfig();
		}
		
		static $information;
		
		if ($this->AcesearchConfig->cache_versions == 1) {
			$cache = AcesearchFactory::getCache('86400');
			$information = $cache->load('versions');
		}
		
		if (!is_array($information)) {
			$information = array();
			$information['acesearch'] = '?.?.?';
			
			$components = AcesearchUtility::getRemoteData('http://www.joomace.net/index.php?option=com_aceversions&view=xml&format=xml&catid=6');
			$extensions = AcesearchUtility::getRemoteData('http://www.joomace.net/index.php?option=com_aceversions&view=xml&format=xml&catid=2');
			
			if (strstr($components, '<?xml version="1.0" encoding="UTF-8" ?>')) {
				$xml =& JFactory::getXMLparser('Simple');
				$xml->loadString($components);
				$manifest = $xml->document;
				$category = $manifest->getElementByPath('category');
				
				foreach ($category->children() as $component) {
					$option = $component->attributes('option');
					$compability = $component->attributes('compability');

					if ($option == 'com_acesearch' && ($compability == '1.5' || $compability == '1.5_1.6')) {
						$information['acesearch'] = trim($component->attributes('version'));
						break;
					}
				}
			}
			
			if (strstr($extensions, '<?xml version="1.0" encoding="UTF-8" ?>')) {
				$xml =& JFactory::getXMLparser('Simple');
				$xml->loadString($extensions);
				$manifest = $xml->document;
				$category = $manifest->getElementByPath('category');
				
				foreach ($category->children() as $extension) {
					$option = $extension->attributes('option');
					$compability = $extension->attributes('compability');
					
					if ($compability == '1.5' || $compability == '1.5_1.6') {
						$ext = new stdClass();
						$ext->version		= trim($extension->attributes('version'));
						$ext->link			= trim($extension->attributes('download'));
						$ext->description	= trim($extension->attributes('description'));
					
						$information[$option] = $ext;
					}
				}
			}
			
			if ($this->AcesearchConfig->cache_versions == 1 && !empty($information)) {
				$cache->save($information, 'versions');
			}
		}
		
		return $information;
	}
}