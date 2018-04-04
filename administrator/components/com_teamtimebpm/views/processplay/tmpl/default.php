<?php
defined('_JEXEC') or die('Restricted access');
?>

<style>
	@import url("/<?= URL_MEDIA_COMPONENT_ASSETS . 'css/operations.css' ?>");
</style>

<div class="win">
	<?= $this->process->name ?><br><span>Сфера: <?= $this->process->space_name ?></span>
</div>
<div class="tableContainer">
	<table>
		<colgroup>
			<col class="col-one">
			<col class="col-two">
			<col class="col-three">
			<col class="col-four">
			<col class="col-five">
		</colgroup>
		<thead>
			<tr>
				<th>
					Дата
				</th>
				<th>
					Название операции
				</th>
				<th>
					Часы
				</th>
				<th>
					Цена
				</th>
				<th>
					Сотрудник
				</th>
			</tr>
		</thead>
	</table>
	<div class="container">
		<div class="content">
			<table>
				<colgroup>
					<col class="col-one">
					<col class="col-two">
					<col class="col-three">
					<col class="col-four">
					<col class="col-five">
				</colgroup>
				<tbody>

					<? foreach ($this->todos as $i => $todo) { ?>

						<tr class="<?= ($i == 0)
								? "first" : "" ?>">
							<td class="date">
								<div class="date-num"><?=
					JHTML::_('date', $todo->created, "%d")
					?></div>
								<div class="date-month-year">
									<?=
									JText::_("STR_MONTH" . (int) JHTML::_('date', $todo->created, "%m"))
									?><br>
	<?= JHTML::_('date', $todo->created, "%Y") ?></div>
							</td>
							<td>
								<span
	<? if ($todo->project_name) { ?>
										class="hasTip"
										title="<?= $this->escape($todo->project_name) ?> / <?= $this->escape($todo->type_name) ?>"
	<? } ?>
									>
									<a href="#"><?= $todo->title ?></a>
								</span>
							</td>
							<td>
								<?=
								number_format($todo->hours_plan, 2, ",", "")
								?>
							</td>
							<td>
	<?= round($todo->price) ?>
							</td>
							<td class="date">
	<?= $todo->user_name ?>
							</td>
						</tr>

<? } ?>

				</tbody>
			</table>
		</div>
	</div>
	<table>
		<colgroup>
			<col class="col-one">
			<col class="col-two">
			<col class="col-three">
			<col class="col-four">
			<col class="col-five">
		</colgroup>
		<tfoot>
			<tr>
				<td class="date">
					<span>Всего</span>
				</td>
				<td>
					операций - <span><?= $this->todosStat->totalOperations ?></span>
				</td>
				<td>
					<span><?=
number_format($this->todosStat->totalHoursPlan, 2, ",", "")
?></span>
				</td>
				<td>
					<span><?=
number_format($this->todosStat->totalPrice, 0, ",", " ")
?></span>
				</td>
				<td class="date">
					сотрудников - <span><?= $this->todosStat->totalUsers ?></span>
				</td>
			</tr>
		</tfoot>
	</table>
</div>
<div class="win">

<? if ($this->process->is_started) { ?>

		<div id="btnStartPlay" class="end">
			Отмена
		</div>

		<?
	}
	else {
		?>

		<div id="btnStartPlay" class="start">
			Запуск
		</div>

<? } ?>
</div>