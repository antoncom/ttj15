<?php

class TeamTime_EventHandlers_Bpmn {

	public function onInit() {
		$path = JPATH_ADMINISTRATOR . '/components/com_teamtimebpm';

		// include js libs
		JHTML::script('default.js', 'media/com_teamtimebpm/assets/js/teamtimebpm/');
		
		// set joomla includes
		JTable::addIncludePath($path . '/tables');
		JHTML::addIncludePath($path . '/helpers');

		// include models
		require_once($path . '/models/bpmnrole.php');
		require_once($path . '/models/space.php');
		require_once($path . '/models/process.php');
		require_once($path . '/models/template.php');

		$lang = & JFactory::getLanguage();
		$lang->load("com_teamtimebpm", JPATH_BASE);
	}

	public function onSaveTodo($todoItem, $post) {
		if (isset($post["process_id"])) {
			$model = new TeamtimebpmModelProcess();
			$model->setTodoData(array(
				"todo_id" => $todoItem->id,
				"process_id" => $post["process_id"]
			));
		}
	}

	public function onDeleteTodo($todoId) {
		$model = new TeamtimebpmModelProcess();
		$model->deleteTodoData($todoId);
	}

	private function generateSpaceChangedContent($data) {
		$fname = JPATH_ADMINISTRATOR .
				"/components/com_teamtimebpm/assets/templates/spacechangenotice.html";

		$helperBase = TeamTime::helper()->getBase();

		$tpl = new HTML_Template_IT("");
		$tpl->loadTemplatefile($fname, true, true);

		$urlEdit = JURI::root() . "administrator/index.php?option=com_teamtimebpm" .
				"&controller=space&view=space&task=edit" .
				"&cid[]=" . $data['current']->id;

		$urlFollow = JURI::root() . "administrator/index.php?option=com_teamtimebpm" .
				"&controller=space&view=space&task=unfollow" .
				"&id=" . $data['current']->id;

		$tpl->setVariable("base_url", JURI::root());
		$tpl->setVariable("url_edit", $urlEdit);
		$tpl->setVariable("url_follow", $urlFollow);

		//$tpl->setVariable("user_name", $data['user']->name);

		$tpl->setVariable("current_name", $data['current']->name);
		$tpl->setVariable("current_tags", $data['current']->tags);
		$tpl->setVariable("current_modified_by",
				JText::sprintf('MODIFIED_BY_ON', $data['current']->user_name,
						$helperBase->getFormatedDate($data['current']->modified)));

		$tpl->setVariable("current_state", JText::_("Space state " . $data['current']->archived));

		$tpl->setVariable("current_description", $data['current']->description);

		$tpl->setVariable("prev_name", $data['prev']->name);
		$tpl->setVariable("prev_tags", $data['prev']->tags);
		$tpl->setVariable("prev_modified_by",
				JText::sprintf('MODIFIED_BY_ON1', $data['prev']->user_name,
						$helperBase->getFormatedDate($data['prev']->modified)));

		$tpl->setVariable("prev_state", JText::_("Space state " . $data['prev']->archived));

		$tpl->setVariable("prev_description", $data['prev']->description);

		return $tpl->get();
	}

	public function onSpaceChanged($prevData, $row) {
		if (!$prevData) {
			return;
		}

		$config = new JConfig();

		//error_log("trigger: onSpaceChanged");
		//error_log(print_r($prevData, true));
		//error_log(print_r($row, true));

		$mSpace = new TeamtimebpmModelSpace();
		$prevData->user_name = $mSpace->getModifiedUserName($prevData->modified_by);
		$row->user_name = $mSpace->getModifiedUserName($row->modified_by);

		$subject = JText::_("Spaces were changed");
		$body = $this->generateSpaceChangedContent(array(
			"prev" => $prevData, "current" => $row));

		foreach ($mSpace->getFollowedUsers($row->id) as $user) {
			$tmpBody = str_replace("[user_name]", $user->name, $body);

			//error_log(print_r($user, true));
			/*
			  error_log(print_r(array(
			  $config->mailfrom, $config->fromname, $user->email,
			  $subject, $tmpBody), true));

			 */

			JUTility::sendMail($config->mailfrom, $config->fromname, $user->email, $subject, $tmpBody, true);
		}
	}

}