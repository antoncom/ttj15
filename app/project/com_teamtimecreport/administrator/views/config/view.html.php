<?php
/**
 * @version		$Id: view.php 9764 2007-12-30 07:48:11Z ircmaxell $
 * @package		Joomla
 * @subpackage	Menus
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.view');


/**
 * Extension Manager Default View
 *
 * @package		Joomla
 * @subpackage	Installer
 * @since		1.5
 */
class ConfigViewConfig extends JView
{
	function display( $tpl = null )
	{
		$db		=& JFactory::getDBO();
		
		$conf_data = TeamTimeCReport_get_config();
		
		$this->assignRef('conf_data', $conf_data);
		
		parent::display($tpl);
	}
}