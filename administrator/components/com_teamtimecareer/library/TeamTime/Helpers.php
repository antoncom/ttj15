<?php

class TeamTime_Helpers_Dotu {

	const ORDER_INDEX = 3;

	public function __construct() {
		//...
	}

	private function fetchChecklistOptions($description) {
		$result = array();

		preg_match_all('{<li.+?</li>}si', $description, $matches);
		foreach ($matches[0] as $m) {
			// filter options with status = done
			if (strpos($m, 'class="checklist-checkbox checked done"') === false) {
				continue;
			}

			$hoursplan = 0;
			$targetId = 0;
			preg_match('{data-hoursplan="(.+?)"}si', $m, $data);
			if (isset($data[1])) {
				$hoursplan = $data[1];
				$tmp = explode(":", $hoursplan);
				$hoursplan = (int) $tmp[0] * 60 + (int) $tmp[1];
			}
			preg_match('{data-targetvector="(.+?)"}si', $m, $data);
			if (isset($data[1])) {
				$targetId = $data[1];
			}

			$result[] = array($hoursplan, $targetId, strip_tags(trim($m)));
		}

		return $result;
	}

	public function onSaveLog($log, $post) {
		$mTodo = new TeamtimeModelTodo();

		// add selected checklist options
		if ($log->todo_id) {
			if (isset($post['logs_checklist'])) {
				$post['logs_checklist'] = $_REQUEST['logs_checklist'];
			}
			// protect for clear todo description
			//error_log("description = [" . trim(strip_tags($post['logs_checklist'])) . "]");
			if (trim(strip_tags($post['logs_checklist'])) != "") {
				// set status = done for checked options
				$post['logs_checklist'] = str_replace(
						'class="checklist-checkbox checked"', 'class="checklist-checkbox checked done"',
						$post['logs_checklist']);
				$todo = $mTodo->getById($log->todo_id);
				$todo->description = $post['logs_checklist'];
				$mTodo->store($todo);
			}
		}

		// save state only if set - close todo
		if (!isset($post['close_todo']) || (int) $post['close_todo'] != 1) {
			return;
		}

		$model = new TeamtimecareerModelTargetvector();
		$state = new TeamtimecareerModelStatevector();

		$todo = $mTodo->getById($log->todo_id);
		$options = $this->fetchChecklistOptions($todo->description);

		//error_log($todo->description);
		//error_log(print_r($options, true));
		// default target
		$targetId = $model->getTargetIdByTodoId($log->todo_id);
		if (!$targetId) {
			$targetId = $model->getTargetIdByTaskId($log->task_id);
		}

		// has checklist options
		if ($log->todo_id && strpos($todo->description, 'class="checklist-checkbox') !== false) {
			//error_log("save as checklist");
			if (sizeof($options) > 0) {
				// save state by options
				foreach ($options as $option) {
					$data = array(
						"num" => $option[0] ? $option[0] : 1,
						"target_id" => $option[1] ? $option[1] : $targetId,
						"description" => $todo->title . " / " . $option[2],
						"user_id" => $log->user_id,
						"log_id" => $log->id,
						"todo_id" => $log->todo_id,
						"date" => $log->date
					);
					//error_log(print_r($data, true));
					$state->store($data);
				}
				// set status = done recorded for checked options
				$todo->description = str_replace(
						'class="checklist-checkbox checked done"', 'class="checklist-checkbox checked done recorded"',
						$todo->description);
				$mTodo->store($todo);
			}
		}
		else {
			//error_log("save as regular todo");
			// save state by default target
			if ($targetId) {
				$data = array(
					"target_id" => $targetId,
					"description" => $log->description,
					"num" => $log->duration,
					"user_id" => $log->user_id,
					"log_id" => $log->id,
					"todo_id" => $log->todo_id,
					"date" => $log->date
				);
				if ($log->todo_id) {
					$data["description"] = $todo->title;
					$data["num"] = $todo->hours_plan * 60;

					// check state exists for todo log
					$row = $state->getStateByKey($targetId, $log->user_id, $log->todo_id);
					if ($row) {
						return;
					}
				}
				$state->store($data);
			}
		}
	}

	private function getChildVisibilityFlag($user_data, $index) {
		$parent = $user_data["list"][$index];
		$result = false;

		foreach ($user_data["list"] as $i => $target) {
			if ($target->parent_id != $parent->id) {
				continue;
			}

			if (isset($user_data["balance"][$target->id])) {
				$result = true;
			}
			else {
				$result = $this->getChildVisibilityFlag($user_data, $i);
			}

			if ($result) {
				break;
			}
		}

		return $result;
	}

