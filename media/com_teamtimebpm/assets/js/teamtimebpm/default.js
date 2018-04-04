//
// TeamTime Bpm
//

TeamTime.Bpm = {

	loadFormalProcesses: function () {
		var $ = TeamTime.jQuery;

		$.get(TeamTime.baseUrl +
			"index.php?option=com_teamtimebpm&controller=process&task=loadprocesses", {
				id: $("#project_id").val(),
				doctype_id: $("#doctype_id").val(),
				from: $("#from-period").val(),
				until: $("#until-period").val()
			},
			function (data) {
				$("div.select-process").html(data);
			});
	}

/*,
	setParentForTodo: function (id, parentId) {
		console.log(id, parentId);
	}*/

};
