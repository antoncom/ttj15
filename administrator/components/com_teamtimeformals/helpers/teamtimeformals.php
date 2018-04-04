<?php

defined('_JEXEC') or die('Restricted access');

class JHTMLTeamtimeformals {

	function typesList($options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $idtag = false, $translate = false) {
		$query = 'SELECT a.id AS value, a.name AS text'
				. ' FROM #__teamtimeformals_type AS a'
				. ' ORDER BY a.name';
		return self::queryList($query, $options, $name, $attribs, $key, $text, $selected, $idtag,
						$translate);
	}

	function templatesList($options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $idtag = false, $translate = false) {
		$query = 'SELECT a.id AS value, a.name AS text'
				. ' FROM #__teamtimeformals_template AS a'
				. ' ORDER BY a.name';
		return self::queryList($query, $options, $name, $attribs, $key, $text, $selected, $idtag,
						$translate);
	}

	function generatortypesList($options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $idtag = false, $translate = false) {
		$query = 'SELECT a.generator AS value, concat(a.generator, ".php") AS text'
				. ' FROM #__teamtimeformals_type AS a'
				. ' group by a.generator ORDER BY a.name';
		return self::queryList($query, $options, $name, $attribs, $key, $text, $selected, $idtag,
						$translate);
	}

	private function getProjectOptions() {
		$db = & JFactory::getDBO();
		$acl = new TeamTime_Acl();
		$projectId = $acl->filterUserProjectIds();

		$where = array();
		$where[] = "a.state != 1";

		if ($projectId !== null) {
			$where[] = "a.id in (" . implode(",", $projectId) . ")";
		}

		if (sizeof($where) > 0) {
			$where = " where " . implode(" and ", $where);
		}
		else {
			$where = "";
		}

		//$s = ($using === "0" && $selected == "") ? "selected" : "";
		//$result[] = "<option class='fproject filtergroup' value='' {$s}>" . JText::_('Projects') . "</option>";
		$query = 'SELECT a.id AS value, a.name AS text
			FROM #__teamtime_project AS a
			' . $where . '
			ORDER BY a.name';

		//error_log($query);

		$db->setQuery($query);
		$result = $db->loadObjectList();
		if (!$result) {
			$result = array();
		}

		return $result;
	}

	private function getUserOptions() {
		$db = & JFactory::getDBO();
		$mProject = new TeamtimeModelProject();
		$acl = new TeamTime_Acl();
		$projectId = $acl->filterUserProjectIds();

		$where = " where a.block = 0";
		if ($projectId !== null) {
			$where .= ' and a.id in (' . implode(",", $mProject->getUsersIds($projectId)) . ")";
		}

		//$s = ($using === "1" && $selected == "") ? "selected" : "";
		//$result[] = "<option class='fuser filtergroup' value='' {$s}>" . JText::_('Users') . "</option>";
		$query = 'SELECT a.id AS value, a.name AS text
			FROM #__users AS a
			' . $where . '
			ORDER BY name';

		//error_log($query);

		$db->setQuery($query);
		$result = $db->loadObjectList();
		if (!$result) {
			$result = array();
		}

		return $result;
	}

	public function variables_filter($options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $using = "", $noselect = false) {
		$result = array();
		if (!$noselect) {
			$result[] = "<select name='{$name}' id='{$name}' {$attribs}>";
		}

		$result[] = "<option value='' class='filtergroup0'>" .
				'- ' . JText::_('All') . ' -' . "</option>";

		if ($using === "0") {
			foreach (self::getProjectOptions() as $row) {
				$s = ($using == "0" && $row->value == $selected) ? "selected" : "";
				$result[] = "<option class='fproject' value='{$row->value}' {$s}>" . $row->text . "</option>";
			}
		}
		else if ($using === "1") {
			foreach (self::getUserOptions() as $row) {
				$s = ($using == "1" && $row->value == $selected) ? "selected" : "";
				$result[] = "<option class='fuser' value='{$row->value}' {$s}>" . $row->text . "</option>";
			}
		}

		if (!$noselect) {
			$result[] = "</select>";
		}

		return implode("", $result);
	}

	public function assignment_filter($options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $using = "project") {
		$result = array();

		foreach ($options as $row) {
			if (is_string($option)) {
				$result[] = $option;
			}
			else {
				$s = $row->value == $selected ? "selected" : "";
				$result[] = "<option value='{$row->value}' {$s}>" . $row->text . "</option>";
			}
		}

		if ($using == "project") {
			foreach (self::getProjectOptions() as $row) {
				$s = $row->value == $selected ? "selected" : "";
				$result[] = "<option value='{$row->value}' {$s}>" . $row->text . "</option>";
			}
		}
		else if ($using == "user") {
			foreach (self::getUserOptions() as $row) {
				$s = $row->value == $selected ? "selected" : "";
				$result[] = "<option value='{$row->value}' {$s}>" . $row->text . "</option>";
			}
		}

		return implode("", $result);
	}

	function getdbtitle($table, $fieldname, $id, $key = "id") {
		$db = & JFactory::getDBO();
		$db->setQuery("select * from {$table} where {$key} = " . (int) $id);
		$row = $db->loadObject();
		return $row->{$fieldname};
	}

	function queryList($query, $options, $name, $attribs = null, $key = 'value', $text = 'text',
			$selected = null, $idtag = false, $translate = false) {

		if (is_array($options)) {
			reset($options);
		}
		else {
			$options = array($options);
		}

		$db = & JFactory::getDBO();
		$db->setQuery($query);
		$list = $db->loadObjectList();

		if ($db->getErrorMsg()) {
			echo $db->stderr(true);
		}

		$options = array_merge($options, $list);
		return JHTML::_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag,
						$translate);
	}

}