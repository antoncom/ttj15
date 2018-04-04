
TeamTime.jQuery(function ($) {

	var currentBlock = null;

	var makeDeletableTags = function (obj) {
		$(".deleteButton", obj).click(function () {
			var p = $(this).parent();
			var tag = $.trim($(".mid .label", $(this).parent()).text());
			
			var id = $(this).closest(".itemSummary").attr("data-id");

			$.post(TeamTime.getUrlForTask("removeTag"), {
				tag: tag,
				id: id
			},
			function (data) {
				// remove tag
				$(p).remove();

			// remove other blocks with this tag
			/*
				$(".itemSummary").each(function (i, n) {
					if ($(n).attr("data-id") != id) {
						return;
					}

					$(".tag", n).each(function (j, tn) {
						if ($.trim($(".mid .label", $(tn)).text()) == tag) {
							$(tn).remove();
						}
					});
				});*/
			});
		});
	};

	var refreshTags = function (tags, parent) {
		if ($.trim(tags) == "") {
			return;
		}

		var tmp = tags.split(",");
		if (tmp.length == 0) {
			return;
		}

		// remove old tags except first one
		$(".tag", parent).each(function (i, n) {
			$(n).remove();
		});

		var f = $(".emptyTag", parent);
		for (var i = 0; i < tmp.length; i++) {
			if (tmp[i] == "") {
				continue;
			}

			var b = $(f).clone();
			$(f).after(b);
			$(b).removeClass("emptyTag").addClass("tag");
			$(".mid .label", b).text(tmp[i]);

			makeDeletableTags(b);
		}
	};

	var textBoxBlur = function (obj) {
		var tags = $(obj).val();

		var onBlur = function (data) {
			var editButton = $(obj).parent().children(".editTags");

			$(obj).hide();
			$(editButton).show();

			$(obj).closest(".tagsEditor").removeClass("editing");

			refreshTags(data, $(obj).closest(".itemSummary"));
		};

		if ($.trim(tags) == "") {
			onBlur("");
		}
		else {
			var id = $(obj).closest(".itemSummary").attr("data-id");

			$.post(TeamTime.getUrlForTask("appendTag"), {
				tag: tags,
				id: id
			}, onBlur);
		}
	};

	var textBoxCancel = function (obj) {
		var editButton = $(obj).parent().children(".editTags");

		$(obj).hide();
		$(editButton).show();

		$(obj).closest(".tagsEditor").removeClass("editing");
	};

	var makeEditable = function (textBox) {
		$(textBox).keyup(function (event) {
			if (event.which == 13) {
				event.preventDefault();
				textBoxBlur(this);
			}
			else if (event.which == 27) {
				event.preventDefault();
				textBoxCancel(this);
			}
		});
	};

	$(".editTags").click(function () {
		var textBox = $(this).parent().children("input.textBox");

		$(textBox).val("").show();
		$(textBox).focus();

		$(this).hide();
		$(this).closest(".tagsEditor").addClass("editing");
	});

	$(".itemSummary").click(function () {
		if (currentBlock && currentBlock != this) {
			textBoxBlur($("input.textBox", currentBlock));
		}

		currentBlock = this;
	});
	
	$(".itemSummary").each(function (i, n) {
		makeEditable($("input.textBox", n));
	});
	
	$(".tag").each(function (i, n) {
		makeDeletableTags(n);		
	});

});