<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );
 
class PhocaDocumentationCpViewPhocaDocumentations extends JView
{
	function display($tpl = null)
	{
		global $mainframe;
		JHTML::_('behavior.modal', 'a.modal-button');
		// Get width and height from default settings
		$params = JComponentHelper::getParams('com_phocadocumentation') ;
		JHTML::stylesheet( 'phocadocumentation.css', 'administrator/components/com_phocadocumentation/assets/' );

		JToolBarHelper::title(   JText::_( 'Phoca Documentation' ), 'doc' );
		JToolBarHelper::preferences('com_phocadocumentation', '360');
		JToolBarHelper::help( 'screen.phocadocumentation', true );
		
		parent::display($tpl);
	}
}
?>