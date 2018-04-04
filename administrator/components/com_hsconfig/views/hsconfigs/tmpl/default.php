<?php defined('_JEXEC') or die('Restricted access'); ?>
<form action="index.php" method="post" name="adminForm">
<div id="tablecell">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="2%">
				<?php echo JText::_( 'Num' ); ?>
			</th>
			<th width="2%">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows ); ?>);" />
			</th>
			<th width="20%">
				<?php echo JText::_( 'Configuration' ); ?>
			</th>
			<th width="2%" align="center">
				<?php echo JText::_( 'Published' ); ?>
			</th>
			<th width="2%">
				<?php echo JText::_( 'ID' ); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
	<tr>
		<td colspan="5">
			<?php echo $this->page->getListFooter(); ?>
		</td>
	</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	$config	=& JFactory::getConfig();
	$db		=& JFactory::getDBO();
	$nullDate = $db->getNullDate();

	for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
	{
		$row = &$this->rows[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_hsconfig&controller=hsconfig&task=edit&cid[]='. $row->id );
		$times		= '';

		if ($row->modified != $nullDate)
		{
			$mod =& JFactory::getDate($row->modified);
			$mod->setOffset($config->getValue('config.offset'));
			$times .= JText::_( 'Modified' ) .": ". $mod->toFormat();
		}

		if ($row->publish_tmst != $nullDate)
		{
			$pub =& JFactory::getDate($row->publish_tmst);
			$pub->setOffset($config->getValue('config.offset'));
			$times .= "<br />". JText::_( 'Published' ) .": ". $pub->toFormat();
		}

		if ($row->id == -1)
		{
			$row->title = JText::_('Site Configuration');
		}

		if ( $row->published == 1 )
		{
			$img = 'publish_g.png';
			$alt = JText::_( 'Published' );
			if ( $row->modified == $nullDate
			   ||$row->publish_tmst == $nullDate
			   ||$mod->toUNIX() > $pub->toUNIX()
			   )
			{
				$img = 'publish_y.png';
				$alt = JText::_('Published, Not Current');
				$times .= "<br />". JText::_('Not Current');
			}
			else
			{
				$times .= "<br />". JText::_('Current');
			}
		}
		else
		{
			$img = 'publish_x.png';
			$alt = JText::_( 'Unpublished' );
			$times .= "<br />". JText::_('Unpublished');		}
		?>

		<tr class="<?php echo "row$k"; ?>">
			<td align="center">
			<?php echo $this->page->getRowOffset( $i ); ?>
			</td>
			<td align="center">
			<?php echo $checked; ?>
			</td>
			<td>
			<a href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
			</td>
			<td align="center">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'Publish Information' );?>::<?php echo htmlspecialchars( $times ); ?>"><a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $row->published ? 'unpublish' : 'publish' ?>')">
					<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /></a></span>
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
<?php JHTML::_('hsconfig.legend'); ?>
</div>

<input type="hidden" name="option" value="com_hsconfig" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="hsconfig" />
</form>