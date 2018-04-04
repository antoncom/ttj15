<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$mLog = new TeamtimeModelLog();
$mUser = new TeamtimeModelUser();
$user = & JFactory::getUser();
$unclog = $mLog->getUncompletedLog($user->id);

$log_id = null;
$created = null;
if (isset($unclog[0])) {
	$log_id = $unclog[0]->id;
	$created = $unclog[0]->created;
}

$autotodoText = JRequest::getVar("autotodo_text");
$showAutotodoText = $teamtimeConfig->use_autotodos &&
		!(isset($unclog[0]) && $unclog[0]->todo_id);

if ($created) {
	?>

	<? if ($showAutotodoText) {
		?>

		<p class="autotodo-data-caption"><?= JText::_("Given") ?>:</p>
		<div class="autotodo-message">
			<?=
			$editor->display(
					// parameters : areaname, content, width, height, cols, rows, show xtd buttons
					'autotodo_text', $autotodoText, '500px', '200px', '60', '20', array())
			?>
		</div>

		<p>&nbsp;</p>

		<p class="autotodo-data-parent">
			<?= JText::_('Parent order') ?>&nbsp;<?
		$options = JHTML::_(
						'select.option', '', '- ' . JText::_('INCLUDED TO TEAM TODO') . ' -',
						'value', 'text');
		print JHTML::_('teamtime.todolist', $unclog[0]->project_id, null, $options,
						'curtodoid', 'class="inputbox"', 'value', 'text', null);
			?>
		</p>

	<? } ?>

	<h5 align="left" class="style1"><?= JText::_("Inform the client in short about your job") ?>:</h3>
	<div class="report-message main-editor" 
			 data-field-type="report"
			 data-current-id="<?= $log_id ?>">
				 <?=
				 $editor->display(
						 'description', "", '500px', '200px', '60', '20')
				 ?>
	</div>
	<?
}