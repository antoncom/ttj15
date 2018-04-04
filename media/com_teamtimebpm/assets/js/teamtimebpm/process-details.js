var load_task_description = function (obj) {
};

TeamTime.jQuery(function ($) {
	// get diagram window
	var srcFrame = parent.TeamTime.jQuery("#processDiagramWindow")[0];
	srcFrame = srcFrame.contentWindow || srcFrame.contentDocument || srcFrame.document;

	//console.log(srcFrame);

	var swPanel = srcFrame.draw2d.bpmn.getSwimlanePanel();
	var workflow = srcFrame.draw2d.bpmn.getWorkflow();
	var currentFigure = workflow.getFigure(purl().param("id"));

	var validateData = function () {
		if ($("#curtaskid").val() == "" ||
			$("#user_id").val() == "" ||
			$("#project_id").val() == "") {
			alert("Please select user, project and task");

			return false;
		}
		
		// TODO add check length for description

		return true;
	};

	var initData = function () {
		$("#Subject").val(currentFigure.getText());

		var params = currentFigure.paramsData;
		if (!params) {
			if ($("#project_id").val() != "") {
				TeamTime.Base.loadTasksForProject();
			}
			return;
		}

		if (params._id) {
			console.log("init from todo");
			return;
		}

		TeamTime.Base.setEditorContent("descr", params.description);

		$("#user_id").val(params.user);
		$("#state").val(params.state);
		$("#project_id").val(params.project);

		TeamTime.Base.loadTasksForProject(
			function () {
				$("#curtaskid").val(params.task);
			},
			function () {
				$("#target_id").val(params.target);
			});

		$("#hoursPlan").val(params.hoursPlan);
		$("#hourly_rate").val(params.hourlyRate);
		$("#costs").val(params.costs);

		if (params.sendmail) {
			$("#sendmail").attr("checked", "checked");
		}
		if (params.clientmail) {
			$("#clientmail").attr("checked", "checked");
		}
		if (params.showSkills) {
			$("#showskills").attr("checked", "checked");
		}
		if (params.markHoursPlan) {
			$("#mark_hours_plan").attr("checked", "checked");
		}
		if (params.markExpenses) {
			$("#mark_expenses").attr("checked", "checked");
		}
	};

	var storeData = function () {
		var params = {};

		// copy all old values
		if (currentFigure.paramsData) {
			params = currentFigure.paramsData;
		}

		// set new values
		params.name = $("#Subject").val();
		currentFigure.setText(params.name);
		params.description = TeamTime.Base.getEditorContent("descr");

		params.user = $("#user_id").val();
		params.state = $("#state").val();

		if ($("#user_id").val() != "") {
			params.userName = $("#user_id option:selected").text();
		}

		params.created = $("#realStartDate").val() + " " + $("#startTime option:selected").text();

		console.log("real:" + params.created);

		params.project = $("#project_id").val();
		params.task = $("#curtaskid").val();
		params.type = TeamTime.Base.getTypeByTask(params.task, $("#curtaskid")[0]);
		params.target = $("#target_id").val();

		params.hoursPlan = $("#hoursPlan").val();
		params.hourlyRate = $("#hourly_rate").val();
		params.costs = $("#costs").val();

		params.sendmail = $("#sendmail").attr("checked")? true : false;
		params.clientmail = $("#clientmail").attr("checked")? true : false;
		params.showSkills = $("#showskills").attr("checked")? true : false;
		params.markHoursPlan = $("#mark_hours_plan").attr("checked")? true : false;
		params.markExpenses = $("#mark_expenses").attr("checked")? true : false;

		currentFigure.paramsData = params;

		// mark figure as changed
		swPanel.changedFigures.setItem(currentFigure.id, {
			_id: params._id,
			_action: "changed"
		});
	};

	// init

	$("#Savebtn").click(function () {
		if (validateData()) {
			storeData();
			parent.TeamTime.jQuery.fancybox.close();

			//var frame = parent.jQuery("iframe#processDiagramWindow")[0];
			//console.log(frame);
			//console.log(frame.id);

			parent.parent.submitbutton("saveprocess");
		}
	});

	$("#Closebtn").click(function () {
		parent.TeamTime.jQuery.fancybox.close();
	});

	$("#costs").autoNumeric({
		mNum:5,
		mDec:2,
		aSep:''
	});

	$("#curtodoid").attr("disabled", "disabled");
	TeamTime.form.disableElements($(".repeatParamsFieldset")); //, .dateStart

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

	// init start time selector
	var hoursStart = TeamTime.date.getMinutesByHMStr($("#startTime").attr("data-hours"));
	_.each(_.range(0, 60 * 24, 30), function (i) {
		var selected = "";
		if (hoursStart >= i && hoursStart < i + 30) {
			selected = " selected";
		}
		$("#startTime").append(
			'<option value="' + i + '"' + selected + '>' +
			TeamTime.date.getHMStrByMinutes(i) + '</option>');
	});

	// init end time selector
	var hoursPlan = Math.round($("#hoursPlan").attr("data-hours-plan") * 60);
	_.each(_.range(0, 60 * 60 + 5, 5), function (i) {
		var selected = "";
		if (hoursPlan >= i && hoursPlan < i + 5) {
			selected = " selected";
		}
		$("#hoursPlan").append(
			'<option value="' + i + '"' + selected + '>' +
			TeamTime.date.getHMStrByMinutes(i) + '</option>');
	});

	/*$("#timezone").val(new Date().getTimezoneOffset() / 60 * -1);

	var check = $("#IsAllDayEvent").click(function (e) {
		if (this.checked) {
			$("#startTime").val("00:00").hide();
			$("#hoursPlan").val("00:00").hide();
		}
		else {
			var d = new Date();
			var p = 60 - d.getMinutes();
			if (p > 30) p = p - 30;
			d = dateTools.DateAdd("n", p, d);
			$("#startTime").val(d.getHM()).show();
			$("#hoursPlan").val(dateTools.DateAdd("h", 1, d).getHM()).show();
		}
	});

	if (check[0].checked) {
		$("#startTime").val("00:00").hide();
		$("#hoursPlan").val("00:00").hide();
	}*/

	$("#project_id option:first").attr("disabled", "disabled");

	// load targets selector

	$("#project_id").change(function() {
		TeamTime.Base.loadTasksForProject();
	});

	$("#user_id").change(function () {
		TeamTime.Base.onTaskChange();
	});

	$("#target_id").change(function () {
		TeamTime.Base.loadPrice();
	});

	initData();

/*to define parameters of ajaxform
	var options = {
		beforeSubmit: function() {
			return true;
		},
		dataType: "json",
		success: function(data) {
			alert(data.Msg);
			if (data.IsSuccess) {
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

			$.get("/administrator/index.php?option=com_teamtimecalendar&controller=calendar&task=check_project_for_user"+
				"&project_id="+$("#project_id").val()+
				"&user_id="+$("#user_id").val(),
				function(data){
					var enable_project = false;
					var alert_msg = false;

					var user_name = $("#user_id").val()?
					$("#user_id")[0].options[$("#user_id")[0].selectedIndex].text : "";
					var project_name = $("#project_id").val()?
					$("#project_id")[0].options[$("#project_id")[0].selectedIndex].text : "";

					if(data == "0"){
						var confirm_str = 'CHECK_PROJECT_USER_STR';
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
						$.get("/administrator/index.php?option=com_teamtimecalendar&controller=calendar&task=enable_project_for_user"+
							"&project_id="+$("#project_id").val()+
							"&user_id="+$("#user_id").val(),
							function(data){
								$("#fmEdit").ajaxSubmit(options);
							});
					}
					else{
						if(alert_msg){
							var alert_str = 'ALERT_PROJECT_USER_STR';
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


	function showerror(error, target) {
		var pos = target.position();
		var height = target.height();
		var newpos = {
			left: pos.left,
			top: pos.top + height + 2
		}
		var form = $("#fmEdit");
		error.appendTo(form).css(newpos);
	}
	*/
});
