<?php
defined('_JEXEC') or die('Restricted access');

$editor = & JFactory::getEditor();
$document = & JFactory::getDocument();
$lang = &JFactory::getLanguage();
$acl = new TeamTime_Acl();
$helperFormals = TeamTime::helper()->getFormals();
?>

<style>
  @import url("/<?= URL_MEDIA_COMPONENT_ASSETS ?>css/calendar.css");
  @import url("/<?= URL_MEDIA_COMPONENT_ASSETS ?>css/main_edit.css");
  @import url("/<?= URL_MEDIA_COMPONENT_ASSETS ?>css/colorselect.css");
  @import url("/<?= URL_MEDIA_COMPONENT_ASSETS ?>css/dropdown.css");
	@import url("/<?= URL_MEDIA_COMPONENT_ASSETS ?>css/todo-details.css");
</style>

<div id="main_calendar_block">
  <div class="toolBotton">
    <a id="Savebtn" class="imgbtn" href="javascript:void(0);">
      <span class="Save"  title="Save the calendar"><?php echo JText::_('Save'); ?>(<u>S</u>)
      </span>
    </a>

		<?php if ($this->event) { ?>
			<a id="Deletebtn" class="imgbtn" href="javascript:void(0);">
				<span class="Delete" title="Cancel the calendar"><?php echo JText::_('Delete'); ?>(<u>D</u>)
				</span>
			</a>
		<?php } ?>

    <a id="Closebtn" class="imgbtn" href="javascript:void(0);">
      <span class="Close" title="Close the window" ><?php echo JText::_('Close'); ?>
      </span>
    </a>

		<?php if ($this->event->id) { ?>
			<a id="Savecopybtn" class="imgbtn" href="javascript:void(0);">
				<span class="Savecopy"  title="Save as copy"><?php echo JText::_('Save as copy'); ?>
				</span>
			</a>
		<?php } ?>
  </div>
  <div style="clear: both">
  </div>
  <div class="infocontainer">
    <form action="<?= $this->controllerUrl ?>&task=adddetails<?php
		echo $this->event ? "&id=" . $this->event->id : "";
		?>"
          class="fform" id="fmEdit" method="post">

      <input id="timezone" name="timezone" type="hidden" value="" />
      <input id="IsAllDayEvent" name="IsAllDayEvent1" type="hidden" value="1"/>

      <input id="was_repeat" name="was_repeat" type="hidden" value="" />

      <table width="100%" cellspacing="8" cellpadding="0">

        <tr>
          <td class="key" nowrap>
						<?php echo JText::_('Subject1'); ?>:
          </td>
          <td width="100%" class="val">
            <div style="float:left; width:92%">
              <input MaxLength="200" class="required safe" id="Subject" name="Subject" style="width:100%;" type="text"
                     value="<?php
						echo $this->event ? htmlspecialchars($this->event->title) : ""
						?>" />
              <input id="colorvalue" name="colorvalue" type="hidden"
                     value="<?php
										 echo $this->event ? $this->event->color : ""
						?>" />
              <input id="isallday" name="isalldayevent" type="hidden"
                     value="<?php
										 echo $this->event ? $this->event->isalldayevent : $this->default_isallday
						?>" />
            </div>
            <div id="calendarcolor" style="clear:right; margin-left:5px;"></div>
          </td>
        </tr>

        <tr>
          <td class="key" nowrap>
						<?php echo JText::_('Time1'); ?>:
          </td>
          <td class="val">
						<?php
						if ($this->event) {
							$this->event->created = JHTML::_('date', $this->event->created, '%Y-%m-%d %H:%M:%S');
							/* $sarr = explode(" ",
							  TeamTime_DateTools::php2JsTime(
							  TeamTime_DateTools::mySql2PhpTime($this->event->created)
							  )
							  ); */
							$sarr = explode(" ", $this->event->created);
							$sarr[1] = substr($sarr[1], 0, 5);

							$earr = explode(" ",
									TeamTime_DateTools::php2JsTime(
											TeamTime_DateTools::mySql2PhpTime($this->event->created)
											+ $this->event->hours_plan * 60 * 60));
						}
						?>
						<input type="hidden" id="realStartDate" value="">
            <input MaxLength="10" class="required date"
									 id="startDate" name="stpartdate"
									 style="padding-left:2px;width:90px;" type="text"
                   value="<?php
						echo $this->event->id ? $sarr[0] : $this->todo_date;
						?>" />&nbsp;
            <input MaxLength="5" class="required time" id="stparttime" name="stparttime" style="width:40px;" type="text"
                   value="<?php
									 echo $this->event->id ? $sarr[1] : $this->todo_hours;
						?>" />
            <span class="key"><?php echo JText::_('Planned hours'); ?>:</span>
            <input MaxLength="10" id="etpartdate" name="etpartdate"
                   style="padding-left:2px;width:90px; display:none;" type="text"
                   value="<?php
									 echo $this->event ? $earr[0] : "";
						?>" />
            <input MaxLength="50" id="etparttime"
                   style="width:40px; display:none" type="text"
                   value="<?php
									 echo $this->event ? $earr[1] : "";
						?>" />

            <select name="etparttime" id="hoursPlan-etparttime">
							<?
							$endtime = 60 * 60;
							$selected = false;
							for ($time = 0; $time <= $endtime; $time += 5) {
								$stime = str_pad(floor($time / 60), 2, "0", STR_PAD_LEFT) . ":" .
										str_pad($time % 60, 2, "0", STR_PAD_LEFT);
								$selected_hours_plan = "";
								if ($this->event) {
									if (!$selected && $this->event->hours_plan <= round($time / 60, 2)) {
										$selected_hours_plan = "selected";
										$selected = true;
									}
								}
								else if ($this->todo_hours_plan) {
									if (!$selected && $this->todo_hours_plan <= round($time / 60, 2)) {
										$selected_hours_plan = "selected";
										$selected = true;
									}
								}
								?>
								<option value="<?= $stime ?>" <?= $selected_hours_plan ?> ><?= $stime ?></option>
							<? } ?>
            </select>

						<?= $this->lists['select_user'] ?>
						<?= $this->lists['select_state'] ?>
          </td>
        </tr>

        <tr>
          <td class="key" nowrap>
						<?php echo JText::_('Project_Task'); ?>:
          </td>
          <td class="val">
            <div style="float:left; margin-right: 6px;"><?= $this->lists['select_project'] ?></div>
            <div id="block_task_id" style="float:left; margin-right: 6px;">
							<?= $this->lists['select_task']; ?>
            </div>

						<? if ($this->lists["select_goals"] != "") { ?>

							<div id="block_target_id">
								<?= $this->lists['select_goals'] ?>
							</div>

						<? } ?>

          </td>
        </tr>

        <tr>
          <td class="key" nowrap>
						<?= JText::_("Todos group") ?>:
          </td>
          <td class="val">
            <div id="block_todo_id" style="float:left;">
							<?php echo $this->lists['select_todo']; ?>
            </div>
            <div>
              <span class="key" style="padding-left: 20px;">
                <input type="hidden" name="is_parent" value="<?= (int) $this->event->is_parent ?>">
                <!--
                <input type="hidden" name="is_parent" value="0">
                <input type="checkbox" name="is_parent" id="is_parent"
                       value="1" <?=
							(int) $this->event->is_parent ? "checked" : "disabled"
							?>>
								<?php echo JText::_('This is a Team todo'); ?>
                -->

                <input type="hidden" name="showskills" value="0">
								<? TeamTime::helper()->getDotu()->getHtmlShowSkills($this->event) ?>
              </span>
            </div>
          </td>
        </tr>

        <tr>
          <td colspan="2" class="val">
						<div class="main-editor"
								 data-field-type="todo"
								 data-current-id="<?= $this->event->id ?>">
									 <?=
									 $editor->display('descr', $this->descr, "100%", "", '60', '20')
									 ?>
						</div>
          </td>
        </tr>

        <tr>
          <td colspan="2" class="val">
            <table width="100%" cellspacing="0" cellpadding="0" class="t3col">

              <tr>
                <td width="100%"  nowrap>
                  <span class="key">
                    <input type="checkbox" id="sendmail"  name="sendmail" value="1">
										<?php echo JText::_('Note by email'); ?></span>
                  <br>
									<?
									$helperFormals->getTodoNotifyClient($this->event, "calendar")
									?>
                </td>


								<? if ($acl->isSuperAdmin()) { ?>

									<td nowrap>
										<span class="key"><?php echo JText::_('Hourly rate'); ?>:</span>
									</td>
									<td>
										<input name="hourly_rate" id="hourly_rate"
													 value="<?= $this->hourly_rate ?>" size="5">
									</td>

									<?
								}
								else if ($acl->isAdmin()) {
									?>

									<td nowrap>
										<span class="key"><?php echo JText::_('Hourly rate'); ?>:</span>
									</td>
									<td>
										<input disabled id="hourly_rate1"
													 value="<?= $this->hourly_rate ?>" size="5">
										<input name="hourly_rate" id="hourly_rate" type="hidden"
													 value="<?= $this->hourly_rate ?>" size="5">
									</td>

									<?
								}
								else {
									?>

									<td nowrap>
									</td>

									<td>
										<input name="hourly_rate" id="hourly_rate" type="hidden"
													 value="<?= $this->hourly_rate ?>" size="5">
									</td>

								<? } ?>

                <td><?
								TeamTime::helper()->getFormals()->getTodoParams($this->event, "hoursplan")
								?></td>
              </tr>

              <tr>
                <td nowrap>
                </td>

                <td nowrap>
                  <span class="key"><?php echo JText::_('Overhead Expenses'); ?>:</span>
                </td>

                <td nowrap>
                  <input name="costs" id="costs"
                         value="<?=
									$this->event ? $this->event->costs : ""
								?>" size="9" maxlength="9">
                </td>

                <td><?
												 TeamTime::helper()->getFormals()->getTodoParams($this->event, "expenses")
								?></td>
              </tr>

            </table>
          </td>
        </tr>

        <tr>
          <td colspan="2" class="val">
						<?
						TeamTime::helper()->getBase()->getTodoRepeatParams($this->event, $this->todo_date)
						?>
          </td>
        </tr>

      </table>

    </form>
  </div>
</div>

<? TeamTime::helper()->getBase()->getRepeatTodoEditcode() ?>