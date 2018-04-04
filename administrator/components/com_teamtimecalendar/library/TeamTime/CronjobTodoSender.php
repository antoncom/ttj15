<?php

class TeamTime_CronjobTodoSender extends TeamTime_Cronjob {

	private function formatTime($time) {
		return str_pad(floor($time), 2, "0", STR_PAD_LEFT) . ":" .
				str_pad(ceil(($time - floor($time)) * 60), 2, "0", STR_PAD_LEFT);
	}

	private function generateTodosMsg($user, $todos, $date) {
		$days = array('ПН', 'ВТ', 'СР', 'ЧТ', 'ПТ', 'СБ', 'ВС');
		$months = array(
			'', 'января', 'февраля', 'марта', 'апреля',
			'мая', 'июня', 'июля', 'августа',
			'сентября', 'октября', 'ноября', 'декабря'
		);

		//print "<br>{$user->id} - {$user->name}<br>";

		$fname = JPATH_COMPONENT . "/assets/templates/todos.html";

		$tpl = new HTML_Template_IT("");
		$tpl->loadTemplatefile($fname, true, true);

		$week_days = array();
		$next_date = strtotime($date);
		foreach ($days as $i => $weekday) {
			$week_days[$i] = array();
			$week_days[$i]["day"] = $weekday;
			$week_days[$i]["date"] = date("j", $next_date) .
					" " . $months[date("n", $next_date)];
			$week_days[$i]["todos"] = array();
			$next_date += 60 * 60 * 24;
		}
		foreach ($todos as $row_todo) {
			$row_todo->description = "";

			$created = strtotime($row_todo->created);
			$todo_week_day = date("w", $created);
			if ($todo_week_day == 0)
				$todo_week_day = 7;
			$week_days[$todo_week_day - 1]["todos"][] = $row_todo;
		}

		//print "<pre>";
		//print_r($week_days);
		//print "</pre>";

		foreach ($week_days as $i => $weekday) {
			$tpl->setCurrentBlock("weekday");
			$tpl->setVariable("weekday", $weekday['day']);
			$tpl->setVariable("date", $weekday['date']);
			$tpl->setVariable("red", ($i == 5 || $i == 6) ? "#D99494" : "#7d899b");
			$tpl->setVariable("red_border", ($i == 5 || $i == 6) ? "#D99494" : "#CCCFD2");

			foreach ($weekday['todos'] as $todos) {
				$tpl->setCurrentBlock("todo");
				$tpl->setVariable("hours_fact", $this->formatTime($todos->hours_fact));
				$tpl->setVariable("hours_plan", $this->formatTime($todos->hours_plan));
				$tpl->setVariable("title", $todos->title);
				$tpl->setVariable("marked", $todos->state == 2 ? "color: #939ba7;" : "");
				$tpl->parseCurrentBlock("todo");
			}

			$tpl->parse("weekday");
		}

		return $tpl->get();
	}

	public function run() {
		$config = & JFactory::getConfig();

		$date = JFactory::getDate();
		$date = $date->toUnix();
		$date = mktime(0, 0, 0, date('n', $date), date('j', $date), date('Y', $date));
		$monday = (date('w', $date) == 1) ?
				$date : strtotime('last Monday', $date);

		$from = date('Y-m-d', $monday);
		$until = date('Y-m-d', strtotime('+6 day', $monday));

		//error_log("This week: $from - $until\n");

		$db = & JFactory::getDBO();
		$db->setQuery("select * from #__users");
		$result = $db->loadObjectList();

		foreach ($result as $row) {
			// todos unclosed
			$db->setQuery("select * from #__teamtime_todo
				where user_id = {$row->id} and
					created < '{$from}' and state != 2
				order by created");
			$res_todos = $db->loadObjectList();
			foreach ($res_todos as $i => $todo) {
				$res_todos[$i]->created = $from;
			}

			// todos for this week
			$db->setQuery("select * from #__teamtime_todo
				where user_id = {$row->id} and
					created >= '{$from}' and created <= '{$until}'
				order by created");
			$res_todos = array_merge($res_todos, $db->loadObjectList());

			if (sizeof($res_todos) > 0) {
				$todosHtml = $this->generateTodosMsg($row, $res_todos, $from);
				$conf = new JConfig();

				//error_log($todosHtml);

				JUTility::sendMail($conf->mailfrom, "Задачник «Медиа Паблиш»", $row->email,
						"План работ на неделю", $todosHtml, true);
			}
		}
	}

}
