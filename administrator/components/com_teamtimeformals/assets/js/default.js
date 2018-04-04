//
// TeamTime Formals
//

TeamTime.Formals = {

	loadTargets: function (onTargetsLoad) {
		var $ = TeamTime.jQuery;

		$.get(TeamTime.baseUrl +
			"index.php?option=com_teamtimecareer&controller=targetvector&task=selector_by_task"
			+ "&task_id=" + $("#curtaskid").val()
			+ "&user_id=" + $("#user_id").val()
			+ "&showskills=" + ($("#showskills").attr("checked")? "1" : "0"),
			function (data) {
				// update targets options
				$("#block_target_id").html(data);

				//console.log($("#target_id").val());

				// set handlers for target selector change
				$("#target_id").change(function() {
					TeamTime.Base.loadPrice($("#target_id").val());
				});

				if (typeof(onTargetsLoad) == "function") {
					onTargetsLoad();
				}
			});
	},

	setNotifyClientStatus: function (projectId) {
		var $ = TeamTime.jQuery;

		$.get(TeamTime.baseUrl +
			"index.php?option=com_teamtimeformals&controller=formal&task=can_notify_client", {
				project_id: projectId
			},
			function (data) {
				if (data == "1") {
					$("#clientmail").removeAttr("disabled");
					$("#clientmail-label").css("color", "");
				}
				else {
					$("#clientmail").attr("disabled", "disabled");
					$("#clientmail-label").css("color", "#888888");
				}
			});
	}

};