
TeamTime.jQuery(function ($) {
	
	// get diagram window
	var srcFrame = parent.TeamTime.jQuery("#processDiagramWindow")[0];
	srcFrame = srcFrame.contentWindow || srcFrame.contentDocument || srcFrame.document;

	var swPanel = srcFrame.draw2d.bpmn.getSwimlanePanel();
	var workflow = srcFrame.draw2d.bpmn.getWorkflow();
	var currentFigure = workflow.getFigure(purl().param("id"));

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

	//

	$(".simpleSelectProcessRow").click(function () {
		// TODO delete created todo
		//currentFigure.paramsData._id = 0;

		if (!currentFigure.paramsData) {
			currentFigure.paramsData = {};
		}
		currentFigure.paramsData.linkedId = parseInt($(this).attr("data-id"));
		currentFigure.paramsData.name = $.trim($(".processName", this).text());
		currentFigure.setText(currentFigure.paramsData.name);

		console.log(currentFigure.paramsData);

		parent.TeamTime.jQuery.fancybox.close();
		parent.submitbutton("saveprocess");
	});

});