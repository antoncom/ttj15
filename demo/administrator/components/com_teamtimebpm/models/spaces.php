<?php

class TeamtimebpmModelSpaces extends Core_Joomla_ManagerList {

	public $_table = 'teamtimebpmspace';

	public function __construct() {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		parent::__construct();

		// get request vars
		$search = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.search', 'search', '', 'string');
		$search = JString::strtolower($search);
		if ($search == JText::_("BPM FILTER DEFAULT")) {
			$search = "";
		}
		$search = JString::strtolower($search);

		$filterArchived = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.filter_archived', 'archived', '', 'string');
		//$fromPeriod = $mainframe->getUserStateFromRequest(
		//		$option . $this->getName() . '.from_period', 'from_period', '', 'string');
		//$untilPeriod = $mainframe->getUserStateFromRequest(
		//		$option . $this->getName() . '.until_period', 'until_period', '', 'string');
		// set model vars
		$this->setState('filter_archived', $filterArchived);
		//$this->setState('from_period', $fromPeriod);
		//$this->setState('until_period', $untilPeriod);
		$this->setState('search', $search);
	}

	public function getData() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			$mSpace = new TeamtimebpmModelSpace();

			$query = $this->_buildQuery();
			$this->_data["Untagged"] = $this->_getList($query
					/* , $this->getState('limitstart'), $this->getState('limit') */);

			foreach ($this->_data["Untagged"] as $i => $item) {
				$this->_data["Untagged"][$i]->tags = $mSpace->getTags($item->tags);
			}
		}
		if ($this->_db->getErrorMsg()) {
			JError::raiseWarning(500, $this->_db->getErrorMsg());
		}

		return $this->_data;
	}

	public function getGroupedData() {
		if (empty($this->_data)) {
			$mSpace = new TeamtimebpmModelSpace();

			foreach ($mSpace->getAllTags() as $tag) {
				$query = $this->_buildQuery(array("tag" => $tag));
				$this->_data[$tag] = $this->_getList($query
						/* , $this->getState('limitstart'), $this->getState('limit') */);
				foreach ($this->_data[$tag] as $i => $item) {
					$this->_data[$tag][$i]->tags = $mSpace->getTags($item->tags);
				}
			}

			$query = $this->_buildQuery(array("tag" => ""));
			$this->_data["Untagged"] = $this->_getList($query
					/* , $this->getState('limitstart'), $this->getState('limit') */);
			foreach ($this->_data["Untagged"] as $i => $item) {
				$this->_data["Untagged"][$i]->tags = $mSpace->getTags($item->tags);
			}
		}
		if ($this->_db->getErrorMsg()) {
			JError::raiseWarning(500, $this->_db->getErrorMsg());
		}

		return $this->_data;
	}

	protected function _buildQuery($filter = array()) {
		$table = & $this->getTable($this->_table);
		$where = $this->_buildContentWhere($filter);
		$orderby = $this->_buildContentOrderBy();

		$fields = array();
		$joins = array();
		$fields[] = "u.name as user_name";
		$joins[] = " left join #__users as u on a.modified_by = u.id";
		$joins[] = " left join #__teamtimebpm_project_space AS b on a.id = b.space_id";

		if (sizeof($fields) > 0) {
			$fields = ", " . implode(", ", $fields);
		}
		else {
			$fields = "";
		}

		if (sizeof($joins) > 0) {
			$joins = implode("\n", $joins);
		}
		else {
			$joins = "";
		}

		$query = "select a.* {$fields}
			from " . $table->getTableName() . " as a
			{$joins}
			" . $where . "
			group by a.id
			" . $orderby;

		//error_log($query);

		return $query;
	}

	protected function _buildContentWhere($filter = array()) {
		$search = $this->getState('search');
		$filterArchived = $this->getState('filter_archived');
		//$fromPeriod = $this->getState('from_period');
		//$untilPeriod = $this->getState('until_period');

		$where = array();

		$acl = new TeamTime_Acl();
		$projectId = $acl->filterUserProjectIds();
		if ($projectId !== null) {
			$where[] = 'b.project_id in (' . implode(",", $projectId) . ")";
		}

		if ($search) {
			$where[] = 'LOWER(a.name) LIKE ' .
					$this->_db->Quote('%' . $this->_db->getEscaped($search, true) . '%', false);
		}

		if ($filterArchived) {
			$where[] = ' a.archived = ' . $this->_db->Quote($filterArchived);
		}

		if (isset($filter["tag"])) {
			if ($filter["tag"] == "") {
				$where[] = "a.tags = ''";
			}
			else {
				$where[] = '(LOWER(a.tags) LIKE ' .
						$this->_db->Quote('%,' . $this->_db->getEscaped($filter["tag"], true) . ',%', false) .
						' or LOWER(a.tags) LIKE ' .
						$this->_db->Quote('%,' . $this->_db->getEscaped($filter["tag"], true), false) .
						' or LOWER(a.tags) LIKE ' .
						$this->_db->Quote($this->_db->getEscaped($filter["tag"], true) . ',%', false) .
						' or LOWER(a.tags) = ' .
						$this->_db->Quote($this->_db->getEscaped($filter["tag"], true), false) . ")";
			}
		}

		$where = sizeof($where) ? ' WHERE ' . implode(' AND ', $where) : '';

		return $where;
	}

	protected function _buildContentOrderBy() {
		$order = $this->getState('filter_order');
		if ($order == "a.tags") {
			$order = "a.name";
		}

		$orderby = ' ORDER BY ' . $order .
				' ' . $this->getState('filter_order_Dir');

		return $orderby;
	}

}