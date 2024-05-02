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
					<td>
						Показать:
					</td>
					<td>
						<?= $this->lists["select_show"] ?>
					<td>
					<td>
						<input id="SwimlanePanel-toolbar-SnapGrid10" class="cmdSnapGrid10" type="checkbox" />
						<label for="SwimlanePanel-toolbar-SnapGrid10">
							<span class="show_grid">
								Grid
							</span>
						</label>
					</td>
				</tr>
			</tbody>
		</table>
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
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="1" />
	<input type="hidden" name="filter_order" id="filter_order" value="<?= $this->lists['order'] ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>