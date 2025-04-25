
var submitbutton;
var SwimlanePanelButtons = null;

TeamTime.jQuery(function ($) {

	SwimlanePanelButtons = {};

	SwimlanePanelButtons.buttonPlay = $("#toolbar-playtemplate");
	SwimlanePanelButtons.buttonSave = $("#toolbar-savetemplate");
	SwimlanePanelButtons.buttonExit = $("#toolbar-exittemplate");

	SwimlanePanelButtons.buttonGrid = $("#SwimlanePanel-toolbar .cmdSnapGrid10");

	SwimlanePanelButtons.selectorShowInfo = $("#filter_show");
	//SwimlanePanelButtons.selectorShowInfoFullscreen = $("#filter_show_fullscreen");
	
	SwimlanePanelButtons.isTemplate = true;

	$("#processDiagram").height($("body").height() - 150);

	$(window).resize(function() {
		$("#processDiagram").height($("body").height() - 150);
	});
});