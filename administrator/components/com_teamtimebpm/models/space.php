<?php

class TeamtimebpmModelSpace extends Core_Joomla_Manager {

	public $_table = 'teamtimebpmspace';

	public function store($data) {
		$row = & $this->getTable($this->_table);

		// bind the form fields
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// remove spaces in tag list
		$row->tags = str_replace(" ", "", $row->tags);

		// save modified data
		$datenow = & JFactory::getDate();
		$row->modified = $datenow->toMySQL();

		$user = & JFactory::getUser();
		$row->modified_by = $user->get("id");

		// check if model item data is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// get previous value
		$prevData = null;
		if ($row->id) {
			$prevData = & $this->getTable($this->_table);
			$prevData->load($row->id);
		}

		// store model item to the database
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (isset($data["projects"])) {
			$this->setProjectsIds($row->id, $data["projects"]);
		}
		TeamTime::trigger()->onSpaceChanged($prevData, $row);

		$this->_data = $row;

		return true;
	}

	public function removeProcessesAndTemplates($spaceId) {
		$mProcess = new TeamtimebpmModelProcess();
		$ids = array();
		foreach ($mProcess->getProcesses(array("space_id" => $spaceId)) as $process) {
			$ids[] = $process->id;
		}
		$mProcess->delete($ids);

		$mTemplate = new TeamtimebpmModelTemplate();
		$ids = array();
		foreach ($mTemplate->getTemplates(array("space_id" => $spaceId)) as $template) {
			$ids[] = $template->id;
		}
		$mTemplate->delete($ids);
	}

	public function hasTemplatesOrProcesses($spaceId) {
		$mProcess = new TeamtimebpmModelProcess();
		$processes = $mProcess->getProcesses(array("space_id" => $spaceId));
		if (sizeof($processes) > 0) {
			return 1;
		}

		$mTemplate = new TeamtimebpmModelTemplate();
		$templates = $mTemplate->getTemplates(array("space_id" => $spaceId));
		if (sizeof($templates) > 0) {
			return 1;
		}

		return 0;
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
			else {
				foreach ($cid as $id) {
					$this->removeProcessesAndTemplates($id);
					$this->removeFollowed($id);
				}
			}
		}

