<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$_filter_form = JRequest::getVar("callback") != "";
$_nosize = JRequest::getVar("nosize") == "1";
$type_id = JRequest::getVar('type_id');
$hideTasks = JRequest::getVar('hideTasks') == 1;

if ($_filter_form) {
	ob_start();
}

$typeNums = isset($this->task_type_array) ? count($this->task_type_array) : 0;
$todo_id = JRequest::getVar('todo_id');

if (isset($todo_id) && $todo_id != 0) {
	$nTodo = new Todo($todo_id);
	$log_task_id = $nTodo->task_id;
	$log_proj_id = $nTodo->project_id;
	$disabled = "";
}
else {
	$disabled = ((isset($log_proj_id) && $log_proj_id > 0) && $log_task_id > 0) ?
			" disabled style='background-color: #FFDDDD;'" : "";
}
?>

<select id="curtaskid" class="task" name="task_id"
<?= $_filter_form ? "" : 'onchange="load_task_description(this);" ' ?>
<?=
($_filter_form || $_nosize) ? "" : 'size="15"'
?> <?php echo $disabled; ?>>
	<option <?= $_filter_form ? "" : "disabled" ?>
		class="option1" value ="">-- <?php echo JText::_('Select type of work'); ?> --</option>
		<?php
		if (isset($this->task_type_array) && sizeof($this->task_type_array) > 0) {

			foreach ($this->task_type_array as $typename => $tasks) {
				$visible_tasks_count = 0;
				foreach ($tasks as $i => $task) {
					if ($task->state != 1)
						$visible_tasks_count++;
				}
				?>
				<?php
				if ($visible_tasks_count > 0)
					foreach ($tasks as $i => $task) {
						if ($i == 0) {
							?>
						<option <?= $_filter_form ? "" : "disabled" ?> class="option2"
																													 <?=
																													 $type_id != "" && $type_id == $task->type_id ? " selected " : ""
																													 ?>
																													 value ="<?= $task->type_id ?>"><?php echo $typename; ?></option>
																													 <?
																												 }

																												 // filter closed tasks
																												 if ($task->state == 1) {
																													 continue;
																												 }
																												 ?>
																												 <?php $taskNums = count($tasks); ?>
																												 <?php
																												 $selected = ((isset($log_task_id) && $log_task_id == $task->id) || ($taskNums == 1 && $typeNums == 1))
																															 ? " selected" : "";
																												 ?>

					<? if (!$hideTasks) { ?>

						<option value ="<?php
					echo $_filter_form ? $task->name : $task->id;
						?>"<?php echo $selected ?>>- <?php echo $task->name; ?></option>

					<? } ?>

				<? } ?>
		<? } ?>

	<? } ?>
</select>

<?
if ($_filter_form) {
	$res = ob_get_contents();
	ob_clean();
	?>

	(function(){
	document.getElementById('<?= JRequest::getVar("callback") ?>'
	).innerHTML = <?= json_encode($res) ?>;
	})();
	<?
}