	private function initVisibilityFlag(&$user_data) {
		foreach ($user_data["list"] as $i => $target) {
			if (isset($user_data["balance"][$target->id])) {
				$user_data["list"][$i]->showtarget = true;
			}
			else {
				$user_data["list"][$i]->showtarget = $this->getChildVisibilityFlag($user_data, $i);
			}
		}
	}

	public function renderErrorVectorContent($user_data, $template, $title = true, $showtargets = 0) {
		$option = "com_teamtimecareer";

		$targetm = new TeamtimecareerModelTargetvector();

		if ($showtargets) {
			$this->initVisibilityFlag($user_data);
		}

		$tpl = new HTML_Template_IT("");
		$tpl->setTemplate($template, true, true);

		$tpl->setCurrentBlock();

		if ($title) {
			$tpl->setVariable("user_name", $user_data["user"]->name);
		}
		$tpl->setVariable("txt_my_goals", JText::_("My goals"));
		$tpl->setVariable("txt_significance_of_goal", JText::_("Significance of goal"));
		$tpl->setVariable("txt_my_skill_level", JText::_("My skill level"));
		$tpl->setVariable("txt_highness_of_goal", JText::_("Highness of goal"));
		$tpl->setVariable("txt_my_progress", JText::_("My progress"));
		$tpl->setVariable("txt_hourly_rate_in_statement", JText::_("Hourly rate - in statement"));

		$tpl->setVariable("txt_hourly_rate_accrued", JText::_("Hourly rate accrued"));
		$tpl->setVariable("txt_hourly_rate_cash", JText::_("Hourly rate - cash"));
		$tpl->setVariable("txt_total", JText::_("Total"));
		$tpl->setVariable("user_id", $user_data["user"]->id);

		if (isset($user_data["varsdata"][1]["user_tax_1"])) {
			$tpl->setVariable("txt_tax", JText::_("Tax"));
		}
		if (isset($user_data["varsdata"][1]["user_tax_2"])) {
			$tpl->setVariable("txt_social_exp", JText::_("Social exp."));
		}

		if ($user_data["varsdata"] != null &&
				!($user_data["varsdata"] instanceof TeamTime_Undefined)) {
			foreach ($user_data["varsdata"][1] as $tagname => $name) {
				$tpl->setVariable($tagname, $name);
			}
		}

		$total_target_balance = 0;
		$total_target_value = 0;
		$total_state_value = 0;
		$total_achievement = 0;
		$total_target_hourprice = 0;
		$total_money = 0;
		$targets_num = 0;

		$user_data_list = array_values($user_data["list"]);
		foreach ($user_data_list as $row_index => $target) {
			if ($showtargets && !$target->showtarget) {
				continue;
			}

			$tpl->setVariable(
					"node_id_attr", "id='node-" . $user_data["user"]->id . "_" . $target->id . "'");
			if ($target->parent_id) {
				$tpl->setVariable(
						"children_of_node_class",
						"class='child-of-node-"
						. $user_data["user"]->id . "_" . $target->parent_id . "'");
			}
			else {
				$tpl->setVariable("children_of_node_class", "");
			}

			if ($target->is_skill) {
				if (isset($user_data_list[$row_index + 1]) && $user_data_list[$row_index + 1]->is_skill) {
					$skill_class = " skill";
				}
				else {
					$skill_class = " lskill";
				}
			}
			else {
				$skill_class = "";
			}
			foreach (range(1, 6) as $i) {
				$tmpClass = "skill_class{$i}";

				if ($tmpClass == "skill_class5") {
					if (!isset($user_data["varsdata"][1]["user_tax_1"])) {
						continue;
					}
				}
				else if ($tmpClass == "skill_class3") {
					if (!isset($user_data["varsdata"][1]["user_tax_2"])) {
						continue;
					}
				}

				$tpl->setVariable($tmpClass, $skill_class != "" ? ($skill_class . $i) : "");
			}

			$target_num = $target->children > 0 ? $target->num_tree : $target->num;

			if ($target->parent == 0 && $target->children > 0) {
				$tpl->setVariable("rowtype_class", 'class="parent"');
				$inner = "";
				$tpl->setVariable("target_value", $target_num);
			}
			else {
				$tpl->setVariable("rowtype_class", '');
				//$inner = str_replace(array(".", "|", "_"), array("", "", ""), $target->treename) . "- ";
				$inner = $target->children > 0 ? "" : "- ";
				$tpl->setVariable("target_value", $target_num);
			}

			if ($inner != "" || $target->parent == 0) {
				$tpl->setVariable("row_padding_attr", 'style="padding-left:19px;"');
			}
			else {
				$tpl->setVariable("row_padding_attr", '');
			}

			$tmp_descr = trim(strip_tags($target->description));
			if (strlen($tmp_descr) > 10) {
				$tpl->setVariable("target_name",
						$inner
						. '<a href="javascript:void(0);" onclick="return hs.htmlExpand(this, { headingText: \''
						. htmlspecialchars($target->title, ENT_QUOTES) . '\' });">'
						. $target->title . '</a>&nbsp;'
						. '<img src="' . JURI::root() . 'components/' . $option . '/assets/images/arr_desk_ico.png">');
				$tpl->setVariable("target_description",
						'<div class="highslide-maincontent">'
						. $target->description
						. '</div>');
			}
			else {
				$tpl->setVariable("target_name", $inner . $target->title);
				$tpl->setVariable("target_description", "");
			}

			if (isset($user_data["balance"][$target->id])) {
				$targets_num++;

				$tpl->setVariable("target_balance", $user_data["balance"][$target->id] . "%");

				if ($target->parent == 0) {
					$total_target_balance += $user_data["balance"][$target->id];
					$total_state_value += $target->state_value;
				}

				//$money = $target->hourprice * ($target->state_value / 60);
				$money = $targetm->getDotuPriceForTask(null, $user_data["user"]->id, $target->id);
				$tpl->setVariable("target_hourprice", round($money, 2));

				$total_target_hourprice += $money;
			}
			else {
				$tpl->setVariable("target_balance", "");
				$tpl->setVariable("target_hourprice", "");
			}

			$achievement = ($target_num != 0 ?
							($target->state_value / $target_num) : 0) * 100;
			if ($achievement > 100) {
				$achievement = 100;
			}
			if ($target->parent == 0) {
				// $total_achievement += $achievement;
				$total_achievement = $achievement;
			}

			if (isset($user_data["balance"][$target->id])) {
				$tpl->setVariable("state_value", (float) $target->state_value);

				if ($showtargets || (!$showtargets && $target->children == 0)) {
					$tpl->setVariable("achievement", round($achievement, 2) . "%");
				}
				else {
					$tpl->setVariable("achievement", "");
				}
			}
			else {
				$tpl->setVariable("state_value", "");
				$tpl->setVariable("achievement", "");
			}

			$class_achievement = "class='green'";
			if ($achievement < 100) {
				$class_achievement = "class='red'";
			}
			$tpl->setVariable("class_achievement", $class_achievement);

			$tmp_sum = 0;
			if ($user_data["varsdata"] != null &&
					!($user_data["varsdata"] instanceof TeamTime_Undefined)) {

				list($result_money, $user_tax_data) = TeamTime::helper()->getFormals()
						->getUserTaxData($user_data["varsdata"], $money, array("user_tax_1"));
				foreach ($user_tax_data as $tagname => $value) {

					if (isset($user_data["balance"][$target->id])) {
						$tpl->setVariable($tagname . "_value", round($value, 2));
					}
					else {
						$tpl->setVariable($tagname . "_value", "");
					}

					if ($tagname != "user_tax_1") {
						$tmp_sum += $value;
					}
				}
			}

			if ($target->parent == 0) {
				$total_target_value += $target_num;
			}

			if (isset($user_data["balance"][$target->id])) {
				$tpl->setVariable("money", round($money - $tmp_sum, 2));
				$tpl->setVariable("result_money", round($result_money, 2));
			}
			else {
				$tpl->setVariable("money", "");
				$tpl->setVariable("result_money", "");
			}

			$tpl->parse("row");
		}

		$tpl->setCurrentBlock();
		$tpl->setVariable("total_target_balance", $total_target_balance . "%");
		$tpl->setVariable("total_target_value", $total_target_value);
		$tpl->setVariable("total_state_value", $total_state_value);

		$tpl->setVariable("total_achievement",
				round(
						$targets_num != 0 ? ($total_achievement / $targets_num) : 0, 2) . "%");

		$tpl->setVariable("total_target_hourprice",
				round(
						$targets_num != 0 ? ($total_target_hourprice / $targets_num) : 0, 2));
		$tpl->setVariable("total_money", "");
		$tpl->setVariable("total_result_money", "");

		if ($user_data["varsdata"] != null &&
				!($user_data["varsdata"] instanceof TeamTime_Undefined)) {
			foreach ($user_data["varsdata"][1] as $tagname => $name) {
				$tpl->setVariable("total_" . $tagname, "");
			}
		}

		$result = $tpl->get();
		$result .= "<p>";

		return $result;
	}

