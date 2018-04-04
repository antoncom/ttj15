<?php
defined('_JEXEC') or die('Restricted access');

JFilterOutput::objectHTMLSafe($this->item, ENT_QUOTES);
$user = & JFactory::getUser();
$editor = & JFactory::getEditor();
?>

<form action="index.php" method="post" name="adminForm">

  <table class="admintable" width="100%">
    <tr valign="top">
      <td width="60%">
        <fieldset class="adminform">
          <legend><?php echo JText::_('Details'); ?></legend>
          <table class="admintable">
            <tr>
              <td width="110" class="key">
                <label for="name">
									<?php echo JText::_('Name'); ?>:
                </label>
								<?php //echo $this->item->rate; ?>
              </td>
              <td>
                <input class="inputbox" type="text" name="name" id="name" size="60"
                       value="<?php echo $this->item->name; ?>" />
              </td>
              <td rowspan="3" class="key">
                <label for="users"><?= JText::_("PROJECT OPENED FOR") ?>:<br>
									<?php echo $this->lists['select_users']; ?>
                </label>
              </td>
            </tr>
            <tr>
              <td width="110" class="key">
                <label for="state">
									<?php echo JText::_('State'); ?>:
                </label>
              </td>
              <td>
								<?php echo $this->lists['select_state']; ?>
              </td>
            </tr>

            <tr>
							<? if ($user->usertype == "Super Administrator") { ?>
								<td width="110" class="key">
									<label for="rate">
										<?php echo JText::_('Rate'); ?>:
									</label>
								</td>
								<td>
									<?= $this->lists['radio_rate'] ?><p>

										<input class="inputbox" type="text" name="rate" id="rate" size="10"
													 value="<?php echo $this->item->rate; ?>" />

									<div id="max_rate" <?=
								!$this->item->dynamic_rate ?
												'style="display:none;"' : ''
									?> >
												 <?= JText::_("MAX HOURLY-RATE TO BE PAID BY THE CLIENT") ?>
										<span id="max_rate_change"><?= $this->item->maxRate ?></span>
										<?= $this->conf_data->currency ?>

										<br>
										<?= JText::_("MIN HOURLY-RATE TO BE PAID BY THE CLIENT") ?>
										<span id="min_rate_change"><?= $this->item->minRate ?></span>
										<?= $this->conf_data->currency ?>

									</div>
								</td>
								<?
							}
							else if ($user->usertype == "Administrator") {
								?>
								<td width="110" class="key">
									<label for="rate">
										<?php echo JText::_('Rate'); ?>:
									</label>
								</td>
								<td>
									<?=
									!$this->item->dynamic_rate ?
													JText::_("Fixed price") : JText::_("Multiplier of man-hour price")
									?><p>

										<input class="inputbox" type="text" disabled size="10"
													 value="<?php echo $this->item->rate; ?>" />

									<div id="max_rate" <?=
								!$this->item->dynamic_rate ?
												'style="display:none;"' : ''
									?> >

										<?= JText::_("MAX HOURLY-RATE TO BE PAID BY THE CLIENT") ?>
										<span id="max_rate_change"><?= $this->item->maxRate ?></span>
										<?= $this->conf_data->currency ?>

										<br>
										<?= JText::_("MIN HOURLY-RATE TO BE PAID BY THE CLIENT") ?>
										<span id="min_rate_change"><?= $this->item->minRate ?></span>
										<?= $this->conf_data->currency ?>

									</div>

									<input type="hidden" name="dynamic_rate"
												 value="<?php echo $this->item->dynamic_rate; ?>" />
									<input type="hidden" name="rate" id="rate"
												 value="<?php echo $this->item->rate; ?>" />
								</td>
								<?
							}
							else {
								?>
								<td></td>
								<td>
									<input class="inputbox" type="hidden" name="rate" id="rate" size="10"
												 value="<?php echo $this->item->rate; ?>" />
								</td>
							<? } ?>
            </tr>

          </table>
        </fieldset>
        <fieldset class="adminform">
          <legend><?php echo JText::_('PROJECT DESCRIPTION'); ?></legend>
          <table class="admintable">
            <tr>
              <td valign="top" colspan="3">
								<?php
// parameters : areaname, content, width, height, cols, rows, show xtd buttons
								echo $editor->display('description', $this->item->description, '550', '300', '60', '20',
										array());
								?>
              </td>
            </tr>
          </table>
        </fieldset>

      </td>

      <td width="40%">
				<?=
				TeamTime::getProjectParams($this->item)
				?>
      </td>
    </tr>
  </table>

  <input type="hidden" name="max_rate_value" id="max_rate_value"
         value="<?php echo $this->item->maxRate; ?>" />
	<input type="hidden" name="min_rate_value" id="min_rate_value"
         value="<?php echo $this->item->minRate; ?>" />

  <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
  <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
  <input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<?php echo JHTML::_('form.token'); ?>

</form>

<script>

  jQuery(function ($) {

    $("#rate").autoNumeric({mDec: 5, aSep: ''});

    var calcRate = function () {
      var k = $("#rate").val() != ""? parseFloat($("#rate").val()) : 0;

			var maxR = parseFloat($("#max_rate_value").val());
			var minR = parseFloat($("#min_rate_value").val());

      $("#max_rate_change").text(Math.round(maxR * k));
			$("#min_rate_change").text(Math.round(minR * k));
    };

    calcRate();

    $("#rate").keyup(function () {
      calcRate();
    });

    $("#dynamic_rate0, #dynamic_rate1").change(function () {
      if (this.value == "1") {
        $("#max_rate").css("display", "");
      }
      else {
        $("#max_rate").css("display", "none");
      }
    });

  });

</script>