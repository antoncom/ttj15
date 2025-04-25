<?php
defined('_JEXEC') or die('Restricted access');

$editor = & JFactory::getEditor();

$document = & JFactory::getDocument();
$lang = &JFactory::getLanguage();
$user = & JFactory::getUser();

$helperFormals = TeamTime::helper()->getFormals();
$canNotifyUserByEmail = $this->process->is_started ? "" : "disabled";
?>

<style>
	@import url("/<?= URL_MEDIA_COMPONENT_ASSETS . 'css/details_edit.css' ?>");
</style>

<div id="main_calendar_block">
  <div class="toolBotton">
    <a id="Savebtn" class="imgbtn" href="javascript:void(0);">
      <span class="Save"  title="Save the calendar"><?= JText::_('Save') ?>(<u>S</u>)
      </span>
    </a>

		<a id="Closebtn" class="imgbtn" href="javascript:void(0);">
      <span class="Close" title="Close the window" ><?= JText::_('Close') ?>
      </span>
    </a>
  </div>

  <div style="clear: both">
  </div>

  <div class="infocontainer">
    <form action="" class="fform" id="fmEdit" method="post">

      <input id="timezone" name="timezone" type="hidden" value="" />
      <input id="IsAllDayEvent" name="IsAllDayEvent1" type="hidden" value="1"/>
      <input id="was_repeat" name="was_repeat" type="hidden" value="" />

      <table width="100%" cellspacing="0" cellpadding="0">

        <tr>
          <td class="key" nowrap>
						<?= JText::_('Subject1') ?>:
          </td>
          <td width="100%" class="val">
            <div style="float:left; width:92%">
              <input MaxLength="200" class="required safe" id="Subject" name="Subject" style="width:100%;" type="text" value="<?= $this->item->title ?>" />
              <input id="colorvalue" name="colorvalue" type="hidden"
                     value=""/>
            </div>
            <div id="calendarcolor" style="clear:right; margin-left:5px;"></div>
          </td>
        </tr>

        <tr>
          <td class="key" nowrap>
						<?php echo JText::_('Time1'); ?>:
          </td>
          <td class="val">
						<div class="dateStart" style="float:left; padding-right: 4px;">
							<input type="hidden" id="realStartDate" value="">
							<input MaxLength="10" class="required date" id="startDate" name="startDate" style="padding-left:2px;width:90px;" type="text"
										 value="<?= $this->todo_date ?>" />&nbsp;
							<select id="startTime" name="startTime"
											data-hours="<?= $this->todo_hours ?>"
											style="width:60px;" ></select>
						</div>
            <span class="key"><?php echo JText::_('Planned hours'); ?>:</span>            
            <select id="hoursPlan" name="hoursPlan"
										data-hours-plan="<?= $this->todo_hours_plan ?>"
										style="width:60px;" ></select>

						<?= $this->lists['select_user'] ?>
						<?= $this->lists['select_state'] ?>
          </td>
        </tr>

        <tr>
          <td class="key" nowrap>
						<?= JText::_('Project_Task') ?>:
          </td>
          <td class="val">
            <div style="float:left; margin-right: 6px;"><?= $this->lists['select_project'] ?></div>
            <div id="block_task_id" style="float:left; margin-right: 6px;">
							<?= $this->lists['select_task'] ?>
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
							<?= $this->lists['select_todo'] ?>
            </div>
            <div>
              <span class="key" style="padding-left: 20px;">
                <input type="hidden" name="is_parent" value="">
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
                  <span class="key sendmail-<?= $canNotifyUserByEmail ?>">
                    <input type="checkbox" id="sendmail" name="sendmail" 
										<?= $canNotifyUserByEmail ?>
													 value="1">
										<?php echo JText::_('Note by email'); ?></span>
									<br>
									<?
									$helperFormals->getTodoNotifyClient($this->event, "calendar")
									?>
                </td>

								<? if ($user->usertype == "Super Administrator") { ?>

									<td nowrap>
										<span class="key"><?= JText::_('Hourly rate'); ?>:</span>
									</td>
									<td>
										<input name="hourly_rate" id="hourly_rate"
													 value="<?= $this->hourly_rate ?>" size="5">
									</td>

									<?
								}
								else if ($user->usertype == "Administrator") {
									?>

									<td nowrap>
										<span class="key"><?= JText::_('Hourly rate') ?>:</span>
									</td>
									<td>
										<input disabled id="hourly_rate1" value="<?= $this->hourly_rate ?>" size="5">
										<input name="hourly_rate" id="hourly_rate" type="hidden" value="" size="5">
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
                  <span class="key"><?= JText::_('Overhead Expenses') ?>:</span>
                </td>

                <td nowrap>
                  <input name="costs" id="costs"
                         value="" size="9" maxlength="9">
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
						TeamTime::helper()->getBase()->getTodoRepeatParams($this->event,
								$this->todo_date)
						?>
          </td>
        </tr>

      </table>

    </form>
  </div>
</div>

<? TeamTime::helper()->getBase()->getRepeatTodoEditcode() ?>