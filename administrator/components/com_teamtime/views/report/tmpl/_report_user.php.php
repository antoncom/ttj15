<?php
defined('_JEXEC') or die('Restricted access');

$format = JText::_('DATE_FORMAT_LC1');

$total_amount = 0;

$user = & JFactory::getUser();
$isAdminPage = strpos(JURI::base(), "/administrator/") !== false;

$descriptionLoaderUrl =
		JURI::root() . "index.php?option=com_teamtime&controller=reports&task=load_description";

$helperBase = TeamTime::helper()->getBase();
?>

<div class="report-header">
  <h2><?php
$text = ($this->from_period && $this->until_period) ? JHTML::_('date',
				$this->from_period, $format) . ' - ' . JHTML::_('date', $this->until_period,
				$format) : JText::_('No Period Specified');
echo $text;
?>
  </h2>


</div>

<?php 
	if (count($this->report['data'])) : ?>

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
			$n = 0; // number of records inside the current week;

			echo $user->usertype;
			?>
			<div class="user-report-week">
				<h3><?php echo $week_logs['title']; ?></h3>
				<table class="adminlist">
					<thead>
						<tr>
							<th class="col_project">
								<?php echo JText::_('Project'); ?>
							</th>
							<!-- It works, but not needed right now.
							<th class="col_task">
								<?php echo JText::_('Task'); ?>
							</th> -->
							<th class="col_todo">
								<?php echo JText::_('Todos'); ?>
							</th>
							<th class="col_log">
								<?php echo JText::_('Log'); ?>
							</th>
							<th class="col_date">
								<?php echo JText::_('Date'); ?>
							</th>
							<!-- It works, but not needed right now.
							<th class="col_planned_actual_hours">
								План часов
							</th>
							//-->
							<th class="col_actual_hours" style="width:100px;">
								<?php echo JText::_('Work Time'); ?>
							</th>

							<?
							if (in_array($user->usertype, array("Manager", "Administrator", "Super Administrator"))) {
								?>
								<th class="col_hourly_rate" style="width:80px;">
									<?php echo JText::_('Hour Price'); ?>
								</th>
								<!-- It works, but not needed right now.
								<th class="col_planned_cost" style="width:80px;">
									<?php echo JText::_('Price-Plan'); ?>
								</th>
								/-->
								<th class="col_actual_cost" style="width:80px;">
									<?php echo JText::_('Price-Fact'); ?>
								</th>
								<th class="col_actual_cost" style="width:80px;">
									<?php echo "Оклад"; ?>
								</th>
							<? } ?>

							<th class="col_overhead_expenses" style="width:100px;">
								<?php echo JText::_('Overhead Expenses'); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$todoModel = new TeamtimeModelTodo();
						$weekly_earnings = 0;

						$k = 0;
						foreach ($week_logs['logs'] as $log) {
							$has_descr = $todoModel->isEditedDescription($log['todo_description']);
							$todo_col_content = "";

							if ($has_descr) {
								$todo_col_content = '<a href="' . $descriptionLoaderUrl
										. "&todo_id=" . $log["todo_id"] . "&project_id=" . $log["project_id"]
										. '" class="fancybox">' . $log['todo_title'] . '</a>';
							}
							else {
								$todo_col_content = $log['todo_title'];
							}
							?>

							<tr class="<?php echo "row$k"; ?>">
								<td class="col_project">
									<?php echo $log['project_name']; ?>
								</td>
								<!-- It works, but not needed right now.
								<td class="col_task">
									<?php echo $log['task_name']; ?>
								</td>
								//-->

								<td class="col_todo">
									<?= $todo_col_content ?>
								</td>

								<td class="col_log">
									<?=
									$helperBase->getReportText(array(
										"id" => $log['id'],
										"content" => $log['log'],
										"title" => $log["todo_title"]))
									?>
								</td>
								<td class="col_date">
									<?php
									echo JHTML::_('date', $log['date'], $format);
									?>
								</td>
								<!-- It works, but not needed right now.
								<td class="col_planned_actual_hours" align="right">
									<?php
										echo $log['hours_plan'];
										//echo DateHelper::formatTimespan($log['hours_plan'], 'h:m');
									?>

								</td> -->
								<td class="col_actual_hours" align="right">
									<?php
									echo DateHelper::formatTimespan($log['duration'], 'h:m');
									?>
								</td>

								<?
							$n = $n+1;
							if (in_array($user->usertype, array("Manager", "Administrator", "Super Administrator"))) {
									?>
									<td class="col_hourly_rate" align="center">
										<?php echo $log['hourly_rate']; ?>
									</td>

								<!-- It works, but not needed right now.
									<td class="col_planned_cost" align="center">
										<?php
										echo round($log['hours_plan_price'], 2);
										?>
									</td> -->

									<td class="col_actual_cost" align="center">
										<?php
										echo round($log['hours_fact_price'], 2);
										$weekly_earnings += round($log['hours_fact_price'], 2);
										?>
									</td>
									<? if ($n == 1) { ?>
									<td rowspan="<?php echo count($week_logs['logs']); ?>" class="col_salary" align="center">
										<?php
										echo $this->report['salary'];
										?>
									</td>
									<? } ?>
								<? } ?>

								<td class="col_overhead_expenses" align="center">
									<?= (int) $log['money'] ?>
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
							<!-- It works, but not needed right now.
							<td class="col_task"></td>
							//-->
							<td class="col_todo"></td>
							<td class="col_log"></td>

							<td class="col_date" style="font-weight:bold;text-align:right;"></td>

						<!-- It works, but not needed right now.
						<td class="col_planned_actual_hours" style="font-weight:bold;text-align:right;">
								<?=
								round($week_logs['total'] / 60, 2);
								?>
							</td> -->

							<td class="col_actual_hours" style="font-weight:bold;text-align:right;">
								<?=
								round($week_logs['total'] / 60, 2);
								?>
							</td>

							<?
							if (in_array($user->usertype, array("Manager", "Administrator", "Super Administrator"))) {
								?>
								<td class="col_hourly_rate" ></td>
								<!-- It works, but not needed right now.
								<td class="col_planned_cost" ></td> -->
								<td class="col_actual_cost" style="font-weight:bold;">
									<?php
								//echo $week_logs['total_money']; // != 0 ? $week_logs['total_money'] : ""
								echo $weekly_earnings;
								$weekly_earnings = 0;
								?>
								</td>
								<td class="" style="font-weight:bold;"></td>
								<td class="" style="font-weight:bold;">
								<?php
								echo $week_logs['total_money']; // != 0 ? $week_logs['total_money'] : ""								
								?>
							</td>
							<? } ?>

							<td class="col_overhead_expenses"></td>
						</tr>

						<? if ($week_i >= sizeof($this->report['data'])) { ?>
							<tr style="font-size: 20px;">
								<td class="col_project"></td>
								<td class="col_task"></td>
								<td class="col_todo"></td>
								<!-- It works, but not needed right now.
								<td class="col_todo"></td>
								//-->
								<td class="col_log"></td>
								<td class="col_date"></td>
								<!-- It works, but not needed right now.
								<td class="col_planned_actual_hours" style="font-weight:bold;text-align:right;"></td>
								//-->
								<td class="col_actual_hours" style="font-weight:bold;text-align:right;"><?=
				round($total_amount / 60, 2)
							?></td>

								<?
							if (in_array($user->usertype, array("Manager", "Administrator", "Super Administrator"))) {
									?>
									<td class="col_hourly_rate" ></td>
									<!-- It works, but not needed right now.
									<td class="col_planned_cost"  style="font-weight:bold;text-align:center;"><?
					echo round($this->report['total_plan_price'], 2)
									?></td>
									//-->
									<td class="col_actual_cost" style="font-weight:bold;text-align:center;"><?
						echo round($this->report['total_fact_price'], 2)
									?></td>
 								<td class="col_salary"  style="font-weight:bold;text-align:center;">
									<?php echo (int) $this->report['salary'] ?>
								</td>
							<? } ?>



									<td class="col_overhead_expenses"  style="font-weight:bold;text-align:center;">
									<?php echo (int) $this->report['total_money'] ?>
								</td>

							
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