	/*
	  public function getMaxPrice($user_id) {
	  $model = new TargetvectorModelTargetvector();

	  return $model->getMaxDotuPrice($user_id);
	  } */

	public function getAddonButton() {
		$lang = & JFactory::getLanguage();
		$lang->load("com_teamtimecareer", JPATH_BASE);

		return TeamTime::helper()->getBase()->quickiconButton(
						'index.php?option=com_teamtimecareer',
						'components/com_teamtimecareer/assets/images/icon-48-dotu.png', JText::_('TeamTime DOTU'));
	}

	public function addonMenuItem($controller) {
		JSubMenuHelper::addEntry(
				JText::_("DOTU"), "index.php?option=com_teamtimecareer", $controller == "dotu");
	}

	public function getUserParamsBlock($userItem) {
		$option = "com_teamtimecareer";

		jimport('joomla.html.pane');

		$result = "";
		$pane = & JPane::getInstance('sliders', array('allowAllClose' => true));

		$result .= '<div style="padding-top:7px;">';
		$result .= $pane->startPane('pane');

		$result .= $pane->startPanel(JText::_("Vector of goals balance"), "user_params_teamtimecareer");

		// get target balance table
		$model = new TeamtimecareerModelTargetvector();
		$params = array();
		$tree = $model->getTree($params, 0);
		$items = $model->flattenTree($tree);
		$target_balance = $model->getTargetBalance($userItem->id);
		$target_data = $model->getDataForTreelist($items);

		//error_log(print_r($target_data, true));

		$skillValue = 0.5;

		$result .= '<table id="userParamsTeamtimeCareer"
    width="100%" class="paramlist admintable" cellspacing="1">';
		foreach ($items as $row) {

			$row->num_tree = $target_data[$row->id]->num;
			$row = $model->calcFieldsForParent($row->id, $row);

			//$s = str_repeat("&nbsp;&nbsp;", $row->level);
			$s = "";
			$bold_none = "";
			$class = "";

			if (isset($row->parent_id)) {
				$class = "class='child-of-node-" . $row->parent_id . ($row->level > 0 ? " clpsd" : "") . "'";
				$bold_none = "font-weight:normal;";

				if ($row->isSkill) {
					$s = "-&nbsp;";
				}
			}

			$balance = isset($target_balance[$row->id]) ? $target_balance[$row->id] : "";

			//if (!$row->isSkill) {

			$input = '<input type="text" name="target_balance[' . $row->id . ']"
        value="' . $balance . '" size="6"
        class="text_area target_balance" id="target' . $row->id . '"><span
        class="target_balance_text"></span>&nbsp;%';

			/* }
			  else {
			  $input = '<input type="hidden" name="target_balance[' . $row->id . ']" value="0">
			  <input type="checkbox" name="target_balance[' . $row->id . ']"
			  value="' . $skillValue . '" ' . ($balance > 0 ? "checked" : "") . '
			  class="target_balance" id="target' . $row->id . '_cb">';
			  } */

			$inputCheckbox = '<input type="checkbox" class="cb_target_value" '
					. ($balance > 0 ? "checked" : "") . '
      id="tmp_target_value' . $row->id . '"
      value="' . $row->num_tree . '">';

			if (!isset($row->description)) {
				$row->description = "";
			}
			$result .= '<tr id="node-' . $row->id . '" ' . $class . '>
      <td valign="top" width="1%">' . $inputCheckbox . '</td>
			<td class="paramlist_key"
        style="text-align:left; padding-left:10px;' . $bold_none . '" valign="top"><span
          class="editlinktip"><label
        id="target' . $row->id . '-lbl" for="target' . $row->id . ($row->isSkill
								? "_cb" : "")
					. '" class="hasTip" title="' . strip_tags($row->description) . '">&nbsp;'
					. $s . $row->title . '</label></span></td>
			<td width="10%" class="paramlist_value" nowrap>' . $input . '</td>
		</tr>';
		}

