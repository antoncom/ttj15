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

class AcesearchViewFilters extends AcesearchView {

	function view($tpl = null){
		JToolBarHelper::title(JText::_('COM_ACESEARCH_CPANEL_FILTERS' ), 'acesearch');
		parent::display($tpl);
	}
}