		return true;
	}

	public function getModifiedUserName($userId = null) {
		if ($userId == null) {
			$userId = $this->modified_by;
		}

		$query = 'select * from #__users
			where id = ' . (int) $userId;
		$this->_db->setQuery($query);

		$row = $this->_db->loadObject();

		return $row->name;
	}

	public function getTags($stags) {
		$result = array();

		foreach (explode(",", $stags) as $tag) {
			$s = trim($tag);
			if ($s != "") {
				$result[] = $s;
			}
		}

		return $result;
	}

	public function getAllTags() {
		$table = & $this->getTable($this->_table);
		$result = array();

		$query = 'select * from ' . $table->getTableName();
		$this->_db->setQuery($query);

		foreach ($this->_db->loadObjectList() as $row) {
			foreach ($this->getTags($row->tags) as $tag) {
				$result[$tag] = 1;
			}
		}

		return array_keys($result);
	}

	public function removeTag($tag, $id) {
		$this->setId($id);
		$data = $this->getData($id);

		$result = $this->getTags($data->tags);
		$i = array_search($tag, $result);
		if ($i !== false) {
			unset($result[$i]);
		}
		$data->tags = implode(",", $result);

		$this->store($data);

		return $data->tags;
	}

	public function appendTag($tags, $id) {
		if (!$id) {
			return null;
		}

		$this->setId($id);
		$data = $this->getData($id);

		$result = array();
		foreach ($this->getTags($data->tags) as $tag) {
			$result[$tag] = 1;
		}
		foreach (explode(",", $tags) as $tag) {
			$result[$tag] = 1;
		}

		$data->tags = implode(",", array_keys($result));

		$this->store($data);

		return $data->tags;
	}

	public function getOptionsList() {
		$table = & $this->getTable($this->_table);
		$acl = new TeamTime_Acl();
		$projectId = $acl->filterUserProjectIds();

		$where = array();
		if ($projectId !== null) {
			$where[] = " b.project_id in (" . implode(",", $projectId) . ")";
		}

		if (sizeof($where) > 0) {
			$where = " where " . implode(" and ", $where);
		}
		else {
			$where = "";
		}

		$query = 'select a.id as value, a.name as text
			from ' . $table->getTableName() . ' as a
			left join #__teamtimebpm_project_space as b on a.id = b.space_id
			' . $where . '
			group by a.id
			order by a.name';

		//error_log($query);

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();

		return $rows;
	}

	public function getProjectBySpace($spaceId) {
		$this->setId($spaceId);
		$space = $this->getData();

		$query = "select * from #__teamtime_project
			where name = " . $this->_db->Quote($space->name);

		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();

		return $row ? $row->id : "";
	}

	public function setFollowed($follow, $id, $userId = null) {
		if ($userId == null) {
			$user = &JFactory::getUser();
			$userId = $user->id;
		}

		$query = "insert into #__teamtimebpm_followspace
			(space_id, user_id, follow)
      values(" . (int) $id . ", " . (int) $userId . ", " . (int) $follow . ")
      on duplicate key update follow = " . (int) $follow;

		$this->_db->Execute($query);
	}

	public function isFollowed($id, $userId = null) {
		$result = false;

		if ($userId == null) {
			$user = &JFactory::getUser();
			$userId = $user->id;
		}

		$query = "select * from #__teamtimebpm_followspace
			where space_id = " . (int) $id . " and user_id = " . (int) $userId;

		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();
		if ($row) {
			$result = $row->follow;
		}

		return $result;
	}

	public function removeFollowed($id, $userId = null) {
		if ($userId == null) {
			$user = &JFactory::getUser();
			$userId = $user->id;
		}

		$query = "delete from #__teamtimebpm_followspace
			where space_id = " . (int) $id . " and user_id = " . (int) $userId;

		$this->_db->Execute($query);
	}

	public function getFollowedUsers($id) {
		$result = array();

		$query = "select u.* from #__teamtimebpm_followspace as a
			left join #__users as u on a.user_id = u.id
			where a.space_id = " . (int) $id . " and a.follow = 1 and u.id is not null";

		//error_log($query);

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		if ($rows) {
			$result = $rows;
		}

		return $result;
	}

	public function removeProjectsIds($spaceId) {
		$query = "delete from #__teamtimebpm_project_space
			where space_id = " . (int) $spaceId;
		$this->_db->Execute($query);
	}

	public function setProjectsIds($spaceId, $projectIds) {
		if (!is_array($projectIds) || sizeof($projectIds) == 0 || $projectIds[0] == 0) {
			return;
		}

		$this->removeProjectsIds($spaceId);

		foreach ($projectIds as $id) {
			$query = "insert into #__teamtimebpm_project_space (space_id, project_id)
					values(" . (int) $spaceId . ", " . (int) $id . ")";
			$this->_db->Execute($query);
		}
	}

	public function getProjectsIds($spaceId) {
		$result = array();
		$query = "select * from #__teamtimebpm_project_space
			where space_id = " . (int) $spaceId;

		$this->_db->setQuery($query);
		foreach ($this->_db->loadObjectList() as $row) {
			$result[] = $row->project_id;
		}

		return $result;
	}

	public function filterWithAllowedProjects($ids, $acl) {
		$table = & $this->getTable($this->_table);
		$result = array();

		$where = array();

		$acl = new TeamTime_Acl();
		$projectId = $acl->filterUserProjectIds();
		if ($projectId !== null) {
			$where[] = 'b.project_id in (' . implode(",", $projectId) . ")";
			$where[] = 'a.id in (' . implode(",", $ids) . ")";
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		$query = 'select * from ' . $table->getTableName() . ' as a
			left join #__teamtimebpm_project_space as b on a.id = b.space_id
			' . $where . '
			group by a.id';

		//error_log($query);

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		foreach ($rows as $row) {
			$result[] = $row->space_id;
		}

		return $result;
	}

}