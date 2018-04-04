<?php
defined('_JEXEC') or die('Restricted access');

$helperBase = TeamTime::helper()->getBase();
?>

<form action="index.php" method="post" name="adminForm">
  <div class="col width-50">
    <table class="admintable">
      <tr>
        <td style="vertical-align:top;">
          <fieldset class="adminform">
            <legend><?php echo JText::_('TEAMLOG_USER_PREFS'); ?></legend>
            <table width="100%" cellspacing="1" class="paramlist admintable">
              <tbody><tr>
              <!--td width="40%" class="paramlist_key"><span class="editlinktip"><label class="hasTip" for="paramseditor_gzip" id="paramseditor_gzip-lbl">PARAM GZIP</label></span></td>
              <td class="paramlist_value"><select class="inputbox" id="paramseditor_gzip" name="params[editor_gzip]"><option selected="selected" value="0">No</option><option value="1">Yes</option></select></td>
              </tr-->
                <tr>
                  <td width="40%" class="paramlist_key">
                    <span class="editlinktip"><label
												class="hasTip" for="paramseditor_state"
												title="<?php echo JText::_('Overhead expenses'); ?>"
												id="paramseditor_state-lbl"><?php echo JText::_('Overhead expenses'); ?></label></span>
                  </td>
                  <td class="paramlist_value">
                    <input type="radio" <?=
$this->config->show_costs == "1" ? "checked" : ""
?>
                           value="1" id="paramseditor_statemceEditor" name="params[show_costs]">
                    <label for="paramseditor_statemceEditor"><?= JText::_('On') ?></label>
                    <input type="radio" <?=
										$this->config->show_costs == "0" ? "checked" : ""
?>
                           value="0" id="paramseditor_statemceNoEditor" name="params[show_costs]">
                    <label for="paramseditor_statemceNoEditor"><?= JText::_('Off') ?></label>
                  </td>
                </tr>

                <tr>
                  <td width="40%" class="paramlist_key">
                    <span class="editlinktip"><label
												class="hasTip" for="paramseditor_state"
												title="<?php echo JText::_('Teamtime Currency'); ?>"
												id="paramseditor_state-lbl"><?php echo JText::_('Teamtime Currency'); ?></label></span>
                  </td>
                  <td class="paramlist_value">
                    <input type="text" value="<?= $this->config->currency ?>"
                           id="params_currency" name="params[currency]" maxlength="4" size="4">

                  </td>
                </tr>

                <tr>
                  <td width="40%" class="paramlist_key">
                    <span class="editlinktip"><label
												class="hasTip" for="paramseditor_state"
												title="<?php echo JText::_('Teamtime Baseurl'); ?>"
												id="paramseditor_state-lbl"><?php echo JText::_('Teamtime Baseurl'); ?></label></span>
                  </td>
                  <td class="paramlist_value">
                    <input type="text" value="<?= $this->config->baseurl ?>"
                           id="params_currency" name="params[baseurl]" size="60" maxsize="300">

                  </td>
                </tr>

                <tr>
                  <td width="40%" class="paramlist_key">
                    <span class="editlinktip"><label
												class="hasTip" for="paramseditor_state"
												title="<?php echo JText::_('Show filter fields in the orders list'); ?>"
												id="paramseditor_state-lbl"><?php echo JText::_('Show filter fields in the orders list'); ?></label></span>
                  </td>
                  <td class="paramlist_value">
                    <input type="hidden" value="0" name="params[show_todos_datefilter]">
                    <input type="checkbox" <?=
										$this->config->show_todos_datefilter == "1" ? "checked" : ""
?>
                           value="1" id="paramseditor_statemceEditor" name="params[show_todos_datefilter]">

                  </td>
                </tr>

                <tr>
                  <td width="40%" class="paramlist_key">
                    <span class="editlinktip"><label
												class="hasTip" for="paramseditor_state"
												title="<?php echo JText::_('Accounting for time without an order'); ?>"
												id="paramseditor_state-lbl"><?php echo JText::_('Accounting for time without an order'); ?></label></span>
                  </td>
                  <td class="paramlist_value">
                    <input type="hidden" value="0" name="params[use_autotodos]">
                    <input type="checkbox" <?=
										$this->config->use_autotodos == "1" ? "checked" : ""
