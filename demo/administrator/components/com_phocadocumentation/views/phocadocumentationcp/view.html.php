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
defined( '_JEXEC' ) or die();
jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.pane' );

class phocadocumentationCpViewPhocaDocumentationcp extends JView
{
	function display($tpl = null) {
		
		global $mainframe;
		$uri		=& JFactory::getURI();
		$document	=& JFactory::getDocument();
		$db		    =& JFactory::getDBO();
		JHTML::stylesheet( 'phocadocumentation.css', 'administrator/components/com_phocadocumentation/assets/' );
		JToolBarHelper::title( JText::_( 'Phoca Documentation Control Panel' ), 'phocadocumentation' );
		JToolBarHelper::preferences('com_phocadocumentation', '460');
		JToolBarHelper::help( 'screen.phocadocumentation', true );
		JHTML::_('behavior.tooltip');
		$version = PhocaDocumentationHelper::getPhocaVersion();
		$this->assignRef('version',	$version);
		parent::display($tpl);
	}
}
?>