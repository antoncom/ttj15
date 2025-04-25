
var load_task_description = function (obj) {
};

if (!DateAdd || typeof (DateDiff) != "function") {

	var DateAdd = function (interval, number, idate) {
		number = parseInt(number);
		var date;

		if (typeof (idate) == "string") {
			date = idate.split(/\D/);
			eval("var date = new Date(" + date.join(",") + ")");
		}

		if (typeof (idate) == "object") {
			date = new Date(idate.toString());
		}

		switch (interval) {
			case "y":
				date.setFullYear(date.getFullYear() + number);
				break;
			case "m":
				date.setMonth(date.getMonth() + number);
				break;
			case "d":
				date.setDate(date.getDate() + number);
				break;
			case "w":
				date.setDate(date.getDate() + 7 * number);
				break;
			case "h":
				date.setHours(date.getHours() + number);
				break;
			case "n":
				date.setMinutes(date.getMinutes() + number);
				break;
			case "s":
				date.setSeconds(date.getSeconds() + number);
				break;
			case "l":
				date.setMilliseconds(date.getMilliseconds() + number);
				break;
		}

		return date;
	}

}

function getHM(date) {
	var hour = date.getHours();
	var minute = date.getMinutes();
	var ret = (hour > 9? hour : "0" + hour) + ":" + (minute > 9? minute : "0" + minute);

	return ret;
}

