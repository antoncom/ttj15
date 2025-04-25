<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$filter_project = JRequest::getVar('filter_project', '');
$_filter_form = JRequest::getVar("callback") != "";

if ($_filter_form) {
	if (JRequest::getVar("client")) {
		$client = explode(",", JRequest::getVar("client"));
	}
	else {
		$client = array();
	}

	ob_start();
}
?>

<select name="project_id" class="project" id="project-id"
        onchange="report_load_projecttasks('<?= JURI::base() ?>');"
        style="width:100%; <?=
sizeof($client) > 0 ? "display:none;" : ""
?>">
  <option class="option1" value="">-- <?= JText::_("SELECT PROJECT") ?> --</option>

	<? foreach ($this->projects as $project) { ?>
		<?
		if (sizeof($client) > 0 && !in_array($project->id, $client)) {
			continue;
		}
		?>
		<option value ="<?= $project->id ?>"
		<?=
		(isset($log_proj_id) && $log_proj_id == $project->id) ? " selected" : ""
		?>
		<?= ($filter_project != "" && $filter_project == $project->id) ? " selected" : ""
		?>
						><?= $project->name ?></option>
					<? } ?>
</select>

<?
if ($_filter_form) {
	$res = ob_get_contents();
	ob_clean();
	?>

	(function () {
	document.getElementById('<?= JRequest::getVar("callback") ?>').innerHTML =
	<?= json_encode($res) ?>;
	})();
	<?
}