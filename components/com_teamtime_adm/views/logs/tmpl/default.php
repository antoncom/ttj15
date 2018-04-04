<?php defined('_JEXEC') or die('Restricted access');

	$format = JText::_( 'DATE_FORMAT_LC2' );

?>

<script language="javascript" type="text/javascript">
	function submitStateChange(pressbutton, id) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		$('cid').setProperty('value',id);
		submitform( pressbutton );
	}
</script>

<form action="index.php" method="post" name="adminForm">
<table>
	<tr>
		<td align="left" width="100%">
			<?php echo JText::_('Filter'); ?>:
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_('Reset'); ?></button>

		</td>
		<td nowrap="nowrap">
			<?php echo $this->lists['select_user']; ?>
		</td>
		<td nowrap="nowrap">
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
				<?php echo JHTML::_('grid.sort', 'Description', 'a.description', @$this->lists['order_Dir'], @$this->lists['order']); ?>
			</th>
			<th  class="title">
				<?php echo JHTML::_('grid.sort', 'User', 'user_name', @$this->lists['order_Dir'], @$this->lists['order']); ?>
			</th>
			<th  class="title">
				<?php echo JHTML::_('grid.sort', 'Project', 'project_name', @$this->lists['order_Dir'], @$this->lists['order']); ?>
			</th>
			<th  class="title">
				<?php echo JHTML::_('grid.sort', 'Task', 'task_name', @$this->lists['order_Dir'], @$this->lists['order']); ?>
			</th>
			<th  class="title">
				<?php echo JHTML::_('grid.sort', 'Duration', 'a.duration', @$this->lists['order_Dir'], @$this->lists['order']); ?>
			</th>
			<th  class="title">
				<?php echo JHTML::_('grid.sort', 'Date', 'a.date', @$this->lists['order_Dir'], @$this->lists['order']); ?>
			</th>
			<th width="1%" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort', 'ID', 'a.id', @$this->lists['order_Dir'], @$this->lists['order']); ?>
			</th>
			<th width="1%" nowrap="nowrap">
				<?php echo JText::_('Overhead expenses'); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="10">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count($this->items); $i < $n; $i++)
	{
		$row     = &$this->items[$i];
		$link    = JRoute::_('index.php?option='.$this->option.'&controller='.$this->controller.'&view=type&task=edit&cid[]='.$row->id);
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
				<span class="editlinktip hasTip" title="<?php echo JText::_('Edit Project');?>::<?php echo $row->description; ?>">
					<?php
					$des = $row->description;
					if(strlen($des) > 150){
						$des = substr($des, 0, 150);
						$des = $des . '...';
					}
					?>
					<a href="<?php echo $link  ?>"><?php echo $des; ?></a>
				</span>
			</td>
			<td>
				<?php echo $row->user_name; ?>
			</td>
			<td>
				<?php echo $row->project_name; ?>
			</td>
			<td>
				<?php echo $row->task_name; ?>
			</td>
			<td align="right">
				<?php echo DateHelper::formatTimespan($row->duration, 'h:m'); ?>
			</td>
			<td>
				<?php echo JHTML::_('date',  $row->date, $format); ?>
			</td>
			<td align="center">
				<?php echo $row->id; ?>
			</td>
			<td align="center">
				<?php echo $row->money != 0? $row->money : "" ?>
			</td>
		</tr>
		<?php
			$k = 1 - $k;
		}
		?>
	</tbody>
	</table>
</div>

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<?php echo JHTML::_('form.token'); ?>

</form>