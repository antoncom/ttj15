<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$mLog = new TeamtimeModelLog();
$mUser = new TeamtimeModelUser();
$user = & JFactory::getUser();
$config = & JFactory::getConfig();
$offset = $config->getValue('config.offset');

$unclog = $mLog->getUncompletedLog($user->id);
if (isset($unclog[0])) {
	$log_id = $unclog[0]->id;
	$created = $unclog[0]->created;
}
else {
	$log_id = null;
	$created = null;
}
$now = time();

if ($created) {
	$dt = JFactory::getDate($created);
	$dtNow = JFactory::getDate('now');
	$dif = $dtNow->toUnix() - $dt->toUnix();
	$dif = floor($dif / 60); // in minutes
	$mDif = $dif % 60;
	$hDif = floor($dif / 60);

	//print $dif . " min<br>";
	//print $dt->toMySQL() . " / " . $dtNow->toMySQL() . "<br>";
	//print $hDif . ":" . $mDif . "<br>";
	?>
	<div id="duration">
		<label for="hours"><?php echo JText::_('Hours'); ?>:</label>
		<select class="hours" name="hours" id="hours"
						<? if ($mUser->checkPause()) { ?>disabled<? } ?>
						>
							<?php for ($i = 0; $i <= 10; $i++) : ?>
				<option value ="<?php echo $i; ?>" <?php if ($i == $hDif) echo "selected"; ?>><?php echo $i; ?></option>
			<?php endfor; ?>
		</select>
		<label for="minutes"><?php echo JText::_('Minutes'); ?>:</label>
		<select class="minutes" name="minutes" id="minutes"
						<? if ($mUser->checkPause()) { ?>disabled<? } ?>
						>
							<?php for ($i = 1; $i <= 59; $i++) : ?>
				<option value ="<?php echo $i; ?>" <?php if ($i == $mDif) echo "selected"; ?>><?php echo $i; ?></option>
			<?php endfor; ?>
		</select>
	</div>

	<?php
}