TeamTime.jQuery(function ($) {
	var calendarVars = TeamTime.resource.calendar;
	var DATA_FEED_URL = calendarVars.controllerUrl;

	var arrT = [];
	var tt = "{0}:{1}";
	for (var i = 0; i < 24; i++) {
		arrT.push(
		{
			text: StrFormat(tt, [i >= 10 ? i : "0" + i, "00"])
		},
		{
			text: StrFormat(tt, [i >= 10 ? i : "0" + i, "30"])
		});
	}

	$("#timezone").val(new Date().getTimezoneOffset() / 60 * -1);

	$("#stparttime").dropdown({
		dropheight: 200,
		dropwidth:60,
		selectedchange: function () { },
		items: arrT
	});

	$("#etparttime").dropdown({
		dropheight: 200,
		dropwidth:60,
		selectedchange: function () { },
		items: arrT
	});

	var check = $("#IsAllDayEvent").click(function (e) {
		if (this.checked) {
			$("#stparttime").val("00:00").hide();
			$("#etparttime").val("00:00").hide();
		}
		else {
			var d = new Date();
			var p = 60 - d.getMinutes();
			if (p > 30) p = p - 30;
			d = DateAdd("n", p, d);
			$("#stparttime").val(getHM(d)).show();
			$("#etparttime").val(getHM(DateAdd("h", 1, d))).show();
		}
	});

	if (check[0].checked) {
		$("#stparttime").val("00:00").hide();
		$("#etparttime").val("00:00").hide();
	}

	$("#Savebtn").click(function() {

		if($("#curtaskid").val() == "" ||
			$("#user_id").val() == "" ||
			$("#project_id").val() == "") {
			alert(calendarVars.text.select_user_project_task);
			return;
		}

		if (calendarVars.todoId) {
			check_repeat_event(calendarVars.todoId, function () {
				edit_repeat_events_box(calendarVars.todoId,
					new Date(calendarVars.currentDate[0], calendarVars.currentDate[1] - 1,
						calendarVars.currentDate[2], calendarVars.currentDate[3],
						calendarVars.currentDate[4], calendarVars.currentDate[5]),
					function (data) {
						if (data != "1") {
							alert(data);
							parent.jQuery.fancybox.close();
						}
					});
			},
			function () {
				$("#fmEdit").submit();
			});
		}
		else {
			$("#fmEdit").submit();
		}
	});

	$("#Savecopybtn").click(function () {
		$("#fmEdit")[0].action = $("#fmEdit")[0].action + "&copy=1";
		$("#fmEdit").submit();
	});

	$("#Closebtn").click(function () {
		parent.jQuery.fancybox.close();
	});

	$("#Deletebtn").click(function() {
		check_repeat_event(calendarVars.todoId,
			function () {
				delete_repeat_events_box(calendarVars.todoId,
					new Date(calendarVars.currentDate[0], calendarVars.currentDate[1] - 1,
						calendarVars.currentDate[2], calendarVars.currentDate[3],
						calendarVars.currentDate[4], calendarVars.currentDate[5]),
					function (data) {
						if (data != "1") {
							alert(data);
							parent.jQuery.fancybox.close();
						}
						else {
							alert("Error occurs.\r\n");
						}
					});
			},
			function () {
				if (confirm(calendarVars.text.are_you_sure_to_delete_this_todo)) {
					var param = [{
						"name": "calendarId",
						value: calendarVars.todoId
					}];
					$.post(DATA_FEED_URL + "&task=remove_data",
						param,
						function(data){
							if (data.IsSuccess) {
								alert(data.Msg);
								parent.jQuery.fancybox.close();
							}
							else {
								alert("Error occurs.\r\n" + data.Msg);
							}
						}
						,"json");
				}
			});
	});

	//$("#stpartdate").datepicker({ picker: "<button class='calpick'></button>"});
	// init date selector
	var date = $("#startDate").val();
	$("#startDate").val("");
	$("#startDate").datepicker({
		altField: "#realStartDate",
		altFormat: "yy-mm-dd",
		showOn: "button",
		buttonImage: TeamTime.getMediaAssetsUrl() + "css/images/calendar.png",
		buttonImageOnly: true
	});
	if (date != "") {
		$("#startDate").datepicker("setDate", new Date(date));
	}

	var cv = $("#colorvalue").val();
	if (cv == "") {
		cv = "-1";
	}
	$("#calendarcolor").colorselect({
		title: "Color",
		index: cv,
		hiddenid: "colorvalue"
	});

	// to define parameters of ajaxform
	var options = {
		beforeSubmit: function() {
			return true;
		},
		dataType: "json",
		success: function(data) {
			alert(data.Msg);
			if (data.IsSuccess) {
				parent.jQuery.fancybox.close();
			}
		}
	};

	$.validator.addMethod("date", function (value, element) {
		var arrs = value.split(i18n.datepicker.dateformat.separator);
		var year = arrs[i18n.datepicker.dateformat.year_index];
		var month = arrs[i18n.datepicker.dateformat.month_index];
		var day = arrs[i18n.datepicker.dateformat.day_index];
		var standvalue = [year,month,day].join("-");
		return this.optional(element) || /^(?:(?:1[6-9]|[2-9]\d)?\d{2}[\/\-\.](?:0?[1,3-9]|1[0-2])[\/\-\.](?:29|30))(?: (?:0?\d|1\d|2[0-3])\:(?:0?\d|[1-5]\d)\:(?:0?\d|[1-5]\d)(?: \d{1,3})?)?$|^(?:(?:1[6-9]|[2-9]\d)?\d{2}[\/\-\.](?:0?[1,3,5,7,8]|1[02])[\/\-\.]31)(?: (?:0?\d|1\d|2[0-3])\:(?:0?\d|[1-5]\d)\:(?:0?\d|[1-5]\d)(?: \d{1,3})?)?$|^(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])[\/\-\.]0?2[\/\-\.]29)(?: (?:0?\d|1\d|2[0-3])\:(?:0?\d|[1-5]\d)\:(?:0?\d|[1-5]\d)(?: \d{1,3})?)?$|^(?:(?:16|[2468][048]|[3579][26])00[\/\-\.]0?2[\/\-\.]29)(?: (?:0?\d|1\d|2[0-3])\:(?:0?\d|[1-5]\d)\:(?:0?\d|[1-5]\d)(?: \d{1,3})?)?$|^(?:(?:1[6-9]|[2-9]\d)?\d{2}[\/\-\.](?:0?[1-9]|1[0-2])[\/\-\.](?:0?[1-9]|1\d|2[0-8]))(?: (?:0?\d|1\d|2[0-3])\:(?:0?\d|[1-5]\d)\:(?:0?\d|[1-5]\d)(?:\d{1,3})?)?$/.test(standvalue);
	}, "Invalid date format");

	$.validator.addMethod("time", function(value, element) {
		return this.optional(element) || /^([0-1]?[0-9]|2[0-3]):([0-5][0-9])$/.test(value);
	}, "Invalid time format");

	$.validator.addMethod("safe", function(value, element) {
		return this.optional(element) || /^[^$\<\>]+$/.test(value);
	}, "$<> not allowed");

	var showerror = function (error, target) {
		var pos = target.position();
		var height = target.height();
		var newpos = {
			left: pos.left,
			top: pos.top + height + 2
		}
		var form = $("#fmEdit");
		error.appendTo(form).css(newpos);
	};

	$("#fmEdit").validate({
		submitHandler: function(form) {
			if(typeof(JContentEditor) != 'undefined'){
				$("#descr").val(JContentEditor.getContent("descr"));
			}
			else if(typeof(tinyMCE) != 'undefined'){
				$("#descr").val(tinyMCE.get('descr').getBody().innerHTML);
			}

			if($("#user_id").val() == "")
				return;

			$.get(TeamTime.baseUrl +
				"index.php?option=com_teamtimecalendar&controller=calendar&task=check_project_for_user"+
				"&project_id="+$("#project_id").val()+
				"&user_id="+$("#user_id").val(),
				function(data){
					var enable_project = false;
					var alert_msg = false;

					var user_name = $("#user_id").val()?
					$("#user_id")[0].options[$("#user_id")[0].selectedIndex].text : "";
					var project_name = $("#project_id").val()?
					$("#project_id")[0].options[$("#project_id")[0].selectedIndex].text : "";

					if (data == "0") {
						var confirm_str = calendarVars.text.check_project_user_str;
						confirm_str = confirm_str.replace("{name}", user_name);
						confirm_str = confirm_str.replace("{project}", project_name);
						confirm_str = confirm_str.replace("{newline}", "\n");

						if(confirm(confirm_str)){
							enable_project = true;
						}
						else{
							alert_msg = true;
						}
					}

					if(enable_project){
						$.get(TeamTime.baseUrl +
							"index.php?option=com_teamtimecalendar&controller=calendar&task=enable_project_for_user"+
							"&project_id="+$("#project_id").val()+
							"&user_id="+$("#user_id").val(),
							function(data){
								$("#fmEdit").ajaxSubmit(options);
							});
					}
					else{
						if(alert_msg){
							var alert_str = calendarVars.text.alert_project_user_str;
							alert_str = alert_str.replace("{name}", user_name);
							alert(alert_str);
						}

						$("#fmEdit").ajaxSubmit(options);
					}
				});
		},
		errorElement: "div",
		errorClass: "cusErrorPanel",
		errorPlacement: function(error, element) {
			showerror(error, element);
		}
	});

	//-----

	$("#project_id option:first").attr("disabled", "disabled");

	var task_id = $("#curtaskid").val();
	var parent_todo_id = $("#curtodoid").val();

	var loadPrice = function(target_id) {
		// update hourly rate

		var target_param = "";
		if (typeof(target_id) != 'undefined' && target_id != 0) {
			target_param = "&target_id=" + target_id;
		}

		var params = "&user_id=" + $("#user_id").val()
		+ "&task_id=" + $("#curtaskid").val()
		+ "&project_id=" + $("#project_id").val()
		+ target_param
		+ "&t=" + new Date().getTime();

		$.get(TeamTime.baseUrl +
			"index.php?option=com_teamtime&controller=task&task=loadpriceinfo" +
			params,
			function (data) {
				$("#hourly_rate").val(data);
				$("#hourly_rate1").val(data);
			});
	};

	var onTaskChangeD = function () {
		if (TeamTime.Career) {
			TeamTime.Career.loadTargetsSelector(loadPrice);
		}
	};

	var onTaskChange = function () {
		loadPrice();
		onTaskChangeD();
	};

	// load project at first time
	if ($("#project_id").val() != "") {

		//get tasks list for project
		$.get(TeamTime.getFrontendUrl() +
			"index.php?option=com_teamtime&controller=&view=log&format=raw&task=loadtasks&nosize=1"
			+"&project_id=" + $("#project_id").val()
			+ "&todo_id=0&t=" + new Date().getTime(),

			function (data) {
				$("#block_task_id").html(data);
				$("#curtaskid").val(task_id);
				$("#curtaskid").change(onTaskChange);

				if (calendarVars.todoId == 0) {
					onTaskChange();
				}
			});

		//get todos list for project
		$.get(TeamTime.baseUrl +
			"index.php?option=com_teamtime&controller=todo&task=get_list_todos&nosize=1&project_id=" +
			$("#project_id").val() +
			"&todo_id=" + parent_todo_id +
			"&current_id=" + calendarVars.todoId +
			"&t=" + new Date().getTime(),
			function (data) {
				$("#block_todo_id").html(data);
				$("#curtodoid").val(parent_todo_id);
			});
	}
	else {
	//$("#project_id")[0].selectedIndex = -1;
	//$("#curtodoid").attr("disabled", "disabled");
	}

	$("#project_id").change(function() {

		//get tasks list for project
		$.get(TeamTime.getFrontendUrl() +
			"index.php?option=com_teamtime&controller=&view=log&format=raw&task=loadtasks&nosize=1&project_id=" +
			$("#project_id").val() + "&todo_id=0&t=" + new Date().getTime(),

			function (data) {
				$("#block_task_id").html(data);
				$("#curtaskid").change(onTaskChange);

				onTaskChange();
			});

		// get todos list for project
		$.get(TeamTime.baseUrl +
			"index.php?option=com_teamtime&controller=todo&task=get_list_todos&nosize=1&project_id=" +
			$("#project_id").val() +
			"&current_id=" + calendarVars.todoId +
			"&t=" + new Date().getTime(),
			function (data) {
				$("#block_todo_id").html(data);
			});
	});

	$("#user_id").change(function () {
		onTaskChange();
	});

	$("#target_id").change(function () {
		loadPrice();
	});

	$("#costs").autoNumeric({
		mNum:5,
		mDec:2,
		aSep:''
	});

	setTimeout(function () {
		$("#Subject").focus();
	}, 1000);

	if (TeamTime.Career) {
		$("#showskills").change(function () {
			//console.log($("#showskills").attr("checked"));
			onTaskChangeD();
		});
	}

});
