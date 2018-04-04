<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/*
  Class: TaskModelTasks
  The Model Class for Tasks
 */

class TaskModelTasks extends JModel {

  /**
   * Table
   *
   * @var array
   */
  var $_table = 'task';
  /**
   * Data array
   *
   * @var array
   */
  var $_data = null;
  /**
   * Total
   *
   * @var integer
   */
  var $_total = null;
  /**
   * Pagination object
   *
   * @var object
   */
  var $_pagination = null;

  /**
   * Constructor
   *
   */
  function __construct() {
    parent::__construct();

    global $mainframe, $option;

    // get request vars
    $filter_order = $mainframe->getUserStateFromRequest(
            $option . $this->getName() . '.filter_order', 'filter_order', 'a.id', 'cmd');
    $filter_order_Dir = $mainframe->getUserStateFromRequest(
            $option . $this->getName() . '.filter_order_Dir', 'filter_order_Dir', '', 'word');
    $filter_state = $mainframe->getUserStateFromRequest(
            $option . $this->getName() . '.filter_state', 'filter_state', '', 'cmd');
    $limit = $mainframe->getUserStateFromRequest(
            'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
    $limitstart = $mainframe->getUserStateFromRequest(
            $option . $this->getName() . '.limitstart', 'limitstart', 0, 'int');
    $search = $mainframe->getUserStateFromRequest(
            $option . $this->getName() . '.search', 'search', '', 'string');

    $filter_project = $mainframe->getUserStateFromRequest(
            $option . $this->getName() . '.filter_project', 'filter_project', '', 'cmd');
    $filter_type = $mainframe->getUserStateFromRequest(
            $option . $this->getName() . '.filter_type', 'filter_type', '', 'cmd');

    // convert search to lower case
    $search = JString::strtolower($search);

    // in case limit has been changed, adjust limitstart accordingly
    $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

    // set model vars
    $this->setState('filter_order', $filter_order);
    $this->setState('filter_order_Dir', $filter_order_Dir);
    $this->setState('filter_state', $filter_state);

    $this->setState('filter_project', $filter_project);
    $this->setState('filter_type', $filter_type);

    $this->setState('limit', $limit);
    $this->setState('limitstart', $limitstart);
    $this->setState('search', $search);

    if (TeamTime::addonExists("com_teamtimedotu")) {
      $filter_target_id = $mainframe->getUserStateFromRequest(
              $option . '.filter_target_id', 'filter_target_id', '', 'int');

      $this->setState('filter_target_id', $filter_target_id);
    }
  }

  /**
   * Method to get item data
   *
   * @access public
   * @return array
   */
  function getData() {
    // Lets load the content if it doesn't already exist
    if (empty($this->_data)) {
      $query = $this->_buildQuery();
      $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
    }
    if ($this->_db->getErrorMsg()) {
      JError::raiseWarning(500, $this->_db->getErrorMsg());
    }

    return $this->_data;
  }

  /**
   * Method to get the total number of items
   *
   * @access public
   * @return integer
   */
  function getTotal() {
    // Lets load the content if it doesn't already exist
    if (empty($this->_total)) {
      $query = $this->_buildQuery();
      $this->_total = $this->_getListCount($query);
    }

    return $this->_total;
  }

  /**
   * Method to get a pagination object
   *
   * @access public
   * @return integer
   */
  function getPagination() {
    // Lets load the content if it doesn't already exist
    if (empty($this->_pagination)) {
      jimport('joomla.html.pagination');
      $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
    }

    return $this->_pagination;
  }

  function _buildQuery() {
    $table = & $this->getTable($this->_table);
    $where = $this->_buildContentWhere();
    $orderby = $this->_buildContentOrderBy();

    $sql_teamtimedotu = TeamTime::_("get_tasks_sqldata_teamtimedotu", null);

    $query = ' SELECT a.*, b.name AS project_name, c.name AS type_name';

    if ($sql_teamtimedotu != null) {
      $query .= $sql_teamtimedotu["fields"];
    }

    $query .= ' FROM ' . $table->getTableName() . ' AS a '
        . ' LEFT JOIN #__teamlog_project AS b ON b.id = a.project_id'
        . ' LEFT JOIN #__teamlog_type AS c ON c.id = a.type_id';

    if ($sql_teamtimedotu != null) {
      $query .= $sql_teamtimedotu["join"];
    }

    $query .= $where . $orderby;

    //error_log($this->_db->replacePrefix($query));

    return $query;
  }

  function _buildContentWhere() {
    global $mainframe, $option;

    $db = & JFactory::getDBO();
    $search = $this->getState('search');
    $state = $this->getState('filter_state');

    $project = $this->getState('filter_project');
    $type = $this->getState('filter_type');

    $target_id = $this->getState('filter_target_id');

    $where = array();

    // search filter
    if ($search) {
      $where[] = 'LOWER(a.name) LIKE ' . $db->Quote('%' . $db->getEscaped($search, true) . '%', false);
    }

    // state filter
    if ($state !== '') {
      $where[] = 'a.state = ' . intval($state);
    }

    if ($project !== '') {
      $where[] = 'a.project_id = ' . intval($project);
    }
    if ($type !== '') {
      $where[] = 'a.type_id = ' . intval($type);
    }

    if ($target_id > 0) {
      $where[] = 'tv.id = ' . intval($target_id);
    }

    $where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

    return $where;
  }

  function _buildContentOrderBy() {
    global $mainframe, $option;

    if (TeamTime::addonExists("com_teamtimedotu")
        && $this->getState('filter_order') == 'a.rate') {
      $orderby = ' ORDER BY if(tp.price, tv.hourprice, a.rate)'
          . ' ' . $this->getState('filter_order_Dir');
    }
    else {
      $orderby = ' ORDER BY ' . $this->getState('filter_order')
          . ' ' . $this->getState('filter_order_Dir');
    }

    return $orderby;
  }

}