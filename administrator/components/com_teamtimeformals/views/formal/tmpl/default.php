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
            <label for="doctype_id">
							<?php echo JText::_('FORMALS TEMPLATE'); ?>:
            </label>
          </td>
          <td>
						<?php
						echo $this->is_edit ?
								$this->template_name : $this->lists['select_template'];
						?>
          </td>
        </tr>

        <tr>
          <td width="110" class="key">
            <label for="project_id">
							<?php echo JText::_('Using in'); ?>:
            </label>
          </td>
          <td>
						<?php
						echo $this->is_edit ?
								$this->project_name : $this->lists['select_project'];
						?>
          </td>
        </tr>

				<? if (!$this->is_edit) { ?>
					<tr>
						<td width="110" class="key">
							<label for="period">
								<?php echo JText::_('FORMAL DOCUMENT PERIOD'); ?>:
							</label>
						</td>
						<td>
							<div class="select-date">
								<?php echo $this->lists['select_date']; ?>
								<?php
								echo JHTML::_('calendar', $this->from_period, 'from_period', 'from-period',
										JText::_('DATE_FORMAT_MYSQL_WITHOUT_TIME'));
								?>
								<?php
								echo JHTML::_('calendar', $this->until_period, 'until_period', 'until-period',
										JText::_('DATE_FORMAT_MYSQL_WITHOUT_TIME'));
								?>
							</div>
						</td>
					</tr>

					<? if (isset($this->lists['select_process'])) { ?>

						<tr>
							<td width="110" class="key" valign="top">
								<label for="process_id">
									<?php echo JText::_('Process'); ?>:
								</label>
							</td>
							<td>
								<div class="select-process">
									<?= $this->lists['select_process'] ?>
								</div>
							</td>
						</tr>

					<? } ?>

				<? } ?>

				<? if ($this->is_edit) { ?>
					<tr>
						<td width="110" class="key">
							<label for="name">
								<?php echo JText::_('FORMAL DOCUMENT NAME'); ?>:
							</label>
						</td>
						<td>
							<input class="inputbox" type="text" name="name" id="name" size="60" value="<?php echo $this->item->name; ?>" />
						</td>
					</tr>
				<? } ?>

				<? if ($this->is_edit) { ?>
					<tr>
						<td width="110" class="key">
							<label for="price">
								<?php echo JText::_('FORMAL DOCUMENT PRICE'); ?>:
							</label>
						</td>
						<td>
							<input class="inputbox" type="text" name="price" id="price" size="60" value="<?php echo $this->item->price; ?>" />
						</td>
					</tr>
				<? } ?>

      </table>
    </fieldset>

		<? if ($this->is_edit) { ?>
			<fieldset class="adminform">
				<legend><?php echo JText::_('FORMAL DOCUMENT CONTENT'); ?></legend>
				<table class="admintable">
					<tr>
						<td valign="top" colspan="3">
							<?php
							// parameters : areaname, content, width, height, cols, rows, show xtd buttons
							echo $editor->display('content', $this->item->content, '550', '300', '60', '20', array());
							?>
						</td>
					</tr>
				</table>
			</fieldset>
		<? } ?>
  </div>

  <div class="clr"></div>

  <input type="hidden" name="is_edit" value="<?php echo (int) $this->is_edit; ?>" />

  <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
  <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
  <input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<?php echo JHTML::_('form.token'); ?>

</form>

<script>

	TeamTime.jQuery(function ($) {

		var changeDocType = function () {
			$.post(TeamTime.getUrlForTask("load_assignment"), {
				doctype_id: $("#doctype_id").val()
			},
			function (data) {
				$("#project_id").html(data);

				if (TeamTime.Bpm) {
					if ($("#project_id").val() != "") {
						TeamTime.Bpm.loadFormalProcesses();
					}
				}
			});
		};

		$("#doctype_id").change(changeDocType);

		if ($("#doctype_id").val() != "") {
			changeDocType();
		}

		if (TeamTime.Bpm) {
			$("#project_id").change(TeamTime.Bpm.loadFormalProcesses);
			$("#period").change(TeamTime.Bpm.loadFormalProcesses);
		}

	});

</script>