<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TeamtimecareerModelStatevector extends Core_Joomla_Manager {

	public $_table = 'statevector';
	//
	private $_targetBalance = array();
	private $_filterStateByBalance = false;

	private function storeSkills($data) {
		$datenow = & JFactory::getDate();
		$sdate = $datenow->toMySQL();

		// add skills state
		//if (isset($data["skill_num"])) {
		if (isset($data["skill_value"])) {
			foreach ($data["skill_value"] as $k => $v) {
				$add_value = $data["new_skill_value"][$k] - $data["skill_value"][$k];

				//if (trim($v) == "" || $add_value == 0) {
				if ($add_value == 0) {
					continue;
				}

				$title = array();
				if (isset($data["skill_title"][$k])) {
					$title[] = $data["skill_title"][$k];
				}

				if (trim($data["description"]) != "") {
					$title[] = $data["description"];
				}

				if (sizeof($title) > 0) {
					$title = implode(" - ", $title);
				}

				$query = "insert into #__teamtimecareer_statevector
          (target_id, description, num, user_id, `date`) " //, skill_target_id
						. "values(" . $this->_db->Quote($k) // $this->_db->Quote($data["target_id"])
						. ", " . $this->_db->Quote($title)
						. ", " . $this->_db->Quote($add_value)
						. ", " . $this->_db->Quote($data["user_id"])
						. ", " . $this->_db->Quote($sdate)
						//. ", " . $this->_db->Quote($k)
						. ")";

				$this->_db->Execute($query);
			}
		}
	}

	public function store($data) {
		error_log("STATEVECTOR.PHP");
		$row = & $this->getTable($this->_table);

		// bind the form fields
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->id) {
			$datenow = & JFactory::getDate();
			$row->date = $datenow->toMySQL();
		}

		// check if model item data is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if ($row->num != 0) {
			// store model item to the database
			if (!$row->store()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		$this->_data = $row;

		$this->storeSkills($data);

		return true;
	}

	public function setFilterTargetBalance($balance) {
		$this->_targetBalance = $balance;
	}

	public function setFilterStateByBalance($value) {
		$this->_filterStateByBalance = $value;
	}

	public function getStateVectorValue($targetId, $userId, $isSkill = false) {
		$table = & $this->getTable($this->_table);
		$result = null;

		if (is_array($targetId)) {
			// filter for user targets
			if ($this->_filterStateByBalance) {
				$tmpIds = array();
				foreach ($targetId as $id) {
					if (isset($this->_targetBalance[$id])) {
						$tmpIds[] = $id;
					}
				}
				$targetId = $tmpIds;
			}

			if (sizeof($targetId) == 0) {
				return $result;
			}

			$stargetId = "target_id in (" . implode(",", $targetId) . ")";
		}
		else {
			// filter for user targets
			if ($this->_filterStateByBalance) {
				if (!isset($this->_targetBalance[$targetId])) {
					return $result;
				}
			}

			if (!$isSkill) {
				$stargetId = "target_id = " . (int) $targetId;
			}
			else {
				//$starget_id = "skill_target_id = " . (int) $target_id;
				$stargetId = "target_id = " . (int) $targetId;
			}
		}

		$query = "select sum(a.num) as num from " . $table->getTableName() . " as a
			where " . $stargetId . " and user_id = " . (int) $userId;

		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();
		if ($row) {
			$result = $row->num;
		}

		return $result;
	}

	public function getStateVectorValueForParent($targetId, $userId) {
		$result = 0;
		$params = array();

		$targetm = new TeamtimecareerModelTargetvector();
		$tree = $targetm->getTree($params, $targetId);
		$ids = array();

		$ids[] = $targetId;
		foreach ($targetm->flattenTree($tree) as $target) {
			$ids[] = $target->id;
		}

		if (sizeof($ids) > 0) {
			$result = $this->getStateVectorValue($ids, $userId);
		}

		return $result;
	}

	public function getStateByKey($targetId, $userId, $todoId = null) {
		$result = null;
		$table = & $this->getTable($this->_table);

		$query = "select * from " . $table->getTableName() . " as a
			where target_id = " . (int) $targetId
				. " and user_id = " . (int) $userId
				. ($todoId != null ? (" and todo_id = " . (int) $todoId) : "")
				. " order by id desc";

		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();
		if ($row) {
			$result = $row;
		}

		return $result;
	}

	public function getMarkedSkills($targetId, $userId) {
		$targetModel = new TeamtimecareerModelTargetvector();

		$skillsIds = array();
		foreach ($targetModel->getSkills($targetId) as $row) {
			$skillsIds[] = $row->id;
		}
		$skillsIds = sizeof($skillsIds) > 0 ? implode(",", $skillsIds) : 0;

		$result = array();

		$table = & $this->getTable($this->_table);
		$query = "select * from " . $table->getTableName() . " as a "
				. " where target_id in (" . $skillsIds . ") "
				. " and user_id = " . (int) $userId
				. " group by target_id";

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();

		if (!$rows) {
			return $result;
		}

		foreach ($rows as $row) {
			$result[$row->skill_target_id] = $row;
		}

		return $result;
	}

}