<?php
defined('_JEXEC') or die('Restricted access');

$user = & JFactory::getUser();
$colspan = 7;
$acl = new TeamTime_Acl();
?>

<form action="index.php" method="post" name="adminForm">
	<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_('Filter'); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_('Go'); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='';this.form.getElementById('from_period').value='';this.form.getElementById('until_period').value='';this.form.submit();"><?php echo JText::_('Reset'); ?></button>
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
				<?php echo $this->lists['select_state']; ?>
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
						echo JHTML::_('grid.sort', 'Name', 'a.name', @$this->lists['order_Dir'],
								@$this->lists['order']);
						?>
					</th>

					<th>
						<?php echo JText::_('Plan'); ?>
					</th>

					<th>
						<?php echo JText::_('Fact'); ?>
					</th>

					<th width="1%" nowrap="nowrap">
						<?php
						echo JHTML::_('grid.sort', 'ID', 'a.id', @$this->lists['order_Dir'], @$this->lists['order']);
						?>
					</th>

					<?
					if ($acl->isAdmin() || $acl->isSuperAdmin()) {
						$colspan = 8;
						?>
						<th width="1%" nowrap="nowrap">
							<?php
							echo JHTML::_('grid.sort', 'Rate', 'a.rate', @$this->lists['order_Dir'],
									@$this->lists['order']);
							?>
						</th>
					<? } ?>

					<th width="1%" >
						<?php
						echo JHTML::_('grid.sort', 'State', 'a.state', @$this->lists['order_Dir'],
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
					$row = &$this->items[$i];
					$link = JRoute::_('index.php?option=' . $this->option .
									'&controller=' . $this->controller . '&task=edit&cid[]=' . $row->id);
					$checked = JHTML::_('grid.id', $i, $row->id);
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td>
							<?php echo $this->pagination->getRowOffset($i); ?>
						</td>
						<td align="center">
							<?php echo $checked; ?>
						</td>
						<td width="100%">
							<span class="editlinktip hasTip"
										title="<?php echo JText::_('Edit Project'); ?>::<?php echo $row->name; ?>">
								<a href="<?php echo $link ?>"><?php echo $row->name; ?></a>
							</span>
						</td>

						<td align="right" style="padding-right:10px">
							<?=
							TeamTime::helper()->getCalendar()->getLink(
									round((float) $row->splan, 2),
									"project_id={$row->id}&view_type=" .
									$this->calendar_viewname . $this->calendar_filter)
							?>
						</td>

						<td align="right" style="padding-right:10px">
							<?=
							round((float) $row->sfact, 2)
							?>
						</td>

						<td align="center">
							<?php echo $row->id; ?>
						</td>

						<?
						if ($acl->isAdmin() || $acl->isSuperAdmin()) {
							?>
							<td align="center">
								<?php echo $row->rate; ?>
							</td>
						<? } ?>

						<td align="center">
							<?php
							echo JHTML::_('teamtime.projectstatelist', array(), "state" . $row->id,
									'class="inputbox" onchange="submitStateChange(\'setState\', ' . $row->id . ');"', 'value',
									'text', $row->state);
							?>
						</td>

					</tr>
					<?php
					$k = 1 - $k;
				}
				?>
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

<script>
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
</script>