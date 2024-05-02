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

class PhocaDocumentationCpControllerDocumentation extends PhocaDocumentationCpController
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
		$msg = JText::_( 'Phoca Documentation succesfully installed' );
		$link = 'index.php?option=com_phocadocumentation';
		$this->setRedirect($link, $msg);
	}
	
	function upgrade()
	{
		$msg = JText::_( 'Phoca Documentation succesfully upgraded' );
		$link = 'index.php?option=com_com_phocadocumentation';
		$this->setRedirect($link, $msg);
	}
	
	function cancel()
	{
		$model = $this->getModel( 'phocadocumentation' );
		$model->checkin();

		$this->setRedirect( 'index.php?option=com_phocadocumentation' );
	}

}
?>
