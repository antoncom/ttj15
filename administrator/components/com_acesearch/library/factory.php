<?php
/**
* @version		1.5.0
* @package		AceSearch Library
* @subpackage	Factory
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted Access');

// Factory class
abstract class AcesearchFactory {
	
	static function &getConfig() {
		static $instance;
		
		if (!is_object($instance)) {
			$instance = new stdClass();
			
			jimport('joomla.application.component.helper');
			$acesearch =& JComponentHelper::getComponent('com_acesearch');
			
			$params = explode("\n", $acesearch->params);
			if (!empty($params)) {
				$array_keys = array();
				
				foreach ($params as $param){
					$pos = strpos($param, '=');
					
					$key = trim(substr($param, 0, $pos));
					$value = trim(substr($param, $pos + 1));
					
					if (empty($key)) {
						continue;
					}
					
					if (!isset($value)) {
						$value = '';
					}
					
					if (!empty($array_keys) && in_array($key, $array_keys)) {
						$value = json_decode(stripslashes($value), true);
					}
					
					$instance->$key = $value;
				}
			}
		}
		
		return $instance;
	}
	
	static function &getCache($lifetime = '315360000') {
		static $instances = array();
		
		if (!isset($instances[$lifetime])) {
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_acesearch'.DS.'library'.DS.'cache.php');
			$instances[$lifetime] = new AcesearchCache($lifetime);
		}
		
		return $instances[$lifetime];
	}

	static function getTable($name) {
		static $tables = array();
		
		if (!isset($tables[$name])) {
			JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_acesearch'.DS.'tables');
			$tables[$name] =& JTable::getInstance($name, 'Table');
		}
		
		return $tables[$name];
	}
	
	static function &getExtension($option) {
		static $instances = array();
		
		if (!isset($instances[$option])) {
			jimport('joomla.html.parameter');
			$file = JPATH_ADMINISTRATOR.'/components/com_acesearch/extensions/'.$option.'.php';
			
			if (!file_exists($file)) {
				$instances[$option] = null;
				
				return $instances[$option];
			}
			
			require_once($file);
			
			$cache = self::getCache();
			$extensions = $cache->getExtensions();
			$params = new JParameter($extensions[$option]->params);
			
			$class_name = 'AceSearch_'.$option;
			
			$instances[$option] = new $class_name($extensions[$option], $params);
		}
		
		return $instances[$option];
	}
}