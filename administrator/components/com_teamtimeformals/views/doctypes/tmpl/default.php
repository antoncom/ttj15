<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm">
<table>
	<tr>
		<td align="left" width="100%">
			<?php echo JText::_('Filter'); ?>:
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_type').value='';this.form.submit();"><?php echo JText::_('Reset'); ?></button>			
		</td>
		<td nowrap="nowrap">
			<?php echo $this->lists['select_type']; ?>
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
			<th  class="title" width="100%">
				<?php echo JHTML::_('grid.sort', 'Name', 'a.name', @$this->lists['order_Dir'], @$this->lists['order']); ?>
			</th>

			<th>
				<?php echo JText::_('Using in'); ?>
			</th>

			<th>
				<?php echo JText::_('FORMALS GENERATOR'); ?>
			</th>

			<th width="1%" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort', 'ID', 'a.id', @$this->lists['order_Dir'], @$this->lists['order']); ?>
			</th>			
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="8">
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
				<span class="editlinktip hasTip" title="<?php echo JText::_('Edit Doctype');?>::<?php echo $row->name; ?>">
					<a href="<?php echo $link  ?>"><?php echo JText::_($row->name); ?></a>
				</span>
			</td>

			<td align="left" style="padding-left:20px">
				<?=JText::_("Formal select ".$row->using_in. "s")?>
			</td>

			<td align="left" style="padding-left:20px">
				<?=$row->generator?>.php
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

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<?php echo JHTML::_('form.token'); ?>

</form>

