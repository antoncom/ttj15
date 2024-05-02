<div class="padding">
	<div class="expandablePanel">
		<div class="flowTable">

			<? foreach ($items as $item) { ?>

				<div class="itemSummary processSummary" style="width: 33%; "
						 data-id="<?= $item->id ?>"
						 data-archived="<?= $item->archived ?>">
					<div class="hoverArea">
						<img src="/<?= URL_MEDIA_COMPONENT_ASSETS ?>images/template.png" class="gwt-Image icon" title="Process Blueprint">
						<div class="body">
							<div class="browserShortenedLabel name" style="max-width: 509px; ">
								<a href="<?= $currentUrl . '&view=space&task=edit&cid[]=' . $item->id ?>"><?= $item->name ?></a>
							</div>
							<div class="popupMenu" title="Select an action to perform on this space.">

							</div>
							<div class="followingButton <?=
			$this->mTemplate->isFollowed($item->id) ?
					"followingButton-isFollowed" :
					"followingButton-isNotFollowed"
				?>"
									 title="Follow this space">

							</div>
							<div class="clearBoth">

							</div>
							<div class="lastModified" style="min-width: 250px; ">
								<? if ($item->modified_by) { ?>
									<?=
									JText::sprintf('Last modified by %s on %s', $item->user_name,
											JHTML::_('date', $item->modified, "%b %d, %Y"))
									?>
								<? } ?>
							</div>

							<div class="tagsEditorWidget tagsEditor">
								<div class="padding">
									<div class="unboundContainer">
										<div class="container">


											<div class="tripleFlowPanel emptyTag">

												<div class="left">

												</div>

												<div class="mid">
													<div class="browserShortenedLabel label">
														&nbsp;
													</div>

												</div>
												<div class="right">

												</div>
												<div class="clearBoth">

												</div>
												<div class="deleteButton">

												</div>

											</div>

											<? foreach ($item->tags as $tag) { ?>
												<div class="tripleFlowPanel tag">


													<div class="left">

													</div>

													<div class="mid">
														<div class="browserShortenedLabel label">
															<?= $tag ?>
														</div>

													</div>
													<div class="right">

													</div>
													<div class="clearBoth">

													</div>
													<div class="deleteButton">

													</div>

												</div>

											<? } ?>

											<div class="editTags">
												Edit Tags
											</div>
											<input class="textBox" maxlength="50" style="display:none;"/>

										</div>

									</div>

								</div>

							</div>

						</div>
						<div class="clearBoth">

						</div>

					</div>

				</div>

			<? } ?>

		</div>

	</div>

</div>