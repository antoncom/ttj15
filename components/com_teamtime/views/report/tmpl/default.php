<?php
defined('_JEXEC') or die('Restricted access');

$user = &JFactory::getUser();

$client = JRequest::getVar("client");
$sclient = $client ? implode(",", array_unique($client)) : "";

$filter_project = JRequest::getVar("filter_project", "");
if ($filter_project != "") {
	$project_id = $filter_project;
}
else {
	$project_id = sizeof($client) > 1 ? "" : $client[0];
}

$filter_type = JRequest::getVar("filter_type", "");

ob_start();
?>

<? if (JRequest::getVar("filterform") == "1") { ?>

	<form action="<?= JURI::base() ?>index.php" method="get" name="adminForm" id="adminForm"
				onsubmit="return report_form_submit('<?= JURI::base() ?>');">
		<div id="content-controls">

			<div class="userReportHeader">
				<h1><?= JText::_('User reports') ?>: <span id="currentUser"></span></h1>
			</div>

			<table>
				<tr valign="top">
					<td>
						<div class="select-date">
							<div>
								<?= $this->lists['select_date'] ?>
							</div>
							<div>
								<?=
								str_replace("/templates", "templates",
										JHTML::_(
												'calendar', $this->from_period, 'from_period', 'from-period',
												JText::_(
														'DATE_FORMAT_MYSQL_WITHOUT_TIME')));
								?>
								<?=
								str_replace("/templates", "templates",
										JHTML::_(
												'calendar', $this->until_period, 'until_period', 'until-period',
												JText::_(
														'DATE_FORMAT_MYSQL_WITHOUT_TIME')));
								?>
							</div>
						</div>
					</td>

					<td>
						<? if (!$user->guest || sizeof($client) > 1) { ?>
							<div id="report-filter-users">
								<?= $this->lists["select_user"] ?>
							</div>
							<div id="report-filter-projects">
							</div>
						<? } ?>
						<div id="report-filter-tasks">
						</div>
					</td>
					<td>
						<input type="submit"
									 id="form_submit"
									 value="Применить" >
					</td>
				</tr>
			</table>
		</div>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="showReport" />
		<input type="hidden" name="format" value="raw" />
		<input type="hidden" name="project_id" value="<?= $client[0] ?>" />
		<input type="hidden" name="client_ids" value="<?= $sclient ?>" />
		<input type="hidden" name="callback" value="<?= JRequest::getVar("callback") ?>" />
	</form>

	<div id="report-content-area">
	</div>

	<div id="preloaderbg" class="centerbg1">
		<div class="centerbg2">
			<div id="preloader"></div>
		</div>
	</div>

	<?
}
else {
	if ($this->contentLayout) {
		require_once(dirname(__FILE__) . DS . $this->contentLayout . '.php');
	}
}

$res = ob_get_contents();
ob_clean();

?>

