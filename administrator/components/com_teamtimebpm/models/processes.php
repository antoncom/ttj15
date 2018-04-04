<?php

class TeamtimebpmModelProcesses extends Core_Joomla_ManagerList {

	public $_table = 'teamtimebpmprocess';

	public function __construct() {
		$mainframe =& JFactory::getApplication();
		$option = JRequest::getCmd('option');

		parent::__construct();

		// get request vars
		$search = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.search', 'search', '', 'string');
		if ($search == JText::_("BPM FILTER DEFAULT")) {
			$search = "";
		}
		$search = JString::strtolower($search);

		$filterArchived = $mainframe->getUserStateFromRequest(
				$option . $this->getName() . '.filter_archived', 'archived', '', 'string');

		// set model vars
		$this->setState('filter_archived', $filterArchived);
		$this->setState('search', $search);
	}

	public function getData() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			$mProcess = new TeamtimebpmModelProcess();

			$query = $this->_buildQuery();
			$this->_data["Untagged"] = $this->_getList($query
					/* , $this->getState('limitstart'), $this->getState('limit') */);

			foreach ($this->_data["Untagged"] as $i => $item) {
				$this->_data["Untagged"][$i]->tags = $mProcess->getTags($item->tags);
			}
		}
		if ($this->_db->getErrorMsg()) {
			JError::raiseWarning(500, $this->_db->getErrorMsg());
		}

		return $this->_data;
	}

	public function getGroupedData() {
		if (empty($this->_data)) {
			$order = $this->getState('filter_order');
			$mProcess = new TeamtimebpmModelProcess();
			$mSpace = new TeamtimebpmModelSpace();

			// group by tags
			if ($order == "a.tags") {
				foreach ($mProcess->getAllTags() as $tag) {
					$query = $this->_buildQuery(array("tag" => $tag));
					$this->_data[$tag] = $this->_getList($query
							/* , $this->getState('limitstart'), $this->getState('limit') */);
					foreach ($this->_data[$tag] as $i => $item) {
						$this->_data[$tag][$i]->tags = $mProcess->getTags($item->tags);
					}
				}

				$query = $this->_buildQuery(array("tag" => ""));
				$this->_data["Untagged"] = $this->_getList($query
						/* , $this->getState('limitstart'), $this->getState('limit') */);
				foreach ($this->_data["Untagged"] as $i => $item) {
					$this->_data["Untagged"][$i]->tags = $mProcess->getTags($item->tags);
				}
			}

			// group by spaces
			else if ($order == "a.space_id") {
				foreach ($mSpace->getOptionsList() as $group) {
					$query = $this->_buildQuery(array("space" => $group->value));
					$this->_data[$group->text] = $this->_getList($query
							/* , $this->getState('limitstart'), $this->getState('limit') */);
					foreach ($this->_data[$group->text] as $i => $item) {
						$this->_data[$group->text][$i]->tags = $mProcess->getTags($item->tags);
					}
				}
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
			" . $orderby;

		//error_log($query);

		return $query;
	}

	protected function _buildContentWhere($filter = array()) {
		$search = $this->getState('search');
		$filterArchived = $this->getState('filter_archived');

		$where = array();

		if ($search) {
			$where[] = 'LOWER(a.name) LIKE ' .
					$this->_db->Quote('%' . $this->_db->getEscaped($search, true) . '%', false);
		}

		if ($filterArchived) {
			$where[] = ' a.archived = ' . $this->_db->Quote($filterArchived);
		}

		$acl = new TeamTime_Acl();
		$projectId = $acl->filterUserProjectIds($projectId);
		if ($projectId !== null) {
			$where[] = 'a.project_id in (' . implode(",", $projectId) . ")";
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

		if (isset($filter["space"])) {
			$where[] = "a.space_id = " . (int) $filter["space"];
		}

		$where = sizeof($where) ? ' WHERE ' . implode(' AND ', $where) : '';

		return $where;
	}

	protected function _buildContentOrderBy() {
		$order = $this->getState('filter_order');
		if ($order == "a.tags" || $order == "a.space_id" || $order == "") {
			$order = "a.name";
		}

		$orderby = ' ORDER BY ' . $order .
				' ' . $this->getState('filter_order_Dir');

		return $orderby;
	}

}