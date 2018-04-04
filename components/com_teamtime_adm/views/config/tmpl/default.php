<?php
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

JToolBarHelper::title(JText::_('Control panel'), 'config.png');
JToolBarHelper::save();
JToolBarHelper::apply();
JToolBarHelper::cancel('cancel', JText::_('Close'));
jceToolbarHelper::help('config');

function TeamTimeReport_params_radio($f_label, $f_name, $f_value) {
  ?>
  <tr>
    <td width="40%" class="paramlist_key" nowrap>
      <span class="editlinktip"><label class="hasTip" for="<?= $f_name ?>"
                                       title="<?php echo JText::_($f_label); ?>"
                                       id="<?= $f_name ?>-lbl"><?php echo JText::_($f_label); ?></label></span>
    </td>
    <td class="paramlist_value">
      <input type="radio" <?= $f_value == "1" ? "checked" : "" ?>
             value="1" id="<?= $f_name ?>_on" name="params[<?= $f_name ?>]">
      <label for="<?= $f_name ?>_on"><?= JText::_('On') ?></label>

      <input type="radio" <?= $f_value == "0" ? "checked" : "" ?>
             value="0" id="<?= $f_name ?>_off" name="params[<?= $f_name ?>]">
      <label for="<?= $f_name ?>_off"><?= JText::_('Off') ?></label>
    </td>
  </tr>
<? } ?>

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
                    <span class="editlinktip"><label class="hasTip" for="paramseditor_state"
                                                     title="<?php echo JText::_('Overhead expenses'); ?>"
                                                     id="paramseditor_state-lbl"><?php echo JText::_('Overhead expenses'); ?></label></span>
                  </td>
                  <td class="paramlist_value">
                    <input type="radio" <?= $this->conf_data->show_costs == "1" ? "checked" : "" ?>
                           value="1" id="paramseditor_statemceEditor" name="params[show_costs]">
                    <label for="paramseditor_statemceEditor"><?= JText::_('On') ?></label>
                    <input type="radio" <?= $this->conf_data->show_costs == "0" ? "checked" : "" ?>
                           value="0" id="paramseditor_statemceNoEditor" name="params[show_costs]">
                    <label for="paramseditor_statemceNoEditor"><?= JText::_('Off') ?></label>
                  </td>
                </tr>

                <tr>
                  <td width="40%" class="paramlist_key">
                    <span class="editlinktip"><label class="hasTip" for="paramseditor_state"
                                                     title="<?php echo JText::_('Teamtime Currency'); ?>"
                                                     id="paramseditor_state-lbl"><?php echo JText::_('Teamtime Currency'); ?></label></span>
                  </td>
                  <td class="paramlist_value">
                    <input type="text" value="<?= $this->conf_data->currency ?>"
                           id="params_currency" name="params[currency]" maxlength="4" size="4">

                  </td>
                </tr>

                <tr>
                  <td width="40%" class="paramlist_key">
                    <span class="editlinktip"><label class="hasTip" for="paramseditor_state"
                                                     title="<?php echo JText::_('Teamtime Baseurl'); ?>"
                                                     id="paramseditor_state-lbl"><?php echo JText::_('Teamtime Baseurl'); ?></label></span>
                  </td>
                  <td class="paramlist_value">
                    <input type="text" value="<?= $this->conf_data->baseurl ?>"
                           id="params_currency" name="params[baseurl]" size="60" maxsize="300">

                  </td>
                </tr>

                <tr>
                  <td width="40%" class="paramlist_key">
                    <span class="editlinktip"><label class="hasTip" for="paramseditor_state"
                                                     title="<?php echo JText::_('Show filter fields in the orders list'); ?>"
                                                     id="paramseditor_state-lbl"><?php echo JText::_('Show filter fields in the orders list'); ?></label></span>
                  </td>
                  <td class="paramlist_value">
                    <input type="hidden" value="0" name="params[show_todos_datefilter]">
                    <input type="checkbox" <?= $this->conf_data->show_todos_datefilter == "1" ? "checked" : "" ?>
                           value="1" id="paramseditor_statemceEditor" name="params[show_todos_datefilter]">

                  </td>
                </tr>

                <tr>
                  <td width="40%" class="paramlist_key">
                    <span class="editlinktip"><label class="hasTip" for="paramseditor_state"
                                                     title="<?php echo JText::_('Accounting for time without an order'); ?>"
                                                     id="paramseditor_state-lbl"><?php echo JText::_('Accounting for time without an order'); ?></label></span>
                  </td>
                  <td class="paramlist_value">
                    <input type="hidden" value="0" name="params[use_autotodos]">
                    <input type="checkbox" <?= $this->conf_data->use_autotodos == "1" ? "checked" : "" ?>
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
                  TeamTimeReport_params_radio(
                          JText::_("Date"), "col_date", $this->conf_data->col_date)
                  ?>

                  <?=
                  TeamTimeReport_params_radio(
                          JText::_("Project"), "col_project", $this->conf_data->col_project)
                  ?>

                  <?=
                  TeamTimeReport_params_radio(
                          JText::_("Type"), "col_type", $this->conf_data->col_type)
                  ?>

                  <?=
                  TeamTimeReport_params_radio(
                          JText::_("Task"), "col_task", $this->conf_data->col_task)
                  ?>

                  <?=
                  TeamTimeReport_params_radio(
                          JText::_("Todo"), "col_todo", $this->conf_data->col_todo)
                  ?>

                  <?=
                  TeamTimeReport_params_radio(
                          JText::_("Log"), "col_log", $this->conf_data->col_log)
                  ?>

                  <?=
                  TeamTimeReport_params_radio(
                          JText::_("PLANNED_ACTUAL_HOURS_OF_TODO"), "col_planned_actual_hours", $this->conf_data->col_planned_actual_hours)
                  ?>

                  <?=
                  TeamTimeReport_params_radio(
                          JText::_("ACTUAL_HOURS"), "col_actual_hours", $this->conf_data->col_actual_hours)
                  ?>

                  <?=
                  TeamTimeReport_params_radio(
                          JText::_("Hourly rate"), "col_hourly_rate", $this->conf_data->col_hourly_rate)
                  ?>

                  <?=
                  TeamTimeReport_params_radio(
                          JText::_("Planned Cost"), "col_planned_cost", $this->conf_data->col_planned_cost)
                  ?>

                  <?=
                  TeamTimeReport_params_radio(
                          JText::_("Actual Cost"), "col_actual_cost", $this->conf_data->col_actual_cost)
                  ?>

                  <?=
                  TeamTimeReport_params_radio(
                          JText::_("OVERHEAD_EXPENSES"), "col_overhead_expenses", $this->conf_data->col_overhead_expenses)
                  ?>

                  <?=
                  TeamTimeReport_params_radio(
                          JText::_("User"), "col_user", $this->conf_data->col_user)
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