(function () {

document.getElementById('<?= JRequest::getVar("callback") ?>').innerHTML = <?= json_encode($res) ?>;

if (typeof(creport_process_table) == "function") {
creport_process_table();
}

document.getElementById('period').onchange = function () {
var from  = document.getElementById('from-period');
var until = document.getElementById('until-period');
switch (this.value) {
<?php
foreach ($this->date_presets as $name => $value) {
	$case = "case '" . $name . "':\n";
	$case .= "from.value = '" . $value['from'] . "';\n";
	$case .= "until.value = '" . $value['until'] . "';\n";
	$case .= "break;\n";
	echo $case;
}
?>
}
//document.adminForm.submit();
};

<?php
if (JRequest::getVar("filterform") == "") {
	/*
	  if ($this->type_chart)
	  $this->type_chart->renderChart(true);

	  if ($this->user_chart)
	  $this->user_chart->renderChart(true);

	  if ($this->proj_chart)
	  $this->proj_chart->renderChart(true);
	 */

	if (isset($this->typeChart) && $this->typeChart) {
		?>
		Raphael_PieChart('typeChart', 350, 150, 100);
		<?
	}

	if (isset($this->userChart) && $this->userChart) {
		?>
		Raphael_PieChart('userChart', 350, 150, 100);
		<?
	}

	if (isset($this->projectsChart) && $this->projectsChart) {
		?>
		Raphael_PieChart('projectsChart', 350, 150, 100);
		<?
	}
	?>
	report_hide_loadprogress();
	<?
}
else {
	?>
	var report_get_params = function (url) {
	var params = {};
	var a = [];
	var p = url.indexOf('?') >= 0?
	url.substr(url.indexOf('?') + 1) : "";
	if(p != "")
	a = p.split("&");
	var b;
	for(var i = 0; i < a.length; i++){
	b = a[i].split("=");
	params[b[0]] = b[1];
	}
	return params;
	};

	var script = document.createElement('script');
	script.setAttribute('src',
	"<?= JURI::base() ?>components/com_teamtime/assets/js/client_filter_form.js");
	document.getElementsByTagName('head')[0].appendChild(script);

	script = document.createElement('script');
	script.setAttribute('src',
	"<?= JURI::base() ?>index.php?option=com_teamtime"
	+ "&view=log&format=raw&task=loadtasks&project_id=<?= $project_id ?><?=
	$filter_type != "" ?
					"&type_id=$filter_type" : ""
	?><?= sizeof($client) > 1 ? "&hideTasks=1" : "" ?>&callback=report-filter-tasks");
	document.getElementsByTagName('head')[0].appendChild(script);

	/*
	script = document.createElement('script');
	script.setAttribute('src',
	"<?= JURI::base() ?>administrator/components/com_teamtime/library/fusioncharts/charts/FusionCharts.js");
	document.getElementsByTagName('head')[0].appendChild(script);
	*/

	var params = report_get_params(location.href);
	var sparams = "";
	if("from_period" in params){
	document.getElementById("from-period").value = params["from_period"];
	sparams += "&from_period=" + params["from_period"];
	}
	if("until_period" in params){
	document.getElementById("until-period").value = params["until_period"];
	sparams += "&until_period=" + params["until_period"];
	}

	/*script = document.createElement('script');
	script.setAttribute('src',
	"<?= JURI::base() ?>index.php?option=com_teamtime&controller=report"
	+ "&format=raw&client_ids=<?= $sclient ?>&callback=report-content-area"
	+ sparams);
	document.getElementsByTagName('head')[0].appendChild(script);*/

	// init calendar fields
	Calendar.setup({
	inputField     :    "from-period",     // id of the input field
	ifFormat       :    "%Y-%m-%d",      // format of the input field
	button         :    "from-period_img",  // trigger for the calendar (button ID)
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true
	});

	Calendar.setup({
	inputField     :    "until-period",     // id of the input field
	ifFormat       :    "%Y-%m-%d",      // format of the input field
	button         :    "until-period_img",  // trigger for the calendar (button ID)
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true
	});

	var change_date = function(o) {
	document.getElementById("period").selectedIndex = 0;
	};

	document.getElementById("from-period").onchange = change_date;
	document.getElementById("until-period").onchange = change_date;

	// autosubmit at first load
	setTimeout(function () {
	report_form_firstsubmit(
	'<?= JURI::base() ?>',
	<?= sizeof($client) > 1 ? 1 : 0
	?>
	);
	}, 1000);

	<? if (!$user->guest || sizeof($client) > 1) { ?>
		script = document.createElement('script');
		script.setAttribute('src',
		"<?= JURI::base() ?>index.php?option=com_teamtime&controller=&view=log&format=raw&task=loadprojects&<?=
		$sclient ?
						("&client=" . $sclient) : ""
		?><?=
		$filter_project != "" ?
						"&filter_project=$filter_project" : ""
		?>&callback=report-filter-projects"
		+ sparams);
		document.getElementsByTagName('head')[0].appendChild(script);
	<? } ?>

<? } ?>

})();

