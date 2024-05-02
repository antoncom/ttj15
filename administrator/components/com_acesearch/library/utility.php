<?php
/**
* @version		1.5.0
* @package		AceSearch Library
* @subpackage	Utility
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted Access');

// Imports
jimport('joomla.filesystem.file');

// Utility class
class AcesearchUtility {
	
	static $props = array();
	
	function __construct() {
		// Get config object
		$this->AcesearchConfig = AcesearchFactory::getConfig();
	}
	
	function import($path) {
		require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_acesearch' . DS . str_replace('.', '/', $path).'.php');
	}
	
	function render($path) {
		ob_start();
		require_once($path);
		$contents = ob_get_contents();
		ob_end_clean();
		
		return $contents;
	}
    
    function get($name, $default = null) {
        if (!is_array(self::$props) || !isset(self::$props[$name])) {
            return $default;
        }
        
        return self::$props[$name];
    }
    
    function set($name, $value) {
        if (!is_array(self::$props)) {
            self::$props = array();
        }
        
        $previous = self::get($name);
        self::$props[$name] = $value;
        
        return $previous;
    }
	
	function getConfigState($params, $cfg_name, $prm = "") {
		if (!is_object($params)) {
			return false;
		}
		
		$prm_name = $cfg_name;
		if ($prm != "") {
			$prm_name = $prm;
		}
		
		$param = $params->get($prm_name, 'g');
		
		if (($param == '0') || (isset($this->AcesearchConfig->$cfg_name) && $this->AcesearchConfig->$cfg_name == '0' ) ) {
			return false;
		}
		
		return true;
    }
	
	function &getMenu() {
		jimport('joomla.application.menu');
		$options = array();
		
		$menu =& JMenu::getInstance('site', $options);
		
		if (JError::isError($menu)) {
			$null = null;
			return $null;
		}
		
		return $menu;
	}
	
	function getItemid($advanced = false) {
		require_once(JPATH_ADMINISTRATOR . '/components/com_acesearch/library/extension.php');
		
		$Itemid = '';
		$vars = array();
		
		$vars['option'] = 'com_acesearch';
		
		if ($advanced) {
			$vars['view'] = 'advancedsearch';
			$item = AcesearchExtension::findItemid($vars);
			
			if (!$item) {
				$vars['view'] = 'search';
				$item = AcesearchExtension::findItemid($vars);
			}
		}
		else {
			$vars['view'] = 'search';
			$item = AcesearchExtension::findItemid($vars);
			
			if (!$item) {
				$vars['view'] = 'advancedsearch';
				$item = AcesearchExtension::findItemid($vars);
			}
		}
		
		if (!empty($item)) {
			$Itemid = '&Itemid='.$item->id;
		}
		
		return $Itemid;
	}
	
	function getComponents() {
		static $components;
		
		if(!isset($components)) {
			$filter = "'com_user', 'com_joomfish', 'com_config', 'com_media', 'com_installer', 'com_templates', 'com_cpanel', 'com_cache', 'com_messages',  'com_massmail', 'com_languages'";
			$rows = AceDatabase::loadObjectList("SELECT `name`, `option` FROM `#__components` WHERE `parent` = '0' AND `option` != '' AND `option` NOT IN ({$filter}) ORDER BY `name`");
			
			foreach($rows as $row) {
				$components[] = JHTML::_('select.option', $row->option, $row->name);
			}
		}
		
		return $components;
	}
	
	function getExtensionFromRequest() {
		static $extension;
		
		if (!isset($extension)) {
			$cid = JRequest::getVar('cid', array(0), 'method', 'array');
			$extension = AceDatabase::loadResult("SELECT extension FROM #__acesearch_extensions WHERE id = ".$cid[0]);
		}
		
		return $extension;
	}
	
	function getHandlerList($component) {
		static $handlers = array();
		
		if (!isset($handlers[$component])) {
			$extension_file = JPATH_ACESEARCH_ADMIN.'/extensions/'.$component.'.php';
			if (file_exists($extension_file)) {
				$handlers[$component][] = JHTML::_('select.option', 1, JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_SELECT_EXTENSION'));
			}
			
			$plugin = self::findSearchPlugin($component);
			if ($plugin) {
				$handlers[$component][] = JHTML::_('select.option', 2, JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_SELECT_PLUGIN'));
			}
			
			$handlers[$component][] = JHTML::_('select.option', 0, JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_SELECT_DISABLE'));
		}
		
		return $handlers[$component];
	}
	
	function findSearchPlugin($component) {
		jimport('joomla.plugin.helper');
		
		$plugin = substr($component, 4);
		
		$found = JPluginHelper::isEnabled('search', $plugin);
		
		if (!$found) {
			$plugin = $plugin.'search';
			$found = JPluginHelper::isEnabled('search', $plugin);
		}
		
		if (!$found) {
			$plugin = self::_fixSearchPlugin($component);
			$found = JPluginHelper::isEnabled('search', $plugin);
		}
		
		if (!$found) {
			return false;
		}
		
		return $plugin;
	}
	
	function _fixSearchPlugin($component) {
		$com = '';
		
		switch($component) {
			case 'com_x':
				$com = 'x';
				break;
			default:
				$com = substr($component, 4);
				break;
		}
		
		return $com;
	}

	function getOptionFromRealURL($url) {
		$url = str_replace('&amp;', '&', $url);
		$url = str_replace('index.php?', '', $url);		
		parse_str($url, $vars);
		
		if (isset($vars['option'])) {
			return $vars['option'];
		} else {
			return '';
		}
	}
	
    function replaceLoop($search, $replace, $text) {
        $count = 0;
		
		if (!is_string($text)) {
			return $text;
		}
		
		while ((strpos($text, $search) !== false) && ($count < 10)) {
            $text = str_replace($search, $replace, $text);
			$count++;
        }

        return $text;
    }
	
	function storeConfig($AcesearchConfig) {
		$old_config = get_object_vars($AcesearchConfig);
	
		$config = "";
		
		foreach ($old_config as $key => $value) {
			if (is_array($value)) {
				$config .= $key.'='.addslashes(json_encode($value)).'\n';
			} else {
				$config .= $key.'='.$value.'\n';
			}
		}
		
		$db =& JFactory::getDBO();
		$db->setQuery('UPDATE #__components SET params = "'.$config.'" WHERE link = "option=com_acesearch" AND parent = 0');
		$db->query();
	}
	
	function getParam($text, $param) {
		$params = new JParameter($text);
		return $params->get($param);
	}
	
	function storeParams($table, $id, $db_field, $new_params) {
		$row = AcesearchFactory::getTable($table);
		if (!$row->load($id)) {
			return false;
		}
		
		$params = new JParameter($row->$db_field);
		
		foreach ($new_params as $name => $value) {
			$params->set($name, $value);
		}
		
		$row->$db_field = $params->toString();
		
		if (!$row->check()) {
			return false;
		}
		
		if (!$row->store()) {
			return false;
		}
	}
	
	function setData($table, $id, $db_field, $new_field) {
		$row = AcesearchFactory::getTable($table);
		if (!$row->load($id)) {
			return false;
		}
		$row->$db_field = $new_field;	

		if (!$row->check()) {
			return false;
		}
		
		if (!$row->store()) {
			return false;
		}
	}
	
	function checkPlugin() {
		if ((ACESEARCH_PACK == 'plus' || ACESEARCH_PACK == 'pro') && strlen(AcesearchFactory::getConfig()->download_id) != 32) {
			return false;
		}
		
		return true;
	}
	
	function getPlugin() {
		if (strlen(AcesearchFactory::getConfig()->download_id) != 32) {
			$bs = 'ba'.'s'.'e'.'6'.'4'.'_'.'de'.'co'.'de';
			echo $bs('PGRpdiBzdHlsZT0idGV4dC1hbGlnbjpjZW50ZXI7IGZvbnQtc2l6ZTo5cHg7IHZpc2liaWxpdHk6
					dmlzaWJsZTsiIHRpdGxlPSJKb29tbGEgU2VhcmNoIGJ5IEFjZVNlYXJjaCI+PGEgaHJlZj0iaHR0
					cDovL3d3dy5qb29tYWNlLm5ldC9qb29tbGEtZXh0ZW5zaW9ucy9hY2VzZWFyY2giIHRhcmdldD0i
					X2JsYW5rIj5Kb29tbGEgU2VhcmNoIGJ5IEFjZVNlYXJjaDwvYT48L2Rpdj4=');
			
		}
	}
		
	function getRemoteData($url) {
		$user_agent = "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 (.NET CLR 3.5.30729)";
		$data = false;

		// cURL
		if (extension_loaded('curl')) {
			// Init cURL
			$ch = @curl_init();
			
			// Set options
			@curl_setopt($ch, CURLOPT_URL, $url);
			@curl_setopt($ch, CURLOPT_HEADER, 0);
			@curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			@curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			@curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
			@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
			
			// Set timeout
			@curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			
			// Grab data
			$data = @curl_exec($ch);
			
			// Clean up
			@curl_close($ch);
			
			// Return data
			return $data;
		}

		// fsockopen
		if (function_exists('fsockopen')) {
			$errno = 0;
			$errstr = '';
			
			$url_info = parse_url($url);
			if($url_info['host'] == 'localhost')  {
				$url_info['host'] = '127.0.0.1';
			}

			// Open socket connection
			$fsock = @fsockopen($url_info['scheme'].'://'.$url_info['host'], 80, $errno, $errstr, 5);
		
			if ($fsock) {				
				@fputs($fsock, 'GET '.$url_info['path'].(!empty($url_info['query']) ? '?'.$url_info['query'] : '').' HTTP/1.1'."\r\n");
				@fputs($fsock, 'HOST: '.$url_info['host']."\r\n");
				@fputs($fsock, "User-Agent: ".$user_agent."\n");
				@fputs($fsock, 'Connection: close'."\r\n\r\n");
		
				// Set timeout
				@stream_set_blocking($fsock, 1);
				@stream_set_timeout($fsock, 5);
				
				$data = '';
				$passed_header = false;
				while (!@feof($fsock)) {
					if ($passed_header) {
						$data .= @fread($fsock, 1024);
					} else {
						if (@fgets($fsock, 1024) == "\r\n") {
							$passed_header = true;
						}
					}
				}
				
				// Clean up
				@fclose($fsock);
				
				// Return data
				return $data;
			}
		}

		// fopen
		if (function_exists('fopen') && ini_get('allow_url_fopen')) {
			// Set timeout
			if (ini_get('default_socket_timeout') < 5) {
				ini_set('default_socket_timeout', 5);
			}
			
			@stream_set_blocking($handle, 1);
			@stream_set_timeout($handle, 5);
			@ini_set('user_agent',$user_agent);
			
			$url = str_replace('://localhost', '://127.0.0.1', $url);
			
			$handle = @fopen($url, 'r');
			
			if ($handle) {
				$data = '';
				while (!feof($handle)) {
					$data .= @fread($handle, 8192);
				}
				
				// Clean up
				@fclose($handle);
			
				// Return data
				return $data;
			}
		}
		
		// file_get_contents
		if (function_exists('file_get_contents') && ini_get('allow_url_fopen')) {
			$url = str_replace('://localhost', '://127.0.0.1', $url);
			@ini_set('user_agent',$user_agent);
			$data = @file_get_contents($url);
			
			// Return data
			return $data;
		}
		
		return $data;
	}
	
	// Get text from XML
	function getXmlText($file, $variable) {
		// Try to find variable
		$value = null;
		if (JFile::exists($file)) {
			$xml =& JFactory::getXMLParser('Simple');
			if ($xml->loadFile($file)) {
				$root =& $xml->document;
				$element =& $root->getElementByPath($variable);
				$value = $element ? $element->data() : '';
			}
		}
		return $value;
    }
}