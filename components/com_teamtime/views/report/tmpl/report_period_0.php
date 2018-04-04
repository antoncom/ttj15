<?php
defined('_JEXEC') or die('Restricted access');

$format = JText::_('DATE_FORMAT_LC1');

$view_type = "month";
if (isset($this->date_select["name"])) {
	if (stripos($this->date_select["name"], "day") !== false &&
			stripos($this->date_select["name"], "days") === false)
		$view_type = "day";
	else if (stripos($this->date_select["name"], "week") !== false)
		$view_type = "week";
}

$user = & JFactory::getUser();
$isAdminPage = strpos(JURI::base(), "/administrator/") !== false;
?>

<div class="report-header">
  <h2><?php
echo JHTML::_('date', $this->from_period, $format) . ' - ' . JHTML::_('date',
		$this->until_period, $format);
?> </h2>
</div>

<?php if (count($this->report['data'])) { ?>

	<div class="project-report-charts" style="overflow:hidden;">
		<div style="width:100%; float:left;">
			<?php //$this->proj_chart->renderChart();   ?>
		</div>
	</div>

	<table class="project-report-charts">
		<tr>
			<th align="center" style="text-align:center;"><?= JText::_("Project stats") ?></th>
		</tr>
		<tr>
			<td <?= $isAdminPage ? 'align="center"' : '' ?>>
				<?= $this->projectsChart ?>
			</td>
		</tr>
	</table>

	<div class="period-report-stats">
		<table class="adminlist">
			<thead>
				<tr>
					<th>
						<?php echo JText::_('Project'); ?>
					</th>
					<th style="width:100px;">
						<?php echo JText::_('Plan Hour'); ?>
					</th>
					<th style="width:100px;">
						<?php echo JText::_('Duration'); ?>
					</th>

					<?
					if (!in_array($user->usertype, array("Manager"))) {
						?>
						<th style="width:100px;">
							<?php echo JText::_('Planned cost'); ?>
						</th>
						<th style="width:100px;">
							<?php echo JText::_('Actual cost'); ?>
						</th>
					<? } ?>

					<th style="width:100px;">
						<?php echo JText::_('Overhead expenses'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$k = 0;
				foreach ($this->report['data'] as $project) {
					$link = JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=showreport&project_id=' . $project['id'] . '&from_period=' . $this->from_period . '&until_period=' . $this->until_period);
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td>
							<span class="editlinktip hasTip" title="<?php echo JText::_('Report'); ?>: <?php echo $project['name']; ?>">
								<a href="<?php echo $link ?>"><?php echo $project['name']; ?></a>
							</span>
						</td>

						<td align="right">
							<?=
							TeamTime::helper()->getCalendar()->getLink(
									round($project['splan'], 2),
									"project_id={$project['id']}&view_type=" . $view_type . $filter_date)
							?>
						</td>

						<td align="right">
							<?php
							echo DateHelper::formatTimespan($project['duration'], 'h:m');
							?>
						</td>

						<?
						if (!in_array($user->usertype, array("Manager"))) {
							?>
							<td align="right">
								<?=
								round($project["splanned_cost"], 2)
								?>
							</td>

							<td align="right">
								<?=
								round($project["sfact_cost"], 2)
								?>
							</td>
						<? } ?>

						<td align="center">
							<?= (int) $project['scosts'] ?> / <?=
					(float) round($project['smoney'], 2)
							?>
						</td>
					</tr>
					<?php
					$k = 1 - $k;
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2" style="font-weight:bold;text-align:right;">
						<?php
						echo DateHelper::formatTimespan($this->report['total_plan'], 'hr mi');
						?>
					</td>
					<td style="font-weight:bold;text-align:right;">
						<?php
						echo DateHelper::formatTimespan($this->report['total'], 'hr mi');
						?>
					</td>

					<?
					if (!in_array($user->usertype, array("Manager"))) {
						?>
						<td style="font-weight:bold;text-align:right;">
							<?=
							round($this->report["total_planned_cost"], 2)
							?>
						</td>

						<td style="font-weight:bold;text-align:right;">
							<?=
							round($this->report["total_fact_cost"], 2)
							?>
						</td>
					<? } ?>

					<td style="font-weight:bold;text-align:center;">
						<?= (int) $this->report['total_costs'] ?> / <?=
					(float) round($this->report['total_money'], 2)
						?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php
}
else {
	echo JText::_('No Entries Found');
}