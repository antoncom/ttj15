<?php

class TeamTime_EventHandlers_Dotu {

	public function onInit() {
		$path = JPATH_ADMINISTRATOR . '/components/com_teamtimecareer';

		// include js libs
		JHTML::script('default.js', 'administrator/components/com_teamtimecareer/assets/js/');

		JHTML::addIncludePath($path . '/helpers');

		// set the table directory
		JTable::addIncludePath($path . '/tables');

		JHTML::stylesheet('default.css',
				JURI::root(true) . 'administrator/components/com_teamtime/assets/css/');

		// include models
		require_once($path . '/models/targetvector.php');
		require_once($path . '/models/statevector.php');
		require_once($path . '/models/errorvectors.php');

		// load language files for component
		$lang = & JFactory::getLanguage();
		$lang->load("com_teamtimecareer", JPATH_BASE);
	}

	public function onSaveTodo($todoItem, $post) {
		$model = new TeamtimecareerModelTargetvector();

		if (isset($post["target_id"])) {
			if ((int) $post["target_id"] > 0) {
				$model->setTargetForTodo($todoItem->id, $post["target_id"]);
			}
			else {
				$model->deleteTargetForTodo($todoItem->id);
			}
		}
	}

	public function onDeleteTodo($todo_id) {
		$model = new TeamtimecareerModelTargetvector();
		$model->deleteTargetForTodo($todo_id);
	}

	public function onSaveTask($taskItem, $post) {
		$model = new TeamtimecareerModelTargetvector();

		// set task target
		if (isset($post["target_id"])) {
			if ((int) $post["target_id"] > 0) {
				$model->setTargetForTask($taskItem->id, $post["target_id"]);
			}
			else {
				$model->deleteTargetForTask($taskItem->id);
			}
		}

		// set dotu price flag
		if (!isset($post["is_dotu_price"])) {
			$post["is_dotu_price"] = 0;
		}
		$model->setTaskPrice($taskItem->id, $post["is_dotu_price"]);
	}

	public function onDeleteTask($taskId) {
		$model = new TeamtimecareerModelTargetvector();

		$model->deleteTargetForTask($taskId);
	}

	public function onSaveUserParams($post) {
		$userId = $post['id'];

		$model = new TeamtimecareerModelTargetvector();
		$model->setTargetBalance($userId, $post["target_balance"]);
	}

}