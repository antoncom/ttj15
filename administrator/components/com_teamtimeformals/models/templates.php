<?php

class TemplateModelTemplates extends Core_Joomla_ManagerList {

	public $_table = 'template';

	public function __construct() {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');
		
		parent::__construct();

		// get request vars		
		$filterType = $mainframe->getUserStateFromRequest(
						$option . $this->getName() . '.filter_type', 'filter_type', '', 'cmd');
		$filterUsing = $mainframe->getUserStateFromRequest(
						$option . $this->getName() . '.filter_using', 'filter_using', '', 'cmd');

		// set model vars
		$this->setState('filter_using', $filterUsing);
		$this->setState('filter_type', $filterType);
	}

	protected function _buildQuery($calc_fields = false) {
		$table = & $this->getTable($this->_table);
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();

		$scalc_fields = " , b.name as type_name ";

		$query = ' SELECT a.*'
						. $scalc_fields
						. ' FROM ' . $table->getTableName() . ' AS a
					left join #__teamtimeformals_type as b on a.type = b.id'
						. $where
						. $orderby;

		return $query;
	}

	protected function _buildContentWhere() {		
		$search = $this->getState('search');
		$type = $this->getState('filter_type');
		$using = $this->getState('filter_using');

		$where = array();

		// search filter
		if ($search) {
			$where[] = 'LOWER(a.name) LIKE ' . $this->_db->Quote(
					'%' . $this->_db->getEscaped($search, true) . '%', false);
		}

		if ($type !== '') {
			$where[] = 'a.type = ' . intval($type);
		}
		
		if ($using) {
			$where[] = "b.using_in  = " . $this->_db->Quote($using);
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}	

}