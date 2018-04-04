<?php
defined('_JEXEC') or die('Restricted access');
?>

<!-- swimlane panel toolbar -->

<div id="SwimlanePanel-toolbar">
	<!--div class="header_top">
		<div class="menu">
			<div class="cmd_menu">
				<button class="cmdPlay">
					<div class="cmd_img cmd_playback"></div>
					<div class="cmd_label">Playback</div>
				</button>
			</div>
			<div class="cmd_menu">
				<button class="cmdSave">
					<div class="cmd_img cmd_save cmdSave"></div>
					<div class="cmd_label">Save</div>
				</button>
			</div>
			<div class="cmd_menu">
				<button class="cmdClose">
					<div class="cmd_img cmd_end-edit"></div>
					<div class="cmd_label">End Edit</div>
				</button>
			</div>
		</div>
		<div class="title-diagramm">Типовая веб-разработка функции сайта</div>
		<div class="subtitle-sfera">Сфера: Процессы МедиаПаблиш</div>
	</div-->

	<div class="header_bottom">
		<table>
			<tbody>
				<tr>
					<td class="title-diagramm"><?= $this->process->name ?></td>
					<td>
						Показать:&nbsp;&nbsp;
						<?= $this->lists["select_show"] ?>
					<td>
					<td>
						<input id="SwimlanePanel-toolbar-SnapGrid10" class="cmdSnapGrid10" type="checkbox" />
						<label for="SwimlanePanel-toolbar-SnapGrid10">
							<span class="show_grid" title="Grid">
							</span>
						</label>
					</td>

					<td><div id="FullscreenSwitch" title="На весь экран"></div></td>

				</tr>
			</tbody>
		</table>
		<div class="toolbar" id="toolbar">
			<table class="toolbar"><tbody><tr>
						<td class="button" id="toolbar-playprocess2">
							<a href="#" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('Выберите из списка для');}else{  submitbutton('playprocess')}" class="toolbar">
								<span class="icon-32-playprocess" title="Playback">
								</span>
							</a>
						</td>

						<td class="button" id="toolbar-saveprocess2">
							<a href="#" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('Выберите из списка для');}else{  submitbutton('saveprocess')}" class="toolbar">
								<span class="icon-32-saveprocess" title="Сохранить">
								</span>
							</a>
						</td>

						<td class="button" id="toolbar-exitprocess2">
							<a href="#" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('Выберите из списка для');}else{  submitbutton('exitprocess')}" class="toolbar">
								<span class="icon-32-exitprocess" title="End Edit">
								</span>
							</a>
						</td>

					</tr></tbody></table>
		</div>

		<div class="time">
			<div>
				План часов: <span id="totalPlannedHours">0,0</span> ч.
			</div>
			<div>
				Факт часов: <span id="totalFactHours">0,0</span> ч.
			</div>
		</div>
	</div>

</div>

<!-- /swimlane panel toolbar -->


<div id="processDiagram">
	<iframe id="processDiagramWindow" src="<?= $this->frameUrl ?>"
					width="100%"
					height="100%"
					scrolling="yes"
					frameborder="no"></iframe>
</div>

<form id="adminForm" name="adminForm">

	<input type="hidden" id="isProcessStarted" value="<?= $this->process->is_started ?>" />

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="1" />
	<input type="hidden" name="filter_order" id="filter_order" value="<?= $this->lists['order'] ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>
