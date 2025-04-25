
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

				$todoModel = new TeamtimeModelTodo();

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
							$todo_col_content = '<a href="' . $descriptionLoaderUrl
									. "&todo_id=" . $log["todo_id"] . "&project_id=" . $log["project_id"]
									. '" class="fancybox">' . $log['todo_title'] . '</a>';
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
											TeamTime::helper()->getCalendar()->getLink(
													round((float) $log["hours_plan"], 2),
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
								<? //= $log['project_name']   ?>
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
								<?=
								$helperBase->getReportText(array(
									"id" => $log['id'],
									"content" => $log['log'],
									"title" => $log["todo_title"]))
								?>
							</td>

							<td class="col_planned_actual_hours" align="right" nowrap>
								<? if ($todo_id != "" && $show_todo) { ?>
									<?=
									TeamTime::helper()->getCalendar()->getLink(
											round((float) $log["hours_plan"], 2),
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
