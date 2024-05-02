
TeamTime.jQuery(function ($) {

	// init highslide
	hs.width = 740;

	var initUserReportHeader = function () {
		var s = "";
		if ($("#user_id").val() != "") {
			s = $("#user_id option:selected").text();
		}
		$("#currentUser").html(s);
	};

	$("#form_submit").click(function () {
		initUserReportHeader();
	});
	//$("#user_id").change(function () {
	//	initUserReportHeader();
	//});

	initUserReportHeader();
});

