
var submitbutton;
var SwimlanePanelButtons = null;

TeamTime.jQuery(function ($) {

	SwimlanePanelButtons = {};

	SwimlanePanelButtons.buttonPlay = $("#toolbar-playprocess");
	SwimlanePanelButtons.buttonSave = $("#toolbar-saveprocess");
	SwimlanePanelButtons.buttonExit = $("#toolbar-exitprocess");

	SwimlanePanelButtons.buttonGrid = $("#SwimlanePanel-toolbar .cmdSnapGrid10");

	SwimlanePanelButtons.selectorShowInfo = $("#filter_show");
	//SwimlanePanelButtons.selectorShowInfoFullscreen = $("#filter_show_fullscreen");

	SwimlanePanelButtons.isTemplate = false;

	var changePlayButton = function (buttonType) {
		var obj = $(".icon-32-playprocess", SwimlanePanelButtons.buttonPlay);
		var content = $(obj).parent().html();

		if (buttonType == "stop") {
			content = content.split("Playback").join("Stop");
			$(obj).parent().html(content);
			$(".icon-32-playprocess", SwimlanePanelButtons.buttonPlay).addClass("stop");
			$("#toolbar-playprocess2 .icon-32-playprocess").addClass("stop");
		}
		else {
			content = content.split("Stop").join("Playback");
			$(obj).parent().html(content.replace());
			$(".icon-32-playprocess", SwimlanePanelButtons.buttonPlay).removeClass("stop");
			$("#toolbar-playprocess2 .icon-32-playprocess").removeClass("stop");
		}
	};

	var changePlayButtonAtStart = function () {
		changePlayButton(parseInt($("#isProcessStarted").val())? "stop" : "start");
	};

	SwimlanePanelButtons.changePlayButton = function () {
		var obj = $(".icon-32-playprocess", SwimlanePanelButtons.buttonPlay);
		changePlayButton(!$(obj).hasClass("stop")? "stop" : "start");
	};

	changePlayButtonAtStart();

	$("#processDiagram").height($("body").height() - 150);

	$(window).resize(function() {
		if ($(document.body).hasClass("fullscreen")) {
			$("#processDiagram").height($("body").height() - 10);
		}
		else {
			$("#processDiagram").height($("body").height() - 150);
		}
	});
});