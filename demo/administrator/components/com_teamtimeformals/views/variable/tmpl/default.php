<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
JFilterOutput::objectHTMLSafe($this->item, ENT_QUOTES);

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
							<?php echo JText::_('Variable name'); ?>:
						</label>
					</td>
					<td>
						<input class="inputbox" type="text" name="name" id="name" size="60" value="<?php echo $this->item->name; ?>" />
					</td>

					<td rowspan="4" class="key"  valign="top">
						<label for="using_in"><?= JText::_("USING IN") ?>:</label><br>
						<?php echo $this->lists['select_using']; ?>
					</td>

					<td rowspan="4" class="key">
						<div id="using_data">
							<? if ($this->item->using_in == 0) { ?>

								<label for="projects"><?= JText::_("Used in project data") ?></label>:<br>
								<?php echo $this->lists['select_projects']; ?>
								<input type="hidden" value="" name="users[]">

							<? }
							else { ?>

								<label for="users"><?= JText::_("Used in user data") ?></label>:<br>
	<?php echo $this->lists['select_users']; ?>
								<input type="hidden" value="" name="projects[]">

<? } ?>
						</div>
					</td>
				</tr>

				<tr>
					<td width="110" class="key">
						<label for="tagname">
<?php echo JText::_('Variable tagname'); ?>:
						</label>
					</td>
					<td>
						<input class="inputbox" type="text" name="tagname" id="tagname"
									 size="60" value="<?php echo $this->item->tagname; ?>" />
					</td>
				</tr>

				<tr>
					<td width="110" class="key">
						<label for="xsize">
<?php echo JText::_('FORMAL INPUT WIDTH'); ?>:
						</label>
					</td>
					<td>
						<input class="inputbox" type="text" name="xsize" id="xsize"
									 size="10" value="<?php echo $this->item->xsize; ?>" />
					</td>
				</tr>

				<tr>
					<td width="110" class="key">
						<label for="ysize">
<?php echo JText::_('FORMAL INPUT HEIGHT'); ?>:
						</label>
					</td>
					<td>
						<input class="inputbox" type="text" name="ysize" id="ysize"
									 size="10" value="<?php echo $this->item->ysize; ?>" />
					</td>
				</tr>

				<!--
		
				<tr>
					<td width="110" class="key">
						<label for="using_in">
<?php echo JText::_('FORMAL USING IN'); ?>:
						</label>
					</td>
					<td>
<?php echo $this->lists['select_using']; ?>
					</td>
				</tr>
		
				-->

				<tr>
					<td width="110" class="key" valign="top">
						<label for="defaultval">
<?php echo JText::_('FORMAL DEFAULT VALUE'); ?>:
						</label>
					</td>
					<td nowrap>
						<input class="inputbox" type="text" name="defaultval" id="defaultval"
									 size="60" value="<?php echo $this->item->defaultval; ?>" />
						<p>
							<strong><?= JText::_("VARIABLE TEXT1") ?></strong><br>
							<?= JText::_("VARIABLE TEXT2") ?><br>
<?= JText::_("VARIABLE TEXT3") ?>
					</td>
				</tr>

			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('FORMAL DESCRIPTION'); ?></legend>
			<table class="admintable">
				<tr>
					<td valign="top" colspan="3">
						<?php
						// parameters : areaname, content, width, height, cols, rows, show xtd buttons
						echo $editor->display('description', $this->item->description, '550',
								'300', '60', '20', array());
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

	(function($){
		$(function(){

			$("#using_in").change(function(){
				$.post(TeamTime.baseUrl +
					"index.php?option=com_teamtimeformals&controller=formal&task=load_using_in",
				{using_in: $("#using_in").val()},
				function(data){
					$("#using_data").html(data);
				});
			});

		});
	})(jQuery);


	function submitbutton(pressbutton) {
		var $ = jQuery;

		if(pressbutton == "save" || pressbutton == "apply") {
			var alert_str = $("#using_in").val() == "1"?
				'<?= JText::_("ALERT_CHANGE_USERVARIABLEVALUE") ?>' :
				'<?= JText::_("ALERT_CHANGE_PROJECTVARIABLEVALUE") ?>';
			alert(alert_str);
		}

		submitform(pressbutton);
	}
</script>