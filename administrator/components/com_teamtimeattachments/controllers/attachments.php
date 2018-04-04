<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// TEST 

jimport('joomla.application.component.controller');

class TeamtimeAttachmentsControllerAttachments extends JController {

	var $excludedExtentions;

	function __construct($default = array()) {
		parent::__construct($default);

		$this->excludedExtentions = array('php', 'exe');
	}

	function display() {
		// set defaults
		JRequest::setVar('layout', 'default');
		JRequest::setVar('view', 'attachments');

		parent::display();
	}

	function upload() {
		$helper = TeamTime::helper()->getBase();
		$post = JRequest::get();

		// type, id, base_path
		$params = $post;
		list($currentPath, $prefix) = TeamTime::helper()->getBpmn()->getUploadPath($params);

		//error_log("current path: " . $currentPath);
		//error_log("prefix for filename: " . $prefix);

		$path = JPATH_ROOT . "/images/stories/com_teamtime/" . $currentPath;
		$urlPath = JURI::root(true) . "/images/stories/com_teamtime/" . $currentPath;

		$helper->createDirRecurive($path);

		$result = array();

		$upload = new Zend_File_Transfer();
		$upload->setDestination($path);
		$upload->addValidator('ExcludeExtension', false, $this->excludedExtentions);

		$files = $upload->getFileInfo();
		$errors = array();
		foreach ($files as $file => $info) {
			if (!$upload->isUploaded($file)) {
				continue;
			}

			if (!$upload->isValid($file)) {
				foreach ($upload->getMessages() as $k => $v) {
					$errors[$k] = true;
				}
				continue;
			}

			$result[] = array(
				'filelink' => $info["name"],
				'filename' => $info["name"]
			);
		}

		if ($upload->receive()) {
			foreach ($result as $i => $info) {
				$newname = $prefix . $helper->translit($info["filename"], 0, '\.');
				rename($path . $info["filename"], $path . $newname);
				$result[$i]["filelink"] = $urlPath . $newname;
			}
		}
		else {
			$errorMessages = array(JText::_("Error on files upload"));
			foreach ($errors as $k => $v) {
				if (isset($errors[Zend_Validate_File_ExcludeExtension::FALSE_EXTENSION])) {
					$errorMessages[] = JText::_("Not allowed file for upload");
				}
			}
			$result = array("errors" => implode("\n", $errorMessages));
		}

		print json_encode($result);

		jexit();
	}

	function upload1() {

		error_log(print_r($_FILES, true));

		jexit();
	}

}