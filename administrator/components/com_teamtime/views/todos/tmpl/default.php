<?php
defined('_JEXEC') or die('Restricted access');

$format = JText::_('DATE_FORMAT_LC1');

$user = & JFactory::getUser();
$colspan = 11;
?>

<style>
	.todo_parent a {
		font-size:12px;
		font-weight: bold;
		color:#034E7F;
		letter-spacing:1px;
	}

	.parent_sum {
		color: #972B2B;
		font-weight: bold;
	}

	#project_id, #type_id, #task_id {
		width:180px;
	}

</style>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_('Filter'); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_('Go'); ?></button>
				<button id="adminForm_reset"><?php echo JText::_('Reset'); ?></button>
				<p>
				<div class="select-date">
					<?php echo $this->lists['select_date']; ?>
					<?php
					echo JHTML::_('calendar', $this->from_period, 'from_period', 'from-period',
							JText::_('DATE_FORMAT_MYSQL_WITHOUT_TIME'));
					?>
					<?php
					echo JHTML::_('calendar', $this->until_period, 'until_period', 'until-period',
							JText::_('DATE_FORMAT_MYSQL_WITHOUT_TIME'));
					?>
				</div>
			</td>

			<td nowrap="nowrap">
				<?php echo $this->lists['select_project']; ?>&nbsp;
				<?php echo $this->lists['select_type']; ?>&nbsp;
<?php echo $this->lists['select_task']; ?>&nbsp;

				<br>
				<br>

				<?php echo $this->lists['levellist']; ?>&nbsp;
				<?php echo $this->lists['select_user']; ?>&nbsp;
<?php echo $this->lists['select_state']; ?>&nbsp;

			</td>
		</tr>
	</table>
	<div id="tablecell">
		<table class="adminlist">
			<thead>
				<tr>
					<th width="5">
<?php echo JText::_('NUM'); ?>
					</th>
					<th width="20">
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
					</th>
					<th  class="title">
						<?php
						echo JHTML::_('grid.sort', 'Title', 'a.title', @$this->lists['order_Dir'],
								@$this->lists['order']);
						?>
					</th>
					<th width="1%" nowrap="nowrap">
						<?php
						echo JHTML::_('grid.sort', 'Plan', 'a.hours_plan', @$this->lists['order_Dir'],
								@$this->lists['order']);
						?>
					</th>
					<th width="1%" nowrap="nowrap">
						<?php echo JText::_('Fact'); ?>
					</th>
					<th  class="title">
<?php
echo JHTML::_('grid.sort', JText::_('Assigned To'), 'b.name', @$this->lists['order_Dir'],
		@$this->lists['order']);
?>
					</th>
					<th  class="title">
						<?php
						echo JHTML::_('grid.sort', 'State', 'a.state', @$this->lists['order_Dir'],
								@$this->lists['order']);
						?>
					</th>
					<th width="1%" nowrap="nowrap">
						<?php
						echo JHTML::_('grid.sort', 'ID', 'a.id', @$this->lists['order_Dir'], @$this->lists['order']);
						?>
					</th>

					<th class="title">
					<?php
					echo JHTML::_('grid.sort', 'Date', 'a.created', @$this->lists['order_Dir'],
							@$this->lists['order']);
					?>
					</th>

						<?
						if (!in_array($user->usertype, array("Manager"))) {
							$colspan = 12;
							?>
						<th class="title">
	<?php
	echo JHTML::_('grid.sort', JText::_('Hour Price'), 'a.hourly_rate', @$this->lists['order_Dir'],
			@$this->lists['order']);
	?>
						</th>

						<th class="title">
							<?php echo JText::_('Price-Plan'); ?>
						</th>
						<? } ?>

					<th class="title">
<?php
echo JHTML::_('grid.sort', JText::_('Overhead Expenses'), 'a.costs', @$this->lists['order_Dir'],
		@$this->lists['order']);
