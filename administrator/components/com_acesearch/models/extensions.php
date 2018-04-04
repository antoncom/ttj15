<?php
/**
* @version		1.5.0
* @package		AceSearch
* @subpackage	AceSearch
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

//No Permision
defined( '_JEXEC' ) or die( 'Restricted access' );

// Imports
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.installer.installer');
jimport('joomla.installer.helper');
require_once(JPATH_ACESEARCH_ADMIN.DS.'adapters'.DS.'acesearch_ext.php');

class AcesearchModelExtensions extends AcesearchModel{
	
	function __construct() {
		parent::__construct('extensions');
		
		$this->_getUserStates();
		$this->_buildViewQuery();
	}
	
	function _getUserStates() {
		$this->filter_order		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order',		'filter_order',		'name');
		$this->filter_order_Dir	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order_Dir',	'filter_order_Dir',	'ASC');
		$this->search_name		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search_name', 		'search_name', 		'');
        $this->filter_access	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_access', 	'filter_access', 	'-1');
        $this->filter_handler	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_handler', 	'filter_handler', 	'-1');
        $this->search_cname		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search_cname', 	'search_cname', 	'');
        $this->client			= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.client',  			'client', 			'-1');
        $this->search_name		= JString::strtolower($this->search_name);
		$this->search_cname		= JString::strtolower($this->search_cname);
	}
	
	
	function getLists() {
		$lists = array();
		
		// Table ordering
		$lists['order_dir'] = $this->filter_order_Dir;
		$lists['order'] 	= $this->filter_order;
		
		// Filter's action
		$javascript = 'onchange="document.adminForm.submit();"';
		
		// Client filters
		$clients = array();
		$clients[] = JHTML::_('select.option', '-1', JText::_('COM_ACESEARCH_EXTENSIONS_CLIENT_ALL'));
		$clients[] = JHTML::_('select.option', '0', JText::_('COM_ACESEARCH_EXTENSIONS_CLIENT_FRONT'));
		$clients[] = JHTML::_('select.option', '1', JText::_('COM_ACESEARCH_EXTENSIONS_CLIENT_BACK'));
		$clients[] = JHTML::_('select.option', '2', JText::_('COM_ACESEARCH_EXTENSIONS_CLIENT_BOTH'));
		$lists['client'] = JHTML::_('select.genericlist',$clients , 'client', $javascript, 'value' ,'text' , $this->client);
		
		// Reset filters
		$lists['reset_filters'] = '<button onclick="resetFilters();">'. JText::_('Reset') .'</button>';
		
		// Search name
        $lists['search_name'] = "<input type=\"text\" name=\"search_name\" value=\"{$this->search_name}\" size=\"25\" maxlength=\"255\" onchange=\"document.adminForm.submit();\" />";

		// Access Filter
		$access_list = array();
		$access_list[] = JHTML::_('select.option', '-1', JText::_('COM_ACESEARCH_COMMON_SELECT'));
		$groups = AceDatabase::loadObjectList('SELECT id, name FROM #__groups');
		foreach($groups as $group) {
			$access_list[] = JHTML::_('select.option', $group->id, $group->name);
		}
		$lists['access_list'] = JHTML::_('select.genericlist', $access_list, 'filter_access', 'class="inputbox" size="1"'.$javascript, 'value', 'text', $this->filter_access);
		
		// Handler Filter
		$handler_list = array();
		$handler_list[] = JHTML::_('select.option', '-1', JText::_('COM_ACESEARCH_COMMON_SELECT'));
		$handler_list[] = JHTML::_('select.option', '1', JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_SELECT_EXTENSION'));
		$handler_list[] = JHTML::_('select.option', '2', JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_SELECT_PLUGIN'));
		$handler_list[] = JHTML::_('select.option', '0', JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_SELECT_DISABLE'));
   	   	$lists['handler_list'] = JHTML::_('select.genericlist', $handler_list, 'filter_handler', 'class="inputbox" size="1"'.$javascript,'value', 'text', $this->filter_handler);
		
        // Search custon name
        $lists['search_cname'] = "<input type=\"text\" name=\"search_cname\" value=\"{$this->search_cname}\" size=\"20\" maxlength=\"255\" onchange=\"document.adminForm.submit();\" />";
        
		return $lists;
	}
	
	// Routers state
	function checkComponents() {
		$filter = "'com_acesearch', 'com_search', 'com_user', 'com_contact', 'com_dump', 'com_wrapper', 'com_mailto', 'com_joomfish', 'com_config', 'com_media', 'com_installer', 'com_templates',  'com_cpanel', 'com_cache', 'com_messages',  'com_massmail', 'com_languages'";
		$components = AceDatabase::loadObjectList('SELECT `option`, `id` FROM `#__components` WHERE `parent` = "0" AND `option` != "" AND `option` NOT IN ('.$filter.')');

		foreach ($components as $component) {
			// Check if there is already a record available
			$total = AceDatabase::loadResult("SELECT COUNT(*) FROM #__acesearch_extensions WHERE extension = '".$component->option."'");
			
			if ($total < 1) {
				$name = "";
				$handler_found = false;
				
				if (!$handler_found){
					$ext = JPATH_ACESEARCH_ADMIN.DS.'extensions'.DS.$component->option.'.php';
					if (file_exists($ext)) {
						$name = AcesearchUtility::getXmlText(JPATH_ACESEARCH_ADMIN.DS.'extensions'.DS.$component->option.'.xml', 'name');
						$handler = 1;
						$handler_found = true;
					}
				}
				
				if (!$handler_found){
					$plugin = AcesearchUtility::findSearchPlugin($component->option);
					if ($plugin) {
						$handler = 2;
						$handler_found = true;
					}
				}
				
				if (!$handler_found){
					$handler = 0;
				}
				
				$params = "handler={$handler}";
				$params .= "\ncustom_name=";
				
				AceDatabase::query("INSERT INTO #__acesearch_extensions (name, extension, params) VALUES ('{$name}', '{$component->option}', '{$params}')");
			}
		}
	}
	
	// Install / Upgrade extensions
	function installUpgrade() {
		// Check if the extensions directory is writable
		$directory = JPATH_ACESEARCH_ADMIN.DS.'extensions';
		if (!is_writable($directory)) {
			JError::raiseWarning('1001', JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_INSTALL_DIR_CHMOD_ERROR'));
		}
		
		$result = false;
		
		// Get vars
		$userfile 	= JRequest::getVar('install_package', null, 'files', 'array');
		$ext_url 	= JRequest::getVar('joomaceurl');
		
		// Manual upgrade or install
		AcesearchUtility::import('library.installer');
		if ($userfile) {
			$package = AcesearchInstaller::getPackageFromUpload($userfile);
		}
		// Automatic upgrade
		elseif($ext_url) {
			// Download the package
			$package = AcesearchInstaller::getPackageFromServer($ext_url);
		}

		// Get an installer instance
		$installer =& JInstaller::getInstance();
		$adapter = new JInstallerAcesearch_Ext($installer);
		$installer->setAdapter('acesearch_ext', $adapter);
		
		// Install the package
		if (!$installer->install($package['dir'])) {
			// There was an error installing the package
			$msg = JText::sprintf('INSTALLEXT', JText::_($package['type']), JText::_('Error'));
			$result = false;
		} else {
			// Package installed sucessfully
			$msg = JText::sprintf('INSTALLEXT', JText::_($package['type']), JText::_('Success'));
			$result = true;
		}

		return $result;
	}
	
	// Uninstall extensions
	function uninstall() {
		// Get where
		$where = AcesearchController::_buildSelectedWhere();
		
		// Get extensions
		$extensions = AceDatabase::loadAssocList("SELECT id, extension, params FROM #__acesearch_extensions {$where}", "id");
		
		// Action
		if (AceDatabase::query("DELETE FROM #__acesearch_extensions {$where}")) {
			foreach ($extensions as $id => $record) {
				$extension = $record['extension'];
				
				// Update router param
				$params = array();
				$router = 1;
				
				$p = new JParameter($record['params']);
				
				$params['custom_name'] = $p->get('custom_name', '');
				AcesearchUtility::storeParams('AcesearchExtensions', $id, 'params', $params);
				
				// Remove the extension files
				if (file_exists(JPATH_ACESEARCH_ADMIN.DS.'extensions'.DS.$extension.'.php')){
					JFile::delete(JPATH_ACESEARCH_ADMIN.DS.'extensions'.DS.$extension.'.xml');
					JFile::delete(JPATH_ACESEARCH_ADMIN.DS.'extensions'.DS.$extension.'.php');
				}
			}
		}
		
		return;
	}
	
	// Save changes
	function save() {
		$ids 			= JRequest::getVar('id');
		$handler 		= JRequest::getVar('handler');
		$custom_name 	= JRequest::getVar('custom_name');
		$order			= JRequest::getVar('order');
	
		foreach ($ids as $id => $val) {
			$params = array();
			$params['handler'] = $handler[$id];
			$params['custom_name'] = $custom_name[$id];
			AcesearchUtility::storeParams('AcesearchExtensions', $id, 'params', $params);
			
			AcesearchUtility::setData('AcesearchExtensions', $id, 'ordering', $order[$id]);
		}
	}
	
	function getInfo() {
		static $information;
		
		$information = array();
		if ($this->AcesearchConfig->version_checker == 1) {
			$information = AcesearchCache::getRemoteInfo();
			unset($information['acesearch']);
		}
		
		return $information;
    }
		
	function getGroups () {
		return AceDatabase::loadObjectList("SELECT name FROM #__groups");
	}
	
	function _buildViewQuery() {
		$where = $this->_buildViewWhere();
	
		$orderby = " ORDER BY {$this->filter_order} {$this->filter_order_Dir}, extension";
		
		$this->_query = "SELECT * FROM #__acesearch_extensions  {$where}{$orderby}";
	}
	
	// Filters function
	function _buildViewWhere() {
		$where = array();
		
		if (!empty($this->search_name)) {
			$src = parent::secureQuery($this->search_name, true);
			$where[] = "LOWER(name) LIKE {$src} OR LOWER(extension) LIKE {$src}";
		}
		
		if ($this->filter_access != '-1') {
			$src = $this->_db->getEscaped($this->filter_access, true);
			$where[] = "params LIKE '%access={$src}%'";
		}
		
		if ($this->filter_handler != '-1') {
			$src = $this->_db->getEscaped($this->filter_handler, true);
			$where[] = "params LIKE '%handler={$src}%'";
		}
		
		if (!empty($this->search_cname)) {
			$src = $this->_db->getEscaped($this->search_cname, true);
			$where[] = "params LIKE '%custom_name={$src}%'";
		}
		
		if ($this->client != '-1') {
			$where[] = "client = {$this->client}";
		}
		
		$where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');
		
		return $where;
	}
}