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

jimport('joomla.application.component.view');

class AcesearchViewSearch extends JView {

	function display($tpl = null) {
		$mainframe =& JFactory::getApplication();
		$params =& $mainframe->getParams();
		$document =& JFactory::getDocument();
		$this->AcesearchConfig = AcesearchFactory::getConfig();
		$this->extensions = AcesearchCache::getExtensions();

		JHTML::_('behavior.mootools');
		
		$document->addStyleSheet(JURI::root().'components/com_acesearch/assets/css/acesearch.css');
		
		// Get autocomplete
		if ($this->AcesearchConfig->enable_complete == '1') {
			$document->addScript(JURI::root().'components/com_acesearch/assets/js/autocompleter.js');
		}
		
		$filter_id = JRequest::getInt('filter'); 
		if (empty($filter_id)) {
			$lists = AcesearchSearch::getExtensionList();
			$lists['adf']  = "";
		}
		else {
			$filter = AcesearchCache::getFilter($filter_id);
			$advanced_filter ="";
			$lists = array();
			$lists['filter'] = '<input type="hidden" name="ext" value="'.$filter->extension.'" />';
			$advanced_filter ="&filter=$filter_id&ext=$filter->extension";
			if (!empty($filter->category)) {
				$lists['filter'] .= '<input type="hidden" name="category" value="'.$filter->category.'" />';
				$advanced_filter .="&category=$filter->category";
			}
			
			if ($filter->author != '-1'){
				$lists['filter'] .= '<input type="hidden" name="usr" value="'.$filter->author.'" />';
				$advanced_filter .="&user=$filter->author";
			}
			$lists['adf']  = $advanced_filter;
			$lists['extension'] = '';
		}
		
		$this->assignRef('params', 		$params);
		$this->assignRef('lists', 		$lists);
		$this->assignRef('results', 	$this->get('Data'));
		$this->assignRef('total', 		$this->get('Total'));
		$this->assignRef('pagination', 	$this->get('Pagination'));
		
		parent::display($tpl);
	}
	
	function getExtensionName($extension) {
		$extensions = AcesearchCache::getExtensions();
		$params = new JParameter($extensions[$extension]->params);
		
		$prm_name = $params->get('custom_name', '');
		
		if (!empty($prm_name)) {
			return $prm_name;
		} else {
			return $extensions[$extension]->name;
		}
	}
	
	function getSearchQuery() {
		$q = $query = $any = $none = $exact = '';
		
		$query = JRequest::getString('query', '');
		$an = JRequest::getString('any', '');
		$ex = JRequest::getString('exact', '');
		$no = JRequest::getString('none', '');

		if (!empty($query)) {
			$q = $query;
		}
		else {
			if (!empty($an)) {
				$q = ' '.$an.' ';
			}

			if (!empty($ex)) {
				$q = ' "'.$ex.'" ';
			}

			if (!empty($no)) {
				$q = ' -'.$no;
			}
		}
		
		$ret = $this->escape($q);
		
		return $ret;
	}
}