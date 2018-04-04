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

class  PhocaDocumentationCpViewPhocaDocumentationin extends JView
{
	function display($tpl = null)
	{
		global $mainframe;
		
		JHTML::stylesheet( 'phocadocumentation.css', 'administrator/components/com_phocadocumentation/assets/' );	
		JToolBarHelper::title(   JText::_( 'Phoca Documentation Info' ), 'info' );
		JToolBarHelper::cancel( 'cancel', 'Close' );
		JToolBarHelper::help( 'screen.phocadocumentation', true );
		$version = PhocaDocumentationHelper::getPhocaVersion();
		$this->assignRef('version',	$version);
		parent::display($tpl);
	}
}
?>
