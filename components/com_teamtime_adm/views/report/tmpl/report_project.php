<?php
defined('_JEXEC') or die('Restricted access');

//JHTML::script('FusionCharts.js',
//		'administrator/components/com_teamtime/libraries/fusioncharts/charts/');

$format = JText::_('DATE_FORMAT_LC1');

$view_type = "month";
if (isset($this->date_select["name"])) {
	if (stripos($this->date_select["name"], "day") !== false &&
			stripos($this->date_select["name"], "days") === false) {
		$view_type = "day";
	}
	else if (stripos($this->date_select["name"], "week") !== false) {
		$view_type = "week";
	}
}

$user = & JFactory::getUser();
$isAdminPage = strpos(JURI::base(), "/administrator/") !== false;

$descriptionLoaderUrl =
		JURI::root() . "index.php?option=com_teamtime&controller=reports&task=load_description";
?>

<style>
  table.adminlist tbody tr.row_todo2 td{
    background-color: #e5e5e5;
  }

  table.adminlist tbody tr.row_untitled td{
    background-color: #EFEFE0;
  }
	
	table.adminlist tbody tr.row_todo2 td.col_date {
		font-weight: bold;
	}
</style>

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
  <h2><?=
($this->from_period && $this->until_period) ?
				JHTML::_('date', $this->from_period, $format) . ' - ' .
				JHTML::_('date', $this->until_period, $format) :
				JText::_('No Period Specified');
