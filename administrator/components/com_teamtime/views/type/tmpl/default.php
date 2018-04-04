<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
	JFilterOutput::objectHTMLSafe($this->item, ENT_QUOTES);

	$user =& JFactory::getUser();
	
	$editor =& JFactory::getEditor();
?>

<form action="index.php" method="post" name="adminForm">

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
			<? if($user->usertype == "Super Administrator"){?>
				<td width="110" class="key">
					<label for="name">
						<?php echo JText::_('Rate'); ?>:
					</label>
				</td>
				<td>
					<input class="inputbox" type="text" name="rate" id="rate" size="10" value="<?php echo $this->item->rate; ?>" />
				</td>
			<?}else if($user->usertype == "Administrator"){?>
				<td width="110" class="key">
					<label for="name">
						<?php echo JText::_('Rate'); ?>:
					</label>
				</td>
				<td>
					<input class="inputbox" disabled size="10" value="<?php echo $this->item->rate; ?>" />

					<input type="hidden" name="rate" id="rate" size="10" value="<?php echo $this->item->rate; ?>" />
				</td>
			<?}else{?>
				<td></td>
				<td>
					<input class="inputbox" type="hidden" name="rate" id="rate" size="10" value="<?php echo $this->item->rate; ?>" />
				</td>
			<?}?>
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