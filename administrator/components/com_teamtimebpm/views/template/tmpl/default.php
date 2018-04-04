<?php
defined('_JEXEC') or die('Restricted access');

JFilterOutput::objectHTMLSafe($this->item, ENT_QUOTES);

$user = & JFactory::getUser();
$editor = & JFactory::getEditor();
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

	<div class="col width-60">
		<fieldset class="adminform">
			<legend><?php echo JText::_('Details'); ?></legend>
			<table class="admintable">
				<tr>
					<td width="110" class="key">
						<label for="name">
							<?php echo JText::_('Name'); ?>:
						</label>
					</td>
					<td>
						<input class="inputbox" type="text" name="name" id="name" size="60" value="<?php echo $this->item->name; ?>" />
					</td>
				</tr>

				<tr>
					<td width="110" class="key">
						<label for="name">
							<?php echo JText::_('Tags'); ?>:
						</label>
					</td>
					<td>
						<input class="inputbox" type="text" name="tags" id="tags" size="60" value="<?php echo $this->item->tags; ?>" />
					</td>
				</tr>

				<tr>
					<td width="110" class="key">
						<label for="name">
							<?= JText::_('Editor'); ?>:
						</label>
					</td>
					<td>
						<? if ($this->item->modified_by) { ?>
							<?=
							JText::sprintf('Last modified by %s on %s', $this->item->user_name,
									JHTML::_('date', $this->item->modified, "%b %d, %Y"))
							?>
						<? } ?>
					</td>
				</tr>

				<tr>
					<td width="110" class="key">
						<label for="archived">
							<?= JText::_('Archived'); ?>:
						</label>
					</td>
					<td>
						<?= $this->lists['select_archived'] ?>
					</td>
				</tr>
				
				<tr>
					<td width="110" class="key">
						<label for="space_id">
							<?= JText::_('Space'); ?>:
						</label>
					</td>
					<td>
						<?= $this->lists['select_spaces'] ?>
					</td>
				</tr>
				
				<tr>
					<td width="110" class="key">
						<label for="project_id">
							<?= JText::_('Project'); ?>:
						</label>
					</td>
					<td>
						<div id="blockProjectId">
							<?= $this->lists['select_project'] ?>
						</div>
					</td>
				</tr>

			</table>
		</fieldset>

		<fieldset class="adminform">
			<legend><?= JText::_('Description'); ?></legend>
			<table class="admintable">
				<tr>
					<td valign="top" colspan="3">
						<?=
						$editor->display('description', $this->item->description, '550', '300', '60', '20', array())
						?>
					</td>
				</tr>
			</table>
		</fieldset>
	</div>

	<div class="clr"></div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<?php echo JHTML::_('form.token'); ?>

</form>

<script>

	TeamTime.jQuery(function ($) {

		$("#space_id").change(function () {
			var spaceId = $("#space_id").val();

			$.get(TeamTime.getUrlForTask("loadprojects") + "&space_id=" + spaceId,
			function (data) {
				$("#blockProjectId").html(data);
			})
		});

	});

</script>
