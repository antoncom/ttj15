<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class TeamlogCreportViewCreport extends JView {

	function display($tpl = null) {
		$config = TeamTimeCReport_get_config();

		$remoteUrl = $config->base_url;
		if (substr($config->base_url, -1) != "/") {
			$remoteUrl .= "/";
		}

		JHTML::script('raphael.js', $remoteUrl . 'administrator/components/com_teamtime/assets/js/libs/');
		JHTML::script('raphael-piechart.js',
				$remoteUrl . 'administrator/components/com_teamtime/assets/js/');

		$clientUrl = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["SCRIPT_NAME"]);
		if (substr($clientUrl, -1) == "/")
			$clientUrl = substr($clientUrl, 0, -1);

		$reportUrl = "index.php?option=com_teamtime&controller=report" .
				"&format=raw&callback=report-filter-area&filterform=1" .
				"&client=" . base64_encode($clientUrl) .
				"&token=" . base64_encode($_SERVER["SERVER_ADDR"]);

		$this->assignRef('remote_url', $remoteUrl);
		$this->assignRef('report_url', $reportUrl);

		$confData = json_encode($config);
		$this->assignRef('conf_data', $confData);

		
		 /* error_log(print_r(array(
		  $client_url, $_SERVER["SERVER_ADDR"], $remote_url . $report_url
		  ), true));
		  */
		 

		parent::display($tpl);
	}

}