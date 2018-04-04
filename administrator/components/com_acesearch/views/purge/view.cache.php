<?php
/**
* @version		1.5.0
* @package		AceSarch
* @subpackage	AceSarch
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL, http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted Access');

// View Class
class AcesearchViewPurge extends AcesearchView {

	function view($tpl = null) {
		// Get data from the model
		$this->assignRef('count', $this->get('CountCache'));
		
		parent::display($tpl);
	}
}