<?php

/**
 * @version		$Id: controller.php 9820 2008-01-03 00:52:19Z eddieajau $
 * @package		Joomla
 * @subpackage	Config
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

/**
 * Plugins Component Controller
 *
 * @package		Joomla
 * @subpackage	Plugins
 * @since 1.5
 */
class ConfigController extends JController {

  /**
   * Custom Constructor
   */
  function __construct($default = array()) {
    parent::__construct($default);
    $this->registerTask('apply', 'save');
  }

  function display() {
    parent::display();
  }

  function cancel() {
    $this->setRedirect(JRoute::_('index.php?option=com_teamtime&controller=cpanel', false));
  }

  function save() {
    global $option;

    // Check for request forgeries
    JRequest::checkToken() or die('Invalid Token');

    set_include_path(
            get_include_path() . PATH_SEPARATOR .
            JPATH_ROOT . "/administrator/components/com_teamtime/assets/PEAR");
    if (!class_exists("Services_JSON")) {
      require_once("Services/JSON.php");
    }

    $db = & JFactory::getDBO();
    $task = $this->getTask();

    $params = JRequest::getVar('params');

    $json = new Services_JSON();
    file_put_contents(JPATH_COMPONENT . DS . "config.json", $json->encode($params));

    $msg = JText::_('Successfully Saved changes to Teamlog configuration');
    
    switch ($task) {
      case 'apply':
        $link = 'index.php?option=' . $option . '&controller=' . $this->getName();
        break;

      case 'save':
      default:
        $link = 'index.php?option=' . $option . '&controller=cpanel';
        break;
    }

    $this->setRedirect($link, $msg);
  }

}