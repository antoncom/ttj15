/*

//
// Delayed tasks queue
//

var DelayedTasksQueue = function (items, delay, callback, callbackEnd) {
	this.items = items;
	this.delay = delay;
	this.callbackWork = callback;
	this.callbackEnd = callbackEnd;

	this.index = 0;
	this.isStarted = false;
};

DelayedTasksQueue.prototype.start = function () {
	this.isStarted = true;
	this.processNext();
};

DelayedTasksQueue.prototype.stop = function () {
	this.isStarted = false;
};

DelayedTasksQueue.prototype.processNext = function () {
	if (!this.isStarted || this.index >= this.items.length || this.items.length <= 0) {
		this.callbackEnd();
		return;
	}

	var self = this;

	setTimeout(function () {
		self.callbackWork(self.index, self.items[self.index]);
		self.index++;
		self.processNext();
	}, this.delay);
};

*/

//
// Swimlane toolbar implementation
//

var SwimlanePanelToolbar = {

	init: function () {
		var $ = TeamTime.jQuery;

		var buttonsPanel = parent.SwimlanePanelButtons;

		var swPanel = draw2d.bpmn.getSwimlanePanel();
		var workflow = draw2d.bpmn.getWorkflow();
		var sJson = new draw2d.bpmn.JsonSerializer();

		var saveWorkflow = function () {
			var res = sJson.serialize(swPanel);
			if (!swPanel.checkMaxSizeDiagram(res)) {
				return;
			}

			/*
			var getFiguresTimeline = function () {
				var workflow = draw2d.bpmn.getWorkflow();

				return _.filter(workflow.getFigures().asArray(), function (figure) {
					return figure instanceof draw2d.bpmn.Activity;
				});
			};

			var createTodo = function (i, obj) {
				console.log(i);
				console.log(obj.id);

				if (!obj.paramsData) {
					obj.paramsData = {};
					obj.paramsData.name = obj.getText();
				}

				console.log(obj.paramsData);

				$.post(TeamTime.getUrlForTask("saveTodo"), obj.paramsData,
					function (data) {
						console.log(obj.paramsData.name + " saved");
					});
			};

			var saveTodos = function () {
				var endSaveTodos = function () {
					alert("data saved");
				};

				var taskQueue = new DelayedTasksQueue(getFiguresTimeline(),
					1 * 1000,
					createTodo,
					endSaveTodos);
				taskQueue.start();
			};*/

			console.log(swPanel.changedFigures.getItems());

			var initCreatedTodoIds = function (figures) {
				for (var i = 0, l = figures.length; i < l; i++) {
					if (!figures[i].paramsData || !figures[i].paramsData._id) {
						continue;
					}

					var f = workflow.getFigure(figures[i].id);

					if (!f.paramsData) {
						f.paramsData = {};
					}
					f.paramsData._id = figures[i].paramsData._id;
					f.paramsData.name = figures[i].paramsData.name;

					console.log(f.paramsData.name);
					console.log(f.paramsData._id);

					// process children blocks
					if (figures[i].children && figures[i].children.length > 0) {
						figures[i].children = initCreatedTodoIds(figures[i].children);
					}
				}
			};

			// url for process
			var url = TeamTime.getUrlForTask("saveDiagram");

			// for template - get other source url
			if (buttonsPanel.isTemplate) {
				url = TeamTime.getUrlForController("template") + "&task=saveDiagram";
			}

			$.post(url, {
				id: purl().param("id"),
				data: res
			},
			function (data) {
				try {
					var result = JSON.parse(data);

					result.figures = initCreatedTodoIds(result.figures);
					swPanel.changedFigures.clear();

					alert(i18n.SwimlanePanel.diagram_saved);

				//if (_.isFunction(callback)) {
				//	callback();
				//}
				}
				catch (e) {
					alert(e);
				}
			});
		};

		/*
		var loadWorkflow = function () {
			$.get("controller.php", {
				task: "load"
			},
			function (data) {
				if (data == "") {
					alert("Данных нет");
					return;
				}

				var sJson = new draw2d.bpmn.JsonSerializer();
				var swPanel = draw2d.bpmn.getSwimlanePanel();
				swPanel.clear();
				sJson.unserialize(swPanel, data);
			});
		};*/

		var toggleSnapGrid10 = function () {
			workflow.setGridWidth(10, 10);

			//console.log($(this).attr("checked"));

			if ($(this).attr("checked")) {
				workflow.setBackgroundImage(draw2d.bpmn.assetsUrl +
					"js/draw2d/bpmn/resources/grid_10.png", true);
				workflow.setSnapToGrid(true);
			}
			else {
				workflow.setBackgroundImage(null, false);
				workflow.setSnapToGrid(false);
			}
		};

		var playbackProcess = function () {
			var url = TeamTime.getUrlForController() +
			"&tmpl=component&view=processplay" +
			"&id=" + purl().param("id");

			parent.TeamTime.jQuery.fancybox({
				href: url,
				type: 'iframe',
				width: 800,
				height: Math.max(parent.TeamTime.jQuery('body').height(), 650),
				autoSize: false,
				padding : 0,
				scrolling: 'no',
				openEffect: 'none',
				closeEffect: 'none',
				helpers : {
					overlay : {
						css : {
							'background' : 'rgba(0, 0, 0, 0.4)'
						}
					}
				}
			});
		};

		var endEdit = function () {
			// url for process
			var url = TeamTime.getUrlForController();

			// for template - get other source url
			if (buttonsPanel.isTemplate) {
				url = TeamTime.getUrlForController("template");
			}

			parent.location.href = url;
		};

		// init toolbar

		//$("#SwimlanePanel-toolbar").draggable({
		//	distance: 5
		//});

		//$(parent.SwimlanePanelButtons.buttonPlay).click(playbackProcess);
		//$(parent.SwimlanePanelButtons.buttonSave).click(saveWorkflow);
		//$(parent.SwimlanePanelButtons.buttonExit).click(endEdit);

		$(buttonsPanel.buttonGrid).button().click(toggleSnapGrid10);

		//$("#SwimlanePanel-toolbar .cmdBlock").button().click(createFigure);


		//$("#SwimlanePanel-toolbar .cmdLoad").button().click(loadWorkflow);

		parent.submitbutton = function (pressbutton) {
			switch (pressbutton) {
				case "playprocess":
					playbackProcess();
					break;

				case "saveprocess":
				case "savetemplate":
					saveWorkflow();
					break;

				case "exittemplate":
				case "exitprocess":
					endEdit();
					break;
			}

			return false;

		/*
			if (pressbutton == "print") {
				jQuery("input[name='cid[]']").each(function (i, n) {
					if (n.checked) {
						window.open("<?php echo $url; ?>" + n.value);
					}
				});
			}
			else {
				submitform(pressbutton);
			}
			*/
		};

		var changeShowInfo = function () {
			var src;
			//var dest;

			src = buttonsPanel.selectorShowInfo;

			/*
		if (parent.jQuery("body").hasClass("fullscreen")) {
			src = buttonsPanel.selectorShowInfoFullscreen;
			dest = buttonsPanel.selectorShowInfo;
		}
		else {
			src = buttonsPanel.selectorShowInfo;
			dest = buttonsPanel.selectorShowInfoFullscreen;
		}*/

			// update second selector
			//$(dest).val($(src).val());

			// update info with current selector
			swPanel.showInfo($(src).val());
		};

		$(buttonsPanel.selectorShowInfo).change(changeShowInfo);
	//$(buttonsPanel.selectorShowInfoFullscreen).change(changeShowInfo);
	}
};
