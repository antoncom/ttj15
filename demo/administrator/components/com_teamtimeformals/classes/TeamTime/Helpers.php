<?php

class TeamTime_Helpers_Formals {

	const ORDER_INDEX = 4;

	function __construct() {
		//...
	}

	public function getTodoNotifyClient($todoItem, $display = "") {
		$assetsPath = "administrator/components/com_teamtimeformals/assets";
		$user = & JFactory::getUser();

		if (in_array($user->usertype, array("Super Administrator"))) {
			// add handlers for page
			JHTML::script('todo-edit.js', $assetsPath . '/js/teamtime/');

			if ($display == "calendar") {
				?>
				<span class="key" id="clientmail-label">
					<input type="checkbox"
								 id="clientmail" name="clientmail" value="1">
					<?php echo JText::_('Note client by email'); ?></span>
				<?
			}
			else {
				?>
				<tr>
					<td width="110" class="key">
						<label for="clientmail" id="clientmail-label">
							<?php echo JText::_('Note client by email'); ?>:
						</label>
					</td>
					<td>
						<input type="checkbox" id="clientmail"
									 name="clientmail" value="1">
					</td>
				</tr>
				<?
			}
		}
	}

	public function getClientId($client, $ip) {
		$db = & JFactory::getDBO();

		// check url
		$url = $db->Quote($client);
		$url2 = $db->Quote($client . "/");
		$query = "select * from #__teamtimeformals_variable as a
			left join #__teamtimeformals_formaldata as b on a.id = b.variable_id
			where a.tagname = 'url_client_cabinet' and (b.content = {$url} or b.content = {$url2})";
		$db->setQuery($query);
		$res = $db->loadObjectList();
		if (!$res || sizeof($res) == 0) {
			return array();
		}

		$ids = array();
		foreach ($res as $row) {
			$ids[] = $row->project_id;
		}
		$ids = implode(",", $ids);

		// check ip address
		$ip_addr = $db->Quote($ip);
		$query = "select * from #__teamtimeformals_variable as a
			left join #__teamtimeformals_formaldata as b on a.id = b.variable_id
			where a.tagname = 'client_cabinet_ip' and b.content = {$ip_addr}
				and b.project_id in ({$ids})";
		$db->setQuery($query);
		$res = $db->loadObjectList();
		if (!$res || sizeof($res) == 0) {
			return array();
		}

		$ids = array();
		foreach ($res as $row) {
			$ids[] = $row->project_id;
		}

		return $ids;
	}

	public function getAddonButton() {
		$lang = & JFactory::getLanguage();
		$lang->load("com_teamtimeformals", JPATH_BASE);

		return TeamTime::helper()->getBase()->quickiconButton(
						'index.php?option=com_teamtimeformals',
						'components/com_teamtimeformals/assets/images/icon-48-formals.png',
						JText::_('TeamTime Formals'));
	}

	public function addonMenuItem($controller) {
		JSubMenuHelper::addEntry(JText::_("Formals"),
				"index.php?option=com_teamtimeformals", $controller == "formal");
	}

	public function getProjectParamsBlock($item, $using = "project") {
		$db = & JFactory::getDBO();

		$content = "";

		if ($using == "project") {
			$sql = "SELECT * FROM #__teamtimeformals_variable as b
			left join `#__teamtimeformals_variable_project` as a on b.id = a.variable_id
			WHERE a.project_id = " . (int) $item->id . " or a.project_id = 0";
		}
		else if ($using == "user") {
			$sql = "SELECT * FROM #__teamtimeformals_variable as b
			left join `#__teamtimeformals_variable_user` as a on b.id = a.variable_id
			WHERE a.user_id = " . (int) $item->id . " or a.user_id = 0";
		}
		else {
			return "";
		}

		$db->setQuery($sql);
		$res = $db->loadObjectList();

		$content .= '<table width="100%" class="paramlist admintable" cellspacing="1">';
		foreach ($res as $row) {
			$param_value = "";

			if ($using == "project") {
				$sql = "SELECT * FROM  `#__teamtimeformals_formaldata` as a
				WHERE a.variable_id = " . (int) $row->variable_id . " and a.project_id = " . (int) $item->id .
						" order by mdate desc limit 1";
			}
			else if ($using == "user") {
				$sql = "SELECT * FROM  `#__teamtimeformals_formaldata` as a
				WHERE a.variable_id = " . (int) $row->variable_id . " and a.user_id = " . (int) $item->id .
						" order by mdate desc limit 1";
			}
			$db->setQuery($sql);
			$tmp_res = $db->loadObject();

			if ($tmp_res) {
				$param_value = $tmp_res->content;
			}
			else {
				$param_value = $row->defaultval;
			}

			if ($row->ysize > 1) {
				$param_value = htmlspecialchars(stripslashes($param_value));
				$input = '<textarea name="params[variables][' . $row->variable_id . ']"
					cols="' . $row->xsize . '" rows="' . $row->ysize . '" class="text_area" id="' . $row->tagname . '">' .
						$param_value . '</textarea>';
			}
			else {
				$param_value = htmlspecialchars(stripslashes(str_replace("\n", "",
										$param_value)));
				$input = '<input name="params[variables][' . $row->variable_id . ']"
				value="' . $param_value . '" size="' . $row->xsize . '" class="text_area" id="' . $row->tagname . '">';
			}

			$content .= '	<tr>
			<td width="40%" class="paramlist_key" style="text-align:left" valign="top"><span class="editlinktip">
				<label id="' . $row->tagname . '-lbl" for="' . $row->tagname . '" class="hasTip"
					title="' . strip_tags($row->description) . '">' . $row->name . '</label></span></td>
			<td class="paramlist_value">' . $input . '</td>
		</tr>';
		}

		if (sizeof($res) == 0) {
			$content .= '	<tr>
			<td width="40%" class="paramlist_key" style="text-align:left" valign="top"><span class="editlinktip">
				</span></td>
			<td class="paramlist_value">&nbsp;</td>
		</tr>';
		}

		$content .= '</table>' .
				"<p style='color:#646464; padding-left:5px;'>
			<span style='color:#3c8e00'><strong>" . JText::_("ADD_DELETE_INPUT_FIELD") . "</strong></span><br>" .
				JText::_("IN ORDER TO ADD_DELETE A FIELD") . ":<br>" .
				JText::_("TEAMTIME - FORMALS - TEMPLATE TAGS") . "<br>
		</p>";

		return array(
			"title" => JText::_('Formals Data'),
			"name" => 'formals_params',
			"content" => $content
		);
	}

}

