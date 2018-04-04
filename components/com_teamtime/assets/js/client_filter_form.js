var report_show_loadprogress = function () {
	document.getElementById('preloaderbg').style.display = 'block';
	document.body.style.overflow = 'hidden';
};

var report_hide_loadprogress = function () {
	document.getElementById('preloaderbg').style.display = 'none';
	document.body.style.overflow = 'visible';
};

var report_form_submit = function (base_url) {
	var s = [];
	var i;

	var f = document.getElementById("adminForm");

	// process input items
	var items = f.getElementsByTagName("input");
	for (i = 0; i < items.length; i++) {
		if (items[i].name != "") {
			if (items[i].name == "callback") {
				items[i].value = "report-content-area";
			}
			s.push(items[i].name + "=" + items[i].value);
		}
	}

	// process selector items
	items = f.getElementsByTagName("select");
	for (i = 0; i < items.length; i++) {
		if (items[i].name == "task_id" && items[i].selectedIndex > 0) {
			if (items[i].options[items[i].selectedIndex].className == "option2") {
				s.push("type_id=" + items[i].value);
			}
			else {
				s.push("task_id=" + items[i].value);
			}
		}
		else {
			s.push(items[i].name + "=" + items[i].value);
		}
	}

	report_show_loadprogress();

	var script = document.createElement('script');
	script.setAttribute('src', base_url + "/index.php?"
		+ s.join("&")
		+ "&ttt=" + new Date().getTime());
	document.getElementsByTagName('head')[0].appendChild(script);

	return false;
};

var report_form_firstsubmit = function (baseUrl, isClient) {
	// select project - if only one project in selector
	var obj = document.getElementById("project-id");

	if (obj) {
		if (isClient) {
			if (obj.options.length == 2) {
				obj.selectedIndex = 1;
				obj.setAttribute("style", "display:none;");
				report_load_projecttasks(baseUrl, isClient);
			}
			else {
				obj.setAttribute("style", "width: 100%;");
			}
		}
		else {
			obj.setAttribute("style", "width: 100%;");
		}
	}

	report_form_submit(baseUrl);
};

var report_load_projecttasks = function (base_url, isClient) {
	var obj = document.getElementById("project-id");

	var project_id = obj.selectedIndex >= 0? obj.options[obj.selectedIndex].value : "";

	var script = document.createElement('script');
	script.setAttribute('src', base_url
		+ "/index.php?option=com_teamtime&view=log&format=raw&task=loadtasks&project_id="
		+ project_id
		+ (isClient? "&hideTasks=1" : "")
		+ "&callback=report-filter-tasks");

	document.getElementsByTagName('head')[0].appendChild(script);
};