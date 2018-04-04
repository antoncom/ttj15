<?php
defined('_JEXEC') or die('Restricted access');

JFilterOutput::objectHTMLSafe($this->item, ENT_QUOTES);

$acl = new TeamTime_Acl();
?>

<form action="index.php" method="post" name="adminForm">

  <table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr valign="top">
      <td>
        <fieldset class="adminform">
          <legend><?php echo JText::_('Details'); ?></legend>
          <table class="admintable">
            <tr>
              <td width="110" class="key">
                <label for="state">
									<?php echo JText::_('User'); ?>:
                </label>
              </td>
              <td>
								<?php echo $this->item->name; ?>
              </td>
            </tr>
            <tr>
              <td width="110" class="key">
                <label for="sendmsg">
									<?php echo JText::_('Subscribe'); ?>:
                </label>
              </td>
              <td>
                <input type="checkbox" name="send_msg" id="sendmsg" value="1"
								<?=
								$this->item->send_msg ? "checked" : ""
								?>> <?php echo JText::_('Receive a week Calendar'); ?>
              </td>
            </tr>

            <tr>
              <td width="110" class="key">
                <label for="hideforother">
									<?php echo JText::_("Don't show reports to other users"); ?>
                </label>
              </td>
              <td>
                <input type="checkbox" name="hideforother" id="hideforother" value="1"
								<?=
								$this->item->hideforother ? "checked" : ""
								?>>
              </td>
            </tr>

            <tr>
              <td width="110" class="key">
                <label for="salary">
									<?php echo JText::_('Salary'); ?>:
                </label>
              </td>
              <td>
                <input name="salary" id="salary" maxlength="9"
                       value="<?= (float) $this->item->salary ?>">
              </td>
            </tr>

						<? if ($acl->isSuperAdmin()) { ?>
							<tr>
								<td width="110" class="key">
									<label for="hour_price">
										<?php echo JText::_('Base Rate'); ?>:
									</label>
								</td>
								<td>
									<input name="hour_price" id="hour_price"
												 value="<?= (float) $this->item->hour_price ?>">
								</td>
							</tr>

							<tr>
								<td width="110">
								</td>
								<td>
									<?php echo JText::_('HOURLY RATE TEXT'); ?>
								</td>
							</tr>
							<?
						}
						else if ($acl->isAdmin()) {
							?>
							<tr>
								<td width="110" class="key">
									<label for="hour_price">
										<?php echo JText::_('Base Rate'); ?>:
									</label>
								</td>
								<td>
									<input disabled
												 value="<?= (float) $this->item->hour_price ?>">

									<input name="hour_price" id="hour_price" type="hidden"
												 value="<?= (float) $this->item->hour_price ?>">
								</td>
							</tr>

							<tr>
								<td width="110">
								</td>
								<td>
									<?php echo JText::_('HOURLY RATE TEXT'); ?>
								</td>
							</tr>
							<?
						}
						else {
							?>
							<tr>
								<td></td>
								<td>
									<input name="hour_price" id="hour_price" type="hidden"
												 value="<?= (float) $this->item->hour_price ?>">
								</td>
							</tr>
						<? } ?>

						<? if ($acl->isSuperAdmin()) { ?>
							<tr>
								<td width="110" class="key" valign="top">
									<label>
										<?= JText::_('Available Projects') ?>
									</label>
								</td>
								<td>
									<?= $this->lists["select_projects"] ?>
								</td>
							</tr>
						<? } ?>

          </table>
        </fieldset>

      </td>

      <td width="40%">

				<? if ($acl->isSuperAdmin()) { ?>

					<?= TeamTime::helper()->getBase()->getUserParams($this->item) ?>

				<? } ?>

      </td>


    </tr>
  </table>

  <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
  <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
  <input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<?php echo JHTML::_('form.token'); ?>

</form>

<script>
  jQuery(document).ready(function ($) {

    $("#salary").autoNumeric({mDec:10, aSep:''});

  });
</script>