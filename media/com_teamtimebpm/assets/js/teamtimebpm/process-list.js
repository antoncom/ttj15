
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

	$(".btnSortBySpace").click(function () {
		$("#filter_order").val("a.space_id");
		$(adminForm).submit();
	});

	// make context menu for blocks

	var onBlockContextMenuCmd = function (key, options, src) {

		var processId = $(src).attr("data-id");
		console.log(key);
		console.log(processId);

		// archive commands
		if (key == "archieve" || key == "restore") {
			$.post(TeamTime.getUrlForTask("setData"), {
				cmd: key,
				id: processId
			},
			function (data) {
				location.href = TeamTime.getUrlForController();
			});

		}
		else if (key == "editdiagram") {
			location.href = TeamTime.getUrlForController() +
			"&view=processdiagrampage&id=" + processId;
		}
		else if (key == "delete") {
			var name = $.trim($(".browserShortenedLabel.name", src).text());
			if (confirm("Вы удаляете процесс: " + name + "\nБудут удалены все задания процесса")) {
				$("#processId").val(processId);
				$("#task").val("remove");
				$(adminForm).submit();
			}
		}
		else if (key == "import") {
			var url = TeamTime.getUrlForController() +
			"&tmpl=component&view=processimport" +
			"&process_id=" + processId;

			console.log(url);

			$.fancybox({
				href: url,
				type: 'iframe',
				width: 400,
				height: 220,
				autoSize: false,
				padding : 1,
				openEffect: 'none',
				closeEffect: 'none',
				helpers : {
					overlay : {
						css : {
							'background' : 'rgba(0, 0, 0, 0.4)'
						}
					}
				}
			});
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
				"import": {
					name: "Save as template",
					icon: "edit"
				},
				"editdiagram": {
					name: "Edit diagram",
					icon: "edit"
				},
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