?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="<?= $colspan ?>">
				<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php
				$k = 0;
				for ($i = 0, $n = count($this->items); $i < $n; $i++) {
					$tree_row = &$this->items[$i];
					$row = &$this->items[$i]->data;
					$link = JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&cid[]=' . $row->id);
					$checked = JHTML::_('grid.id', $i, $row->id);
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td>
							<?php echo $this->pagination->getRowOffset($i); ?>
						</td>

						<td align="center">
							<?php echo $checked; ?>
						</td>

						<td>
	<? if ($tree_row->children) { ?>
								<span class="editlinktip hasTip todo_parent"
											title="<?php //echo JText::_('Edit Todo'); ?>::<?php //echo $row->description;  ?>">
								<?= $tree_row->treename ?>
									<a href="<?php echo $link ?>">[<?= $row->title; ?>]</a>
								</span>
											<?
											}
											else {
												?>
								<span class="editlinktip hasTip"
											title="<?php //echo JText::_('Edit Todo'); ?>::<?php //echo $row->description;  ?>">
		<?= $tree_row->treename ?>
									<a href="<?php echo $link ?>"><?= $row->title; ?></a>
								</span>
							<? } ?>
						</td>

						<td align="center" nowrap>
							<? if ($tree_row->children) { ?>
								<span class="parent_sum"><?= $row->todo_hours_plan ?> [<?= $row->hours_plan ?>]</span>
	<?
	}
	else {
		?>
								<?= $row->hours_plan ?>
							<? } ?>
						</td>

						<td align="center">
							<? if ($row->hours_fact) { ?>

								<? if ($tree_row->children) { ?>
									<span class="parent_sum"><?= $row->todo_hours_fact ?> [<?= $row->hours_fact ?>]</span>
								<?
								}
								else {
									?>
									<?= $row->hours_fact ?>
		<? } ?>

							<? } ?>
						</td>

						<td>
							<?php echo $row->username; ?>
						</td>
						<td align="center">
							<?php
							echo JHTML::_('teamtime.todostatelist', array(), "state" . $row->id,
									'class="inputbox" onchange="submitStateChange(\'setState\', ' . $row->id . ');"', 'value',
									'text', $row->state);
							?>
						</td>
						<td align="center">
							<?php echo $row->id; ?>
						</td>

						<td width="80">
						<?php
						echo!$row->is_repeated ?
								str_replace(", ", ",<br>", JHTML::_('date', $row->created, $format)) :
								$row->repeat_params_str;
						?>
						</td>

							<? if (!in_array($user->usertype, array("Manager"))) { ?>
							<td width="60" align="center">
								<?php echo $row->hourly_rate; ?>
							</td>

							<td width="60" align="center">
							<? if ($tree_row->children) { ?>
									<span class="parent_sum"><?= $row->todo_hours_plan_costs ?> [<?= $row->hours_plan_costs ?>]</span>
		<?
		}
		else {
			?>
									<?= $row->hours_plan_costs ?>
								<? } ?>
							</td>
							<? } ?>

						<td width="60" align="center">
					<? if ($tree_row->children) { ?>
								<span class="parent_sum"><?= $row->todo_costs ?> [<?= $row->costs ?>]</span>
					<?
					}
					else {
						?>
		<?= $row->costs ?>
	<? } ?>
						</td>
					</tr>
	<?php
	$k = 1 - $k;
}
?>
				<tr>
					<td></td>
					<td></td>
					<td>
						<b><?php echo JText::_('Sum plan and fact'); ?></b>
					</td>

					<td>
						<b><?=
round($this->total_hours[0], 2)
?></b>
					</td>

					<td>
						<b><?= round($this->total_hours[1], 2) ?></b>
					</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>

<?
if (!in_array($user->usertype, array("Manager"))) {
	?>
						<td></td>
						<td align="center"><b><?= round($this->total_hours[2], 2)
	?></b></td>
<? } ?>

					<td align="center"><b><?= round($this->total_hours[3], 2) ?></b></td>
				</tr>
			</tbody>
		</table>
	</div>
	<input type="hidden" id="state_change_id" name="state_change_id" value="" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<?php echo JHTML::_('form.token'); ?>

</form>

<script type="text/javascript">

	function submitStateChange(pressbutton, id) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		$('state_change_id').setProperty('value',id);
		submitform( pressbutton );
	}

	window.addEvent('domready', function(){
		$('period').addEvent('change', function(){
			var from  = $('from-period');
			var until = $('until-period');
			switch (this.value) {
<?php
foreach ($this->date_presets as $name => $value) {
	$case = "case '" . $name . "':\n";
	$case .= "from.setProperty('value', '" . $value['from'] . "');\n";
	$case .= "until.setProperty('value', '" . $value['until'] . "');\n";
	$case .= "break;\n";
	echo $case;
}
?>
			}
			document.adminForm.submit();
		});
	});

	jQuery(function ($) {

		$('#adminForm_reset').click(function () {

			$('#search').val('');
			$('#from-period').val('');
			$('#until-period').val('');

			$('#filter_user_id')[0].selectedIndex = 0;
			$('#filter_state')[0].selectedIndex = 0;
			$('#project_id')[0].selectedIndex = 0;
			$('#type_id')[0].selectedIndex = 0;
			$('#task_id')[0].selectedIndex = 0;

			$('#adminForm').submit();

		});

	});

</script>