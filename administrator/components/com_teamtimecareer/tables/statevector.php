<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableStatevector extends JTable {

  var $id = null;
  var $target_id = null;
  var $description = null;
  var $num = null;
  var $user_id = null;
  var $log_id = null;
  var $todo_id = null;
  var $date = null;
  var $skill_target_id = null; // not used

  function __construct(&$db) {
    parent::__construct('#__teamtimecareer_statevector', 'id', $db);
  }

}