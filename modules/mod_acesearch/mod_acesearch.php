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

$lang = JFactory::getLanguage();
$lang->load('com_acesearch' , JPATH_SITE);

$loader = JPATH_ADMINISTRATOR.'/components/com_acesearch/library/loader.php';
if (!file_exists($loader)) {
    return;
}

require_once($loader);

$this->AcesearchConfig = AcesearchFactory::getConfig();

$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::root().'components/com_acesearch/assets/css/acesearch.css');
if ($params->get('enable_complete', '0') == '1') {
	$document->addScript(JURI::root().'components/com_acesearch/assets/js/autocompleter.js');
}

$text = $focus = '';
$f = $params->get('text', 'search...');

$query = JRequest::getString('query');
if (!empty($query)) {
	$text = $query; 
	$focus = "";
}
elseif(!empty($f)) {
	$text = $f;
	$focus = 'onblur="if(this.value==\'\') this.value=\''.$text.'\';" onfocus="if(this.value==\''.$text.'\') this.value=\'\';"';
}

$text_class = $params->get('text_class', '');
if (!empty($text_class)) {
	$text_class = 'class="'.$text_class.'"';
}

$output = '<input type="text" name="query" value="'.$text.'" id="qr"  '.$text_class.' '.$focus.' />';

$Itemid = '';
$prm_itemid = $params->get('set_itemid', '');
if (!empty($prm_itemid)) {
	$Itemid = $prm_itemid;
}
else {
	$ace_itemid = AcesearchUtility::getItemid();
	if (!empty($ace_itemid)) {
		$Itemid = str_replace('&Itemid=', '', $ace_itemid);
	}
}

$filter_id = $params->get('filter', '');

// Advanced Search link
if ($params->get('show_advanced_search', '0') != '0') {
	$link = 'index.php?option=com_acesearch&view=advancedsearch'.AcesearchUtility::getItemid(true);

	$advaced = '<a href='.JRoute::_($link).' title="'.$params->get('advanced_label', 'Advanced Search').'">'.$params->get('advanced_label', 'Advanced Search').'</a>';
	
	switch ($params->get('advanced_position', 'right')) {
		case 'top':
			$advaced ='<center>'.$advaced.'</center>';
			$output = $advaced.$output;
			break;
		case 'bottom':
			$advaced = '<center>'.$advaced.'</center>';
			$output = $output.$advaced;
			break;
		case 'right':
			$output = $output.' '.$advaced;
			break;
		case 'left':
		default :
			$output = $advaced.' '.$output;
			break;
	}
}

require(JModuleHelper::getLayoutPath('mod_acesearch'));