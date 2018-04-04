<?php
/**
* @version		1.5.0
* @package		AceSearch Library
* @subpackage	View
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

// Imports
jimport('joomla.application.component.view');

class AcesearchView extends JView {

	public $toolbar;
	public $document;
	
	function __construct() {
		parent::__construct();
		
		// Get toolbar object
		$this->toolbar =& JToolBar::getInstance();
		
		// Import CSS
		$this->document =& JFactory::getDocument();
		$this->document->addStyleSheet('components/com_acesearch/assets/css/acesearch.css');
		
		// Get config object
		$this->AcesearchConfig = AcesearchFactory::getConfig();
	}
	
	function getIcon($i, $task, $img) {
		$html = '<a href="javascript:void(0);" onclick="return listItemTask(\'cb'.$i.'\',\''.$task.'\')">';
		$html .= '<img src="components/com_acesearch/assets/images/'.$img.'" border="0" />';
		$html .= '</a>';
		
		return $html;
	}
}