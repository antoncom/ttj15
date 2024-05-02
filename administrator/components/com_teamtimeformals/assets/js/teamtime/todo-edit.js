TeamTime.jQuery(function ($) {

	$("#project_id").change(function () {
		TeamTime.Formals.setNotifyClientStatus($("#project_id").val());
	});

	// init code here
	TeamTime.Formals.setNotifyClientStatus($("#project_id").val());
});