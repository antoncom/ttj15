//
// TeamTime
//

var TeamTime = {
	baseUrl: "",
	option: "",
	controller: "",
	assetsUrl: "",

	frontendUrl: "",

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

		return this.frontendUrl;
	}
};

//
// TeamTime form tools
//

TeamTime.form = {

	disableElements: function (parent) {
		var $ = jQuery;

		$("input, select", parent).attr("disabled", "disabled");
	}

};

//
// TeamTime Base
//

TeamTime.Base = {

	loadPrice: function (target_id) {
		var $ = jQuery;

		var target = "";
		if (target_id != 'undefined' && target_id != 0) {
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
		var $ = jQuery;

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
		var $ = jQuery;
		var result = "";

		if (typeof(JContentEditor) != 'undefined') {
			result = JContentEditor.getContent(name);
		}
		else if (typeof(tinyMCE) != 'undefined') {
			result = tinyMCE.get(name).getBody().innerHTML;
		}
		$("#" + name).val(result);

		return result;
	},

	setEditorContent: function (name, content) {
		var $ = jQuery;

		//console.log(JContentEditor);
		//console.log(tinyMCE);

		if (typeof(JContentEditor) != 'undefined') {
		//JContentEditor.setContent(name, content);
		}
		else if (typeof(tinyMCE) != 'undefined') {
		//tinyMCE.get(name).getBody().innerHTML = content;
		}

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

		return result;
	}

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
			result = parseInt(t[0]) * 60 + parseInt(t[1]);
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