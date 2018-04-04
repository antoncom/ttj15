<?php
defined('_JEXEC') or die('Restricted access');

JHTML::script('FusionCharts.js',
		'administrator/components/com_teamtime/libraries/fusioncharts/charts/');

$format = JText::_('DATE_FORMAT_LC1');

$total_amount = 0;

$user = & JFactory::getUser();
$isAdminPage = strpos(JURI::base(), "/administrator/") !== false;

$descriptionLoaderUrl =
		JURI::root() . "index.php?option=com_teamtime&controller=reports&task=load_description";
?>

<script type="text/javascript">
  hs.graphicsDir = "<?= JURI::root() ?>components/com_teamtime/assets/highslide/graphics/";
  hs.outlineType = "rounded-white";
  hs.wrapperClassName = "draggable-header";
  hs.showCredits = false;
  hs.width = 740;
  /*hs.dimmingOpacity = 0.4;
        hs.maxWidth = 800;
        hs.maxHeight = 200;
        hs.maxHeight = 600;
        hs.align = "auto";
        hs.allowWidthReduction = true;*/
</script>

<div class="report-header">
  <h2><?php
$text = ($this->from_period && $this->until_period) ? JHTML::_('date',
				$this->from_period, $format) . ' - ' . JHTML::_('date', $this->until_period,
				$format) : JText::_('No Period Specified');
echo $text;
?>
  </h2>
</div>

