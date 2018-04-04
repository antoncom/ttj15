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

class AcesearchViewCSS extends AcesearchView {

	function view($tpl = null){
		// Toolbar
		JToolBarHelper::title(JText::_('CSS' ), 'acesearch');
		parent::display($tpl);
	}
}