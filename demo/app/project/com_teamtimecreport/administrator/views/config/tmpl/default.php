<?php
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

JToolBarHelper::title(TEAMLOG_TOOLBAR_TITLE . JText::_('Config'), 'config.png');
JToolBarHelper::save();
JToolBarHelper::apply();
JToolBarHelper::cancel('cancel', JText::_('Close'));

function TeamTimeCreport_params_radio($f_label, $f_name, $f_value) {
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
            <legend><?php echo JText::_('Report Settings'); ?></legend>
            <table width="100%" cellspacing="1" class="paramlist admintable">
              <tbody>

                <tr>
                  <td width="50%" class="paramlist_key" nowrap>
                    <span class="editlinktip"><label class="hasTip" for="url"
                                                     title="<?php echo JText::_("Source site URL"); ?>"
                                                     id="url-lbl"><?php echo JText::_("Source site URL"); ?></label></span>
                  </td>
                  <td class="paramlist_value">
                    <input class="inputbox" type="text" maxlength="255" size="40"
                           name="params[base_url]" id ="url"
                           value="<?= $this->conf_data->base_url ?>">
                  </td>
                </tr>

                <?=
                TeamTimeCreport_params_radio(
                    JText::_("Date"), "col_date", $this->conf_data->col_date)
                ?>

                <?=
                TeamTimeCreport_params_radio(
                    JText::_("Project"), "col_project", $this->conf_data->col_project)
                ?>

                <?=
                TeamTimeCreport_params_radio(
                    JText::_("Type"), "col_type", $this->conf_data->col_type)
                ?>

                <?=
                TeamTimeCreport_params_radio(
                    JText::_("Task"), "col_task", $this->conf_data->col_task)
                ?>

                <?=
                TeamTimeCreport_params_radio(
                    JText::_("Todo"), "col_todo", $this->conf_data->col_todo)
                ?>

                <?=
                TeamTimeCreport_params_radio(
                    JText::_("Log"), "col_log", $this->conf_data->col_log)
                ?>

                <?=
                TeamTimeCreport_params_radio(
                    JText::_("PLANNED_ACTUAL_HOURS_OF_TODO"), "col_planned_actual_hours", $this->conf_data->col_planned_actual_hours)
                ?>

                <?=
                TeamTimeCreport_params_radio(
                    JText::_("ACTUAL_HOURS"), "col_actual_hours", $this->conf_data->col_actual_hours)
                ?>

                <?=
                TeamTimeCreport_params_radio(
                    JText::_("Hourly rate"), "col_hourly_rate", $this->conf_data->col_hourly_rate)
                ?>

                <?=
                TeamTimeCreport_params_radio(
                    JText::_("Planned Cost"), "col_planned_cost", $this->conf_data->col_planned_cost)
                ?>

                <?=
                TeamTimeCreport_params_radio(
                    JText::_("Actual Cost"), "col_actual_cost", $this->conf_data->col_actual_cost)
                ?>

                <?=
                TeamTimeCreport_params_radio(
                    JText::_("Statement Cost"), "col_statement_cost", $this->conf_data->col_statement_cost)
                ?>

                <?=
                TeamTimeCreport_params_radio(
                    JText::_("OVERHEAD_EXPENSES"), "col_overhead_expenses", $this->conf_data->col_overhead_expenses)
                ?>

                <?=
                TeamTimeCreport_params_radio(
                    JText::_("User"), "col_user", $this->conf_data->col_user)
                ?>

              </tbody></table>
          </fieldset>
        </td>
      </tr>
    </table>
  </div>

  <input type="hidden" name="option" value="com_teamtimecreport" />
  <input type="hidden" name="controller" value="config" />
  <input type="hidden" name="task" value="" />
  <?php echo JHTML::_('form.token'); ?>

</form>