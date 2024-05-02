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
class AcesearchModelConfig extends AcesearchModel {

	// Main constructer
	function __construct(){
		parent::__construct('config');
	}

	// Save configuration
	function save() {
		$config = new stdClass();
		
		// Main
		$config->version_checker				= JRequest::getVar('version_checker',			1,	'post', 'int');
		$config->download_id					= JRequest::getVar('download_id', 				'', 'post',	'string');
		$config->cache_versions					= JRequest::getVar('cache_versions',			0,	'post', 'int');
		$config->cache_extensions				= JRequest::getVar('cache_extensions',			0,	'post', 'int');
		
		// Front-end
		$config->save_results    				= JRequest::getVar('save_results',				1,		'post', 'int');
		$config->show_db_errors    				= JRequest::getVar('show_db_errors',			1,		'post', 'int');
		$config->show_url						= JRequest::getVar('show_url',					1,		'post', 'int');
		$config->show_properties				= JRequest::getVar('show_properties',			1,		'post', 'int');
		$config->show_display					= JRequest::getVar('show_display',				1,		'post', 'int');
		$config->show_ext_flt					= JRequest::getVar('show_ext_flt',				1,		'post', 'int');
		$config->search_char					= JRequest::getVar('search_char',				'', 	'post',	'string');
		$config->show_adv_search				= JRequest::getVar('show_adv_search',			1,		'post', 'int');
		$config->enable_complete				= JRequest::getVar('enable_complete',			1,		'post', 'int');
		$config->enable_suggestion				= JRequest::getVar('enable_suggestion',			1,		'post', 'int');
		$config->enable_highlight				= JRequest::getVar('enable_highlight',			1,		'post', 'int');
		$config->show_desc						= JRequest::getVar('show_desc',					1,		'post', 'int');
		$config->description_length				= JRequest::getVar('description_length', 		'', 	'post',	'string');
		$config->title_length					= JRequest::getVar('title_length', 				'', 	'post',	'string');
		$config->blacklist						= JRequest::getVar('blacklist', 				'', 	'post',	'string');
		$config->result_limit					= JRequest::getVar('result_limit',				'50', 	'post',	'string');
		$config->access_checker					= JRequest::getVar('access_checker',			0,		'post', 'int');
		$config->max_search_char				= JRequest::getVar('max_search_char',			0,		'post', 'int');
		$config->results_format					= JRequest::getVar('results_format',			1,		'post', 'int');
		$config->date_format					= JRequest::getVar('date_format',				0,		'post', 'string');
		
		// Back-end
		$config->admin_display					= JRequest::getVar('admin_display', 			'', 	'post',	'string');
		$config->admin_show_desc				= JRequest::getVar('admin_show_desc',			1,		'post', 'int');
		$config->admin_show_url					= JRequest::getVar('admin_show_url',			1,		'post', 'int');
		$config->admin_show_properties			= JRequest::getVar('admin_show_properties',		1,		'post', 'int');
		$config->admin_show_display				= JRequest::getVar('admin_show_display',		1,		'post', 'int');
		$config->admin_show_ext_flt				= JRequest::getVar('admin_show_ext_flt',		1,		'post', 'int');
		$config->admin_enable_complete			= JRequest::getVar('admin_enable_complete',		1,		'post', 'int');
		$config->admin_enable_suggestion		= JRequest::getVar('admin_enable_suggestion',	1,		'post', 'int');
		$config->admin_enable_highlight			= JRequest::getVar('admin_enable_highlight',	1,		'post', 'int');
		$config->admin_show_page_title			= JRequest::getVar('admin_show_page_title',		1,		'post', 'int');
		$config->admin_show_page_desc			= JRequest::getVar('admin_show_page_desc',		1,		'post', 'int');
		$config->admin_description_length		= JRequest::getVar('admin_description_length', 	'', 	'post',	'string');
		$config->admin_title_length				= JRequest::getVar('admin_title_length', 		'', 	'post',	'string');
		$config->admin_result_limit				= JRequest::getVar('admin_result_limit',		'50', 	'post',	'string');
		$config->admin_max_search_char			= JRequest::getVar('admin_max_search_char',		0,		'post', 'int');
		
		//Highlight
		$config->highlight_back1				= JRequest::getVar('highlight_back1', 		'', 	'post',	'string');
		$config->highlight_back2				= JRequest::getVar('highlight_back2', 		'', 	'post',	'string');
		$config->highlight_back3				= JRequest::getVar('highlight_back3',		'', 	'post',	'string');
		$config->highlight_back4				= JRequest::getVar('highlight_back4',		'', 	'post',	'string');
		$config->highlight_back5				= JRequest::getVar('highlight_back5',		'', 	'post',	'string');
		$config->highlight_text1				= JRequest::getVar('highlight_text1', 		'', 	'post',	'string');
		$config->highlight_text2				= JRequest::getVar('highlight_text2', 		'', 	'post',	'string');
		$config->highlight_text3				= JRequest::getVar('highlight_text3',		'', 	'post',	'string');
		$config->highlight_text4				= JRequest::getVar('highlight_text4',		'', 	'post',	'string');
		$config->highlight_text5				= JRequest::getVar('highlight_text5',		'', 	'post',	'string');
		
		AcesearchUtility::storeConfig($config);
	}
	
