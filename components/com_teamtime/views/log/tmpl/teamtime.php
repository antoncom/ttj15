<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$helperBase = TeamTime::helper()->getBase();

foreach ($this->other_logs as $row) {
	?>
	<div class="user"><?= $row['user']->name ?>
		<span class="delta"> - <?
	$delta = DateHelper::getDeltaOrWeekdayText($row['user']->getStateModified(),
					$this->user->getParam('timezone'));
	if ($delta) {
		print $delta;
	}
	?></span>
	</div>
	<h2 class="state"><?= $row['user']->getStateDescription() ?></h2>
	<ul class="log">
		<? foreach ($row['logs'] as $log) { ?>
			<li>
				<span href="javascript:void(0)" class="tooltip"
							title="<?=
		$log->getProjectName() . ' :: ' . $log->getTaskName() .
				' (' . $log->getDurationText() . ')'
			?>">
								<?=
								$helperBase->getReportText(array(
									"id" => $log->id,
									"content" => $log->description,
									"title" => $log->getTodoTitle()))
								?>
				</span>
				<span class="delta"> - <?=
						DateHelper::getDeltaOrWeekdayText(
								$log->date, $this->user->getParam('timezone'));
								?> </span>
			</li>
		<? } ?>
	</ul>
<? } ?>

