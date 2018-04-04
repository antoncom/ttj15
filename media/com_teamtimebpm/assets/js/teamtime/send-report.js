TeamTime.jQuery(function ($) {

	var setSendReportCheckbox = function () {
		if ($('#send_report').attr("disabled")) {
			return false;
		}

		if ($('#close_todo').attr("checked")) {
			$('#send_report').attr("checked", "checked");
		}

		return false;
	};

	$('#toggle_close_todo').click(setSendReportCheckbox);
	$('#close_todo').change(setSendReportCheckbox);

	$('#toggle_send_report').click(
		function () {
			if ($('#send_report').attr("disabled")) {
				return false;
			}

			if ($('#send_report').attr("checked")) {
				$('#send_report').removeAttr("checked");
			}
			else {
				$('#send_report').attr("checked", "checked");
			}

			return false;
		});

});