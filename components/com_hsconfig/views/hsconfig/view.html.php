<?php
/**
 * HsConfig View for Highslide Configuration Component
 *
 * @license		GNU/GPL
 */

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Highslide Configuration Component
 *
 */
class HsConfigViewHsConfig extends JView
{
	function display($tpl = null)
	{
		$id = $this->get( 'Id' );
		$this->assignRef( 'id',	$id );

		parent::display($tpl);
	}
}
?>
