<?php

class TeamTime_EventHandlers_Formals {

	public function onInit() {
		$path = JPATH_ADMINISTRATOR . '/components/com_teamtimeformals';

		// include js libs
		JHTML::script('default.js',
				'administrator/components/com_teamtimeformals/assets/js/');

		// set the table directory
		JTable::addIncludePath($path . '/tables');

		// include models
		require_once($path . '/models/variable.php');
		require_once($path . '/models/template.php');
		require_once($path . '/models/formal.php');

		// load language files for component
		$lang = & JFactory::getLanguage();
		$lang->load("com_teamtimeformals", JPATH_BASE);
	}

	public function onSaveTodo($todoItem, $post) {
		$db = & JFactory::getDBO();

		// save todo formals flags

		$mark_expenses = 0;
		if (isset($post["mark_expenses"])) {
			$mark_expenses = (int) $post["mark_expenses"];
		}
		$mark_hours_plan = 0;
		if (isset($post["mark_hours_plan"])) {
			$mark_hours_plan = (int) $post["mark_hours_plan"];
		}
		$query = "insert into `#__teamtimeformals_todo`
		(todo_id, mark_expenses, mark_hours_plan)
		values(" . $todoItem->id . ", " . $mark_expenses . ", " . $mark_hours_plan . ")
		ON DUPLICATE KEY UPDATE
			mark_expenses = " . $mark_expenses . ",
			mark_hours_plan = " . $mark_hours_plan;
		$db->Execute($query);

		// notify client

		if (isset($post["clientmail"]) && $post["clientmail"] == "1") {
			$config = new JConfig();

			$modelVariable = new VariableModelVariable();
			list($variables, $variablesData) = $modelVariable->getVariablesByProject(
					$todoItem->project_id, array("client_email", "requirements_template_name"));

			//error_log(print_r($variablesData, true));

			$modelTemplate = new TemplateModelTemplate();
			$data = $modelTemplate->getDataByName($variablesData["requirements_template_name"]);

			//error_log(print_r($data, true));

			$date = & JFactory::getDate();
			$params["created"] = $date->toMySQL();
			$params["doctype_id"] = $data->id;
			$params["project_id"] = $todoItem->project_id;
			$formalData = $params;

			$params["from_period"] = "";
			$params["until_period"] = "";
			$params["filter"] = array("todo_id" => $todoItem->id);

			$modelFormal = new FormalModelFormal();
			$doctype = TeamTime::_("Formals_getDoctype", $params["doctype_id"]);
			$formalData["name"] = JText::_($doctype->name) . " - " . $todoItem->title;
			$res = $modelFormal->generateContent($params);
			$formalData["content"] = $res[0];
			$formalData["price"] = $res[1];

			// save as formal
			//$modelFormal->store($formalData);

			$formalData["content"] = TeamTime::helper()->getBase()
					->processRelativeLinks($formalData["content"], JURI::root());
			//error_log($formalData["content"]);

			$recipient = array();
			foreach (explode(",", $variablesData["client_email"]) as $email) {
				$recipient[] = trim($email);
			}

			JUTility::sendMail($config->mailfrom, $config->fromname, $recipient,
					$formalData["name"], $formalData["content"], true);
		}
	}

	public function onSaveProjectParams($model, $post) {
		if (!isset($post["params"]["variables"])) {
			return;
		}

		$project_id = $model->_data->id;

		$variablem = new VariableModelVariable();
		$variablem->setVariablesForProject($project_id, $post["params"]["variables"]);
	}

	public function onSaveUserParams($post) {
		if (!isset($post["params"]["variables"])) {
			return;
		}

		$user_id = $post["id"];

		$variablem = new VariableModelVariable();
		$variablem->setVariablesForUser($user_id, $post["params"]["variables"]);
	}

}