?></h2>
</div>
<?php if (sizeof($this->report['data'])) { ?>

	<div class="project-report-charts" style="overflow:hidden;">
		<div style="width:50%; float:left;">
			<?php //$this->type_chart->renderChart();    ?>
		</div>
		<div style="width:50%; float:left;">
			<?php //$this->user_chart->renderChart();    ?>
		</div>
	</div>

	<table class="project-report-charts">
		<tr>
			<th align="center" style="text-align:center;" ><?= JText::_("Type stats") ?></th>
			<th align="center" style="text-align:center;" ><?= JText::_("User stats") ?></th>
		</tr>
		<tr>
			<td <?=
		$isAdminPage ? 'align="center"' : ''
			?> width="50%">
					<?= $this->typeChart ?>
			</td>
			<td <?=
				$isAdminPage ? 'align="center"' : ''
					?> width="50%">
					<?= $this->userChart ?>
			</td>
		</tr>
	</table>

	<div class="project-report-stats">
		<table class="adminlist">
			<thead>
				<tr>
					<th class="col_date">
						<?php echo JText::_('Date'); ?>
					</th>

					<th class="col_project">
						<?php echo JText::_('Project'); ?>
					</th>

					<th class="col_type">
						<?php echo JText::_('Type'); ?>
					</th>

					<th class="col_task">
						<?php echo JText::_('Task'); ?>
					</th>

					<th class="col_todo">
						<?php echo JText::_('Todo'); ?>
					</th>

					<th class="col_log">
						<?php echo JText::_('Log'); ?>
					</th>

					<th class="col_planned_actual_hours">
						<?php echo JText::_('PLANNED_ACTUAL_HOURS_OF_TODO'); ?>
					</th>

					<th class="col_actual_hours" style="width:100px;">
						<?php echo JText::_('ACTUAL_HOURS'); ?>
					</th>

					<?
					if (!in_array($user->usertype, array("Manager"))) {
						?>
						<th class="col_hourly_rate" style="width:80px;" align="center">
							<?php echo JText::_('Hourly rate'); ?>
						</th>

						<th class="col_planned_cost" style="width:80px;" align="center">
							<?php echo JText::_('Planned Cost'); ?>
						</th>

						<th class="col_actual_cost" style="width:80px;" align="center">
							<?php echo JText::_('Actual Cost'); ?>
						</th>
					<? } ?>

					<th class="col_statement_cost" style="width:80px;" align="center">
						<?php echo JText::_('Statement Cost'); ?>
					</th>

					<th class="col_overhead_expenses" style="width:100px;">
						<?php echo JText::_('OVERHEAD_EXPENSES'); ?>
					</th>

					<th class="col_user">
						<?php echo JText::_('User'); ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php
				$k = 0;
				$todo_id = 0;

				$show_type = true;
				$show_task = true;

				$todoModel = new TodoModelTodo();

				foreach ($this->report['data'] as $todo_id => $todo_data) {					
					$show_todo = true;
					$actual_hours = 0;
					$actual_costs = 0;
					$actual_money = 0;

					foreach ($todo_data as $log) {
						$actual_hours += $log["duration"];
						$actual_costs += $log["hours_fact_price"];
						$actual_money += (float) $log['money'];
					}

					foreach ($todo_data as $log) {

						$has_descr = $todoModel->isEditedDescription($log['todo_description']);
						$todo_col_content = "";

						if ($has_descr) {
							/* $todo_col_content = '<a href="javascript:void(0);"
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

						// display todo title as long column
						if ($todo_id != "" && sizeof($todo_data) > 0 && $show_todo) {
							?>

							<tr class="<?php echo "row$k"; ?> row_todo2">

								<td class="col_date">
									<?=
									JHTML::_('date', $log['todo_date'], $format)
									?>
								</td>

								<td class="col_project">
									<?= $log['project_name'] ?>
								</td>

								<td class="col_type"><b>
										<?php
										echo ($show_type ? $log['type_name'] : null);
										//$show_type = false;
										?></b>
								</td>

								<td class="col_task">
									<b><?php
						echo ($show_task ? $log['task_name'] : null);
						//$show_task = false;
										?></b>
								</td>

								<td class="col_todo2" colspan="2"><b><?= $todo_col_content ?></b></td>

								<td class="col_planned_actual_hours" align="right" nowrap><b>
										<? if ($show_todo) { ?>
											<?=
											TeamTime::_("Calendar_getLink", round((float) $log["hours_plan"], 2),
													"project_id={$this->project_id}&type_id={$log["type_id"]}&" .
													"task_id={$log["task_name"]}&view_type=" . $view_type . $filter_date)
											?>
										<? } ?></b>
								</td>

								<td class="col_actual_hours" align="right">
									<b><?=
						round($actual_hours / 60, 2);
										?></b>
								</td>

								<?
								if (!in_array($user->usertype, array("Manager"))) {
									?>
									<td class="col_hourly_rate" align="center">
										<b><?= $log['hourly_rate'] ?></b>
									</td>

									<td class="col_planned_cost" align="center">
										<b><?=
					$show_todo ? round($log['hours_plan_price'], 2) : ""
									?></b>
									</td>

									<td class="col_actual_cost" align="center">
										<b><?=
						round($actual_costs, 2)
									?></b>
									</td>
								<? } ?>

								<td class="col_statement_cost" align="center">
									<b><?=
				$show_todo ? round($log["hours_statement_price"], 2) : ""
								?></b>
								</td>

								<td class="col_overhead_expenses" align="center">
									<b><?= round((float) $log['costs']) ?> / <?= round($actual_money) ?></b>
								</td>

								<td class="col_user"></td>
							</tr>

							<?
							$show_todo = false;
						}
						?>

						<tr class="<?= $log['todo_title'] == "" ? "row_untitled" : "row$k" ?>">

							<td class="col_date">
								<?php
								echo JHTML::_('date', $log['date'], $format);
								?>
							</td>

							<td class="col_project">
								<? //= $log['project_name'] ?>
							</td>

							<td class="col_type">
								<?php
								//echo ($show_type ? $log['type_name'] : null);
								//$show_type = false;
								?>
							</td>

							<td class="col_task">
								<?php
								//echo ($show_task ? $log['task_name'] : null);
								//$show_task = false;
								?>
							</td>

							<td class="col_todo">
								<? if ($show_todo) { ?>
									<?= $todo_col_content ?>
								<? } ?>
							</td>

							<td class="col_log">
								<?php echo convert_to_links($log['log']); ?>
							</td>

							<td class="col_planned_actual_hours" align="right" nowrap>
								<? if ($todo_id != "" && $show_todo) { ?>
									<?=
									TeamTime::_("Calendar_getLink", round((float) $log["hours_plan"], 2),
											"project_id={$this->project_id}&type_id={$log["type_id"]}&" .
											"task_id={$log["task_name"]}&view_type=" . $view_type . $filter_date)
									?>
								<? } ?>
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
									<?= $log['hourly_rate'] ?>
								</td>

								<td class="col_planned_cost" align="center">
									<?=
									($todo_id != "" && $show_todo) ?
													round($log['hours_plan_price'], 2) : ""
									?>
								</td>

								<td class="col_actual_cost" align="center">
									<?=
									round($log['hours_fact_price'], 2)
									?>
								</td>
							<? } ?>

							<td class="col_statement_cost" align="center">
								<b><?=
				($todo_id != "" && $show_todo) ?
								round($log["hours_statement_price"], 2) : ""
							?></b>
							</td>

							<td class="col_overhead_expenses" align="center">
								<?= round((float) $log['money']) ?>
							</td>

							<td class="col_user">
								<?php echo $log['username']; ?>
							</td>
						</tr>
						<?
						$show_todo = false;
						$k = 1 - $k;
					}
				}
				?>
			</tbody>

			<tfoot>
				<tr>
					<td class="col_date">
					</td>

					<td class="col_project">
					</td>

					<td class="col_type">
					</td>

					<td class="col_task">
					</td>

					<td class="col_todo">
					</td>

					<td class="col_log">
					</td>

					<td  class="col_planned_actual_hours" style="font-weight:bold;text-align:right;">
						<?=
						round($this->report['total_plan'] / 60, 2);
						?>
					</td>

					<td class="col_actual_hours" style="font-weight:bold;text-align:right;">
						<?=
						round($this->report['total'] / 60, 2);
						?>
					</td>

					<?
					if (!in_array($user->usertype, array("Manager"))) {
						?>
						<td class="col_hourly_rate">
						</td>

						<td class="col_planned_cost" style="font-weight:bold;text-align:center;">
							<?=
							round($this->report['total_plan_price'], 2)
							?>
						</td>

						<td class="col_actual_cost" style="font-weight:bold;text-align:center;">
							<?=
							round($this->report['total_fact_price'], 2)
							?>
						</td>
					<? } ?>

					<td class="col_statement_cost" style="font-weight:bold;text-align:center;">
						<?=
						round($this->report['total_statement_price'], 2)
						?>
					</td>

					<td class="col_overhead_expenses" style="font-weight:bold;text-align:center;">
						<?= (int) $this->report['total_costs'] ?> / <?=
					(float) round($this->report['total_money'], 2)
						?>
					</td>

					<td class="col_user"></td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php
}
else {
	echo JText::_('No Entries Found');
}?>