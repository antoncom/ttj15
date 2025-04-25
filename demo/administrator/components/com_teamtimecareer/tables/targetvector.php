<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableTargetvector extends JTable {

  var $id = null;
  var $parent_id = null;
  var $title = null;
  var $description = null;
  var $num = null;
  var $hourprice = null;
  var $ordering = null;
  var $is_skill = null;

  function __construct(&$db) {
    parent::__construct('#__teamtimecareer_targetvector', 'id', $db);
  }

}