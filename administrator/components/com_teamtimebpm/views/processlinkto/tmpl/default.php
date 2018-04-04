<?php
defined('_JEXEC') or die('Restricted access');
?>

<style>
	body {
		margin: 0px;
	}
</style>

<form action="" method="post" name="adminForm" id="adminForm">

	<div class="borderTImage">
		<div class="bpDialogHeader">
			Выберите связанный процесс
		</div>
	</div>
	<table class="bpDialog">
		<tbody>
			<tr>
				<td class="bpDialogContent">
					<div class="bpCreateProcessLayoutTable bpDialogClientPadding">
						<div>
							<div class="typeListBoxPanel">
								<?= $this->lists['select_spaces'] ?>
							</div>

							<? if ($this->lists['search'] == "") { ?>
								<div class="filterWidget">
									<div id="img_search" class="filter_left">
										<div>
										</div>
									</div>
									<div class="filter_middle">
										<input type="text" id="search" name="search" class="filter_text"
													 value=""
													 data-placeholder="название процесса">
										<div class="filter_cancel">
										</div>
									</div>
								</div>
								<div class="clearBoth">
								</div>
								<?
							}
							else {
								?>

								<!--активная функция фильтра-->
								<div class="filterWidget">

									<div id="img_search" class="filter_left filter_left_activ">
										<div>
										</div>
									</div>

									<div class="filter_middle">
										<input type="text" id="search" name="search" class="filter_text filter_text_activ"
													 value="<?= $this->lists['search'] ?>"
													 data-placeholder="название процесса">
										<div class="filter_cancel filter_cancel_activ">
										</div>
									</div>

								</div>
								<div class="clearBoth">
								</div>

							<? } ?>

							<div class="ListProcess">
								<div class="" style="position: relative; zoom: 1; ">
									<div>

										<? foreach ($this->processes as $process) { ?>
											<div class="simpleSelectProcessRow"
													 data-id="<?= $process->id ?>">
												<div class="floatLeft">
													<div title="<?= $this->escape($process->name) ?>" class="processName">
														<?= $process->name ?>
													</div>
													<div title="<?= htmlspecialchars($process->project_name) ?>"
															 class="projectName">
																 <?= $process->project_name ?>
													</div>

												</div>
												<div class="checkMark">

												</div>

											</div>
										<? } ?>


									</div>

								</div>

							</div>

						</div>

					</div>
				</td>
			</tr>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />

</form>