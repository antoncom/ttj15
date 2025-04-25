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

class AcesearchViewAdvancedSearch extends JView {

	function display( $tpl = null){
		$mainframe =& JFactory::getApplication();
		$document =& JFactory::getDocument();
		$this->AcesearchConfig = AcesearchFactory::getConfig();

		JHTML::_('behavior.mootools');
		
		$document->addStyleSheet(JURI::root().'components/com_acesearch/assets/css/acesearch.css');

		if ($this->AcesearchConfig->enable_complete == 1) {
			$document->addScript(JURI::root().'components/com_acesearch/assets/js/autocompleter.js');
		}
		
		$this->assignRef('params', 			$mainframe->getParams());
		$this->assignRef('lists', 			AcesearchSearch::getExtensionList());
		$this->assignRef('results', 		$this->get('Data'));
		$this->assignRef('pagination',		$this->get('Pagination'));
		
		parent::display($tpl);
	}
}