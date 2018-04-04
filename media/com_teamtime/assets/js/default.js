//
// TeamTime
//

var TeamTime = {
	jQuery: jQuery.noConflict(),

	baseUrl: "",
	option: "",
	controller: "",
	assetsUrl: "",
	frontendUrl: "",

	resource: {},

	getUrlForTask: function (task) {
		return this.baseUrl + 'index.php?' +
		'option=' + this.option + '&controller=' + this.controller + '&task=' + task;
	},

	getUrlForController: function (controller) {
		controller = controller || this.controller;

		return this.baseUrl + 'index.php?' +
		'option=' + this.option + '&controller=' + controller;
	},

	getAssetsUrl: function (option) {
		option = option || this.option;

		return this.baseUrl + 'components/' + option + "/assets/";
	},

	getMediaAssetsUrl: function (option) {
		option = option || this.option;

		return this.getFrontendUrl() + 'media/' + option + "/assets/";
	},

	getFrontendUrl: function () {
		if (this.frontendUrl != "") {
			return this.frontendUrl;
		}

		var result = this.baseUrl;
		var prefix = "/administrator/";
		var p = result.lastIndexOf(prefix);
		if (p >= 0 && result.substr(p, prefix.length) == prefix) {
			result = result.substr(0, p + 1);
		}
		this.frontendUrl = result;

		console.log(this.frontendUrl);

		return this.frontendUrl;
	}
};

//
// TeamTime form tools
//

TeamTime.form = {

	disableElements: function (parent) {
		var $ = TeamTime.jQuery;

		$("input, select", parent).attr("disabled", "disabled");
	},

	initPlaceholder: function (src, value) {
		var $ = TeamTime.jQuery;

		if ($(src).val() == "") {
			$(src).val(value);
		}

		$(src)
		.focus(function () {
			if ($(this).val() == value) {
				$(this).val("");
			}
		})
		.blur(function () {
			if ($(this).val() == "") {
				$(this).val(value);
			}
		});
	},

	initDateFilter: function (datePresets) {
		TeamTime.jQuery(function ($) {
			if($('#period').val() == "") {
				return;
			}

			$('#period').change(function () {
				var selected = $('#period').val();
				if (selected in datePresets) {
					$('#from-period').val(datePresets[selected].from);
					$('#until-period').val(datePresets[selected].until);
				}
			});

		});
	},

	setTodoHoursPlan: function (hoursPlan) {
		var $ = TeamTime.jQuery;

		//console.log(hoursPlan);

		var tmp = hoursPlan % 5;
		if (tmp > 0) {
			hoursPlan += (5 - tmp);
		//console.log("add:" + hoursPlan);
		}

		var found = false;

		if ($("#hours_plan").length > 0) {
			$("#hours_plan").val((hoursPlan / 60).toPrecision(2));
		}
		else if ($("#hoursPlan-etparttime").length > 0) {
			$("#hoursPlan-etparttime option").each(function (i, n) {
				if (found) {
					return;
				}
				var s = $(n).val();
				var t = s.split(":");
				var h = parseInt(t[0], 10);
				var m = parseInt(t[1], 10);
				m = h * 60 + m;

				if (hoursPlan == m) {
					$(n).attr("selected", "selected");
					found = true;
				//console.log(hoursPlan + " - " + m);
				}
			});
		}
		else if ($("#hoursPlan").length > 0) {
			$("#hoursPlan option").each(function (i, n) {
				if (found) {
					return;
				}
				var s = $(n).val();
				var m = parseInt(s, 10);
				if (hoursPlan == m) {
					$(n).attr("selected", "selected");
					found = true;
				//console.log(hoursPlan + " - " + m);
				}
			});
		}
	}
	
};

//
// TeamTime Base
//

