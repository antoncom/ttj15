<?php

defined('_JEXEC') or die('Restricted access');

class JHTMLTeamtimecareer {

	function goalsList($goal_id, $options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $idtag = false, $translate = false, $user_id = null, $showskills = false) {

		if (is_array($options)) {
			reset($options);
		}
		else {
			$options = array($options);
		}

		$model = new TeamtimecareerModelTargetvector();

		$params = array();
		if (!$showskills) {
			$params["is_skill"] = 0;
		}

		// hide current id in subtree
		$cmd = array(
				"hide_node" => array("node_id" => $goal_id)
		);

		$tree = $model->getTree($params, 0, $cmd);
		$items = $model->flattenTree($tree);

		// filter by user_id
		if ($user_id) {
			$target_balance = $model->getTargetBalance($user_id);
			foreach ($items as $i => $row) {
				if (!isset($target_balance[$row->id]) || $target_balance[$row->id] <= 0) {
					unset($items[$i]);
				}
			}
		}

		foreach ($items as $row) {
			$s = str_repeat("&nbsp;", $row->level) . ($row->level > 0 ? "-" : "");
			$options[] = JHTML::_(
							'select.option', $row->id, $s . $row->title, 'value', 'text', $row->id == $goal_id);
		}

		return JHTML::_(
						'select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

	function getParentTargets($options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $idtag = false, $translate = false) {
		if (is_array($options)) {
			reset($options);
		}
		else {
			$options = array($options);
		}

		$model = new TeamtimecareerModelTargetvector();
		foreach ($model->getParentTargets() as $row) {
			$options[] = JHTML::_('select.option', $row->id, $row->title, $key, $text);
		}

		return JHTML::_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag,
						$translate);
	}

	function dateSelector($from_period, $until_period) {
		$config = & JFactory::getConfig();

		// set date presets
		$date = JFactory::getDate();
		$date = $date->toUnix();
		$date = mktime(0, 0, 0, date('n', $date), date('j', $date), date('Y', $date));
		$monday = (date('w', $date) == 1) ? $date : strtotime('last Monday', $date);

		$date_presets['last_month'] = array(
				'name' => 'Last Month',
				'from' => date('Y-m-d', mktime(0, 0, 0, date('n', $date) - 1, 1, date('Y', $date))),
				'until' => date('Y-m-d', mktime(0, 0, 0, date('n', $date), 0, date('Y', $date))));

		$date_presets['last30'] = array(
				'name' => 'Last 30 days',
				'from' => date('Y-m-d', strtotime('-29 day', $date)),
				'until' => date('Y-m-d', $date));

		$date_presets['last_week'] = array(
				'name' => 'Last Week',
				'from' => date('Y-m-d', strtotime('-7 day', $monday)),
				'until' => date('Y-m-d', strtotime('-1 day', $monday)));

		$date_presets['last30'] = array(
				'name' => 'Last 30 days',
				'from' => date('Y-m-d', strtotime('-29 day', $date)),
				'until' => date('Y-m-d', $date));
		$date_presets['today'] = array(
				'name' => 'Today',
				'from' => date('Y-m-d', $date),
				'until' => date('Y-m-d', $date));
		$date_presets['week'] = array(
				'name' => 'This Week',
				'from' => date('Y-m-d', $monday),
				'until' => date('Y-m-d', strtotime('+6 day', $monday)));
		$date_presets['month'] = array(
				'name' => 'This Month',
				'from' => date('Y-m-d', mktime(0, 0, 0, date('n', $date), 1, date('Y', $date))),
				'until' => date('Y-m-d', mktime(0, 0, 0, date('n', $date) + 1, 0, date('Y', $date))));
		$date_presets['year'] = array(
				'name' => 'This Year',
				'from' => date('Y-m-d', mktime(0, 0, 0, 1, 1, date('Y', $date))),
				'until' => date('Y-m-d', mktime(0, 0, 0, 12, 31, date('Y', $date))));

		/* $date_presets['next_week'] = array(
		  'name' => 'Next Week',
		  'from' => date('Y-m-d', strtotime('+7 day', $monday)),
		  'until' => date('Y-m-d', strtotime('+13 day', $monday)));
		  $date_presets['next_month'] = array(
		  'name' => 'Next Month',
		  'from' => date('Y-m-d', mktime(0, 0, 0, date('n', $date) + 1, 1, date('Y', $date))),
		  'until' => date('Y-m-d', mktime(0, 0, 0, date('n', $date) + 2, 0, date('Y', $date)))); */

		// set period
		$tzoffset = $config->getValue('config.offset');
		$from = JFactory::getDate($from_period, $tzoffset);
		$until = JFactory::getDate($until_period, $tzoffset);

		// check period - set to defaults if no value is set or dates cannot be parsed
		if ($from->_date === false || $until->_date === false) {
			if ($from_period != '?' && $until_period != '?') {
				JError::raiseNotice(500, JText::_('Please enter a valid date format (YYYY-MM-DD)'));
			}
			$from_period = $date_presets['last30']['from'];
			$until_period = $date_presets['last30']['until'];
			$from = JFactory::getDate($from_period, $tzoffset);
			$until = JFactory::getDate($until_period, $tzoffset);
		}
		else {
			if ($from->toUnix() > $until->toUnix()) {
				list($from_period, $until_period) = array($until_period, $from_period);
				list($from, $until) = array($until, $from);
			}
		}

		// simpledate select
		$select = '';
		$options = array(
				JHTML::_('select.option', '', '- ' . JText::_('Select Period') . ' -', 'text', 'value')
		);
		foreach ($date_presets as $name => $value) {
			$options[] = JHTML::_('select.option', $name, JText::_($value['name']), 'text', 'value');
			if ($value['from'] == $from_period && $value['until'] == $until_period) {
				$select = $name;
			}
		}
		$select_date = JHTML::_(
						'select.genericlist', $options, 'period', 'class="inputbox" size="1"', 'text', 'value',
						$select);

		return array(
				"from_period" => $from_period,
				"until_period" => $until_period,
				"date_presets" => $date_presets,
				"selected" => $select,
				"select_date" => $select_date
		);
	}
	
}
