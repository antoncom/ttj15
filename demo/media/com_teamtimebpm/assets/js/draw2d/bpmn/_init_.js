//
// package draw2d.bpmn
// BPM elements implementation
//

draw2d.bpmn = {

	assetsUrl: "",
	SwimlanePanelInstance: null,

	// utils functions

	strReplaceNamed: function (str, data) {
		var result = str;

		for (var k in data) {
			result = result.replace("{" + k + "}", data[k]);
		}

		return result;
	},

	getCssImage: function (name, noUrl) {
		if (name == "") {
			return "none";
		}

		var result = this.assetsUrl + "js/draw2d/bpmn/resources/" + name;
		if (!noUrl) {
			result = "url(" + result + ")";
		}

		return result;
	},

	// methods for figures objects

	setSwimlanePanel: function (swPanel) {
		draw2d.bpmn.SwimlanePanelInstance = swPanel;
	},

	getSwimlanePanel: function () {
		return draw2d.bpmn.SwimlanePanelInstance;
	},

	getWorkflow: function () {
		return draw2d.bpmn.SwimlanePanelInstance.workflow;
	},

	getBlocksMenu: function () {
		return draw2d.bpmn.SwimlanePanelInstance.blocksMenu;
	},

	initInstance: function (self) {
		// links for parent row and col for figure
		self.parentRow = null;
		self.parentCol = null;

		// data for drag event
		self.oldX = 0;
		self.oldY = 0;

		self.oldParentRow = null;
		self.oldParentCol = null;
		//self.oldParent = null;

		self.paramsData = null;
	},

	onDragstart: function (self) {
		self.oldX = self.getX();
		self.oldY = self.getY();

		self.oldParentRow = self.parentRow;
		self.oldParentCol = self.parentCol;
	//self.oldParent = self.getParent();
	},

	onDragend: function (self) {
		var $ = TeamTime.jQuery;

		var coords = {
			x: self.getX(),
			y: self.getY()
		};

		// position not changed
		if (coords.x == self.oldX && coords.y == self.oldY) {
			console.log("coord not changed");
			return;
		}

		var swPanel = draw2d.bpmn.getSwimlanePanel();
		swPanel.hideActiveCell();

		self.parentCol = swPanel.getColumn(coords);
		self.parentRow = swPanel.getRow(coords);

		// column changed
		if ($(self.parentCol).attr("id") != $(self.oldParentCol).attr("id")) {
			swPanel.setColumnMinWidth(self.oldParentCol);
		}

		// row changed
		if ($(self.parentRow).attr("id") != $(self.oldParentRow).attr("id")) {
			swPanel.setRowMinHeight(self.oldParentRow);
		}

		// set bound for current column and row
		swPanel.setMinBounds(self);

	/*
		// parent changed
		if (self.getParent() != self.oldParent) {
			if (self.type == "bpmn.Activity" &&
				self.paramsData && self.getParent().paramsData) {
				TeamTime.Bpm.setParent(self.paramsData._id, self.getParent().paramsData._id);
			}
		}
		*/
	},

	onDrag: function (self) {
		var coords = {
			x: self.getX(),
			y: self.getY()
		};
		// position not changed
		if (coords.x == self.oldX && coords.y == self.oldY) {
			return;
		}

		// TODO добавить оптимизацию
		// сохранение старых ссылок на строку/столбец
		// поиск новой строки/столбца только если выходит за границы старого столбца/строки
		// границы получить по текущим координатам и ширине, высоте

		var swPanel = draw2d.bpmn.getSwimlanePanel();
		self.parentCol = swPanel.getColumn(coords);
		self.parentRow = swPanel.getRow(coords);

		swPanel.showActiveCell(self);
	},

	onResize: function (self) {
		// using in draw2d.CommandResize
		draw2d.bpmn.getSwimlanePanel().setMinBounds(self);
	},

	getVisibility: function (self) {
		return self.html.style.display != "none";
	},

	setVisibility: function (self, visible) {
		if (visible) {
			self.html.style.display = "";
		}
		else {
			self.html.style.display = "none";
		}

		if (self.getPorts) {
			for (var i = 0; i < self.getPorts().getSize(); i++) {
				var connections = self.getPorts().get(i).getConnections();

				// set visibility of connections
				for (var j = 0; j < connections.getSize(); j++) {
					if (visible) {
						connections.get(j).html.style.display = "";
					}
					else {
						connections.get(j).html.style.display = "none";
					}
				}
			}
		}
	},

	/*
	cleanConnections: function (figure) {
		_.each(figure.getPorts().asArray(), function (port) {
			_.each(port.getConnections().asArray(), function (conn) {
				console.log(conn);
			});
		});
	},*/

	// add context menu for any block elements
	addConextMenu: function (self) {
		var $ = TeamTime.jQuery;

		$(self.html).addClass("with-context-menu");

		$.contextMenu({
			selector: '.with-context-menu',
			ignoreRightClick: false,

			// create menu on the fly
			build: function ($trigger, e) {
				var id = $trigger.attr("id");
				var figure = draw2d.bpmn.getWorkflow().getDocument().getFigure(id);

				return {
					events: {
						show: function (opt, x, y) {
							draw2d.bpmn.getSwimlanePanel().blocksMenu.hide();
						}
					},
					callback: function (key, options) {
						figure.onContextMenuCmd({
							id: id,
							figure: figure,
							key: key,
							options: options
						});
					},

					items: figure.getContextMenuItems()
				};
			}
		});

	},

	addConnectionConextMenu: function (self) {
		var $ = TeamTime.jQuery;

		$(self.html).addClass("with-context-menu-connection");

		$.contextMenu({
			selector: '.with-context-menu-connection',
			ignoreRightClick: false,

			build: function ($trigger, e) {
				var id = $trigger.attr("id");
				var figure = draw2d.bpmn.getWorkflow().getDocument().getLine(id);

				return {
					events: {
						show: function (opt, x, y) {
							draw2d.bpmn.getSwimlanePanel().blocksMenu.hide();
						}
					},
					callback: function (key, options) {
						figure.onContextMenuCmd({
							id: id,
							figure: figure,
							key: key,
							options: options
						});
					},

					items: figure.getContextMenuItems()
				};
			}
		});

	},

	addWorkflowConextMenu: function (self) {
		var $ = TeamTime.jQuery;

		var swPanel = draw2d.bpmn.getSwimlanePanel();

		$(self.html)
		.addClass("with-context-menu-workflow")
		.click(function (e) {
			if (!$(e.target).hasClass("with-context-menu-workflow")) {
				return;
			}
			swPanel.blocksMenu.hide();
		});

		$.contextMenu({
			selector: '.with-context-menu-workflow',
			ignoreRightClick: false,

			events: {
				show: function (opt, x, y) {
					swPanel.blocksMenu.show(null, {
						x: x,
						y: y
					});

					// don't show context menu
					return false;
				}
			},

			callback: function (key, options) {
			},

			items: {
				"default": {
					name: "default"
				}
			}
		});
	},

	selectText: function (figure) {
		var contentElement = figure.getContentElement();
		if (!contentElement) {
			return;
		}

		var range = document.createRange();
		range.selectNodeContents(contentElement[0]);
		var sel = window.getSelection();
		sel.removeAllRanges();
		sel.addRange(range);
	}

};