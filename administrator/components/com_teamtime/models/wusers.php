<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class WuserModelWusers extends JModel {

	/**
	 * Table
	 *
	 * @var array
	 */
	var $_table = 'wuser';

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
		$filter_order     = $mainframe->getUserStateFromRequest($option.$this->getName().'.filter_order', 'filter_order', 'a.id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option.$this->getName().'.filter_order_Dir',	'filter_order_Dir',	'', 'word');
		$limit		      = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	      = $mainframe->getUserStateFromRequest($option.$this->getName().'.limitstart', 'limitstart', 0, 'int');
		$search	          = $mainframe->getUserStateFromRequest($option.$this->getName().'.search', 'search', '', 'string');

		$from_period = $mainframe->getUserStateFromRequest(
			$option.$this->getName().'.from_period', 'from_period', '', 'string');
		$until_period = $mainframe->getUserStateFromRequest(
			$option.$this->getName().'.until_period', 'until_period', '', 'string');

		// convert search to lower case
		$search = JString::strtolower($search);

		// in case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		// set model vars
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		$this->setState('search', $search);

		$this->setState('from_period', $from_period);
		$this->setState('until_period', $until_period);
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
		$db		 =& JFactory::getDBO();
		
		$table   =& $this->getTable($this->_table);
		$where   =  $this->_buildContentWhere();
		$orderby =  $this->_buildContentOrderBy();

		$from_period = $this->getState('from_period');
		$until_period = $this->getState('until_period');
		// hours fact date filter
		if ($from_period && $until_period)
			$where_hours_fact = ' and date >= '.$db->Quote($db->getEscaped("$from_period 00:00:00", true), false).
				' and date <= '.$db->Quote($db->getEscaped("$until_period 23:59:59", true), false);
		else
			$where_hours_fact = "";

		$query = 'select a.*, b.id as todo_id, b.created, ud.send_msg, ud.hour_price
				from #__users as a 
				LEFT JOIN #__teamlog_todo AS b ON b.user_id = a.id
				left join #__teamlog_userdata as ud on a.id = ud.user_id '.
				$where.
				" group by a.id ".$orderby;
		
		//print "<pre>";
		//print $query;
		//print "</pre>";

		return $query;
	}

	function _buildContentWhere() {
		global $mainframe, $option;

		$db		=& JFactory::getDBO();
		$search = $this->getState('search');

		$from_period = $this->getState('from_period');
		$until_period = $this->getState('until_period');

		$where = array();

		$where[] = 'a.block = 0';

		if ($search) {
			$where[] = 'LOWER(a.name) LIKE '.$db->Quote('%'.$db->getEscaped( $search, true ).'%', false);
		}

		// date filter
		//if ($from_period && $until_period) {
		//	$where[] = ' (b.created >= '.$db->Quote($db->getEscaped($from_period, true), false).
		//		' and b.created <= '.$db->Quote($db->getEscaped($until_period, true), false).
		//		") or b.user_id is null ";
		//}

		$where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');

		return $where;
	}

	function _buildContentOrderBy() {
		global $mainframe, $option;

		$orderby = ' ORDER BY '.$this->getState('filter_order').' '.$this->getState('filter_order_Dir');

		return $orderby;
	}

}