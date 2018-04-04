<?php defined('_JEXEC') or die('Restricted access');
?>

<form action="index.php" method="post" name="adminForm">
	<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_('Filter'); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_('Go'); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_('Reset'); ?></button>
				<p>
				<div class="select-date">
					<?php echo $this->lists['select_date']; ?>
					<?php //echo JHTML::_('calendar', $this->from_period, 'from_period', 'from-period', JText::_('DATE_FORMAT_MYSQL_WITHOUT_TIME')); ?>
					<?php //echo JHTML::_('calendar', $this->until_period, 'until_period', 'until-period', JText::_('DATE_FORMAT_MYSQL_WITHOUT_TIME')); ?>
				</div>
			</td>
			<td nowrap="nowrap">
				<?= $this->edit_variables ?>
				<?php echo $this->lists['select_using']; ?>
				<?php echo $this->lists['select_project']; ?>
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
						echo JHTML::_(
								'grid.sort', 'Variable Name', 'a.name', @$this->lists['order_Dir'],
								@$this->lists['order']);
						?>
					</th>

					<th>
						<?php
						echo JHTML::_(
								'grid.sort', 'Variable Tagname', 'a.tagname',
								@$this->lists['order_Dir'], @$this->lists['order']);
						?>
					</th>

					<th>
						<?php echo JText::_('Using in'); ?>
					</th>

					<th>
						<?php echo JText::_('Formal Input Size'); ?>
					</th>

					<th width="1%" nowrap="nowrap">
						<?php
						echo JHTML::_(
								'grid.sort', 'ID', 'a.id', @$this->lists['order_Dir'],
								@$this->lists['order']);
						?>
					</th>

				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="7">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php
				$k = 0;
				for ($i = 0, $n = count($this->items); $i < $n; $i++) {
					$row = &$this->items[$i];
					$link = JRoute::_('index.php?option=' . $this->option
									. '&controller=' . $this->controller . '&task=edit&cid[]=' . $row->id);
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
							<span class="editlinktip hasTip"
										title="<?php echo JText::_('Edit Variable'); ?>::<?php echo $row->name; ?>">
								<a href="<?php echo $link ?>"><?php echo $row->name; ?></a>
							</span>
						</td>

						<td align="center" style="padding-right:10px">
							{<?= $row->tagname ?>}
						</td>

						<td align="center" style="padding-right:10px">
							<?=
							$this->doctypeModel->getUsingLabel(
									$this->doctypeModel->getUsingType($row->using_in))
							?>
						</td>

						<td align="center" style="padding-right:10px" nowrap="nowrap">
							<?= $row->xsize ?> x <?= $row->ysize ?>
						</td>

						<td align="center">
							<?php echo $row->id; ?>
						</td>


					</tr>
					<?php
					$k = 1 - $k;
				}
				?>
			</tbody>
		</table>
	</div>

	<!--input type="hidden" id="filter_using" name="filter_using" value="" /-->
	<input type="hidden" id="state_change_id" name="state_change_id" value="" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_('form.token'); ?>

</form>

<script language="javascript" type="text/javascript">

	jQuery(function ($) {

		var url = "index.php?option=<?= $this->option ?>&controller=<?= $this->controller ?>&task=loadusings";

		var loadUsings = function () {
			var currentType = $("#filter_project option:selected").val();

			$.get(url, {
				filter_using: $("#filter_using option:selected").val()
			},
			function (data) {
				$("#filter_project").html(data);

				$("#filter_project option").each(function (i, n) {
					if ($(n).val() == currentType) {
						$(n).attr("selected", true);
					}
				});
			});
		};

		$("#filter_using").change(function () {
			loadUsings();
		});

		loadUsings();

	});

	function submitStateChange(pressbutton, id) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		$('state_change_id').setProperty('value', id);
		submitform( pressbutton );
	}
</script>

<?