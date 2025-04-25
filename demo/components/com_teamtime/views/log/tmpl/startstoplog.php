<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$mLog = new TeamtimeModelLog();
$mUser = new TeamtimeModelUser();
$user = & JFactory::getUser();
$unclog = $mLog->getUncompletedLog($user->id);

$get = JRequest::get('get');
if (isset($unclog[0])) {
	$log_id = $unclog[0]->id;
}
else {
	$log_id = null;
}

$showCloseTodo = ($current_todo_id > 0 && $current_todo_state != TODO_STATE_CLOSED)
		|| $teamtimeConfig->use_autotodos;

if ((isset($get['status']) && $get['status'] == "started") || $log_id > 0) {
	?>
	<button type="submit" id="bStop"
					style="<?= $mUser->checkPause() ? "display:none;" : "" ?>"><?= JText::_("Stop Working") ?></button>
	<input type="hidden" value="stoplog" name="task"/>

	<button type="button" id="bContinue"
					style="<?=
	!$mUser->checkPause() ? "display:none;" : ""
	?>"><?= JText::_("Continue Job") ?></button>

	<button type="button" id="bPause"	<?=
				$mUser->checkPause() ? "disabled" : ""
	?>><?= JText::_("Pause") ?></button>

	<? if ($showCloseTodo) { ?>
		<div>
			<input type="checkbox" name="close_todo" id="close_todo" value="1">
			<a href="#" id="toggle_close_todo"><?= JText::_("TODO_CLOSE") ?></a>
		</div>
		<? TeamTime::helper()->getBpmn()->getCheckboxSendReport($current_todo_id) ?>
	<? } ?>

	<?php
}
else {
	?>
	<button id="start_work" type="submit"><?= JText::_("Start Working") ?></button>
	<input type="hidden" value="startlog" name="task" id="bStart"/>

<?php } ?>