
var SwimlanePanelBlocksMenu = {

	currentObject: null,

	init: function () {
		var $ = TeamTime.jQuery;

		this.currentObject = null;
		this.coords = null;
		var self = this;

		// make blocks functions

		var toggleBlocksMenu = function () {
			// collapse
			if ($(this).hasClass("expanded")) {
				$(this).removeClass("expanded");
				$("#SwimlanePanel-blocksmenu tr.bpmn_insert_menu_hidden").hide();
			}
			// expand
			else {
				$(this).addClass("expanded");
				$("#SwimlanePanel-blocksmenu tr.bpmn_insert_menu_hidden").show();
			}
		};

		var moveToRight = function (figure, dx) {
			if (!figure) {
				return null;
			}

			// console.log(figure.type);

			figure.setPosition(figure.getX() + dx, figure.getY());

			if (!figure.getPort("output")) {
				return figure;
			}

			var existsConn = figure.getPort("output").getConnections();
			if (existsConn.getSize() > 0) {
				return moveToRight(existsConn.get(0).targetPort.getParent(), dx);
			}

			return figure;
		};

		var insertBlock = function (name, objectType) {
			var swPanel = draw2d.bpmn.getSwimlanePanel();
			var workflow = draw2d.bpmn.getWorkflow();
			var obj = new draw2d.bpmn[name]();
			var coords;

			if (!self.currentObject) {
				// just add new block
				workflow.addFigure(obj, self.coords.x, self.coords.y);

				if (name == "End") {
					obj.setPosition(obj.getX() + obj.getWidth() + 80, obj.getY());

					coords = {
						x: obj.getX(),
						y: obj.getY()
					};
					obj.parentCol = swPanel.getColumn(coords);
					obj.parentRow = swPanel.getRow(coords);

					obj = new draw2d.bpmn.Start();
					workflow.addFigure(obj, self.coords.x, self.coords.y);
				}
				else if (objectType == "subprocess") {
					obj.makeSubProcess(null);
					obj.toggle(obj);
				}

				coords = {
					x: obj.getX(),
					y: obj.getY()
				};
				obj.parentCol = swPanel.getColumn(coords);
				obj.parentRow = swPanel.getRow(coords);
				
				swPanel.blocksMenu.hide();

				return obj;
			}

			// add new block and connect with src block
			var figure = self.currentObject.palette.currentFigure;
			workflow.addFigure(obj,
				figure.getX() + figure.getWidth() + 40,
				figure.getY() + (figure.getHeight() - obj.getHeight()) / 2);

			obj.parentCol = figure.getParentCol();
			obj.parentRow = figure.getParentRow();
			if (objectType == "subprocess") {
				obj.makeSubProcess(null);
				obj.toggle(obj);
			}

			// expand parent block
			var parent = figure.getParent();
			if (parent) {
				parent.addChild(obj);
				parent.setDimension(parent.getWidth() + obj.getWidth() + 40,
					Math.max(parent.getHeight(), obj.getHeight() + 60));
			}

			// remove exists connection
			var targetFigure = null;
			var existsConn = figure.getPort("output").getConnections();
			if (existsConn.getSize() > 0) {
				targetFigure = existsConn.get(0).targetPort.getParent();
				workflow.removeFigure(existsConn.get(0));
				var rightFigure = moveToRight(targetFigure, obj.getWidth() + 40);
				draw2d.bpmn.getSwimlanePanel().setWidthForColumn(obj.getParentCol(), rightFigure);
			}
			else {
				draw2d.bpmn.getSwimlanePanel().setWidthForColumn(obj.getParentCol(), obj);
			}

			// add connection
			var	c = new draw2d.bpmn.Connection();
			c.setSource(figure.getPort("output"));
			c.setTarget(obj.getPort("input"));
			workflow.addFigure(c);

			if (targetFigure) {
				c = new draw2d.bpmn.Connection();
				c.setSource(obj.getPort("output"));
				c.setTarget(targetFigure.getPort("input"));
				workflow.addFigure(c);
			}

			return obj;
		};

		// init blocks menu

		$("#SwimlanePanel-blocksmenu td.bpmn_insert_menu_item")
		.mouseover(function () {
			$("#SwimlanePanel-blocksmenu-insert_block").text($(this).attr("data-title"));
		})
		.mouseout(function () {
			$("#SwimlanePanel-blocksmenu-insert_block").text("Insert");
		});

		$("#SwimlanePanel-blocksmenu td.cmdMoreBlocks").click(toggleBlocksMenu);

		$("#SwimlanePanel-blocksmenu td.cmdInsertActivity").click(function () {
			insertBlock("Activity");
		})
		.attr("data-title", "Insert an Activity");

		$("#SwimlanePanel-blocksmenu td.cmdInsertConditionXOR").click(function () {
			insertBlock("ConditionXOR");
		})
		.attr("data-title", "Insert a Decision");

		$("#SwimlanePanel-blocksmenu td.cmdInsertSubprocess").click(function () {
			var obj = insertBlock("Activity", "subprocess");
		})
		.attr("data-title", "Insert a Sub-process");

		$("#SwimlanePanel-blocksmenu td.cmdInsertMessage").click(function () {
			insertBlock("Message");
		})
		.attr("data-title", "Insert a Message Event");

		$("#SwimlanePanel-blocksmenu td.cmdInsertConditionAND").click(function () {
			insertBlock("ConditionAND");
		})
		.attr("data-title", "Insert a Simple Split");

		$("#SwimlanePanel-blocksmenu td.cmdInsertConditionOR").click(function () {
			insertBlock("ConditionOR");
		})
		.attr("data-title", "Insert a Conditional Split");

		$("#SwimlanePanel-blocksmenu td.cmdInsertLinkedSubprocess").click(function () {
			var obj = insertBlock("Activity");
			obj.setViewState("linked");
		})
		.attr("data-title", "Insert a Linked Sub-process");

		$("#SwimlanePanel-blocksmenu td.cmdInsertTimer").click(function () {
			insertBlock("Timer");
		})
		.attr("data-title", "Insert a Timer Event");

		$("#SwimlanePanel-blocksmenu td.cmdInsertException").click(function () {
			insertBlock("Exception");
		})
		.attr("data-title", "Insert an Exception Event");

		$("#SwimlanePanel-blocksmenu td.cmdInsertEnd").click(function () {
			insertBlock("End");
		})
		.attr("data-title", "Insert an End Event");
	},

	show: function (obj, coords) {
		var $ = TeamTime.jQuery;

		this.currentObject = obj;
		this.coords = coords;

		var offset = draw2d.bpmn.getSwimlanePanel().getOffset();
		var x = offset.left;
		var y = offset.top;

		if (this.currentObject) {
			x += this.currentObject.palette.getX() + 24;
			y += this.currentObject.palette.getY() + 16;
		}
		else {
			x = this.coords.x + 6;
			y = this.coords.y + 6;
		}

		// collapsed
		$("#SwimlanePanel-blocksmenu td.cmdMoreBlocks").removeClass("expanded");
		$("#SwimlanePanel-blocksmenu tr.bpmn_insert_menu_hidden").hide();

		$("#SwimlanePanel-blocksmenu").css("left", x).css("top", y).show();
	},

	hide: function () {
		var $ = TeamTime.jQuery;

		$("#SwimlanePanel-blocksmenu").hide();
	}

};
