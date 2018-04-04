<?php
/**
 * HsConfigs View for Highslide Configuration Component
 *
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

/**
 * HsConfigs View
 */
class HsConfigsViewHsConfigs extends JView
{
	/**
	 * HsConfigs view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		// Load tooltips behavior
		JHTML::_('behavior.tooltip');

		JToolBarHelper::title(   JText::_( 'Highslide JS Configuration Manager' ), 'config.png' );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();
		JToolBarHelper::customX( 'copy', 'copy.png', 'copy_f2.png', 'Copy' );
		JToolBarHelper::deleteList('deletereally');

		// Get data from the model
		$rows = &$this->get('List');
		$page = &$this->get('Pagination');

		$this->assignRef('rows', $rows);
		$this->assignRef('page', $page);

		parent::display($tpl);
	}
}