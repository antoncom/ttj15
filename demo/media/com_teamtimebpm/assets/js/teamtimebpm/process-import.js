
TeamTime.jQuery(function ($) {

	$("#saveAsTemplate").click(function () {
		$.post(TeamTime.getUrlForTask("saveAsTemplate"), {
			id: purl().param("process_id")
		},
		function (data) {
			alert("Data saved");
			parent.TeamTime.jQuery.fancybox.close();
		});
	});

	$("#cancelSaveAsTemplate").click(function () {
		parent.TeamTime.jQuery.fancybox.close();
	});

});