	function getLists(){		
		$lists = array();
		
		$results_format = array();
		$results_format[] = JHTML::_('select.option', '1', JText::_('COM_ACESEARCH_CONFIG_RESULTS_FORMAT_1'));
		$results_format[] = JHTML::_('select.option', '2', JText::_('COM_ACESEARCH_CONFIG_RESULTS_FORMAT_2'));
		
		$date_format = array();
		$date_format[] = JHTML::_('select.option', 'l, d F Y', JText::_('Monday, 01 April 2011'));
		$date_format[] = JHTML::_('select.option', 'M/d/y', JText::_('01/04/2011'));
		$date_format[] = JHTML::_('select.option', 'M-d-y', JText::_('(US Format) 01-25-2011'));
		$date_format[] = JHTML::_('select.option', 'd-M-y', JText::_('(European Format) 25-01-2011'));
		$date_format[] = JHTML::_('select.option', 'E, dd MMM yyyy HH:mm:ss', JText::_('Tue, 09 Jan 2002 22:14:02'));
		
		// Main
		$lists['version_checker']				= JHTML::_('select.booleanlist', 'version_checker', null, $this->AcesearchConfig->version_checker);
		$lists['access_checker']				= JHTML::_('select.booleanlist', 'access_checker', null, $this->AcesearchConfig->access_checker);
		$lists['show_db_errors']				= JHTML::_('select.booleanlist', 'show_db_errors', null, $this->AcesearchConfig->show_db_errors);
		$lists['cache_versions']				= JHTML::_('select.booleanlist', 'cache_versions', null, $this->AcesearchConfig->cache_versions);
		$lists['cache_extensions']				= JHTML::_('select.booleanlist', 'cache_extensions', null, $this->AcesearchConfig->cache_extensions);
		
		// Front-end
		$lists['save_results']					= JHTML::_('select.booleanlist', 'save_results', null, $this->AcesearchConfig->save_results);
		$lists['show_url']						= JHTML::_('select.booleanlist', 'show_url', null, $this->AcesearchConfig->show_url);
		$lists['show_properties']				= JHTML::_('select.booleanlist', 'show_properties', null, $this->AcesearchConfig->show_properties);
		$lists['show_display']					= JHTML::_('select.booleanlist', 'show_display', null, $this->AcesearchConfig->show_display);
		$lists['show_ext_flt']					= JHTML::_('select.booleanlist', 'show_ext_flt', null, $this->AcesearchConfig->show_ext_flt);
		$lists['show_adv_search']				= JHTML::_('select.booleanlist', 'show_adv_search', null, $this->AcesearchConfig->show_adv_search);
		$lists['enable_complete']				= JHTML::_('select.booleanlist', 'enable_complete', null, $this->AcesearchConfig->enable_complete);
		$lists['enable_highlight']				= JHTML::_('select.booleanlist', 'enable_highlight', null, $this->AcesearchConfig->enable_highlight);
		$lists['enable_suggestion']				= JHTML::_('select.booleanlist', 'enable_suggestion', null, $this->AcesearchConfig->enable_suggestion);
		$lists['show_desc']						= JHTML::_('select.booleanlist', 'show_desc', null, $this->AcesearchConfig->show_desc);
		$lists['results_format']				= JHTML::_('select.genericlist', $results_format, 'results_format', '', 'value' , 'text', $this->AcesearchConfig->results_format);
		$lists['date_format']					= JHTML::_('select.genericlist', $date_format, 'date_format', '', 'value' , 'text', $this->AcesearchConfig->date_format);
		
		// Back-end
		$lists['admin_show_desc']				= JHTML::_('select.booleanlist', 'admin_show_desc', null, $this->AcesearchConfig->admin_show_desc);
		$lists['admin_show_url']				= JHTML::_('select.booleanlist', 'admin_show_url', null, $this->AcesearchConfig->admin_show_url);
		$lists['admin_show_properties']			= JHTML::_('select.booleanlist', 'admin_show_properties', null, $this->AcesearchConfig->admin_show_properties);
		$lists['admin_show_display']			= JHTML::_('select.booleanlist', 'admin_show_display', null, $this->AcesearchConfig->admin_show_display);
		$lists['admin_show_ext_flt']			= JHTML::_('select.booleanlist', 'admin_show_ext_flt', null, $this->AcesearchConfig->admin_show_ext_flt);
		$lists['admin_enable_complete']	  		= JHTML::_('select.booleanlist', 'admin_enable_complete', null, $this->AcesearchConfig->admin_enable_complete);
		$lists['admin_enable_highlight']  		= JHTML::_('select.booleanlist', 'admin_enable_highlight', null, $this->AcesearchConfig->admin_enable_highlight);
		$lists['admin_enable_suggestion'] 		= JHTML::_('select.booleanlist', 'admin_enable_suggestion', null, $this->AcesearchConfig->admin_enable_suggestion);
		
		return $lists;
	}
}