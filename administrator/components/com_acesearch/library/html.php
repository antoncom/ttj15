<?php
/**
* @version		1.5.0
* @package		AceSearch Library
* @subpackage	HTML
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

//No Permision
defined( '_JEXEC' ) or die( 'Restricted access' );

class AcesearchHTML {
	
	function renderDivStart($id, $class, $style) {
		return '<div style="'.$style.'" id="'.$id.'" class="'.$class.'" >';
	}
	
	function renderDivEnd() {
		return '</div>';
	}
	
	function renderText($field, $params, $suffix) {
		$name = $field->attributes('name');
		$size = $field->attributes('size');
		$style = $field->attributes('style');
		$maxlength = $field->attributes('maxlength');
		
		if(empty($maxlength)) {
			$maxlength = "10";
		}
		
		$read = $value = "";
		$val = JFactory::getURI()->getVar($name);
		if(!empty($val) && is_string($val)) {
			$read = "readonly";
			$value = $val;
		}
		
		return '<span style="'.$style.'"><input type="text" name="'.$name.'" size="'.$size.'" class="acesearch_input_small'.$suffix.'" value="'.$value.'" '.$read.' maxlength="'.$maxlength.'"/></span>';
	}

	function renderJText($field, $params) {
		$jtext = $field->attributes('jtext');
		
		return JText::_($jtext);
	}
	
	function renderCheckBox($field, $params, $suffix) {
		$name = $field->attributes('name');
		$title = $field->attributes('title');
		$value = $field->attributes('value');
		$style = $field->attributes('style');
		
		$checked = '';
		if ($value == '1') {
			$checked = 'checked';
		}
		return '<span style="'.$style.'"><input type="checkbox" name="'.$name.'" value="1" '.$checked.' /></span>';
	}
	
	function renderSelectBox($field, $params, $suffix) {
		$name = $field->attributes('name');
		$title = $field->attributes('title');
		$value = $field->attributes('value');
		$style = $field->attributes('style');
		$all = $field->attributes('all');
		
		$options = array();
		
		if ($all == '1') {
			$options[] = JHTML::_('select.option', '', JText::_('COM_ACESEARCH_ALL'));
		}
		
		$ttls = explode(',', $title);
		$opts = explode(',', $value);
		
		$n = count($opts);
		for ($i = 0; $i < $n; $i++){
			$options[] = JHTML::_('select.option', $opts[$i], JText::_($ttls[$i]));
		}		
		
		return '<span style="'.$style.'">'.JHTML::_('select.genericlist', $options, $name, 'class="acesearch_selectbox'.$suffix.'" size ="1"', 'value', 'text', null).'</span>';
		
	}
	
	function renderRadio($field, $params, $suffix) {
		$name = $field->attributes('name');
		$title = $field->attributes('title');
		$value = $field->attributes('value');
		$style = $field->attributes('style');
		$default = $field->attributes('default');
		
		$options = array();
		$ttls = explode(',', $title);
		$opts = explode(',', $value);
		
		$n = count($opts);
		for ($i = 0; $i < $n; $i++){
			$options[] = JHTML::_('select.option', $opts[$i], JText::_($ttls[$i]));
		}
		
		return '<span style="'.$style.'">'.JHTML::_('select.radiolist', $options, $name, '', 'value', 'text', $default).'</span>';
	}
	
	function renderCalendar($field, $params, $suffix) {
		$name = $field->attributes('name');
		$style = $field->attributes('style');
		
		JHTML::_('behavior.calendar');

		$format	= ($field->attributes('format') ? $field->attributes('format') : '%Y-%m-%d');
		$class	= $field->attributes('class') ? $field->attributes('class') : 'acesearch_input_small';

		return '<span style="'.$style.'">'.JHTML::_('calendar', date('Y-m-d'), $name, $name, $format, array('class' => $class)).'</span>';
	}
	
	function renderSQL($field, $params, $suffix) {
		$name = $field->attributes('name');
		$style = $field->attributes('style');
		$query = $field->attributes('db_query');
		$all = $field->attributes('all');
		
		$db	=& JFactory::getDBO();
		$db->setQuery($query);
		
		$key = ($field->attributes('db_id') ? $field->attributes('db_id') : 'id');
		$val = ($field->attributes('db_name') ? $field->attributes('db_name') : 'name');
		
		$options = $db->loadObjectList();
		
		$categories = self::buildSelectbox($options,$all);
				
		return '<span style="'.$style.'">'.JHTML::_('select.genericlist', $categories, $name, 'class="acesearch_selectbox'.$suffix.'"', 'value', 'text', null).'</span>';
	}
		
	function renderCategory($field, $component, $suffix) {
		$style = $field->attributes('style');
		
		$categories[] = JHTML::_('select.option', '', JText::_('COM_ACESEARCH_ALL'));
		
		$acesearch_ext =& AcesearchFactory::getExtension($component);
		$rows = $acesearch_ext->getCategoryList();
		
		$categories = self::buildSelectbox($rows);
		
		$category = JFactory::getURI()->getVar('category');
		
		$read = $value = "";
		if (!empty($category) && is_numeric($category)) {
			$read = "readonly";
		}
		else {
			$category = JRequest::getInt('category');
		}
		
		return '<span style="'.$style.'">'.JHTML::_('select.genericlist', $categories, 'category', 'class="acesearch_selectbox'.$suffix.'" size ="1" '.$read, 'value', 'text', intval($category)).'</span>';
	}
	
	function buildSelectbox($rows , $all= '1') {
		$categories = array();
		
		if ($all == '1') {
			$categories[] = JHTML::_('select.option', '', JText::_('COM_ACESEARCH_ALL'));
		}
		
		if (!empty($rows)) {
			// Collect childrens
			$children = array();
			foreach ($rows as $row) {
				// Not subcategories
				if (empty($row->parent)) {
					$row->parent = 0;
				}
				
				$pt = $row->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $row);
				$children[$pt] = $list;
			}
			
			// Not subcategories
			if (empty($rows[0]->parent)) {
				$rows[0]->parent = 0;
			}
			
			// Build Tree
			$tree = self::_buildTree(intval($rows[0]->parent), '', array(), $children);
			
			foreach ($tree as $item){
				$categories[] = array('value' => $item->id, 'text' => $item->name);
			}
		}
		return $categories;
	}
	
	function _buildTree($id, $indent, $list, &$children) {
		if (@$children[$id]) {
			foreach ($children[$id] as $ch) {
				$id = $ch->id;

				$pre 	= '<sup>|_</sup>&nbsp;';
				$spacer = '.&nbsp;&nbsp;&nbsp;';

				if ($ch->parent == 0) {
					$txt = $ch->name;
				} else {
					$txt = $pre . $ch->name;
				}
				
				$list[$id] = $ch;
				$list[$id]->name = "$indent$txt";
				$list[$id]->children = count(@$children[$id]);
				$list = self::_buildTree($id, $indent . $spacer, $list, $children);
			}
		}
		
		return $list;
	}
	
	function renderOrder($field, $params, $suffix) {
		$style = $field->attributes('style');

		$options = array();
		$options[] = JHTML::_('select.option', 'ASC', JText::_('COM_ACESEARCH_FIELDS_ORDER_ASC'));
		$options[] = JHTML::_('select.option', 'DESC', JText::_('COM_ACESEARCH_FIELDS_ORDER_DESC'));
		
		$order_dir = '<span style="'.$style.'">'.JHTML::_('select.genericlist', $options, 'orderdir', 'class="acesearch_selectbox'.$suffix.'" size ="1"', 'value', 'text', null).'</span>';
		
		return self::renderSelectBox($field, $params, $suffix) . ' ' . $order_dir;
	}
	
	function renderDays($field, $params, $suffix) {
		$lists = array();
		
		$lists['any'] = JHTML::_('select.option', -1, JText::_('COM_ACESEARCH_FIELDS_DAYS_ANY_TIME'));
		$lists['yesterday'] = JHTML::_('select.option', 1, JText::_('COM_ACESEARCH_FIELDS_DAYS_YESTERDAY'));
		$lists['three'] = JHTML::_('select.option', 3, JText::_('COM_ACESEARCH_FIELDS_DAYS_THREE'));
		$lists['six'] = JHTML::_('select.option', 6, JText::_('COM_ACESEARCH_FIELDS_DAYS_SIX'));
		$lists['year'] = JHTML::_('select.option', 12, JText::_('COM_ACESEARCH_FIELDS_DAYS_YEAR'));
		
		$html = JHTML::_('select.genericlist', $lists, 'days', 'class="acesearch_selectbox" size="1"', 'value','text', '-1');
		
		return $html;
	}
	
	function renderDateRange($field, $params, $suffix) {
		$style = $field->attributes('style');
		
		$lists = self::_getDateLists($suffix);
		
		$html = '<span style="'.$style.'">'.JText::_('From ').$lists['fromyear']. ' ' . $lists['frommonth'] . ' ' . $lists['fromday'] .'</span>'.
		'<div class="acesearch_clear"></div><span style="'.$style.'">&nbsp;&nbsp;'.JText::_('To ').'&nbsp;&nbsp;&nbsp;'. $lists['toyear'] . ' ' . $lists['tomonth'] . ' ' . $lists['today'].'</span>';
	
		return $html;
	}
	
	function _getDateLists($suffix) {
		$years = $months =$tomonths= array();
		
		for ($i = 2000; $i <= date('Y'); $i++) {
			$years[] = JHTML::_('select.option', $i, $i);
		}
		
		for ($i = 1; $i < 13 ; $i++) {
			if ($i < 10) {
				$i = ('0'.$i);
			}
			
			if (date('m') >= $i) {
				$tomonths[] = JHTML::_('select.option', $i, $i);
			}
			
			$months[] = JHTML::_('select.option', $i, $i);
		}
		
		$lists['fromyear'] = JHTML::_('select.genericlist', $years, 'fromyear', 'class="acesearch_selectbox'.$suffix.'"  style="width:70px; margin-right:4px;"size="1"', 'value', 'text', '2000');
		$lists['toyear'] = JHTML::_('select.genericlist', $years, 'toyear', 'class="acesearch_selectbox'.$suffix.'"style="width:70px; margin-right:4px;" size="1"', 'value', 'text', date('Y'));
		$lists['frommonth'] = JHTML::_('select.genericlist', $months, 'frommonth', 'class="acesearch_selectbox'.$suffix.'" style="width:50px; margin-right:4px;" size="1"', 'value', 'text', '01');
		$lists['tomonth'] = JHTML::_('select.genericlist', $tomonths, 'tomonth', 'class="acesearch_selectbox'.$suffix.'" style="width:50px; margin-right:4px;" size="1"', 'value', 'text', date('m'));
		$lists['fromday'] = '<input class="acesearch_input_tiny" type="text" name="fromday" value="01" maxlength="2" />';
		$lists['today'] = '<input class="acesearch_input_tiny" type="text" name="today" value="'.date('d').'" maxlength="2" />';
		
		return $lists;
	}
	
	
	function renderFunction($field, $component, $suffix) {
		$function = $field->attributes('function');

		$acesearch_ext =& AcesearchFactory::getExtension($component);
		
		return $acesearch_ext->$function($field, $suffix);
	}
}