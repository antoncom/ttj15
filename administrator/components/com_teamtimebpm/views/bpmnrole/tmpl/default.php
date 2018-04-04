<?php
defined('_JEXEC') or die('Restricted access');

JFilterOutput::objectHTMLSafe($this->item, ENT_QUOTES);

$user = & JFactory::getUser();
$editor = & JFactory::getEditor();
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
					<? if ($user->usertype == "Super Administrator") { ?>
						<td width="110" class="key">
							<label for="name">
								<?= JText::_('Rate'); ?>:
							</label>
						</td>
						<td>
							<input class="inputbox" type="text" name="rate" id="rate" size="10" value="<?php echo $this->item->rate; ?>" />
						</td>
						<?
					}
					else if ($user->usertype == "Administrator") {
						?>
						<td width="110" class="key">
							<label for="name">
								<?= JText::_('Rate'); ?>:
							</label>
						</td>
						<td>
							<input class="inputbox" disabled size="10" value="<?php echo $this->item->rate; ?>" />

							<input type="hidden" name="rate" id="rate" size="10" value="<?php echo $this->item->rate; ?>" />
						</td>
						<?
					}
					else {
						?>
						<td></td>
						<td>
							<input class="inputbox" type="hidden" name="rate" id="rate" size="10" value="<?php echo $this->item->rate; ?>" />
						</td>
					<? } ?>
				</tr>

				<? if ($this->lists['select_target']) { ?>
					<tr>
						<td width="110" class="key">
							<label for="target_id">
								<?= JText::_('Goals vector'); ?>:
							</label>
						</td>
						<td>
							<?= $this->lists['select_target'] ?>
						</td>
					</tr>
				<? } ?>

				<tr>
					<td width="110" class="key">
						<label for="user_id">
							<?php echo JText::_('User'); ?>:
						</label>
					</td>
					<td>
						<?= $this->lists['select_user'] ?>
					</td>
				</tr>


				<? if ($this->lists['select_target']) { ?>
					<tr>
						<td width="110" class="key">
							<label for="rate_from_dotu">
								<?= JText::_('Take the hourly rate from TeamTime Career'); ?>:
							</label>
						</td>
						<td>
							<input type="hidden" name="rate_from_dotu" value="0">
							<input type="checkbox" name="rate_from_dotu" id="rate_from_dotu"
										 value="1" <?=
							$this->item->rate_from_dotu ? "checked" : ""
								?>>
						</td>
					</tr>
				<? } ?>

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