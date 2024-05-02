<?php

class TeamtimebpmModelTemplate extends Core_Joomla_Manager {

	public $_table = 'teamtimebpmtemplate';

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

		// store model item to the database
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$this->_data = $row;

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
			else {
				foreach ($cid as $id) {
					$this->removeDiagram($id);
					$this->removeFollowed($id);
				}
			}
		}

		return true;
	}

	public function getModifiedUserName($user_id = null) {
		if ($user_id == null) {
			$user_id = $this->modified_by;
		}

		$query = 'select * from #__users
			where id = ' . (int) $user_id;
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

	public function setDiagram($id, $data) {
		if (!$id) {
			return;
		}

		$data = $this->_db->Quote($data);
		$query = "insert into `#__teamtimebpm_templatediagram`
      (template_id, `data`)
      values(" . (int) $id . ", " . $data . ")
      on duplicate key update `data` = " . $data;

		$this->_db->Execute($query);
	}

	public function getDiagram($id) {
		$result = "";

		$query = "select * from #__teamtimebpm_templatediagram
			where template_id = " . (int) $id;

		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();
		if ($row) {
			$result = $row->data;
		}

		return $result;
	}

	public function removeDiagram($id) {
		$result = "";

		$query = "delete from #__teamtimebpm_templatediagram
			where template_id = " . (int) $id;

		$this->_db->setQuery($query);
		$result = $this->_db->query();

		return $result;
	}

	public function getTemplates($filter = array()) {
		$table = & $this->getTable($this->_table);

		$where = array();

		$query = "select a.* from " . $table->getTableName() . " a ";

		if (isset($filter["space_id"])) {
			$where[] = "a.space_id = " . (int) $filter["space_id"];
		}

		if (sizeof($where) > 0) {
			$query .= " where " . implode(" and ", $where);
		}

		//$query .= " order by a.created";

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();

		return $rows ? $rows : array();
	}

	public function setFollowed($follow, $id, $userId = null) {
		if ($userId == null) {
			$user = &JFactory::getUser();
			$userId = $user->id;
		}

		$query = "insert into #__teamtimebpm_followtemplate
			(template_id, user_id, follow)
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

		$query = "select * from #__teamtimebpm_followtemplate
			where template_id = " . (int) $id . " and user_id = " . (int) $userId;

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

		$query = "delete from #__teamtimebpm_followtemplate
			where template_id = " . (int) $id . " and user_id = " . (int) $userId;

		$this->_db->Execute($query);
	}

	public function filterWithAllowedProjects($ids, $acl) {
		$result = array();

		foreach ($ids as $id) {
			$item = $this->getById($id);
			if (sizeof($acl->filterUserProjectIds(array($item->project_id))) > 0) {
				$result[] = $id;
			}
		}

		return $result;
	}

}