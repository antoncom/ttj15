<?php

class TeamlogViewReports extends JView {

	function display($tpl = null) {
		$mainframe = & JFactory::getApplication();
		$option = JRequest::getCmd('option');

		JHTML::stylesheet('reports.css', "components/com_teamtime/assets/css/");
		JHTML::stylesheet('piecharts.css', "components/com_teamtime/assets/css/");

		JHTML::script('reports.js', "components/com_teamtime/assets/js/teamtime/");

		$filterStr = "";

		$filterProject = JRequest::getVar("filter_project", "");
		if ($filterProject != "") {
			$filterStr .= "&amp;filter_project=$filterProject";
		}

		$filterType = JRequest::getVar("filter_type", "");
		if ($filterType != "") {
			$filterStr .= "&amp;filter_type=$filterType";
		}

		$filterFrom = JRequest::getVar("filter_from", "");
		if ($filterFrom != "") {
			$filterStr .= "&amp;filter_from=$filterFrom";
		}

		$filterUntil = JRequest::getVar("filter_until", "");
		if ($filterUntil != "") {
			$filterStr .= "&amp;filter_until=$filterUntil";
		}

		$reportUrl = JURI::base() .
				"index.php?option=com_teamtime&amp;controller=report&amp;format=raw&amp;callback=report-filter-area&amp;filterform=1" .
				$filterStr;
		$this->assignRef('reportUrl', $reportUrl);

		$conf = TeamTime::getConfig();
		TeamTime::helper()->getBase()->addJavaScript(array(
			"resource" => array(
				"configData" => $conf
			)
		));

		parent::display($tpl);
	}

}