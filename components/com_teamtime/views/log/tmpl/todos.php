<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$todosTableColspan = 2;
$noTodosTableColspan = 4;
if ($this->showProcessColumn) {
	$todosTableColspan++;
	$noTodosTableColspan++;
}
?>

<form id="todos-form" method="post" action="<?php echo JRoute::_('index.php'); ?>">
  <div>
    <table cellpadding="0" cellspacing="0" class="todo_table" width="100%">
      <col width="100%" />
      <col  />
      <col  />
      <col  />
      <tr>
        <th class="first-column">
          <span><?= JText::_("Todo") ?></span>

					<?= $this->lists["select_state"] ?>

					<?= $this->lists["select_period"] ?>

          <input id="filter_stodo" name="filter_stodo" type="text" size="20"
                 value="<?= $this->escape($this->filter_stodo) ?>"/>
        </th>

				<? if ($this->showProcessColumn) { ?>
					<th class="process-column" align="right" nowrap><?= JText::_("Process") ?></th>
				<? } ?>

        <th nowrap>
          <span><?= JText::_("Date") ?></span>&nbsp;<?= $this->lists["select_date"] ?>
        </th>

				<th nowrap>
          <span><?= JText::_("Project") ?></span>&nbsp;<input
            id="filter_sproject" name="filter_sproject" type="text"
            value="<?= $this->escape($this->filter_sproject) ?>"/>
        </th>

        <th><span><?= JText::_("FACT_STR") ?>/<?= JText::_("PLAN_STR") ?></span></th>
      </tr>

			<?php
			if ($this->todos) {

				$__model_todo = new TeamtimeModelTodo();
				list($__todo_fhours, $__todo_phours, $this->todos) = $__model_todo->process_todos($this->todos);

				foreach ($this->todos as $row) {
					if ($row->tmp_repeat_date != "") {
						$__todo_tmp_date = $row->tmp_repeat_date;
						$__todo_tmp_sdate = $__todo_tmp_date;
					}
					else {
						$__todo_tmp_date = $row->created;
						$__todo_tmp_sdate = "";
					}

					$date = JFactory::getDate($__todo_tmp_date);
					?>

					<tr <?= $row->tmp_checked ? 'class="active_row"' : '' ?>>
						<td class="first-column">
							<div class="chkbx<?= $row->tmp_checked ? '_checked' : '' ?>"
									 title="<?=
			implode(" / ", array_filter(array($row->created, $row->tmp_repeat_date)))
					?>"></div>
							<span><a href="javascript:;"><?= $row->title ?></a></span>
							<input id="todo-<?php echo $row->id; ?>"
										 type="hidden" name="todo[<?php echo $row->id . "_" . $__todo_tmp_sdate; ?>]"
										 value="<?= $row->tmp_checked ?>"/>
										 <?
										 //	onclick="set_todo_id(=$row->id);"
										 if (isset($current_todo_id) && $current_todo_id == $row->id) {
											 // ini selected todo data
											 $current_todo_state = $row->state;
										 }
										 ?>
						</td>

						<? if ($this->showProcessColumn) { ?>
							<td class="process-column" align="right">

								<? if ($row->processName != "") { ?>

									<a target="_blank" href="<?= $row->processUrl ?>"
										 title="<?= $this->escape($row->processName) ?>"><img
											alt="<?= $this->escape($row->processName) ?>" 
											title="<?= $this->escape($row->processName) ?>"
											src="<?= JURI::root(true) ?>media/com_teamtimebpm/assets/images/esk_icon.png"></a>

								<? } ?>

							</td>
						<? } ?>

						<td nowrap><span class="todo_date"><?= $date->toFormat('%a') ?>,
								<?= $date->toFormat('%e %B %Y') ?></span></td>
						<td nowrap><span class="pr_name"><?= $row->project_name ?></span></td>
						<td nowrap><span class="hours_plan"><?=
						sprintf("%.2f", $row->hours_fact)
								?> / <?= sprintf("%.2f", $row->hours_plan) ?></span></td>
					</tr>

				<? } ?>

				<tr class="result_row">
					<td style="border-right:none;">&nbsp;</td>
					<td colspan="<?= $todosTableColspan ?>" align="right"><span
							class="captioncell"><?= JText::_("TOTAL_HOURS_STR") ?>:</span></td>
					<td><span><?=
			sprintf("%.2f", $__todo_fhours)
				?> / <?= sprintf("%.2f", $__todo_phours) ?></span></td>
				</tr>

				<?
			}
			else {
				?>

				<tr class="result_row">
					<td style="border-right:none; text-align:left"
							colspan="<?= $noTodosTableColspan ?>" align="left">
						<span class="captioncell"><?= JText::_('No Tasks assigned') ?></span></td>
				</tr>

			<? } ?>
    </table>

  </div>

  <input type="hidden" value="com_teamtime" name="option"/>
  <input type="hidden" value="log" name="view"/>
  <input type="hidden" value="updatetodos" name="task"/>
  <input type="hidden" value="raw" name="format"/>
	<?php echo JHTML::_('form.token'); ?>
</form>
