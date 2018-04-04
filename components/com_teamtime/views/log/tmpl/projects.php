<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$mLog = new TeamtimeModelLog();
$mUser = new TeamtimeModelUser();
$user = & JFactory::getUser();
$unclog = $mLog->getUncompletedLog($user->id);
$todo_id = JRequest::getVar('todo_id');

$disabled = "";

if ($unclog) {
	$log_id = $unclog[0]->id;
	$log_proj_id = $unclog[0]->project_id;
	$log_task_id = $unclog[0]->task_id;
	$log_type_id = $unclog[0]->type_id;

	$project = new Project($log_proj_id);
	$tasks = $project->getTasks();
	$this->task_type_array = $project->getTaskTypeArray();

	$disabled = ($log_proj_id > 0 && $log_task_id > 0) ?
			" disabled style='background-color: #FFDDDD;'" : "";
}
else if (isset($todo_id) && $todo_id != 0) {
	$nTodo = new Todo($todo_id);
	$log_proj_id = $nTodo->project_id;
	$log_task_id = $nTodo->task_id;
	$log_type_id = $nTodo->type_id;

	$project = new Project($log_proj_id);
	$tasks = $project->getTasks();
	$this->task_type_array = $project->getTaskTypeArray();
}
?>

<select name="project_id" size="15" class="project" id="project-id" <?= $disabled ?>>
	<option disabled class="option1" value ="">-- <?php echo JText::_('Client'); ?> --</option>
	<? foreach ($this->projects as $project) { ?>
		<option value ="<?= $project->id ?>"
		<?
		if (isset($log_proj_id) && $log_proj_id == $project->id) {
			print " selected";
		}
		?>><?= $project->name ?></option>
					<? } ?>
</select>
