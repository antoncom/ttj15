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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class PhocaDocumentationCpControllerPhocaDocumentationinstall extends PhocaDocumentationCpController
{
	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'install'  , 'install' );
		$this->registerTask( 'upgrade'  , 'upgrade' );		
	}

	
	
	function install()
	{		
		$msg = JText::_( 'Phoca Documentation successfully installed' );
		
		$link = 'index.php?option=com_phocadocumentation';
		$this->setRedirect($link, $msg);
	}
	
	function upgrade()
	{
		$msg = JText::_( 'Phoca Documentation successfully upgraded' );
		
		$link = 'index.php?option=com_phocadocumentation';
		$this->setRedirect($link, $msg);
	}
}