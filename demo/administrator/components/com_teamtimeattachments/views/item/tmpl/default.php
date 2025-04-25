<?php

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
						<input class="inputbox" type="text" name="name" id="name" size="60" 
									 value="<?php echo $this->item->name; ?>" />
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
						$editor->display('description', 
								$this->item->description, '550', '300', '60', '20', array())
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