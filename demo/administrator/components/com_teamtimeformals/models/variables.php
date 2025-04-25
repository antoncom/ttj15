<?php

class VariableModelVariables extends Core_Joomla_ManagerList {

	public $_table = 'variable';

	public function __construct() {
		$mainframe = & JFactory::getApplication();
		$option = JRequest::getCmd('option');

		parent::__construct();

		// get request vars
		$filterProject = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.filter_project', 'filter_project', '', 'cmd');
		$filterUsing = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.filter_using', 'filter_using', '', 'cmd');

		// set model vars
		$this->setState('filter_project', $filterProject);
		$this->setState('filter_using', $filterUsing);
	}

	protected function _buildQuery($calc_fields = false) {
		$table = & $this->getTable($this->_table);
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();

		$using = $this->getState('filter_using');
		if ($using == "") {
			$s = " left join #__teamtimeformals_variable_project as b on a.id = b.variable_id
				left join #__teamtimeformals_variable_user as u on a.id = u.variable_id ";
		}
		else if ($using == "0") {
			$s = " left join #__teamtimeformals_variable_project as b on a.id = b.variable_id ";
		}
		else if ($using == "1") {
			$s = " left join #__teamtimeformals_variable_user as u on a.id = u.variable_id ";
		}

		$query = ' SELECT a.*'
				. ' FROM ' . $table->getTableName() . " AS a
				$s "
				. $where
				. " group by a.id "
				. $orderby;

		return $query;
	}

	protected function _buildContentWhere() {
		$search = $this->getState('search');

		$using = $this->getState('filter_using');
		$project = $this->getState('filter_project');

		$where = array();

		// search filter
		if ($search) {
			$where[] = 'LOWER(a.name) LIKE ' . $this->_db->Quote(
					'%' . $this->_db->getEscaped($search, true) . '%', false);
		}

		if ($using) {
			$where[] = "a.using_in  = " . $using;
		}

		if ($project !== '') {
			$s = $using == "1" ? "u.user_id" : "b.project_id";
			$where[] = "{$s} = " . intval($project) . " or {$s} = 0";
		}
		else {
			if ($using !== "") {
				if ($using == "0") {
					$where[] = "b.project_id is not null";
				}
				else if ($using == "1") {
					$where[] = "u.user_id is not null";
				}
			}
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

}