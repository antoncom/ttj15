<?php
defined('_JEXEC') or die('Restricted access');
?>

<form id="adminForm" name="adminForm">

	<h1><?= JText::_("Import template as process") ?></h1>

	<table class="importTemplateTable" width="100%" height="160">
		<tr>
			<td>
				<div>
					<label>
						<?= JText::_("Select destination for template") ?>
					</label>
					<?= $this->lists["select_spaces"] ?>
				</div>
			</td>
		</tr>

		<tr>
			<td align="right">
				<input type="button"
							 id="importTemplate" value="<?= JText::_("Import template") ?>"/>&nbsp;
				<input type="button"
							 id="cancelImportTemplate"value="<?= JText::_("Cancel import") ?>"/>
			</td>
		</tr>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="1" />
	<input type="hidden" name="filter_order" id="filter_order" value="<?= $this->lists['order'] ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>