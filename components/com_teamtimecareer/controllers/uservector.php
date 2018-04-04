<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class UservectorController extends JController {

  function __construct($default = array()) {
    parent::__construct($default);
  }

  function display() {
    JRequest::setVar('layout', 'default');

    // set the default view
    $view = JRequest::getCmd('view');
    if (empty($view)) {
      JRequest::setVar('view', 'uservectors');
    }

    parent::display();
  }

}