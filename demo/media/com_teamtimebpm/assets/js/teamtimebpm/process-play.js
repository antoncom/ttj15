TeamTime.jQuery(function ($) {

	// init

	/*$("#btnCancel").click(function () {
		parent.jQuery.fancybox.close();
	});*/

	$("#btnStartPlay").click(function () {
		$.post(TeamTime.getUrlForTask("playProcess"), {
			id: purl().param("id")
		},
		function (data) {
			var result = JSON.parse(data);

			alert(result.msg);

			parent.SwimlanePanelButtons.changePlayButton();
			parent.TeamTime.jQuery.fancybox.close();
		});
	});

});
