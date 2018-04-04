<?php

class TeamtimebpmControllerTemplate extends Core_Joomla_EditController {

	public function __construct($default = array()) {
		parent::__construct($default);

		$this->viewEdit = 'template';
		$this->viewList = 'templates';
		$this->acl = new TeamTime_Acl();
	}

	public function display() {
		$ids = null;
		$view = JRequest::getCmd('view');

		if (in_array($view, array("templateimport"))) {
			$ids = array(JRequest::getVar("template_id", 0));
		}
		else if (in_array($view, array("templatediagrampage"))) {
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

		$mTemplate = new TeamtimebpmModelTemplate();
		$mTemplate->removeTag($post["tag"], $post["id"]);

		jexit();
	}

	public function appendTag() {
		$post = JRequest::get('post');
		if (!$this->isAllowed(array($post["id"]))) {
			jexit();
		}

		$mTemplate = new TeamtimebpmModelTemplate();
		print $mTemplate->appendTag($post["tag"], $post["id"]);

		jexit();
	}

	public function setData() {
		$post = JRequest::get('post');
		if (!$this->isAllowed(array($post["id"]))) {
			jexit();
		}

		$mTemplate = new TeamtimebpmModelTemplate();
		$mTemplate->setId($post["id"]);
		$data = $mTemplate->getData();

		switch ($post["cmd"]) {
			case "archieve":
				$data->archived = "archived";
				$mTemplate->store($data);
				break;

			case "restore":
				$data->archived = "active";
				$mTemplate->store($data);
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

		$mTemplate = new TeamtimebpmModelTemplate();
		print $mTemplate->getDiagram($data["id"]);

		jexit();
	}

	public function saveDiagram() {
		$post = JRequest::get('post');
		if (!$this->isAllowed(array($post["id"]))) {
			jexit();
		}

		// get raw data
		$post["data"] = $_REQUEST["data"];
		$mTemplate = new TeamtimebpmModelTemplate();
		$result = $post["data"];
		$mTemplate->setDiagram($post["id"], $result);

		print $result;

		jexit();
	}

	public function importAsProcess() {
		$post = JRequest::get('post');
		$id = (int) $post["id"];
		$spaceId = (int) $post["space_id"];
		if (!$this->isAllowed(array($id))) {
			jexit();
		}

		$mTemplate = new TeamtimebpmModelTemplate();
		$mTemplate->setId($id);
		$template = $mTemplate->getData();
		$template->space_id = $spaceId;
		unset($template->id);

		$templateDiagram = $mTemplate->getDiagram($id);

		$mProcess = new TeamtimebpmModelProcess();
		if ($mProcess->store($template)) {
			$mProcess->setDiagram($mProcess->_data->id, $templateDiagram);
		}

		//print $result;

		jexit();
	}

	public function follow() {
		$post = JRequest::get();
		if (!$this->isAllowed(array($post["id"]))) {
			jexit();
		}
		
		$mProcess = new TeamtimebpmModelTemplate();
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

}