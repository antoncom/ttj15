TeamTime.jQuery(function ($) {
	var calendarVars = TeamTime.resource.calendar;
	var calendarText = calendarVars.text;

	//console.log(calendarVars);

	var view = calendarVars.viewType;
	var DATA_FEED_URL = calendarVars.controllerUrl;

	var cal_beforerequest = function (type) {
		var t = "Loading data...";
		switch (type) {
			case 1:
				t = "Loading data...";
				break;

			case 2:
			case 3:
			case 4:
				t = "The request is being processed ...";
				break;
		}

		$("#errorpannel").hide();
		$("#loadingpannel").html(t).show();
	};

	var cal_afterrequest = function (type) {
		switch(type) {
			case 1:
				$("#loadingpannel").hide();
				break;

			case 2:
			case 3:
			case 4:
				$("#loadingpannel").html("Success!");
				window.setTimeout(function () {
					$("#loadingpannel").hide();
				}, 2000);
				break;
		}
	};

	var cal_onerror = function (type, data) {
		$("#errorpannel").show();
	};

	var Edit = function (data) {
		var filter = calendar_filter();

		var current_date = data[2];
		filter += "&current_date=" +
		[current_date.getFullYear(), current_date.getMonth() + 1, current_date.getDate()].join("-") +
		" " +
		[current_date.getHours(), current_date.getMinutes(), current_date.getSeconds()].join(":");

		if (data[2] && data[3]) {
			var start1 = new Date(data[2]);
			var hours1 = "";
			filter +=  "&todo_date=" +
			[start1.getMonth()+1, start1.getDate(), start1.getFullYear()].join("/");

			if($("#showmonthbtn").attr("class").indexOf("fcurrent") < 0){
				hours1 = [start1.getHours(), start1.getMinutes()].join(":");
				var end1 = new Date(data[3]);

				var todo_hours_plan = ((end1.getTime() - start1.getTime()) / 60 / 60);
				if (data[4]) {
					if (todo_hours_plan == 0) {
						todo_hours_plan = 8000;
					}
					else {
						todo_hours_plan = 8 * ((end1.getTime() - start1.getTime())/60/60/24 + 1000);
					}
				}

				filter += "&todo_hours_plan=" + todo_hours_plan;
			}
			else
				hours1 = "08:00";

			// isallday -> start in 8:00
			if (data[4]) {
				hours1 = "08:00";
			}

			filter +=  "&todo_hours=" + hours1;
		}

		var eurl = calendarVars.editScript +
		"&id={0}&start={2}&end={3}&isallday={4}&title={1}" + filter;
		if (data) {
			var url = TeamTime.string.strFormat(eurl, data);
			$.fancybox({
				href: url,
				type: 'iframe',
				width: 800,
				//height: 750,
				autoSize: false,
				padding : 1,
				openEffect: 'none',
				closeEffect: 'none',
				helpers : {
					overlay : {
						css : {
							'background' : 'rgba(0, 0, 0, 0.4)'
						}
					}
				},
				afterClose: function () {
					console.log("reloading...");
					$("#gridcontainer").reload();
				}
			});
		}
	};

	var View = function (data) {
		var str = "";
		$.each(data, function(i, item){
			str += "[" + i + "]: " + item + "\n";
		});
		alert(str);
	};

	var Delete = function (data,callback) {
		check_repeat_event(data[0],
			function () {
				delete_repeat_events_box(data[0], data[2],
					function (data) {
						$("#gridcontainer").reload();
					});
			},
			function () {
				$.alerts.okButton = calendarText.ok;
				$.alerts.cancelButton = calendarText.cancel;
				hiConfirm(calendarText.are_you_sure_to_delete_this_todo,
					calendarText.confirm,
					function (r) {
						r && callback(0);
					});
			});
	};

	var wtd = function (p) {
		if (p && p.datestrshow) {
			$("#txtdatetimeshow").text(p.datestrshow);
		}
		$("#caltoolbar div.fcurrent").each(function() {
			$(this).removeClass("fcurrent");
		})
		$("#showdaybtn").addClass("fcurrent");
	};

	var op = {
		view: view,
		theme:3,
		showday: new Date(calendarVars.startYear,
			calendarVars.startMonth, calendarVars.startDay),
		EditCmdhandler: Edit,
		DeleteCmdhandler: Delete,
		ViewCmdhandler: View,
		onWeekOrMonthToDay: wtd,
		onBeforeRequestData: cal_beforerequest,
		onAfterRequestData: cal_afterrequest,
		onRequestDataError: cal_onerror,
		autoload: true,
		url: DATA_FEED_URL + "&task=list_data" + calendarVars.filter,
		quickAddUrl: DATA_FEED_URL + "&task=add_data",
		quickUpdateUrl: DATA_FEED_URL + "&task=update_data",
		quickDeleteUrl: DATA_FEED_URL + "&task=remove_data"
	};

	var $dv = $("#calhead");
	var _MH = document.documentElement.clientHeight;
	var dvH = $dv.height() + 2;
	op.height = _MH - dvH;
	op.eventItems =[];

	var p = $("#gridcontainer").bcalendar(op).BcalGetOp();
	if (p && p.datestrshow) {
		$("#txtdatetimeshow").text(p.datestrshow);
	}
	$("#caltoolbar").noSelect();

	$("#hdtxtshow").datepicker({
		picker: "#txtdatetimeshow",
		showtarget: $("#txtdatetimeshow"),
		onReturn: function (r) {
			var p = $("#gridcontainer").gotoDate(r).BcalGetOp();
			if (p && p.datestrshow) {
				$("#txtdatetimeshow").text(p.datestrshow);
			}
		}
	});

	// to show day view
	$("#showdaybtn").click(function (e) {
		//document.location.href="#day";
		$("#caltoolbar div.fcurrent").each(function () {
			$(this).removeClass("fcurrent");
		});

		$(this).addClass("fcurrent");

		var p = $("#gridcontainer").swtichView("day").BcalGetOp();
		if (p && p.datestrshow) {
			$("#txtdatetimeshow").text(p.datestrshow);
		}

		$("#gridcontainer").reload();
	});

	// to show week view
	$("#showweekbtn").click(function(e) {
		//document.location.href="#week";
		$("#caltoolbar div.fcurrent").each(function () {
			$(this).removeClass("fcurrent");
		})

		$(this).addClass("fcurrent");

		var p = $("#gridcontainer").swtichView("week").BcalGetOp();
		if (p && p.datestrshow) {
			$("#txtdatetimeshow").text(p.datestrshow);
		}

		$("#gridcontainer").reload();
	});

	// to show month view
	$("#showmonthbtn").click(function (e) {
		//document.location.href="#month";
		$("#caltoolbar div.fcurrent").each(function () {
			$(this).removeClass("fcurrent");
		})

		$(this).addClass("fcurrent");

		var p = $("#gridcontainer").swtichView("month").BcalGetOp();
		if (p && p.datestrshow) {
			$("#txtdatetimeshow").text(p.datestrshow);
		}

		$("#gridcontainer").reload();
	});

	$("#showreflashbtn").click(function (e) {
		$("#gridcontainer").reload();
	});

	// add a new event
	$("#faddbtn").click(function (e) {
		var filter = "";
		var start1 = new Date();

		filter +=  "&todo_date=" +
		[start1.getMonth()+1, start1.getDate(), start1.getFullYear()].join("/");
		filter +=  "&todo_hours=08:00";

		var url = calendarVars.editScript + filter;
		$.fancybox({
			href: url,
			type: 'iframe',
			width: 800,
			//height: 750,
			autoSize: false,
			padding : 1,
			openEffect: 'none',
			closeEffect: 'none',
			helpers : {
				overlay : {
					css : {
						'background' : 'rgba(0, 0, 0, 0.4)'
					}
				}
			},
			afterClose: function () {
				console.log("reloading...");
				$("#gridcontainer").reload();
			}
		});
	});

	// go to today
	$("#showtodaybtn").click(function (e) {
		var p = $("#gridcontainer").gotoDate().BcalGetOp();
		if (p && p.datestrshow) {
			$("#txtdatetimeshow").text(p.datestrshow);
		}

		$("#gridcontainer").reload();
	});

	// previous date range
	$("#sfprevbtn").click(function (e) {
		var p = $("#gridcontainer").previousRange().BcalGetOp();
		if (p && p.datestrshow) {
			$("#txtdatetimeshow").text(p.datestrshow);
		}

		$("#gridcontainer").reload();
	});

	// next date range
	$("#sfnextbtn").click(function (e) {
		var p = $("#gridcontainer").nextRange().BcalGetOp();
		if (p && p.datestrshow) {
			$("#txtdatetimeshow").text(p.datestrshow);
		}

		$("#gridcontainer").reload();
	});

	var calendar_filter = function () {
		var filter = "";
		if($("#project_id").val() != "")
			filter = filter + "&project_id=" + $("#project_id").val();
		if($("#type_id").val() != "")
			filter = filter + "&type_id=" + $("#type_id").val();
		if($("#task_id").val() != "")
			filter = filter + "&task_id=" + $("#task_id").val();
		if($("#user_id").val() != "")
			filter = filter + "&user_id=" + $("#user_id").val();

		if($("#filter_period").val() != "") {
			filter = filter + "&filter_period=" + $("#filter_period").val();
		}

		if ($("#hidesuborders").attr("checked")) {
			filter = filter + "&hidesuborders=1";
		}

		$("#gridcontainer").BcalSetOp({
			url: DATA_FEED_URL + "&task=list_data"	+ filter
		});

		return filter;
	}

	var update_tasks = function (data) {
		$("#tasks_list").html(data);
		$("#task_id").unbind("change");
		$("#task_id").change(function(){
			calendar_filter();
			$("#gridcontainer").reload();
		});
	};

	var update_types = function (data) {
		if(data != "")
			$("#types_list").html(data);

		var filter = calendar_filter();

		$("#type_id").unbind("change");
		$("#type_id").change(function () {
			update_types();
			$("#gridcontainer").reload();
		});

		// reload task
		$.get(calendarVars.controllerUrl + "&load_tasks=" + filter, update_tasks);
	};

	$("#project_id").change(function () {
		var filter = calendar_filter();

		//reload types
		$.get(calendarVars.controllerUrl + "&load_types=" + filter, update_types);

		$("#gridcontainer").reload();
	});

	$("#user_id, #task_id, #filter_period, #hidesuborders").change(function () {
		calendar_filter();
		$("#gridcontainer").reload();
	});

	$("#type_id").change(function () {
		update_types();
		$("#gridcontainer").reload();
	});

});
