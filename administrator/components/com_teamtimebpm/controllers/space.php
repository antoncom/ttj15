<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class TeamtimebpmControllerSpace extends Core_Joomla_EditController {

	public function __construct($default = array()) {
		parent::__construct($default);

		$this->viewEdit = 'space';
		$this->viewList = 'spaces';
		$this->acl = new TeamTime_Acl();
	}

	//
	// ajax actions
	//

	public function removeTag() {
		$post = JRequest::get('post');
		$mSpace = new TeamtimebpmModelSpace();
		$mSpace->removeTag($post["tag"], $post["id"]);

		jexit();
	}

	public function appendTag() {
		$post = JRequest::get('post');
		$mSpace = new TeamtimebpmModelSpace();
		print $mSpace->appendTag($post["tag"], $post["id"]);

		jexit();
	}

	public function setData() {
		$post = JRequest::get('post');
		$mSpace = new TeamtimebpmModelSpace();
		$mSpace->setId($post["id"]);
		$data = $mSpace->getData();

		switch ($post["cmd"]) {
			case "archieve":
				$data->archived = "archived";
				$mSpace->store($data);
				break;

			case "restore":
				$data->archived = "active";
				$mSpace->store($data);
				break;

			default:
				break;
		}

		jexit();
	}

	public function follow() {
		$post = JRequest::get();
		$mSpace = new TeamtimebpmModelSpace();
		$mSpace->setFollowed($post["follow"], $post["id"]);

		jexit();
	}

	public function unfollow() {
		$option = JRequest::getCmd('option');
		$controller = JRequest::getWord('controller');
		$post = JRequest::get();
		$mSpace = new TeamtimebpmModelSpace();
		$mSpace->setFollowed(0, $post["id"]);
		$mSpace->setId($post["id"]);
		$space = $mSpace->getData();

		$msg = JText::sprintf('SPACE_UNFOLLOWED', $space->name);
		$link = 'index.php?option=' . $option . '&controller=' . $controller;

		$this->setRedirect($link, $msg);
	}

}