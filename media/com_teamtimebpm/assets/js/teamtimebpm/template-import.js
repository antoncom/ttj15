
TeamTime.jQuery(function ($) {

	$("#importTemplate").click(function () {
		$.post(TeamTime.getUrlForTask("importAsProcess"), {
			id: purl().param("template_id"),
			space_id: $("#space_id").val()
		},
		function (data) {
			alert("Data saved");
			parent.TeamTime.jQuery.fancybox.close();
		});
	});

	$("#cancelImportTemplate").click(function () {
		parent.TeamTime.jQuery.fancybox.close();
	});

});