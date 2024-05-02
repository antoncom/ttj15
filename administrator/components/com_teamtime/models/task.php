<?php

class TeamtimeModelTask extends Core_Joomla_Manager {

	public $_table = 'task';

	public function store($data) {
		$projArr = split(",", $data['selectedProjects']);

		for ($i = 0; $i < count($projArr); $i++) {
			$row = & $this->getTable($this->_table);

			// bind the form fields
			if (!$row->bind($data)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			$row->project_id = $projArr[$i];

			// check if model item data is valid
			if (!$row->check()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			// store model item to the database
			if (!$row->store()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			else {
				//$this->storeRate($_id);
			}

			TeamTime::trigger()->onSaveTask($row, $data);

			$this->_data = $row;
		}

		return true;
	}

	public function storeState($id, $state) {
		$row = & $this->getTable($this->_table);

		// store state to database
		if (!$row->setTaskState($id, $state)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	public function storeRate($id) {
		$row = & $this->getTable($this->_table);

		// store state to database
		if (!$row->setTaskRate($id)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	public function delete($cid = array()) {
		$table = & $this->getTable($this->_table);
		if (count($cid)) {
			JArrayHelper::toInteger($cid);
			$cids = implode(',', $cid);
			$query = 'DELETE FROM ' . $table->getTableName()
					. ' WHERE id IN (' . $cids . ')';
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			foreach ($cid as $id) {
				TeamTime::trigger()->onDeleteTask($id);
			}
		}

		return true;
	}

	public function getTasks($filter = array()) {
		$table = & $this->getTable($this->_table);
		$result = array();
		$where = array();

		$query = "select a.*, p.name as project_name
			from " . $table->getTableName() . " a
			left join #__teamtime_project p on a.project_id = p.id ";

		if (isset($filter["ids"])) {
			$where[] = "a.id in (" . implode(",", $filter["ids"]) . ")";
		}

		if (isset($filter["type_ids"])) {
			$where[] = "a.type_id in (" . implode(",", $filter["type_ids"]) . ")";
		}

		if (isset($filter["type_id"])) {
			$where[] = "a.type_id = " . (int) $filter["type_id"];
		}

		if (sizeof($where) > 0) {
			$query .= " where " . implode(" and ", $where);
		}

		$query .= " order by a.name ";

		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();

		return $result;
	}

	public function filterWithAllowedProjects($ids, $acl) {
		$result = array();

		foreach ($ids as $id) {
			$task = $this->getById($id);
			if (sizeof($acl->filterUserProjectIds(array($task->project_id))) > 0) {
				$result[] = $id;
			}
		}

		return $result;
	}

}
