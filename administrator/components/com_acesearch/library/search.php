<?php
/**
* @version		1.5.0
* @package		AceSearch Library
* @subpackage	Search
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

//No Permision
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class AcesearchSearch extends JModel {

	var $_data 		 = null;
	var $_total 	 = null;
	var $_pagination = null;
	var $_request    = null;
	
	function __construct() {
		parent::__construct();
		
		$mainframe =& JFactory::getApplication();
		$this->AcesearchConfig = AcesearchFactory::getConfig();
		
		$limit = $mainframe->getUserStateFromRequest('com_acesearch.limit', 'limit', $mainframe->getCfg('config.list_limit'), 'int');
		
		if ($mainframe->isSite()) { 
			$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
        }
		else {
			$limitstart	= $mainframe->getUserStateFromRequest('limitstart', 'limitstart', 0, 'int');
		}
		
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
	
		$this->_request = self::_getRequest();
	}
	
	// Get advanced search
    function getAdvancedSearch($extension, $is_module = false) {			
		$html = ''; 
		
		$xml_file = JPATH_ACESEARCH_ADMIN.'/extensions/'.$extension.'.xml';
		
		$xml =& JFactory::getXMLParser('Simple');
		if (!$xml->loadFile($xml_file)) {
			unset($xml);
			return $html;
		}
		
		if (is_null($xml)) {
			unset($xml);
			return $html;
		}
		
		$manifest =& $xml->document;
		
        $fields_xml = $manifest->getElementByPath('fields');
		if (!is_a($fields_xml, 'JSimpleXMLElement') || (count($fields_xml->children()) == 0)) {
			return $html;
		}
		
		$extensions = AcesearchCache::getExtensions();
		$params = new JParameter($extensions[$extension]->params);
		
		if ($params->get('handler', '1') == '2') {
			return $html;
		}
		
		$custom_name = $params->get('custom_name', '');
		if (!empty($custom_name)) {
			$name = $custom_name;
		} else {
			$name = $extensions[$extension]->name;
		}
		
		$fields = array();
		
		$childrens = $fields_xml->children();
		
		foreach ($childrens as $children) {
			$field_name = $children->attributes('name');
			$field_client = $children->attributes('client');
			
			if (($params->get($field_name, '1') != '1') && JFactory::getApplication()->isSite()) {
				continue;
			}
			
			if ($field_client == '0' && !(JFactory::getApplication()->isSite())) {
				continue;
			}
			
			if ($field_client == '1' && !(JFactory::getApplication()->isAdmin())) {
				continue;
			}
			
			$fields[] = self::_renderField($children, $extensions[$extension], $is_module);
		}
		
		$acesearch_ext =& AcesearchFactory::getExtension($extension);
		$acesearch_ext->getExtraFieldsHTML($params, $fields, $is_module);
		
		if (empty($fields)) {
			return $html;
		}
		
		foreach ($fields as $field) {
			if (empty($field)) {
				continue;
			}
			
			$html .= $field;
		}
		
		if (!empty($html) && !$is_module) {		
			$html = '<fieldset class="acesearch_fieldset"><legend class="acesearch_legend">'.$name.' ('.JText::_('COM_ACESEARCH_SEARCH_EXTRA').') </legend>'.$html.'</fieldset>';
		}
		
		return $html;
    }
	
	function _renderField($field, $ext, $is_module) {
		$params = new JParameter($ext->params);
		
		$type = $field->attributes('type');
		$function = 'render'.ucfirst($type);
		
		if ($type == 'category' || $type == 'function') {
			$html = self::_renderFieldHTML($function, $field, $params, $ext->extension, $is_module);
		}
		else {
			$html = self::_renderFieldHTML($function, $field, $params, null, $is_module);
		}
		
		return $html;
	}
	
	function _renderFieldHTML($function, $field, $params, $extension, $is_module) {
		$suffix = '';
		
		if ($is_module) {
			if ($function == 'renderDays' || $function == 'renderDaterange' || $function == 'renderOrder') {
				return "";
			}
			
			$suffix = '_module';
		}
		
		$html = '<div style="float:left; width:95%">';
		$html .= '<span class="acesearch_span_label'.$suffix.'">';
		$html .= AcesearchHTML::renderJText($field, $params);
		$html .= '</span>';
		$html .= '<span class="acesearch_span_field'.$suffix.'">';
		
		if (!empty($extension)) {
			$html .= AcesearchHTML::$function($field, $extension, $suffix);
		}
		else {
			$html .= AcesearchHTML::$function($field, $params, $suffix);
		}
		
		$html .= '</span>';
		$html .= '</div>';
		
		return $html;
	}
	
	function getExtensionList($filter = 0, $filt_ext = -1,$module=""){
		$extension = JRequest::getCmd('ext', '');
		
		if(empty($extension)) {
			$uri =& JFactory::getURI();
			$extension =$uri->getVar('ext','');
		}
		$javascript= 'onchange="ChangeType(this.value)"';
		
		$extensions = array();
		$text = "COM_ACESEARCH_SEARCH_SECTIONS";
		if(!empty($module)){
			$text = "MOD_ACESEARCH_SEARCH_ALL";
			$javascript= 'onchange="changeExtModule(this.value)"';
		}
		
		$extensions[] = JHTML::_('select.option', '', JText::_($text));
		
		$items = AcesearchCache::getExtensions($filter);
		
		if (!empty($items)) {
			foreach($items as $key => $row){
				$handler = AcesearchUtility::getParam($row->params , 'handler');
				
				if ($filter == '1' && $handler == '2') {
					continue;
				}				
				
				$custom_name = AcesearchUtility::getParam($row->params, 'custom_name');
				
				if (!empty($custom_name)) {
					$name = $custom_name;
				}
				elseif (!empty($row->name)) {
					$name = $row->name;
				}
				else {
					$name = $row->extension;
				}
				
				$extensions[$row->extension] = JHTML::_('select.option', $row->extension, $name);
			}
		}
		
		if ($filter == '0') {
			$lists['extension'] = JHTML::_('select.genericlist', $extensions, 'ext', 'class="acesearch_selectbox'.$module.'"'.$javascript, 'value' ,'text', $extension);
		} else {
			$lists['extension'] = JHTML::_('select.genericlist', $extensions, 'extension', 'class="inputbox" size="10" style="width:150px height:100px"'.$javascript, 'value' ,'text', $filt_ext);
		}
		
		return $lists;
	}
	
	function getData() {
		$results = array();
		
		$this->_data = "";
		
		if (!empty($this->_request['query']) || !empty($this->_request['any']) || !empty($this->_request['exact']) || !empty($this->_request['none'])){
			if (empty($this->_request['ext'])) {				
				$rows = self::searchAllComponents();
			} else {	
				$rows = self::searchComponent($this->_request['ext']);
			}	
			$this->_total = count($rows);
			if (!empty($rows)){
				if ($this->getState('limit') > 0) {
					$this->_data = array_splice($rows, $this->getState('limitstart'), $this->getState('limit'));
				} else {
					$this->_data = $rows;
				}
			}
			
			if(empty($this->_data)) {
				$this->_data['suggest'] = self::getSuggestion();
			}
			
			if (!empty($this->_data) && (JFactory::getApplication()->isSite()) && ($this->AcesearchConfig->save_results == 1)){ 
				self::_saveResults();
			}
			
			
		}
		
		return $this->_data;
	}
	
	function searchAllComponents(){
		$rows = array();
		
		$components = AcesearchCache::getExtensions();
		
		if (empty($components)) {
			return $rows;
		}
		
		foreach($components as $component) {
			$results = self::searchComponent($component->extension);
			
			if (empty($results)) {
				continue;
			}
			
			$rows = array_merge($rows, $results);
		}
		
		return $rows;
	}
	
	function searchComponent($component) {
		$extensions = AcesearchCache::getExtensions();

		if (!isset($extensions[$component])) {
			return array();
		}
		
		$params = new JParameter($extensions[$component]->params);
		
		if ($params->get('handler', '1') == '0') {
			return array();
		}
		
		if ($params->get('handler', '1') == '1') {
			$acesearch_ext =& AcesearchFactory::getExtension($component);
			
			return $acesearch_ext->getResults();
		}
		
		if ($params->get('handler', '1') == '2' && JFactory::getApplication()->isSite()) {
			$query = $phrase = '';
			
			if (!empty($this->_request['query'])) {
				$phrase = 'all';
				$query = $this->_request['query'];
			}
			elseif (!empty($this->_request['any'])) {
				$phrase = 'any';
				$query = $this->_request['any'];
			}
			elseif (!empty($this->_request['exact'])) {
				$phrase = 'exact';
				$query = $this->_request['exact'];
			}
			
			if (empty($query)) {
				return array();
			}
			
			$plugin = AcesearchUtility::findSearchPlugin($component);
			if (!$plugin) {
				return array();
			}
			
			JPluginHelper::importPlugin('search', $plugin);
			$dispatcher =& JDispatcher::getInstance();
			$results = $dispatcher->trigger('onSearch', array($query, $phrase, 'popular', $plugin));
			
			if (empty($results) || !is_array($results)) {
				return array();
			}
			
			$ret_results = array();
			
			$n = count($results);
			for ($i = 0; $i < $n; $i++) {
				$resultss = @$results[$i];
				
				if (empty($resultss) || !is_array($resultss)) {
					continue;
				}
				
				$nn = count($resultss);
				for ($ii = 0; $ii < $nn; $ii++) {
					$result = @$resultss[$ii];
					
					if (!isset($result->title) || !isset($result->href)) {
						continue;
					}
					
					$result->name = $result->title;
					$result->description = $result->text;
					$result->link = $result->href;
					
					unset($result->title);
					unset($result->text);
					unset($result->href);
					
					$ret_results[] = $result;
				}
			}
			
			return $ret_results;
		}
	}
	
	function getComplete() {
		$query = AcesearchExtension::getSecureText(JRequest::getString('q'));
		
		return AceDatabase::loadResultArray("SELECT DISTINCT keyword FROM #__acesearch_search_results WHERE LOWER(keyword) LIKE {$query} ORDER BY search_result DESC");
	}
	
	function finalizeResult(&$row) {
		$site = JFactory::getApplication()->isSite();
		$admin = JFactory::getApplication()->isAdmin();
		
		$q = self::getQuery();
	
		if ($site) {
			$tlength = "title_length";
			$dlength = "description_length";
		} else {
			$tlength = "admin_title_length";
			$dlength = "admin_description_length";
		}
		
		$row->name = strip_tags(substr($row->name, 0 , $this->AcesearchConfig->$tlength));
		
		if (!empty($row->description)){
			$row->description = strip_tags(substr($row->description, 0, $this->AcesearchConfig->$dlength) . '...');
		} else {
			$row->description = "";
		}
		
		// Higlight
		if (!empty($q) && (($this->AcesearchConfig->admin_enable_highlight && $admin) || ($this->AcesearchConfig->enable_highlight && $site))) {
			$queries = explode(' ', $q);
			
			foreach ($queries as $key => $query) {
				if(!empty($query)) {
					$back  ='highlight_back'.($key +1);
					$clr   ='highlight_text'.($key +1);
					$color = 'background-color:#'.$this->AcesearchConfig->$back.';color:#'.$this->AcesearchConfig->$clr.';';
					$row->name = preg_replace('#('.$query.')#iu', '<span class="acesearch_highlight" style="'.$color.'">\0</span>', $row->name);
					$row->description = preg_replace('#('.$query.')#iu', '<span class="acesearch_highlight" style="'.$color.'">\0</span>', $row->description);
				}
			}
		}
		
		if (isset($row->acesearch_ext) && isset($row->acesearch_type)) {
			$acesearch_ext =& AcesearchFactory::getExtension($row->acesearch_ext);
			
			$row->properties = '';
			if (($this->AcesearchConfig->admin_show_properties && $admin) || ($this->AcesearchConfig->show_properties && $site)){
				$function = '_get'.ucfirst($row->acesearch_type).'Properties';
				$acesearch_ext->$function($row);
			}
			
			if (!isset($row->link)) {
				$function = '_get'.ucfirst($row->acesearch_type).'URL';
				$acesearch_ext->$function($row);
			}
		}
	}
	
	function getSuggestion() {
		$input = self::getQuery();
		
		$words = AceDatabase::loadResultArray('SELECT keyword FROM #__acesearch_search_results ORDER BY search_result DESC');
		$closest = array();
		$shortest = 1; 
		
		$qs = explode(' ', $input);
		if (count($qs) == 1 && !empty($this->_data)) {
			return "";			
		}
		
		foreach($qs as $q) {
			if(!empty($q)) {
				foreach ($words as $key => $word ) {
					$lv = levenshtein(strtolower($input), strtolower($word));	

					if($lv == 0) {
						return "";
					}
					elseif ($lv <5) {
						$closest[$key] = $word;
						$shortest = 0; 
						break;
					}
					else {
						$lev = levenshtein(strtolower($q), strtolower($word));
					
						if ($lev == 0) {
							$closest[$key] = $word;
							break;
						}
						elseif ($lev < 5) {
							$closest[$key] = $word;
							$shortest = 0; 
							break;
						}
					}
				}
			}
			
			if ($shortest == 1) {
				return "";
			}
		}
		
		$closest = count($closest) ? implode(' ', $closest) : '';
		
		if (!empty($closest)) {
			$suggest = "<h2 style='color:#1d82fe;'>".JText::_('COM_ACESEARCH_SEARCH_DID_YOU_MEAN')." ";
			if (JFactory::getApplication()->isAdmin()) {
				$link = "index.php?option=com_acesearch&controller=search&task=view&query={$closest}";
			}
			else {
				$link = "index.php?option=com_acesearch&task=search&suggest={$closest}&Itemid=".JRequest::getInt('Itemid');
			}
			
			$suggest .= '<a style="color:#1d82fe;" href="'.JRoute::_($link).'" title="'.$closest.'">'.$closest.'</a></h2>';
			
			return $suggest;
		} 
		else {
			return "";
		}
	}
	
	function getTotal() {
		if (empty($this->_total)) {
			$this->_total = count($this->_data);	
		}
		
		return $this->_total;
	}
	
	function getPagination(){
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}
	
	function _saveResults(){
		$date = date("Y-m-d H:d:s");
		$query = self::getQuery();
		
		if(empty($query) && strlen($query) < 3) {
			return;
		}
		
		$ext = $this->_request['ext'];
		$total = self::getTotal();
				
		if (empty($ext)) {
			$ext = JText::_('COM_ACESEARCH_SEARCH_SECTIONS');
		}
		
		$query = AceDatabase::quote($query);
		$keyword = AceDatabase::loadObject("SELECT id, search_count FROM #__acesearch_search_results WHERE keyword = {$query} AND extension = '{$ext}'");
		
		if (is_object($keyword)) {
			$keyword->search_count ++;
			AceDatabase::query("UPDATE #__acesearch_search_results SET search_result='{$total}', search_count='{$keyword->search_count}', search_date='{$date}' WHERE id = {$keyword->id}");
		}
		else {
			AceDatabase::query("INSERT INTO #__acesearch_search_results (keyword, extension, search_result, search_count, search_date) VALUES ({$query}, '{$ext}', '{$total}', '1', '{$date}')");
		}
	}
	
	function _getRequest() {
		if (JFactory::getApplication()->isAdmin()) {
			$request = JRequest::get('post');
		
			if (empty($request)) {
				$request = JRequest::get('get');
			}
		}
		else {
			$session = JFactory::getSession();
			
			$request = $session->get('acesearch.post');
			
			$session->clear('acesearch.post');
			
			if (empty($request)) {
				$request = JRequest::get('get');
				$request['ext'] =& JFactory::getURI()->getVar('ext');
				$filter = & JFactory::getURI()->getVar('filter');
				if(!empty($filter)) {
					$request['filter'] =$filter;
				}
			}
		
			if (!empty($request) && is_array($request)) {
				foreach ($request as $key => $value) {
					if (strlen($key) >= '15') {
						unset($request[$key]);
					}
				}
			
				JRequest::set($request, 'post');
			}
			
		}
		
		return $request;	
	}
	
	public function getQuery($type = '') {
		$query = '';
		
		$qu = strip_tags(JRequest::getString('query', '', $type));
		$an = strip_tags(JRequest::getString('any', '', $type));
		$ex = strip_tags(JRequest::getString('exact', '', $type));
	
		if (!empty($qu)){
			$query = $qu;
		}
		elseif(!empty($an)){
			$query = $an;
		}
		elseif(!empty($ex)){
			$query = $ex;
		}
		
		if (empty($query)) {
			return $query;
		}
		
		$badchars = array('#','>','<','\\');
		$query = trim(str_replace($badchars, '', $query));
		
		$query = preg_replace("'<script[^>]*>.*?</script>'si", ' ', $query);
		$query = preg_replace('/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '', $query);
		$query = preg_replace('/\s/u', ' ', $query);
		
		while(strpos($query, '  ')) {
			$query = str_replace('  ', ' ', $query);
		}
		
		return $query;
	}
}