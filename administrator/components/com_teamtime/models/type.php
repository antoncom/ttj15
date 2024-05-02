<?php

class TeamtimeModelType extends Core_Joomla_Manager {

	public $_table = 'type';

	private function getHoursFact($typeId, $whereStr = "") {
		$query = "select sum(duration)/60 as sfact
			from #__teamtime_log
			where type_id = " . (int) $typeId . $whereStr;

		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();

		return $row->sfact;
	}

	private function getHoursPlan($typeId, $whereStr = "") {
		$query = "select sum(hours_plan) as splan
			from #__teamtime_todo
			where type_id = " . (int) $typeId . $whereStr;

		$this->_db->setQuery($query);
		$row = $this->_db->loadObject();

		return $row->splan;
	}

	public function initHoursForTypes($items, $fromPeriod, $untilPeriod) {
		if ($fromPeriod && $untilPeriod) {
			$whereHoursFact = ' and date >= ' . $this->_db->Quote(
							$this->_db->getEscaped("$fromPeriod 00:00:00", true), false) .
					' and date <= ' . $this->_db->Quote(
							$this->_db->getEscaped("$untilPeriod 23:59:59", true), false);
		}
		else {
			$whereHoursFact = "";
		}

		if ($fromPeriod && $untilPeriod) {
			$whereHoursPlan = ' and created >= ' . $this->_db->Quote(
							$this->_db->getEscaped("$fromPeriod 00:00:00", true), false) .
					' and created <= ' . $this->_db->Quote(
							$this->_db->getEscaped("$untilPeriod 23:59:59", true), false);
		}
		else {
			$whereHoursPlan = "";
		}

		foreach ($items as $i => $item) {
			$items[$i]->sfact = $this->getHoursFact($item->id, $whereHoursFact);
			$items[$i]->splan = $this->getHoursPlan($item->id, $whereHoursPlan);
		}

		return $items;
	}

	public function filterWithAllowedProjects($ids, $acl) {
		$table = & $this->getTable($this->_table);
		$result = array();

		$where = array();
		$where[] = 'a.id in (' . implode(",", $ids) . ")";

		$acl = new TeamTime_Acl();
		$projectId = $acl->filterUserProjectIds();
		if ($projectId !== null) {
			$where[] = 'b.project_id in (' . implode(",", $projectId) . ")";
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		$query = 'select a.* from ' . $table->getTableName() . ' as a
			left join #__teamtime_log AS b on a.id = b.type_id
			' . $where . '
			group by a.id';
		
		//error_log($query);

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		foreach ($rows as $row) {
			$result[] = $row->id;
		}
		
		//error_log(print_r($result, true));

		return $result;
	}

}