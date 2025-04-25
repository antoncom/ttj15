
TeamTime.jQuery(function ($) {

	var adminForm = "#adminForm";

	// filter buttons

	$(".filter_cancel").click(function () {
		$('#search').val("");
		$(adminForm).submit();
	});

	$("#search").keypress(function (e) {
		if (e.which == 13) {
			$(adminForm).submit();
		}
	});

	TeamTime.form.initPlaceholder($("#search"), $("#search").attr("data-placeholder"));

	$(".filterWidget").click(function () {
		$("#search").focus();
	});

	// sort by field buttons

	$(".btnSortByName").click(function () {
		$("#filter_order").val("a.name");
		$(adminForm).submit();
	});

	$(".btnSortByDate").click(function () {
		$("#filter_order").val("a.modified");
		$(adminForm).submit();
	});

	$(".btnSortByTags").click(function () {
		$("#filter_order").val("a.tags");
		$(adminForm).submit();
	});

	// make context menu for blocks

	var onBlockContextMenuCmd = function (key, options, src) {

		console.log(key);

		// archive commands
		if (key == "archieve" || key == "restore") {
			$.post(TeamTime.getUrlForTask("setData"), {
				cmd: key,
				id: $(src).attr("data-id")
			},
			function (data) {
				location.href = TeamTime.getUrlForController();
			});
		}
		else if (key == "delete") {
			var name = $.trim($(".browserShortenedLabel.name", src).text());
			var spaceId = $(src).attr("data-id");
			var hasProcesses = $(src).attr("data-hasProcesses") == "1";

			var removeSpace = !hasProcesses;
			if (hasProcesses) {
				removeSpace = confirm("Данная сфера содержит процессы и шаблоны. Вы действительно хотите удалить ее вместе с процессами и шаблонами?");
			}

			if (removeSpace) {
				$("#spaceId").val(spaceId);
				$("#task").val("remove");
				$(adminForm).submit();
			}
		}
	};

	$.contextMenu({
		selector: '.itemSummary .popupMenu',
		ignoreRightClick: true,

		build: function ($trigger, e) {
			var result = {};
			var src = $($trigger).closest(".itemSummary");

			result.determinePosition = function ($menu) {
				$menu.position({
					my: "left top",
					at: "left bottom",
					of: this
				});
			};

			result.callback = function (key, options) {
				onBlockContextMenuCmd(key, options, src);
			};

			result.items = {
				"delete": {
					name: "Remove",
					icon: "delete"
				}
			};

			if ($(src).attr("data-archived") == "active") {
				result.items["archieve"] = {
					name: "Archieve",
					icon: "edit"
				};
			}
			else {
				result.items["restore"] = {
					name: "Restore",
					icon: "edit"
				};
			}

			return result;
		}
	});

	$(".popupMenu").click(function (e) {
		e.preventDefault();
		$(this).contextMenu();
	});

});