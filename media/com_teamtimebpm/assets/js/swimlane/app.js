
TeamTime.jQuery(window).load(function () {

	var $ = TeamTime.jQuery;
	var buttonsPanel = parent.SwimlanePanelButtons;

	var changeShowInfo = function () {
		var src;
		//var dest;

		src = buttonsPanel.selectorShowInfo;

		/*
		if (parent.jQuery("body").hasClass("fullscreen")) {
			src = buttonsPanel.selectorShowInfoFullscreen;
			dest = buttonsPanel.selectorShowInfo;
		}
		else {
			src = buttonsPanel.selectorShowInfo;
			dest = buttonsPanel.selectorShowInfoFullscreen;
		}*/

		// update second selector
		//$(dest).val($(src).val());

		// update info with current selector
		swPanel.showInfo($(src).val());
	};

	var loadWorkflow = function (id) {
		// url for process
		var url = TeamTime.getUrlForTask("loadDiagram");

		// for template - get other source url
		if (buttonsPanel.isTemplate) {
			url = TeamTime.getUrlForController("template") + "&task=loadDiagram";
		}

		$.get(url, {
			id: id
		},
		function (data) {
			if (data == "") {
				//alert("Данных нет");
				return;
			}

			var sJson = new draw2d.bpmn.JsonSerializer();
			var swPanel = draw2d.bpmn.getSwimlanePanel();
			swPanel.clear();
			sJson.unserialize(swPanel, data);

			console.log("diagram loaded");

			changeShowInfo();
		});
	};

	// init path for assets
	draw2d.bpmn.assetsUrl = TeamTime.getMediaAssetsUrl();

	var swPanel = new SwimlanePanel();
	draw2d.bpmn.setSwimlanePanel(swPanel);

	swPanel.init();
	loadWorkflow(purl().param("id"));
});