TeamTime.Base = {

	loadPrice: function (target_id) {
		var $ = TeamTime.jQuery;

		var target = "";
		if (typeof(target_id) != 'undefined' && target_id != 0) {
			target = "&target_id=" + target_id;
		}

		var params = "&user_id=" + $("#user_id").val()
		+ "&task_id=" + $("#curtaskid").val()
		+ "&project_id=" + $("#project_id").val()
		+ target;

		$.get(TeamTime.baseUrl +
			"index.php?option=com_teamtime&controller=task&task=loadpriceinfo" + params,
			function (data) {
				// update hourly rate
				$("#hourly_rate").val(data);
				$("#hourly_rate1").val(data);
			});
	},

	onTaskChange: function (onTargetsLoad) {
		TeamTime.Base.loadPrice();

		if (TeamTime.Formals) {
			TeamTime.Formals.loadTargets(onTargetsLoad);
		}
	},

	loadTasksForProject: function (onTasksLoad, onTargetsLoad) {
		var $ = TeamTime.jQuery;

		$.get(TeamTime.getFrontendUrl() + "index.php?option=com_teamtime&controller=" +
			"&view=log&format=raw&task=loadtasks&nosize=1&todo_id=0", {
				project_id: $("#project_id").val()
			},
			function (data) {
				// update tasks options
				$("#block_task_id").html(data);

				// set handlers for task selector change
				$("#curtaskid").change(TeamTime.Base.onTaskChange);
				TeamTime.Base.onTaskChange(onTargetsLoad);

				if (typeof(onTasksLoad) == "function") {
					onTasksLoad();
				}
			});
	},

	getEditorContent: function (name) {
		var $ = TeamTime.jQuery;
		var result = "";

		//console.log(typeof(JContentEditor));
		//console.log(tinyMCE);

		if (typeof(JContentEditor) != 'undefined') {
			result = JContentEditor.getContent(name);
			$("#" + name).val(result);
		}
		//else if (typeof(tinyMCE) != 'undefined') {
		//result = tinyMCE.get(name).getBody().innerHTML;
		//}

		// by default get value from textarea
		if (result == "") {
			result = $("#" + name).val();
		//console.log("default");
		//console.log(result);
		}

		return result;
	},

	setEditorContent: function (name, content) {
		var $ = TeamTime.jQuery;

		//console.log(typeof(JContentEditor));

		//if (typeof(JContentEditor) != 'undefined') {
		//JContentEditor.setContent(name, content);
		//}
		//else if (typeof(tinyMCE) != 'undefined') {
		//tinyMCE.get(name).getBody().innerHTML = content;
		//}

		$("#" + name).val(content);
	},

	getTypeByTask: function (task, select) {
		var result = "";
		if (task == "") {
			return result;
		}

		for (var i = select.selectedIndex; i > 0; i--) {
			if (select.options[i].className != "" &&
				select.options[i].className.indexOf("option") >= 0) {
				result = select.options[i].value;
				break;
			}
		}

		//console.log("test: type = " + result);

		return result;
	},

	processReportTable: function () {
		var $ = TeamTime.jQuery;
		var config = TeamTime.resource.configData;

		if (config.col_date == 0) {
			$(".col_date").hide();
		}

		if (config.col_project == 0) {
			$(".col_project").hide();
		}

		if (config.col_type == 0) {
			$(".col_type").hide();
		}

		if (config.col_task == 0) {
			$(".col_task").hide();
		}

		if (config.col_todo == 0) {
			$(".col_todo").hide();
			$(".col_todo2").attr("colspan", 1);
		}

		if (config.col_log == 0) {
			$(".col_log").hide();
			if (config.col_todo == 0) {
				$(".col_todo2").hide();
			}
			else {
				$(".col_todo2").attr("colspan", 1);
			}
		}

		if (config.col_planned_actual_hours == 0) {
			$(".col_planned_actual_hours").hide();
		}

		if (config.col_actual_hours == 0) {
			$(".col_actual_hours").hide();
		}

		if (config.col_hourly_rate == 0) {
			$(".col_hourly_rate").hide();
		}

		if (config.col_planned_cost == 0) {
			$(".col_planned_cost").hide();
		}

		if (config.col_actual_cost == 0) {
			$(".col_actual_cost").hide();
		}

		if (config.col_overhead_expenses == 0) {
			$(".col_overhead_expenses").hide();
		}

		if (config.col_user == 0) {
			$(".col_user").hide();
		}
	}

/*checkUsedTask: function (taskIds) {
		var result = [];
		var $ = TeamTime.jQuery;

		$.getJSON(TeamTime.getUrlForTask("checkusedtasks"), {
			task_id: taskIds
		},
		function (data) {
			result = taskIds;

			//if (usedTasks.length > 0) {
			//actionEnabled = false;
			//alert(usedTasks.join("\n"));
		//}

			console.log(data);

		});

		return result;
	}*/

};

//
// String utils
//

TeamTime.string = {

	strFormat: function (self, dataarry) {
		return self.replace(/\{([\d]+)\}/g, function (s1, s2) {
			var s = dataarry[s2];
			if (typeof (s) != "undefined") {
				if (s instanceof (Date)) {
					return s.getTimezoneOffset();
				}
				else {
					return encodeURIComponent(s);
				}
			}
			else {
				return "";
			}
		});
	},

	strFormatNoEncode: function (self, dataarry) {
		return self.replace(/\{([\d]+)\}/g, function(s1, s2) {
			var s = dataarry[s2];
			if (typeof (s) != "undefined") {
				if (s instanceof (Date)) {
					return s.getTimezoneOffset();
				}
				else {
					return s;
				}
			}
			else {
				return "";
			}
		});
	}
};

//
// Date utils
//

TeamTime.date = {

	getHM: function (d) {
		var hour = d.getHours();
		var minute = d.getMinutes();
		var ret = (hour > 9? hour : "0" + hour) + ":" + (minute > 9? minute : "0" + minute);

		return ret;
	},

	getHMStrByMinutes: function (m) {
		var hour = (m - m % 60) / 60;
		var minute = m % 60;

		return (hour > 9? hour : "0" + hour) + ":" + (minute > 9? minute : "0" + minute);
	},

	getMinutesByHMStr: function (timeHM) {
		var result = 0;

		if (timeHM != "") {
			var t = timeHM.split(":");
			result = parseInt(t[0], 10) * 60 + parseInt(t[1], 10);
		}

		return result;
	},

	dateAdd: function (interval, number, idate) {
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
	},

	getLocalDate: function (sdate) {
		var result = sdate;

		return result;
	}
};

//
// initial code
//
window.addEvent('domready', function () {
	var obj = $$('select.auto-submit');
	if (!obj.addEvent) {
		return;
	}

	obj.addEvent('change', function () {
		document.adminForm.submit();
	});
});

var creport_process_table = function () {
	TeamTime.Base.processReportTable();
};