function TeamTime_Formals_getTodoParams($item, $display = "") {
	include(dirname(dirname(dirname(__FILE__))) .
			'/helpers/partials/todoparams.php');
}

function TeamTime_num2curr($num) {
	require_once("Numbers/Words.php");

	$num_data = array();
	$lang = & JFactory::getLanguage();
	$lang_id = str_replace("-", "_", $lang->getTag());
	$s = Numbers_Words::toCurrency((int) ($num), $lang_id);
	if (is_string($s)) {
		$encoding = "UTF-8";
		$s = mb_strtoupper(mb_substr($s, 0, 1, $encoding), $encoding) .
				mb_substr($s, 1, mb_strlen($s, $encoding), $encoding);

		$num_data[0] = $s;
		$tmp = explode(" ", $s);
		$srest = array_pop($tmp);
		$num_data[1] = implode(" ", $tmp);

		$m = round($num - (int) ($num), 2);
		if ($m > 0) {
			$k = array_pop(explode(" ", Numbers_Words::toCurrency($m, $lang_id)));
			$num_data[0] .= " " . ($m * 100) . " " . $k;
			$srest .= " " . ($m * 100) . " " . $k;
		}
		$num_data[2] = $srest;
	}
	return $num_data;
}

function TeamTime_get_expenses_options_teamtimeformals($arr) {
	$arr[] = JHTML::_('select.option', 'in_act', JText::_('Included to be paid'));
	$arr[] = JHTML::_('select.option', 'not_in_act',
					JText::_('Not included to be paid'));
	return $arr;
}

function TeamTime_get_sql_todo_expenses_teamtimeformals($s) {
	return " left join #__teamtimeformals_todo as todo_formals on a.id = todo_formals.todo_id ";
}

function TeamTime_Formals_onCopyTodo($id, $new_id) {
	$db = & JFactory::getDBO();

	$db->Execute("insert into `#__teamtimeformals_todo`
		SELECT {$new_id} as todo_id, mark_expenses, mark_hours_plan
		FROM `#__teamtimeformals_todo`
			WHERE `todo_id` = " . $id);
}

function TeamTime_has_variables($id, $using) {
	$db = & JFactory::getDBO();

	$res = null;

	$id = (int) $id;

	$sql_projects = "select * from #__teamtimeformals_variable_project as a
		where a.project_id = {$id} or a.project_id = 0";

	$sql_users = "select * from #__teamtimeformals_variable_user as a
		where a.user_id = {$id} or a.user_id = 0";

	if ($using == "") {
		$db->setQuery($sql_projects);
		$res = $db->loadObject();
		if (!$res) {
			$db->setQuery($sql_users);
			$res = $db->loadObject();
		}
	}
	else if ($using == "0") {
		$db->setQuery($sql_projects);
		$res = $db->loadObject();
	}
	else if ($using == "1") {
		$db->setQuery($sql_users);
		$res = $db->loadObject();
	}

	return $res;
}

function TeamTime_Formals_getDoctype($template_id) {
	$db = & JFactory::getDBO();

	$db->setQuery("select a.* from #__teamtimeformals_template as b
		left join #__teamtimeformals_type as a on b.type = a.id
		where b.id = {$template_id}");

	$res = $db->loadObject();

	return $res;
}

function TeamTime_formalsdata_get_variables_url($s) {
	return "index.php?option=com_teamtimeformals&controller=variable";
}

function TeamTime_formalsdata_generator_group_by_project_task_type($type_data) {
	$rows = array();

	foreach ($type_data as $todo_data) {
		$k = implode("-",
				array(
			$todo_data->title, $todo_data->type_id, $todo_data->task_id, $todo_data->project_id));
		if (!isset($rows[$k])) {
			$rows[$k] = $todo_data;
		}
		else {
			$rows[$k]->hours_plan += $todo_data->hours_plan;
			//$rows[$k]->project_hourly_rate += $todo_data->project_hourly_rate;
			//$rows[$k]->hourly_rate += $todo_data->hourly_rate;
			$rows[$k]->costs += $todo_data->costs;

			//TOOO other fields
		}
	}

	return array_values($rows);
}

function TeamTime_get_user_variables_teamtimeformals($returned, $user_id,
		$names_filter = array()) {
	$variablem = new VariableModelVariable();

	return $variablem->getVariablesByUser($user_id, $names_filter);
}

function TeamTime_get_user_taxdata_teamtimeformals($user_variables, $money,
		$exclude = array()) {
	$variablem = new VariableModelVariable();

	return $variablem->getUserTaxData($user_variables, $money, $exclude);
}
