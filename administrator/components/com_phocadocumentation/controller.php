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

jimport('joomla.application.component.controller');

// Submenu view
$view	= JRequest::getVar( 'view', '', '', 'string', JREQUEST_ALLOWRAW );

if ($view == '' || $view == 'phocadocumentationcp') {
	JSubMenuHelper::addEntry(JText::_('Control Panel'), 'index.php?option=com_phocadocumentation',true);
	JSubMenuHelper::addEntry(JText::_('Documentation'), 'index.php?option=com_phocadocumentation&view=phocadocumentations');
	JSubMenuHelper::addEntry(JText::_('Info'), 'index.php?option=com_phocadocumentation&view=phocadocumentationin' );
}

if ($view == 'phocadocumentations') {
	JSubMenuHelper::addEntry(JText::_('Control Panel'), 'index.php?option=com_phocadocumentation');
	JSubMenuHelper::addEntry(JText::_('Documentation'), 'index.php?option=com_phocadocumentation&view=phocadocumentations', true);
	JSubMenuHelper::addEntry(JText::_('Info'), 'index.php?option=com_phocadocumentation&view=phocadocumentationin' );
} 

if ($view == 'phocadocumentationin') {
	JSubMenuHelper::addEntry(JText::_('Control Panel'), 'index.php?option=com_phocadocumentation');
	JSubMenuHelper::addEntry(JText::_('Documentation'), 'index.php?option=com_phocadocumentation&view=phocadocumentations');
	JSubMenuHelper::addEntry(JText::_('Info'), 'index.php?option=com_phocadocumentation&view=phocadocumentationin', true );
} 

class PhocaDocumentationCpController extends JController
{
	function display()
	{
		parent::display();
	}
}
?>
