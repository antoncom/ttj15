<?php
defined('_JEXEC') or die('Restricted access');

$helperBpmn = TeamTime::helper()->getBpmn();

$user = & JFactory::getUser();

$list_orders = array(
	"a.name" => false,
	"a.modified" => false,
	"a.tags" => false,
	"a.space_id" => false
);
foreach ($list_orders as $i => $v) {
	if ($i == $this->lists["order"]) {
		$list_orders[$i] = "bp_button_half_round_22-Selected";
	}
	else {
		$list_orders[$i] = "bp_button_half_round_22-Unselected";
	}
}

$currentUrl = 'index.php?option=' . $this->option . '&controller=' . $this->controller;
?>

<form action="index.php" method="post" name="adminForm"  id="adminForm">

	<div class="middle_block">
		<div class="activeArchivePanel activeArchivePanel-spaces">

			<div class="activeArchiveHeader">
				<div class="typeListBoxPanel activeArchiveHeaderElement">
					<?= $this->lists["select_archived"] ?>
				</div>
				<div class="titleLabel activeArchiveHeaderElement">
					<?= JText::_("Processes") ?> (<?= $this->total ?>)
				</div>

				<div class="sortByControlsContainer" style="width:300px;">
					<div class="sortByControls">
						<div class="bp_button_half_round_22 <?= $list_orders["a.name"] ?>">
							<div class="left_round">
								<div class="blue1_blue2">
									<table>
										<tbody>
											<tr>
												<td class="left">
													&nbsp;
												</td>
												<td class="mid">
													<div class="midContainer btnSortByName">
														Name
													</div>
												</td>
												<td class="right">
													&nbsp;
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="bp_button_half_round_22 <?= $list_orders["a.modified"] ?>">
							<div class="square">
								<div class="blue1_blue2">
									<table>
										<tbody>
											<tr>
												<td class="left">
													&nbsp;
												</td>
												<td class="mid">
													<div class="midContainer btnSortByDate">
														Date
													</div>
												</td>
												<td class="right">
													&nbsp;
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="bp_button_half_round_22 <?= $list_orders["a.tags"] ?>">
							<div class="square">
								<div class="blue1_blue2">
									<table>
										<tbody>
											<tr>
												<td class="left">
													&nbsp;
												</td>
												<td class="mid">
													<div class="midContainer btnSortByTags">
														Tag
													</div>
												</td>
												<td class="right">
													&nbsp;
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="bp_button_half_round_22 <?= $list_orders["a.space_id"] ?>">
							<div class="right_round">
								<div class="blue1_blue2">
									<table>
										<tbody>
											<tr>
												<td class="left">
													&nbsp;
												</td>
												<td class="mid">
													<div class="midContainer btnSortBySpace">
														Space
													</div>
												</td>
												<td class="right">
													&nbsp;
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="clearBoth">
						</div>
					</div>
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
										 data-placeholder="фильтр...">
							<div class="filter_cancel">
							</div>
						</div>
						<div class="clearBoth">
						</div>
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
										 data-placeholder="фильтр...">
							<div class="filter_cancel filter_cancel_activ">
							</div>
						</div>
						<div class="clearBoth">
						</div>
					</div>

				<? } ?>

			</div>

			<div class="listContainer">

				<?
				if (!$this->lists["is_grouped"]) {
					$items = $this->items["Untagged"];
					?>

					<div class="dynamicScrollPanel">

						<div class="itemSummaryListPanel">
							<div class="empty" style="display: none; ">
								<i>There are no active spaces</i>
							</div>

							<? include(dirname(__FILE__) . "/partials/item.php") ?>

						</div>

					</div>

					<?
				}
				else {
					?>

					<div class="itemSummaryMultipleListPanel">
						<div class="itemSummaryMultipleListPanelContainer">
							<table width="100%">
								<tbody>

									<?
									foreach ($this->items as $tag => $items) {

										if (sizeof($items) == 0) {
											continue;
										}
										?>

										<tr>
											<td>
												<div class="itemSummaryListPanel">
													<div class="expandableGroupHeader">
														<div class="expanded" title="Click to collapse">
															<div>
																<div>
																	<div class="label">
																		<?= $tag ?>
																	</div>
																</div>
																<div class="tripleFlowPanel groupHeaderCounter">
																	<div class="left">
																	</div>
																	<div class="mid">
																		<div class="gwt-Label">
																			<?= sizeof($items) ?>
																		</div>
																	</div>
																	<div class="right">
																	</div>
																	<div class="clearBoth">
																	</div>
																</div>
																<div class="clearBoth">
																</div>
															</div>
														</div>
													</div>

													<? include(dirname(__FILE__) . "/partials/item.php") ?>

												</div>
											</td>
										</tr>

									<? } ?>
								</tbody>
							</table>
						</div>
						<div class="gwt-HTML" style="display: none; ">
							<i>There are no matching items</i>
						</div>
					</div>



				<? } ?>

			</div>

		</div>

	</div>

	<input type="hidden" id="processId" name="cid[]" value="" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" id="task" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" id="filter_order" value="<?= $this->lists['order'] ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_('form.token'); ?>

</form>