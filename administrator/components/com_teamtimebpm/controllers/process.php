<?php

class TeamtimebpmControllerProcess extends Core_Joomla_EditController {

	public function __construct($default = array()) {
		parent::__construct($default);

		$this->viewEdit = 'process';
		$this->viewList = 'processes';
		$this->acl = new TeamTime_Acl();
	}

	public function display() {
		$ids = null;
		$view = JRequest::getCmd('view');

		if (in_array($view, array("processimport", "processlinkto"))) {
			$ids = array(JRequest::getVar("process_id", 0));
		}
		else if (in_array($view, array("processdiagrampage"))) {
			$ids = array(JRequest::getVar("id", 0));
		}
		if (!$this->isAllowed($ids)) {
			return;
		}

		parent::display();
	}

	//
	// ajax actions
	//

	public function removeTag() {
		$post = JRequest::get('post');
		if (!$this->isAllowed(array($post["id"]))) {
			jexit();
		}

		$mProcess = new TeamtimebpmModelProcess();
		$mProcess->removeTag($post["tag"], $post["id"]);

		jexit();
	}

	public function appendTag() {
		$post = JRequest::get('post');
		if (!$this->isAllowed(array($post["id"]))) {
			jexit();
		}

		$mProcess = new TeamtimebpmModelProcess();
		print $mProcess->appendTag($post["tag"], $post["id"]);

		jexit();
	}

	public function setData() {
		$post = JRequest::get('post');
		if (!$this->isAllowed(array($post["id"]))) {
			jexit();
		}

		$mProcess = new TeamtimebpmModelProcess();
		$mProcess->setId($post["id"]);
		$data = $mProcess->getData();

		switch ($post["cmd"]) {
			case "archieve":
				$data->archived = "archived";
				$mProcess->store($data);
				break;

			case "restore":
				$data->archived = "active";
				$mProcess->store($data);
				break;

			default:
				break;
		}

		jexit();
	}

	public function loadDiagram() {
		$data = JRequest::get('get');
		if (!$this->isAllowed(array($data["id"]))) {
			jexit();
		}

		$mProcess = new TeamtimebpmModelProcess();
		print $mProcess->getDiagram($data["id"]);

		jexit();
	}

	public function saveDiagram() {
		$post = JRequest::get('post');
		if (!$this->isAllowed(array($post["id"]))) {
			jexit();
		}

		// get raw data
		$post["data"] = $_REQUEST["data"];
		$mProcess = new TeamtimebpmModelProcess();
		$result = $mProcess->makeTodosFromDiagram($post["data"], $post["id"]);
		$mProcess->setDiagram($post["id"], $result);

		print $result;

		jexit();
	}

	public function playProcess() {
		$id = JRequest::getVar("id", 0);
		if (!$this->isAllowed(array($id))) {
			jexit();
		}

		$mProcess = new TeamtimebpmModelProcess();
		$mProcess->setId($id);
		$data = $mProcess->getData();

		$result = array();
		if ($data->is_started) {
			// stop process
			$data->is_started = 0;
			$mProcess->stopTodos($id);
			$result["msg"] = "Process stoped";
		}
		else {
			// start process
			$data->is_started = 1;
			$mProcess->startTodos($id);
			$result["msg"] = "Process started";
		}

		$mProcess->store($data);

		$result = json_encode($result);
		print $result;

		jexit();
	}

	public function loadInfo() {
		$post = JRequest::get('post');
		// get raw data
		$post["figures"] = $_REQUEST["figures"];

		$figures = json_decode($post["figures"]);
		$info = $post["info"];

		$mProcess = new TeamtimebpmModelProcess();
		$result = $mProcess->getBlocksInfo($figures, $info);

		$result = json_encode($result);
		print $result;

		jexit();
	}

	public function saveAsTemplate() {
		$post = JRequest::get('post');
		$id = (int) $post["id"];
		if (!$this->isAllowed(array($id))) {
			jexit();
		}

		$mProcess = new TeamtimebpmModelProcess();
		$mProcess->setId($id);
		$process = $mProcess->getData();
		unset($process->id);

		$processDiagram = $mProcess->removeTodoIds($mProcess->getDiagram($id));

		$mTemplate = new TeamtimebpmModelTemplate();
		if ($mTemplate->store($process)) {
			$mTemplate->setDiagram($mTemplate->_data->id, $processDiagram);
		}

		//print $result;

		jexit();
	}

	public function loadRoles() {
		$mRole = new BpmnRoleModelBpmnRole();
		$result = $mRole->getRoles();
		print json_encode($result);

		jexit();
	}

	public function follow() {
		$post = JRequest::get();
		if (!$this->isAllowed(array($post["id"]))) {
			jexit();
		}

		$mProcess = new TeamtimebpmModelProcess();
		$mProcess->setFollowed($post["follow"], $post["id"]);

		jexit();
	}

	public function loadProjects() {
		$spaceId = JRequest::getVar('space_id', 0);
		$helperBase = TeamTime::helper()->getBase();

		print $helperBase->getProjectSelect(null,
						array(
					"showClosed" => false,
					"autosubmit" => false,
					"firstOptions" => JHTML::_('select.option', "", "- " . JText::_('Select project') . " -"),
					"type" => "space",
					"spaceId" => $spaceId
				));

		jexit();
	}

	public function loadProcesses() {
		$id = (int) JRequest::getVar('id');
		if ($id != 0) {
			$doctypeId = JRequest::getVar('doctype_id');
			$helperBpmn = TeamTime::helper()->getBpmn();
			$from = JRequest::getVar('from', '');
			$until = JRequest::getVar('until', '');

			print $helperBpmn->getProcessSelect(null,
							array(
						"attrs" => 'size="10" multiple',
						"doctype_id" => $doctypeId,
						"id" => $id,
						"from" => $from,
						"until" => $until,
						"fieldId" => "process_id[]"
					));
		}

		jexit();
	}

}