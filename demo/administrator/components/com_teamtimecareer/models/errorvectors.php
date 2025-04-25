<?php

class TeamtimecareerModelErrorvectors extends Core_Joomla_ManagerList {

	public $_table = 'targetvector';

	public function __construct() {
		$mainframe = & JFactory::getApplication();
		$option = JRequest::getCmd('option');

		parent::__construct();

		// get request vars
		$filterLimit = 100;
		$filterUserId = $mainframe->getUserStateFromRequest(
				$option . '.filter_user_id', 'filter_user_id', '', 'int');
		$filterTargets = $mainframe->getUserStateFromRequest(
				$option . '.filter_targets', 'filter_targets', '', 'int');

		$this->setState('levellimit', $filterLimit);
		$this->setState('filter_user_id', $filterUserId);
		$this->setState('filter_targets', $filterTargets);
	}

	private function _getUserIds($userId) {
		$result = array();

		$mProject = new TeamtimeModelProject();
		$mUser = new TeamtimeModelUser();
		$acl = new TeamTime_Acl();
		$projectId = $acl->filterUserProjectIds();
		if ($projectId !== null) {
			if ($userId != "") {
				$result = array_intersect(array($userId), $mProject->getUsersIds($projectId));
			}
			else {
				$result = $mProject->getUsersIds($projectId);
			}
		}
		else {
			if ($userId != "") {
				$result[] = $userId;
			}
			else {
				foreach ($mUser->getUsers() as $row) {
					$result[] = $row->id;
				}
			}
		}

		return $result;
	}

	private function _getUser($userId) {
		$query = "select a.* from #__users as a
      where a.id = " . (int) $userId;
		$this->_db->setQuery($query);

		return $this->_db->loadObject();
	}

	public function getUserData($userId) {
		$levellimit = $this->getState('levellimit');
		$filterTargets = $this->getState('filter_targets');

		$this->setState('filter_user_id', $userId);
		$query = $this->_buildQuery();
		$userData = $this->_getList($query);

		// establish the hierarchy of the targets
		$children = array();
		// first pass - collect children
		foreach ($userData as $v) {
			$v->name = "";
			$pt = (int) $v->parent;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push($list, $v);
			$children[$pt] = $list;
		}

		// second pass - get an indent list of the items
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, max(0, $levellimit - 1));
		//$list = array_slice($list, $this->getState('limitstart'), $this->getState('limit'));
		// get error vector data
		$targetm = new TeamtimecareerModelTargetvector();
		$statem = new TeamtimecareerModelStatevector();

		if ($filterTargets) {
			$targetBalance = $targetm->getTargetBalance($userId);

			$statem->setFilterTargetBalance($targetBalance);
			$statem->setFilterStateByBalance(true);

			$targetm->setFilterTargetBalance($targetBalance);
			$targetm->setFilterStateByBalance(true);
		}

		foreach ($list as $i => $row) {
			$stateValue = 0;

			if (/* $row->parent == 0 && */ $row->children > 0) {
				// target value
				$row->num_tree = $row->num;
				$list[$i] = $targetm->calcFieldsForParent($row->id, $row);

				$stateValue = $statem->getStateVectorValueForParent($row->id, $userId);
			}
			else {
				$stateValue = $statem->getStateVectorValue($row->id, $userId);
			}

			$list[$i]->state_value = $stateValue;
		}

		$statem->setFilterStateByBalance(false);
		$targetm->setFilterStateByBalance(false);

		$namesFilter = array("user_tax_1", "user_tax_2");

		return array(
			"user" => $this->_getUser($userId),
			"balance" => $targetm->getTargetBalance($userId),
			"varsdata" => TeamTime::helper()->getFormals()->getUserVariables(
					$userId, $namesFilter),
			"list" => $list
		);
	}

	public function getData() {
		if (empty($this->_data)) {
			$this->_data = array();

			$userId = $this->getState('filter_user_id');
			// get data for users
			foreach ($this->_getUserIds($userId) as $uId) {
				$this->_data[$uId] = $this->getUserData($uId);
			}
		}

		if ($this->_db->getErrorMsg()) {
			JError::raiseWarning(500, $this->_db->getErrorMsg());
		}

		return $this->_data;
	}

	protected function _buildQuery() {
		$table = & $this->getTable($this->_table);
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();

		$query = 'select a.*, a.parent_id as parent from ' . $table->getTableName() . ' as a
			' . $where . '
			' . $orderby;

		return $query;
	}

	protected function _buildContentWhere() {
		$where = array();

		//...

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

	protected function _buildContentOrderBy() {
		$orderby = ' order by a.parent_id, a.ordering';

		return $orderby;
	}

}