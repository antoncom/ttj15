<?php
/**
* @version		1.5.0
* @package		AceSearch Library
* @subpackage	Extension
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted Access');

// Extension class
class AcesearchExtension {
	
	protected $extension = null;
	protected $params = null;
	protected $aid = null;
	protected $db = null;
	protected $search_fields = false;
	protected $admin = true;
	protected $site = false;
	
	function __construct($extension, $params) {
		// Get config object
		$this->AcesearchConfig = AcesearchFactory::getConfig();
		
		$this->extension = $extension;
		$this->params = $params;
		$this->aid =& JFactory::getUser()->get('aid');
		$this->db =& JFactory::getDBO();
		$this->admin = JFactory::getApplication()->isAdmin();
		$this->site = JFactory::getApplication()->isSite();
		$this->fields = self::getFields($extension->extension);
	}
	
	public function getExtraFields($params, &$fields) {
	}

	public function getCategoryList() {
		return array();
	}
	
	public function getMenuParams($id) {
		static $params = array();
		
		if (!isset($params[$id])) {
			$params[$id] = AcesearchUtility::getMenu()->getParams($id);
		}
		
		return $params[$id];
	}
	
	public function getInt($name, $default = 0) {
		$var = JRequest::getVar($name, $default, 'post', 'int');
		
		return $var;
	}
	
	public function getWord($name, $default = '') {
		$var = JRequest::getVar($name, $default, 'post', 'word');
		
		return $var;
	}
	
	public function getCmd($name, $default = '') {
		$var = JRequest::getVar($name, $default, 'post', 'cmd');
		
		return $var;
	}
	
	public function getString($name, $default = '') {
		$var = (string) JRequest::getVar($name, $default, 'post', 'string', 0);
		
		return $var;
	}
	
	public function getSecureString($name, $default = '') {
		$var = (string) JRequest::getVar($name, $default, 'post', 'string', 0);
		
		$var = self::getSecureText($var);
		
		return $var;
	}
	
	protected function getFields($component) {
		$fields = new stdClass();
		$xml_file = JPATH_ACESEARCH_ADMIN.'/extensions/'.$component.'.xml';
		
		$xml =& JFactory::getXMLParser('Simple');
		if (!$xml->loadFile($xml_file)) {
			unset($xml);
			return $fields;
		}
		
		if (is_null($xml)) {
			unset($xml);
			return $fields;
		}
		
		$manifest =& $xml->document;
		
        $fields_xml = $manifest->getElementByPath('fields');
		if (!is_a($fields_xml, 'JSimpleXMLElement') || (count($fields_xml->children()) == 0)) {
			return $fields;
		}
		
		$childrens = $fields_xml->children();
		foreach ($childrens as $children) {
			$name = $children->attributes('name');
			
			$fields->$name = new stdClass();
			$fields->$name->client = $children->attributes('client');
			$fields->$name->type = $children->attributes('type');
		}
		
		return $fields;
	}
		
	protected function getSearchFieldsWhere($src_fields, $or = '') {
		$where = $wh = array();
		
		if (empty($src_fields)) {
			return $where;
		}
		
		$query 		= self::getSearchQuery('query');   
		$exact 		= self::getSearchQuery('exact');
		$any 		= self::getSearchQuery('any');
		$none 		= self::getSearchQuery('none');
		
		$fields = explode(', ', $src_fields);
	
		if (!empty($query) || !empty($any)) {
			$sub_where = array();
			$this->search_fields = true;
			
			$x = 'query';
			if (empty($query)) {
				$query = $any;
				$x = 'any';
			}
			
			if (strpos(trim($query), ' ')) {
				
				if($x == 'query') {
					$wrd = self::getSecureText($query);
					self::_getSubWhere($sub_where, $wrd, $fields, $x);
				}
				
				$words = explode(' ', $query);			
				foreach($words as $word) {
					if(!empty($word)) {
						$word = self::getSecureText($word);

						self::_getSubWhere($sub_where, $word, $fields, $x);
					}
				}
			}
			else {
				$query = self::getSecureText($query);
				self::_getSubWhere($sub_where, $query, $fields, $x);
			}
			
			if (!empty($sub_where)) {
				$where[] = '(' . implode(' OR ', $sub_where) . ')';
			}
		}
		
		if (!empty($exact)) {
			$sub_where = array();
			$this->search_fields = true;
			
			$exact = self::getSecureText($exact);
			
			self::_getSubWhere($sub_where, $exact, $fields, 'exact');
		
			if (!empty($sub_where)) {
				$where[] = '(' . implode(' OR ', $sub_where) . ')';
			}
		}
		
		if (!empty($none)) {
			$sub_where = array();
			$this->search_fields = true;
			
			$none = self::getSecureText($none);
			
			self::_getSubWhere($sub_where, $none, $fields, 'none', ' NOT ');
		
			if (!empty($sub_where)) {
				$where[] = '(' . implode(' AND ', $sub_where) . ')';
			}
		}
		
		if (!empty($this->AcesearchConfig->blacklist)) {
			$sub_where = array();
			$keywords = explode(',', $this->AcesearchConfig->blacklist);
			
			foreach($keywords as $keyword) {
				$keyword = trim($keyword);
				
				$key = self::getSecureText($keyword);
				
				self::_getSubWhere($sub_where, $key, $fields, 'blacklist', ' NOT ');
			}
		
			if (!empty($sub_where)) {
				$where[] = '(' . implode(' OR ', $sub_where) . ')';
			}
		}
		
		if (!empty($where)) {
			$wh[] = '('.implode(' AND ', $where) . $or . ')';
		}
		
		return $wh;
	}
	
	protected function _getSubWhere(&$where, $query, $fields, $x, $not = '') {
		foreach($fields as $field) {
			$field = trim($field);
			
			$pos = strpos($field, ':');
            if ($pos !== false) {
                list($field_db, $field_req) = explode(':', $field);
				
				if (empty($field_db) || empty($field_req)) {
					continue;
				}
			}
			else {
				$field_db = $field_req = $field;
			}
			
			if ($this->site && $this->fields->$field_req->client == '1') {
				continue;
			}
			
			if ($this->admin && $this->fields->$field_req->client == '0') {
				continue;
			}
			
			if ($this->params->get($field_req, '1') != '1') {
				continue;
			}
			
			if ($this->fields->$field_req->type == 'checkbox') {
				$ext = self::getCmd('ext');
				$limitstart = self::getCmd('limitstart');
				if ($x != 'query' && $x != 'blacklist' && (!empty($ext) && empty($limitstart)) ) {
					$req = self::getInt($field_req);
					if (empty($req)) {
						continue;
					}
				}
			}
			else {
				$req = self::getCmd($field_req, '1');
				if (empty($req)) {
					continue;
				}
			}
			
			$where[] = "(LOWER({$field_db}) {$not} LIKE ".$query.")";
		}
	}
	
	protected function getDateFieldsWhere($db_field,$db_field2 = '0', $linux = '0') {
		$where = '';
		
		$days 		= self::getInt('days');
		$fromyear	= self::getInt('fromyear', 2000);
		$toyear		= self::getInt('toyear', date('Y'));
		$frommonth	= self::getInt('frommonth', 01);
		$tomonth	= self::getInt('tomonth', date('m'));
		$fromday	= self::getInt('fromday', 01);
		$today		= self::getInt('today', date('d'));
		
		if ($toyear == date('Y') && $tomonth == date('m') && $today == date('d') && $fromyear == 2000 && $frommonth == 01 && $fromday == 01) {
			$checkdate = '0';
		} else {
			$checkdate = '1';
		}
		
		if($linux == '1') {
			$fromdate     = mktime(date("H"),date("i"),date("s"),$frommonth,$fromday,$fromyear);
			$todate       = mktime(date("H"),date("i"),date("s"),$tomonth,$today,$toyear); 
		} else {
			$fromdate = $fromyear.'-'.$frommonth.'-'.$fromday;
			$todate	= $toyear.'-'.$tomonth.'-'.$today;
		}
		
		if (!empty($days) && $days != '-1'){
			$date = '';
			
			if ($days == 1) {
				if($linux == '1') {
					$date = mktime(12,00,00, date('m'), date("d")-1, date('Y'));
				} else {
					$date = date('Y-m-d', mktime(12,00,00, date('m'), date("d")-1, date('Y')));
				}
			}
			elseif ($days == 3 || $days == 6) {
				if($linux == '1') {
					$date = mktime(12,00,00, date("m") - $days, date('d'), date('Y'));
				} else {
					$date = date('Y-m-d', mktime(12,00,00, date("m") - $days, date('d'), date('Y')));
				}
			}
			elseif ($days == 12) {
				if($linux == '1') {
					$date =	mktime(12,00,00,01,01, date('Y') -1);
				} else {
					$date = date('Y-m-d', mktime(12,00,00,01,01, date('Y') -1));
				}
			}
			
			$where = "({$db_field} >= '".$date."')";
		}
		elseif ($checkdate == '1') {
			if($db_field2 != '0') {
				$where = "({$db_field} >='".$fromdate."' AND {$db_field2} <= '".$todate."')";
			} else {
				$where = "({$db_field} >='".$fromdate."' AND {$db_field} <= '".$todate."')";
			}
		}
		
		return $where;
	}
	
	public function getIdentifier($type = 'Item') {
		return "CONCAT('{$this->extension->extension}') AS acesearch_ext, CONCAT('{$type}') AS acesearch_type";
	}
	
	public function getSqlLimit() {
		if ($this->site) {
			$limit = $this->params->get('result_limit', '');
			
			if (empty($limit)) {
				$limit = $this->AcesearchConfig->result_limit;
			}
			
			return $limit;
		}
		else {
			return $this->AcesearchConfig->admin_result_limit;
		}
	}
	
	public function getUser() {
		return "";
	}
	
	protected function getUserID($req) {
		$user = self::getSecureText(JRequest::getString($req, null));
		
		if (empty($user)) {
			return null;
		}
		
		return AceDatabase::loadResult("SELECT id FROM #__users WHERE LOWER(name) LIKE {$user}");
	}
	
	// --------------------
	
	public function _getItemProperties(&$item) {
		$properties = '';
		
		if ($this->params->get('custom_name', '')) {
			$properties .= JText::_('COM_ACESEARCH_SEARCH_SECTION').': '.$this->params->get('custom_name', '').' | ';
		} else {
			$properties .= JText::_('COM_ACESEARCH_SEARCH_SECTION').': '.$this->extension->name.' | ';
		}
		
		if (!empty($item->category)){ 
			$properties .= JText::_('COM_ACESEARCH_FIELDS_CATEGORY').': '.$item->category.' | ';
		}
		
		if (!empty($item->date)){ 
			if(is_numeric($item->date)) {
				$date = $item->date;				
			} else {
				$date =strtotime($item->date);
			}
			$properties .= JText::_('COM_ACESEARCH_FIELDS_DATE').': '.date($this->AcesearchConfig->date_format, $date).' | ';
		}
		
		if (!empty($item->hits)) {
			$properties .= JText::_('COM_ACESEARCH_FIELDS_HITS').': '.$item->hits;
		}
		
		$item->properties = rtrim($properties, ' | ');
		
		unset($item->category);
		unset($item->date);
		unset($item->hits);
	}
	
	public function _getCategoryProperties(&$cat) {
		self::_getItemProperties($cat);
	}
	
	// --------------------
	
	public function getCategory($catid){
		static $cache = array();
		
		if (!isset($cache[$catid])) {
			$catid = intval($catid);
			$cache[$catid] = AceDatabase::loadObject("SELECT title, alias FROM #__categories WHERE id = {$catid}");
		}
		
		return $cache[$catid];
	}
	
	public function _getCategories($option) {
		$where = self::getSearchFieldsWhere('title:name, description');
		if (empty($where)){
			return array();
		}
		
		$where[] = "section = '{$option}'";
		
		if ($this->site) {
			$where[] = "published = 1";
			
			if ($this->AcesearchConfig->access_checker == '1') {
				$where[] = "access <= {$this->aid}";
			}
		}
		
		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where): '');
		
		$identifier = self::getIdentifier('Category');
		
		return AceDatabase::loadObjectList("SELECT {$identifier}, id, alias, title AS name, description, count AS hits FROM #__categories {$where}", '', 0, self::getSqlLimit());
	}
	
	// -------------------
	
	public function getExtraFieldsHTML($params, &$html, $is_module) {
		$fields = $params->get('extrafields', '');
		$module ="";
		if($is_module) {
			$module = '_module';
		}
		if (empty($fields)) {
			return '';
		}
		
		if (is_array($fields)) {
			foreach ($fields as $field) {
				$html[] = self::_getExtraFieldsHTML($field,$module);
			}
		}
		else {
			$html[] = self::_getExtraFieldsHTML($fields,$module);
		}
	}
	
	protected function _getExtraFieldsHTML($field,$module) {
		$output = '';
		
		list($field_id, $field_name) = explode('_', $field);
		
		$output  = '<div style="float:left; width:95%">';
		$output .= '<span class="acesearch_span_label'.$module.'">';
		$output .= JText::_($field_name);
		$output .= '</span>';
		$output .= '<span class="acesearch_span_field'.$module.'">';
		$output .= '<span><input type="checkbox" name="ja_'.$field_id.'" value="1" checked /></span>';
		$output .= '</span>';
		$output .= '</div>';
		
		return $output;
	}
	
	protected function getExtraFieldsWhere($sql, $secure = true) {
		$fields = $this->params->get('extrafields', '');
		
		if (empty($fields)) {
			return '';
		}
		
		$custom_fields = array();
		
		if(is_array($fields)) {
			foreach ($fields as $field) {
				self::_getExtraFieldsWhere($field, $custom_fields, $sql, $secure);
			}
		}
		else {
			self::_getExtraFieldsWhere($fields, $custom_fields, $sql, $secure);
		}
		
		if (empty($custom_fields)) {
			return '';
		}
		
		$ret = ' OR ' . implode(' OR ', $custom_fields);
		
		return $ret;
	}
	
	protected function _getExtraFieldsWhere($field, &$custom_fields, $sql, $secure) {
		$query = AceSearchSearch::getQuery('post');
		
		list($field_id, $field_name) = explode('_', $field);
		
		$f = '1';
		if (!empty($an) || !empty($ex)) {
			$f = JRequest::getInt('ja_'.$field_id);
		}
		
		if ($f == '1') {
			if (empty($field_id)) {
				return;
			}
			
			if ($secure) {
				$query = self::getSecureText($query);
			}
			
			$search = array('{field_name}', '{field_id}', '{query}');
			$replace = array($field_name, $field_id, $query);
			
			$custom_fields[] = str_replace($search, $replace, $sql);
		}
	}
	
	public function _getCategoryList($option ,$filter) {
		$where = " WHERE section = '{$option}' ";
		
		if ($this->site  || $filter == '1') {
			$where .=" AND published = 1 AND access <= {$this->aid}";
		}
		
		$where .=" ORDER BY title";
		
		return AceDatabase::loadObjectList("SELECT id, title AS name, parent_id AS parent FROM #__categories {$where} ",'','0', self::getSqlLimit());
	}
		
	// -------------------
	
	public function getItemid($vars = array(), $params = null) {
		$v['option'] = $this->extension->extension;
		
		$vars = array_merge($v, $vars);
		
		$item = self::findItemid($vars, $params);
		
		if (!empty($item->id)) {
			return '&Itemid='.$item->id;
		}
		
		return '';
	}
	
	// thanks to Nicholas K. Dionysopoulos, akeebabackup.com
	public function findItemid($vars = array(), $params = null) {
		if (empty($vars) || !is_array($vars)) {
			$vars = array();
		}
		
		$menus =& AcesearchUtility::getMenu();
		
		$items = $menus->getMenu();
		if (empty($items)) {
			return null;
		}
		
		$option_found = null;
		
		foreach ($items as $item) {
			if (is_object($item) && isset($item->published) && $item->published == '1') {
				$query = $item->query;
				
				if (empty($query['option'])) {
					continue;
				}
				
				if ($query['option'] != $vars['option']) {
					continue;
				}
				
				if (count($vars) == 1) {
					return $item;
				}
				
				if (is_null($option_found)) {
					$option_found = $item;
				}
				
				if (self::_checkMenu($item, $vars, $params)) {
					return $item;
				}
			}
		}
		
		if (!empty($option_found)) {
			return $option_found;
		}

		return null; 
	} 

	protected function _checkMenu($item, $vars, $params = null) {
		$query = $item->query;
		
		unset($vars['option']);
		unset($query['option']);
		
		foreach ($vars as $key => $value) {
			if (is_null($value)) {
				return false;
			}
			
			if (!isset($query[$key])) {
				return false;
			}
			
			if ($query[$key] != $value) {
				return false;
			}
		} 

		if (!is_null($params)) {
			$menus =& AcesearchUtility::getMenu(); 
			$check = $item->params instanceof JParameter ? $item->params : $menus->getParams($item->id);
			
			foreach ($params as $key => $value) {
				if (is_null($value)) {
					continue;
				}
				
				if ($check->get($key) != $value) {
					return false;
				}
			}
		}

		return true;
	}
	
	public function fixVar($var) {
        if (!is_null($var)) {
            $pos = strpos($var, ':');
            if ($pos !== false) {
                $var = substr($var, 0, $pos);
			}
        }
		
		return $var;
    }
	
	public function getSecureText($text, $sep = '%') {
		if (empty($text)) {
			return $text;
		}
	
		return AceDatabase::quote(''.$sep.''.AceDatabase::getEscaped(urldecode($text), true).''.$sep.'', false);
	}
		
	public function getSearchQuery($query,$type="") {
		return preg_replace('/\s/u', ' ', trim(strtolower(JRequest::getString($query,'',$type))));
	}
}