//
// TeamTime Career
//

TeamTime.Career = {

	loadTargetsSelector: function (callback) {
		var $ = TeamTime.jQuery;

		$.get(TeamTime.baseUrl +
			"index.php?option=com_teamtimecareer&controller=targetvector&task=selector_by_task"
			+ "&task_id=" + $("#curtaskid").val()
			+ "&user_id=" + $("#user_id").val()
			+ "&showskills=" + ($("#showskills").attr("checked")? "1" : "0")
			+ "&t=" + new Date().getTime(),

			function (data) {
				$("#block_target_id").html(data);

				$("#target_id").change(function() {
					callback($("#target_id").val());
				});

			});
	}

};