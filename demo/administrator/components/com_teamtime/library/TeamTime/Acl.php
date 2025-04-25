<?php

class TeamTime_Acl {

	private $user;

	public function __construct($user = null) {
		$this->user = $user;

		if ($this->user == null) {
			$this->user = $this->getCurrentUser();
		}
	}

	private function getCurrentUser() {
		$user = & JFactory::getUser();

		return $user;
	}

	public function isSuperAdmin() {
		return $this->user->usertype == "Super Administrator";
	}

	public function isAdmin() {
		return $this->user->usertype == "Administrator";
	}

	public function isManager() {
		return $this->user->usertype == "Manager";
	}

	public function isGuest() {
		return $this->user->guest;
	}

	public function filterUserProjectIds($projectIds = null) {
		if ($this->isSuperAdmin()) {
			return $projectIds;
		}

		$mProject = new TeamtimeModelProject();
		$userProjects = $mProject->getActiveProjectsIds(true);

		if ($projectIds !== null) {
			$result = array_intersect($projectIds, $userProjects);
		}
		else {
			$result = $userProjects;
		}

		return $result;
	}

	public function isAllowByProject($cid = null, $controller = "", $option = "") {
		if ($cid === null) {
			$cid = JRequest::getVar('cid', array(), null, 'array');
		}
		if (sizeof($cid) == 0 || (sizeof($cid) == 1 && !$cid[0])) {
			return true;
		}

		//error_log("input:" . print_r($cid, true));

		if ($controller == "") {
			$controller = JRequest::getWord('controller');
		}
		if ($option == "") {
			$option = substr(JRequest::getCmd('option'), 4);
		}

		$className = ucfirst($option) . "Model" . ucfirst($controller);
		//error_log($className);

		$model = new $className();
		$cid = $model->filterWithAllowedProjects($cid, $this);

		//error_log("output:" . print_r($cid, true));

		if (sizeof($cid) > 0) {
			return true;
		}

		return false;
	}

	public function isAllow($cid = null) {
		return $this->isAllowByProject($cid);
	}

}