<?php if (count($this->report['data'])) : ?>

	<div class="project-report-charts" style="overflow:hidden;">
		<div style="width:100%; float:left;">
			<?php //$this->proj_chart->renderChart();     ?>
		</div>
	</div>

	<table class="project-report-charts">
		<tr>
			<th align="center" style="text-align:center;"><?= JText::_("Project stats") ?></th>
		</tr>
		<tr>
			<td <?=
		$isAdminPage ? 'align="center"' : ''
			?>>
					<?= $this->projectsChart ?>
			</td>
		</tr>
	</table>

	<div class="user-report-stats">
		<?php
		$week_i = 0;
		foreach ($this->report['data'] as $week_logs) :
			$total_amount += $week_logs['total'];
			$week_i++;
			?>
			<div class="user-report-week">
				<h3><?php echo $week_logs['title']; ?></h3>
				<table class="adminlist">
					<thead>
						<tr>
							<th class="col_project">
								<?php echo JText::_('Project'); ?>
							</th>
							<th class="col_task">
								<?php echo JText::_('Task'); ?>
							</th>
							<th class="col_todo">
								<?php echo JText::_('Todos'); ?>
							</th>
							<th class="col_log">
								<?php echo JText::_('Log'); ?>
							</th>
							<th class="col_date">
								<?php echo JText::_('Date'); ?>
							</th>
							<th class="col_actual_hours" style="width:100px;">
								<?php echo JText::_('Work Time'); ?>
							</th>

							<?
							if (!in_array($user->usertype, array("Manager"))) {
								?>
								<th class="col_hourly_rate" style="width:80px;">
									<?php echo JText::_('Hour Price'); ?>
								</th>
								<th class="col_planned_cost" style="width:80px;">
									<?php echo JText::_('Price-Plan'); ?>
								</th>
								<th class="col_actual_cost" style="width:80px;">
									<?php echo JText::_('Price-Fact'); ?>
								</th>
							<? } ?>

							<th class="col_overhead_expenses" style="width:100px;">
								<?php echo JText::_('Overhead Expenses'); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$todoModel = new TodoModelTodo();

						$k = 0;
						foreach ($week_logs['logs'] as $log) {
							$has_descr = $todoModel->isEditedDescription($log['todo_description']);
							$todo_col_content = "";

							if ($has_descr) {
								/*
								  $todo_col_content = '<a href="javascript:void(0);"
								  onclick="return hs.htmlExpand(this, { headingText: \''
								  . htmlspecialchars($log['todo_title'], ENT_QUOTES)
								  . '\' });">' . $log['todo_title'] . '</a>
								  <div class="highslide-maincontent">' . $log['todo_description'] . '</div>'; */

								$todo_col_content = '<a href="' . $descriptionLoaderUrl
										. "&todo_id=" . $log["todo_id"] . "&project_id=" . $log["project_id"]
										. '" onclick="return hs.htmlExpand(this, { objectType: \'iframe\', headingText: \''
										. htmlspecialchars($log['todo_title'], ENT_QUOTES) . '\' });">'
										. $log['todo_title'] . '</a>';
							}
							else {
								$todo_col_content = $log['todo_title'];
							}
							?>

							<tr class="<?php echo "row$k"; ?>">
								<td class="col_project">
									<?php echo $log['project_name']; ?>
								</td>
								<td class="col_task">
									<?php echo $log['task_name']; ?>
								</td>

								<td class="col_todo">
									<?= $todo_col_content ?>
								</td>

								<td class="col_log">
									<?php echo convert_to_links($log['log']); ?>
								</td>
								<td class="col_date">
									<?php
									echo JHTML::_('date', $log['date'], $format);
									?>
								</td>
								<td class="col_actual_hours" align="right">
									<?php
									echo DateHelper::formatTimespan($log['duration'], 'h:m');
									?>
								</td>

								<?
								if (!in_array($user->usertype, array("Manager"))) {
									?>
									<td class="col_hourly_rate" align="center">
										<?php echo $log['hourly_rate']; ?>
									</td>

									<td class="col_planned_cost" align="center">
										<?php
										echo round($log['hours_plan_price'], 2);
										?>
									</td>

									<td class="col_actual_cost" align="center">
										<?php
										echo round($log['hours_fact_price'], 2);
										?>
									</td>
								<? } ?>

								<td class="col_overhead_expenses" align="center">
									<?= (int) $log['costs'] ?> / <?= (float) $log['money'] ?>
								</td>
							</tr>
							<?php
							$k = 1 - $k;
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<td class="col_project"></td>
							<td class="col_task"></td>
							<td class="col_todo"></td>
							<td class="col_log"></td>

							<td class="col_date" style="font-weight:bold;text-align:right;">
								<?php
								echo $week_logs['total_money'] != 0 ? $week_logs['total_money'] : ""
								?>
							</td>

							<td class="col_actual_hours" style="font-weight:bold;text-align:right;">
								<?=
								round($week_logs['total'] / 60, 2);
								?>
							</td>

							<?
							if (!in_array($user->usertype, array("Manager"))) {
								?>
								<td class="col_hourly_rate" ></td>
								<td class="col_planned_cost" ></td>
								<td class="col_actual_cost"></td>
							<? } ?>

							<td class="col_overhead_expenses"></td>
						</tr>

						<? if ($week_i >= sizeof($this->report['data'])) { ?>
							<tr>
								<td class="col_project"></td>
								<td class="col_task"></td>
								<td class="col_todo"></td>
								<td class="col_log"></td>
								<td class="col_date"></td>
								<td class="col_actual_hours" style="font-weight:bold;text-align:right;"><?=
				round($total_amount / 60, 2)
							?></td>

								<?
								if (!in_array($user->usertype, array("Manager"))) {
									?>
									<td class="col_hourly_rate" ></td>
									<td class="col_planned_cost"  style="font-weight:bold;text-align:center;"><?
					echo round($this->report['total_plan_price'], 2)
									?></td>
									<td class="col_actual_cost" style="font-weight:bold;text-align:center;"><?
						echo round($this->report['total_fact_price'], 2)
									?></td>
								<? } ?>

								<td class="col_overhead_expenses"  style="font-weight:bold;text-align:center;">
									<?= (int) $this->report['total_costs'] ?> / <?=
						(float) round($this->report['total_money'], 2)
									?></td>
							</tr>
						<? } ?>
					</tfoot>
				</table>
			</div>
		<?php endforeach; ?>

	</div>

<?php else : ?>
	<?php echo JText::_('No Entries Found'); ?>
<?php endif; ?>