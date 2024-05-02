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

jimport('joomla.application.component.controller');

class AcesearchController extends JController {
	
	function __construct() {
		parent::__construct();
		
		$this->AcesearchConfig = AcesearchFactory::getConfig();
	}
	
	function display() {
		$view_name = JRequest::getWord('view', '');
		$view_type = JFactory::getDocument()->getType();

		$view =& $this->getView($view_name, $view_type);
		$model =& $this->getModel('Search');
		
		if (!JError::isError($model)) {
			$view->setModel($model, true);
		}
		
		$params =& JFactory::getApplication()->getParams();
		
		$prm_filter = $params->get('filter', '');
		$url_filter = JRequest::getInt('filter');
		
		if (!empty($prm_filter) && empty($url_filter)) {
			$uri = JFactory::getURI();
			$uri->setVar('filter', $prm_filter);
			$this->setRedirect(JRoute::_('index.php'.$uri->toString(array('query', 'fragment')), false));
		}

		$view->display();
	}
	
	function search() {		
		$post = array();
		$post['option'] = 'com_acesearch';
		$post['view'] = 'search';
		
		$filter = JRequest::getInt('filter');
		if (!empty($filter)) {
			$post['filter'] = $filter;
		}
		else {
			$ext = JRequest::getCmd('ext');
			if (!empty($ext)){
				$post['ext'] = $ext;
			}
		}
		
		$post['query'] = AcesearchSearch::getQuery('post');
		$post['limit'] = JRequest::getInt('limit', null, 'post');
		
		if ($post['limit'] === null) {
			unset($post['limit']);
		}

		unset($post['task']);
		unset($post['submit']);
		
		$uri = JFactory::getURI();
		
		$post_data = JRequest::get();
		
		$suggest = $uri->getVar('suggest');
		if (empty($post_data['query']) && !empty($suggest)) {
			$post['query'] = $suggest;
			$post_data['ext'] = '';
			$post_data['query'] = $suggest;
		}
		
		$mod_itemid = JRequest::getInt('mod_itemid');
		if (!empty($mod_itemid)) {
			$post['Itemid'] = $mod_itemid;
		}
		else {
			$Itemid = JRequest::getInt('Itemid');
			if (!empty($Itemid)) {
				$post['Itemid'] = $Itemid;
			}
			else {
				$Itemid = AcesearchUtility::getItemid();			
				if (!empty($Itemid)) {
					$post['Itemid'] = str_replace('&Itemid=', '', $Itemid);
				}
			}
		}
		
		$uri->setQuery($post);
		
		JFactory::getSession()->set('acesearch.post', $post_data);
		
		$this->setRedirect(JRoute::_('index.php'.$uri->toString(array('query', 'fragment')), false));
	}
	
	function changeExtension() {
		$extension = JRequest::getCmd('ext', '');
		
		if (!empty($extension)) {
			echo AcesearchSearch::getAdvancedSearch($extension);
		}
	}
	
	function changeExtensionMod() {
		$extension = JRequest::getCmd('ext', '');
		
		if (!empty($extension)) {
			echo AcesearchSearch::getAdvancedSearch($extension, true);
		}
	}
	
	function complete() {
		$output	= AcesearchSearch::getComplete();
		
		echo json_encode($output);
		
		JFactory::getApplication()->close();
	}
	
	function ajaxFunction() {
		$extension = JRequest::getWord('extension');
		$function = JRequest::getWord('function');
		$selected = JRequest::getString('selected');
		
		$acesearch_ext =& AcesearchFactory::getExtension($extension);
		
		echo $acesearch_ext->$function($selected);
	}
}