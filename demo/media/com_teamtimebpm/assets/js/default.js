TeamTime.jQuery(function ($) {

	if ($("#FullscreenSwitch").length > 0) {

		// add buttons titles
		$("#FullscreenSwitch").text($("#FullscreenSwitch").attr("title"));
		$("#SwimlanePanel-toolbar .show_grid")
		.text($("#SwimlanePanel-toolbar .show_grid").attr("title"));

		$("#FullscreenSwitch").click(function () {
			$(this).toggleClass("max");

			if ($(document.body).hasClass("fullscreen")) {
				$("#processDiagram").height($("body").height() - 150);
			}
			else {
				$("#processDiagram").height($("body").height() - 10);
			}

			$("html").toggleClass("fullscreen");
			$(document.body).toggleClass("fullscreen");

			if ($(document.body).hasClass("fullscreen")) {
				// remove buttons titles
				$("#FullscreenSwitch").text("");
				$("#SwimlanePanel-toolbar .show_grid").text("");
			}
			else {
				// add buttons titles
				$("#FullscreenSwitch").text($("#FullscreenSwitch").attr("title"));
				$("#SwimlanePanel-toolbar .show_grid")
				.text($("#SwimlanePanel-toolbar .show_grid").attr("title"));
			}
		});

	}

});
