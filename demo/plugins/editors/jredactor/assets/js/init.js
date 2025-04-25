var plgEditorJRedactor = {

	jQuery: jQuery.noConflict(),

	baseUrl: "",
	rootUrl: "",

	defaultConfig: {
		buttons: [
		'html', '|',
		'formatting', '|',
		'bold', 'italic', 'deleted', '|',
		'unorderedlist', 'orderedlist', 'outdent', 'indent', '|',
		'file', 'table', 'link', '|',
		'fontcolor',	'backcolor', '|',
		'alignleft', 'aligncenter', 'alignright', 'justify', '|',
		'horizontalrule', 'fullscreen'
		]
	},

	getUrlForTask: function (task) {
		if (typeof task == "undefined") {
			task = "upload";
		}

		var type = "";
		var id = "";
		if (this.jQuery(".main-editor").length == 1) {
			type = this.jQuery(".main-editor").attr("data-field-type");
			id = this.jQuery(".main-editor").attr("data-current-id");
		}

		var result = this.rootUrl + "index.php?" +
		"option=com_teamtimeattachments&controller=attachments&task=" + task;

		console.log(result);

		if (type != "") {
			result += "&type=" + type;
		}

		if (id != "") {
			result += "&id=" + id;
		}

		return result;
	},

	buttonHoursPlanClick: function (obj, event, key) {
		var $ = plgEditorJRedactor.jQuery;
		var node = obj.getParentNode();
		if (!$(node).is('li')) {
			return;
		}

		var sel = obj.getSelection();
		var text;
		if (obj.oldIE()) {
			text = sel.text;
		}
		else {
			text = sel.toString();
		}

		var min;
		if (text == "") {
			min = 0;
		}
		else {
			min = parseInt(text);
		}
		var hours = Math.floor(min / 60);
		min = min % 60;
		var s = "";
		if (hours >= 10) {
			s += hours;
		}
		else {
			s += "0" + hours;
		}
		s += ":";
		if (min >= 10) {
			s += min;
		}
		else {
			s += "0" + min;
		}

		$(node).addClass("checklist-hoursplan");
		$(node).attr("data-hoursplan", s);
		obj.execCommand('inserthtml', " ");

		// get total hours plan
		var totalHourPlan = 0;
		$(".checklist-hoursplan", $(node).closest("body")).each(function (i, n) {
			var s = $(n).attr("data-hoursplan");
			//console.log(s);
			var t = s.split(":");
			var h = parseInt(t[0], 10);
			var m = parseInt(t[1], 10);
			totalHourPlan += h * 60 + m;
		//console.log(h + " - " + m);
		//console.log(h * 60 + m);
		});

		TeamTime.form.setTodoHoursPlan(totalHourPlan);
	},

	buttonTargetVectorClick: function (obj, event, key) {
		var $ = plgEditorJRedactor.jQuery;
		var node = obj.getParentNode();
		if (!$(node).is('li')) {
			return;
		}

		var targetId = $("#target_id").val();
		if (targetId == "" || targetId == "0") {
			$(node).removeClass("checklist-vector");
			$(node).removeAttr("data-vector");
			$(node).removeAttr("data-targetvector");
		}
		else {
			var target = $.trim($("#target_id :selected").text());
			target = _.str.trim(target, "- ");
			$(node).addClass("checklist-vector");
			$(node).attr("data-vector", target);
			$(node).attr("data-targetvector", targetId);
		}
		obj.syncCode();
	},

	keydownCallback: function (obj, event) {
		var type = obj.$el.closest(".main-editor").attr("data-field-type");
		if (type != "todo") {
			return;
		}

		var key = event.keyCode || event.which;

		if (event.altKey) {
			if (key == 72) { // Alt h
				plgEditorJRedactor.buttonHoursPlanClick(obj, event, 'buttonhoursplan');
			}
			else if (key == 71) { // Alt g
				plgEditorJRedactor.buttonTargetVectorClick(obj, event, 'buttontargetvector');
			}
		}
	}

};

plgEditorJRedactor.jQuery(function ($) {
	var plugin = plgEditorJRedactor;

	// custom localizations
	if (typeof RELANG != "undefined" && typeof RELANG.ru != "undefined") {
		RELANG.ru.label_loaded_results = "Результат:<br>";
		RELANG.ru.label_hours_plan = "План часов";
		RELANG.ru.label_target_vector = "Цель";
	//...
	}
	else {
		RLANG.label_loaded_results = "Result:<br>";
		RELANG.ru.label_hours_plan = "Hours Plan";
		RELANG.ru.label_target_vector = "Target Vector";
	//...
	}

	console.log("test: " + plugin.getUrlForTask());

	// custom config
	var config = $.extend({}, plugin.defaultConfig, {
		lang: plugin.lang,

		keydownCallback: plugin.keydownCallback,

		callback: function (obj) {
			var type = obj.$el.closest(".main-editor").attr("data-field-type");

			if (type == "todo") {
				obj.addBtn('buttonhoursplan', {
					title: RELANG.ru.label_hours_plan,
					callback: plugin.buttonHoursPlanClick
				});

				obj.addBtn('buttontargetvector', {
					title: RELANG.ru.label_target_vector,
					callback: plugin.buttonTargetVectorClick
				});
			}
		},

		/*
	config.buttonsAdd = ['buttonhoursplan', 'buttontargetvector'];
	config.buttonsCustom = {
		buttonhoursplan: {
			title: RELANG.ru.label_hours_plan,
			callback: plugin.buttonHoursPlanClick
		},
		buttontargetvector: {
			title: RELANG.ru.label_target_vector,
			callback: plugin.buttonTargetVectorClick
		}
	};*/

		fileUpload: plugin.getUrlForTask() /*,
		fileUploadCallback: function (obj, json) {
			console.log(obj);
			console.log(json);
		}*/
	});

	$('.redactorEditor').redactor(config);

});