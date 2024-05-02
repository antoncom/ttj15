<form action="index.php" method="post" name="adminForm" id="adminForm">


<?
foreach ($this->items as $row) {
	?>

	<a href="index.php?option=<?= $this->option ?>&controller=<?= $this->controller ?>&task=edit&cid[]=<?= $row->id ?>">Edit <?= $row->name ?></a>
	<br>

	<?
}
?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	
	<?php echo JHTML::_('form.token'); ?>

</form>