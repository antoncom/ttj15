<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

JFilterOutput::objectHTMLSafe($this->item, ENT_QUOTES);
$get = JRequest::get('get');

jimport('joomla.html.pane');

$teamtimeConfig = TeamTime::getConfig();
$mLog = new TeamtimeModelLog();
$mUser = new TeamtimeModelUser();
$user = & JFactory::getUser();
$editor = & JFactory::getEditor();

$pane = & JPane::getInstance('sliders');
$format = JText::_('DATE_FORMAT_LC2');

if ($mUser->checkPause()) {
	$mUser->resetPause();
	$mUser->setPause();
}

// get current id
$current_todo_id = 0;
$current_todo_state = TODO_STATE_OPEN;
$unclog = $mLog->getUncompletedLog($user->id);
if (sizeof($unclog) > 0) {
	$current_todo_id = $unclog[0]->todo_id;
}

$has_any_logs = false;
foreach ($this->other_logs as $row) {
	if (sizeof($row['logs']) > 0) {
		$has_any_logs = true;
		break;
	}
}

$helperBase = TeamTime::helper()->getBase();
?>

<div id="yoo-teamlog">

  <h1 class="user">
		<?php echo $this->user->name; ?>
    <a id="todos-trigger" class="todos-trigger" style="text-transform: none;"
       href="javascript:void(0)">[<?= JText::_("TODOS"); ?>: <?php echo count($this->todos); ?>]</a>
    <a id="projects-trigger" class="todos-trigger" style="text-transform: none;"
       href="javascript:void(0)">[<?= JText::_("PROJECTS"); ?>: <?php echo count($this->projects); ?>]</a>
  </h1>

  <div class="todos">
    <div id="todos" class="todos-line">
			<?php include('todos.php'); ?>
    </div>
  </div>

  <!-- todos end -->

  <div class="logs">

    <div class="left">

      <div class="log-panel">

        <form id="mainFormLog"
              method="post" action="<?php echo JRoute::_('index.php'); ?>">
          <input type="hidden" id="todo_id" name="todo_id" value="<?= $current_todo_id ?>">

          <div>
            <table width="100%" border="0" cellspacing="5">
              <tr>
                <td valign="top"><?php include('selectproj.php'); ?></td>
                <td width="100%" valign="bottom">
									<div class="report-message-block">
										<? include('inputcomment.php') ?>

                    <table>
                      <tr valign="center">
                        <td height="20">
													<? include('durationselect.php') ?>
                        </td>
                        <td height="20">
													<? if ($teamtimeConfig->show_costs == "0") { ?>
														<input name="money" id="tmoney" style="display:none;" value="">
														<?
													}
													else {
														?>
														<div id="money" style="display: none;">
															<label>Накладные&nbsp;расходы:&nbsp;<input name="money" id="tmoney" alt="p9x" value="" size="4"></label>
														</div>
													<? } ?>
                        </td>
                      </tr>
                    </table>

										<? include('startstoplog.php') ?>
                  </div>
								</td>
              </tr>
            </table>

          </div>
          <input type="hidden" value="" id="logs_checklist" name="logs_checklist"/>
					<input type="hidden" value="com_teamtime" name="option"/>
					<?php echo JHTML::_('form.token'); ?>
        </form>

        <div id="project-description">
					<?php
					//get current todo description
					if (isset($get['status']) && $get['status'] == "started") {
						if ($this->_models['log']->_user_todos) {
							foreach ($this->_models['log']->_user_todos as $tod) {
								if ($tod->id == $current_todo_id) {
									echo $tod->description;
								}
							}
						}
					}
					?>
        </div>


        <div id="project_data">
        </div>

        <p>&nbsp;</p>
        <div id="task_data">
        </div>

      </div>
      <!-- log-panel end -->

    </div>
    <!-- left-log end -->

    <table width="100%"  border="0">
      <tr>
        <td valign="top" width="100%"><div class="user-log">

						<?php foreach ($this->user_logs as $date => $logs) : ?>
							<span class="date">
								<?php echo $date; ?>
							</span>
							<ul class="log">
								<?php foreach ($logs as $log) : ?>
									<li>
										<span href="javascript:void(0)" class="tooltip"
													title="<?php
							echo $log->getProjectName() . ' :: ' . $log->getTaskName() .
							' (' . $log->getDurationText() . ')';
									?>"><?=
											$helperBase->getReportText(array(
												"id" => $log->id,
												"content" => $log->description,
												"title" => $log->getTodoTitle()))
									?>
														<?php
														echo $log->money != 0 ? ("[" . $log->money . " руб.]") : "";
														?><br>
											<div class="light"><?php echo $log->getProjectName() . ' :: ' . $log->getTaskName() . ' (' . $log->getDurationText() . ')'; ?></div>
										</span>
										<?php
										//if(DateHelper::isToday($log->date)){
										?>
										<span class="delta">
											- <?php
								echo JText::sprintf('%S ago', $log->getDeltaText());
										?>
											<a href="<?php echo JRoute::_('index.php?log_id=' . $log->id . '&task=removelog'); ?>"><?php echo JText::_('Delete'); ?></a>
										</span>
										<?php
										//}
										?>

									</li>
								<?php endforeach; ?>
							</ul>
						<?php endforeach; ?>
          </div>
          <!-- user-log end --></td>

        <td valign="top" align="right">
          <div class="right">
						<?php if ($has_any_logs) : ?>
							<h5 align="left" class="style1"><?= JText::_("CURRENT_TASKS") ?>: </h5>
							<div class="team-log">
								<div class="team-log-t">
									<div class="team-log-b">
										<div class="team-log-l">
											<div class="team-log-r">
												<div class="team-log-tl">
													<div class="team-log-tr">
														<div class="team-log-bl">
															<div class="team-log-br">
																<div class="team-log-loading">
																	<div class="team-log-line">
																		<div id="team-log" class="team-log-hole">
																			<?php include('teamtime.php'); ?>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?php endif; ?>
            <!-- team-log end -->
          </div>
          <!-- right-log end --></td>
      </tr>
    </table>

  </div>
  <!-- log-container end -->

</div>