		$result .= '<tr>
    <td colspan="3" class="paramlist_key" style="font-style:italic; font-size:110%;"
      align="right" valign="top">' . JText::_("Total") . ':&nbsp;<span id="total_target_balance">100&nbsp;%</td>
		</tr>';
		$result .= '</table>';

		$result .= $pane->endPanel();

		$result .= $pane->endPane();
		$result .= '</div>';

		ob_start();
		include(dirname(dirname(dirname(__FILE__))) .
				'/helpers/partials/get_user_params_teamtimecareer.php');
		$result .= ob_get_contents();
		ob_end_clean();

		return $result;
	}

	public function getHtmlShowSkills($item) {
		$path = dirname(dirname(dirname(__FILE__))) . '/helpers/partials/html_showskills.php';
		include($path);
	}

	public function onCheckUserHourPrice() {
		$path = dirname(dirname(dirname(__FILE__))) . '/helpers/partials/oncheck_user_hourprice.php';
		include($path);
	}

	public function userHourPrice($taskItem) {
		$model = new TeamtimecareerModelTargetvector();

		$checked = $model->isTaskPrice($taskItem->id) ? "checked" : "";
		$html = "<label for='is_dotu_price'>
    <input type='checkbox' name='is_dotu_price' id='is_dotu_price' value='1' {$checked}>&nbsp;" .
				JText::_("Calculated according to TeamTime DOTU") .
				"</label>";

		return $html;
	}

	public function getPrice($params, $checkTargetId = false) {

		$result = null;
		$model = new TeamtimecareerModelTargetvector();

		$task_id = $params["task_id"];
		$user_id = $params["user_id"];

		if ($checkTargetId) {
			$target_id = $model->getTargetIdByTaskId($task_id);
		}
		else {
			$target_id = isset($params["target_id"]) ? $params["target_id"] : null;
		}

		if (!$model->isTaskPrice($task_id)) {
			return $result;
		}

		// calc dotu price
		$mtask = new TeamtimeModelTask();
		$mtask->setId($task_id);
		$task = $mtask->getData();
		$result = $model->getDotuPriceForTask($task, $user_id, $target_id);


		return $result;
	}

	public function getTargetPrice($params, $checkTargetId = false) {
		$result = null;
		$model = new TeamtimecareerModelTargetvector();

		$task_id = $params["task_id"];

		if ($checkTargetId) {
			$target_id = $model->getTargetIdByTaskId($task_id);
		}
		else {
			$target_id = isset($params["target_id"]) ? $params["target_id"] : null;
		}

		if (!$model->isTaskPrice($task_id)) {
			return $result;
		}

		error_log("---" . $target_id);

		$model->setId($target_id);
		$target_data = $model->getData();
		$result = $target_data->hourprice;

		error_log(print_r($target_data, true));

		return $result;
		//return 555;
	}

	public function getTasksSqlData($filter = array()) {
		$fields = ',
			tv.title as target_title,
			tv.hourprice as target_hourprice,
			tp.price as is_dotu_price';

		$join = '
			left join #__teamtimecareer_task_target as tt on a.id = tt.id
			left join #__teamtimecareer_targetvector as tv on tt.target_id = tv.id
			left join #__teamtimecareer_task_price as tp on a.id = tp.id
		';

		$where = array();

		return array(
			"fields" => $fields,
			"join" => $join,
			"where" => $where,
		);
	}

	public function targetVectorSelectorByTodo($todoItem, $s_default = "") {
		// get target_id for $todoItem
		$model = new TeamtimecareerModelTargetvector();
		$target_id = $model->getTargetIdByTodoId($todoItem->id);

		if ($s_default == "") {
			$s_default = 'All goals';
		}
		$options = JHTML::_(
						'select.option', '0', '- ' . JText::_($s_default) . ' -', 'value', 'text', false);

		return JHTML::_(
						'teamtimecareer.goalslist', 0, $options, 'target_id', 'class="inputbox"', 'value', 'text',
						$target_id ? $target_id : "-", false, false, $todoItem->user_id, $todoItem->showskills);
	}

	public function targetVectorSelector(
	$taskItem, $size = true, $s_default = "", $user_id = null, $show_skills = false) {

		// get target_id for $taskItem
		$model = new TeamtimecareerModelTargetvector();

		if (is_object($taskItem)) { // use as row object
			$task_id = $taskItem->id;
		}
		else { // use as id
			$task_id = $taskItem;
		}
		$target_id = $model->getTargetIdByTaskId($task_id);

		if ($size) {
			$s_size = " size=16";
		}
		else {
			$s_size = "";
		}

		if ($s_default == "") {
			$s_default = 'All goals';
		}
		$options = JHTML::_(
						'select.option', '0', '- ' . JText::_($s_default) . ' -', 'value', 'text', false);

		return JHTML::_(
						'teamtimecareer.goalslist', 0, $options, 'target_id', 'class="inputbox"' . $s_size, 'value',
						'text', $target_id ? $target_id : "-", false, false, $user_id, $show_skills);
	}

}
