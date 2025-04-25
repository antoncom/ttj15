<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

ob_start();
?>

<?php
JFilterOutput::objectHTMLSafe($this->item, ENT_QUOTES);
$get = JRequest::get('get');

jimport('joomla.html.pane');

$format = JText::_('DATE_FORMAT_LC2');
?>

<?php if (count($this->other_logs)) : ?>

	<?php
	foreach ($this->other_logs as $row) :
		if (sizeof($row['logs']) == 0)
			continue;
		?>
		<div class="user"><?php echo $row['user']->name; ?>
			<span class="delta"> - <?php
		$delta = DateHelper::getDeltaOrWeekdayText($row['user']->getStateModified(),
						$this->user->getParam('timezone'));
		if ($delta)
			echo $delta;
		?></span>
		</div>
		<h2 class="state"><?php echo $row['user']->getStateDescription(); ?></h2>
		<ul class="log">
					<?php foreach ($row['logs'] as $log) : ?>
				<li>
					<a href="javascript:void(0)" class="tooltip" title="<?php echo $log->getProjectName() . ' :: ' . $log->getTaskName() . ' (' . $log->getDurationText() . ')'; ?>">
						<?php echo $log->description; ?>
					</a>
					<span class="delta"> - <?php echo DateHelper::getDeltaOrWeekdayText($log->date,
					$this->user->getParam('timezone'));
			?> </span>
				</li>
		<?php endforeach; ?>
		</ul>
	<?php endforeach; ?>

	<?php
endif;

$res = ob_get_contents();
ob_clean();
//print JRequest::getVar("callback")."({'value':".json_encode($res)."})";
?>

(function(){
document.getElementById('<?= JRequest::getVar("callback") ?>'
).innerHTML = <?= json_encode($res) ?>;
})();