?>
                           value="1" id="paramseditor_statemceEditor" name="params[use_autotodos]">
                  </td>
                </tr>

                <!--tr>
                <td width="40%" class="paramlist_key"><span class="editlinktip"><label class="hasTip" for="paramseditor_toggle_text" id="paramseditor_toggle_text-lbl">PARAM EDITOR TOGGLE TEXT</label></span></td>
                <td class="paramlist_value"><input type="text" class="text_area" value="[show/hide]" id="paramseditor_toggle_text" name="params[editor_toggle_text]"></td>
                </tr-->
              </tbody></table>

          </fieldset>
        </td>
      </tr>

      <tr>
        <td style="vertical-align:top;">
          <fieldset class="adminform">
            <legend><?php echo JText::_('Reports customizing'); ?></legend>
            <table width="100%" cellspacing="1" class="paramlist admintable">
              <tbody><tr>
              <!--td width="40%" class="paramlist_key"><span class="editlinktip"><label class="hasTip" for="paramseditor_gzip" id="paramseditor_gzip-lbl">PARAM GZIP</label></span></td>
              <td class="paramlist_value"><select class="inputbox" id="paramseditor_gzip" name="params[editor_gzip]"><option selected="selected" value="0">No</option><option value="1">Yes</option></select></td>
              </tr-->

									<?=
									$helperBase->reportParamsRadio(
											JText::_("Date"), "col_date", $this->config->col_date)
									?>

									<?=
									$helperBase->reportParamsRadio(
											JText::_("Project"), "col_project", $this->config->col_project)
									?>

									<?=
									$helperBase->reportParamsRadio(
											JText::_("Type"), "col_type", $this->config->col_type)
									?>

									<?=
									$helperBase->reportParamsRadio(
											JText::_("Task"), "col_task", $this->config->col_task)
									?>

									<?=
									$helperBase->reportParamsRadio(
											JText::_("Todo"), "col_todo", $this->config->col_todo)
									?>

									<?=
									$helperBase->reportParamsRadio(
											JText::_("Log"), "col_log", $this->config->col_log)
									?>

									<?=
									$helperBase->reportParamsRadio(
											JText::_("PLANNED_ACTUAL_HOURS_OF_TODO"), "col_planned_actual_hours",
											$this->config->col_planned_actual_hours)
									?>

									<?=
									$helperBase->reportParamsRadio(
											JText::_("ACTUAL_HOURS"), "col_actual_hours", $this->config->col_actual_hours)
									?>

									<?=
									$helperBase->reportParamsRadio(
											JText::_("Hourly rate"), "col_hourly_rate", $this->config->col_hourly_rate)
									?>

									<?=
									$helperBase->reportParamsRadio(
											JText::_("Planned Cost"), "col_planned_cost", $this->config->col_planned_cost)
									?>

									<?=
									$helperBase->reportParamsRadio(
											JText::_("Actual Cost"), "col_actual_cost", $this->config->col_actual_cost)
									?>

									<?=
									$helperBase->reportParamsRadio(
											JText::_("OVERHEAD_EXPENSES"), "col_overhead_expenses",
											$this->config->col_overhead_expenses)
									?>

									<?=
									$helperBase->reportParamsRadio(
											JText::_("User"), "col_user", $this->config->col_user)
									?>

                  <!--tr>
                  <td width="40%" class="paramlist_key"><span class="editlinktip"><label class="hasTip" for="paramseditor_toggle_text" id="paramseditor_toggle_text-lbl">PARAM EDITOR TOGGLE TEXT</label></span></td>
                  <td class="paramlist_value"><input type="text" class="text_area" value="[show/hide]" id="paramseditor_toggle_text" name="params[editor_toggle_text]"></td>
                  </tr-->
              </tbody></table>

          </fieldset>
        </td>
      </tr>


    </table>
  </div>
  <input type="hidden" name="option" value="com_teamtime" />
  <input type="hidden" name="controller" value="config" />
  <input type="hidden" name="task" value="" />
	<?php echo JHTML::_('form.token'); ?>
</form>