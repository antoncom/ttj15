
//
// Swimlane changed figures list
//

var SwimlaneChangedFigures = function () {
	var figures = {};

	this.setItem = function (figureId, params) {
		figures[figureId] = params;
	};

	this.getItem = function (figureId) {
		return figures[figureId];
	};

	this.removeItem = function (figureId) {
		delete figures[figureId];
	};

	this.getItems = function () {
		return figures;
	};

	this.clear = function () {
		figures = {};
	};
};

//
// Swimlane panel implementation
//

var SwimlanePanel = function () {

	// id prefix for panel
	this.name = "";

	// array of changed figures
	this.changedFigures = null;

	// drawing area
	this.workflow = null;

	// partisipants
	this.rows = null;
	// milestones
	this.columns = null;

	var $ = TeamTime.jQuery;
	var self = this;

	var minRowHeight = 120;
	var minColumnWidth = 120;

	// private functions

	var getHeaderTitle = function (obj, parentType) {
		var result = "";

		if (parentType == "row") {
			result = $(obj).children("span.rowTitle").text();
		}
		else {
			result = $(obj).text();
		}

		return result;
	};

	var setHeaderTitle = function (obj, text, parentType) {
		if (parentType == "row") {
			$(obj).children("span.rowTitle").text(text);

			var textObj = $(obj).data("verticalTextObj");
			// clear previous object
			if (textObj != null) {
				textObj.paper.remove();
			}

			var w = $(obj).parent().width();
			var h = $(obj).parent().height();
			var paper = Raphael(obj[0]);
			textObj = paper.text(10, (h / 2) - 10, text).attr({
				"font": "12px normal",
				"fill": $(obj).css("color"),
				"clip-rect": [0, 0, w, h - 20].join(",")
			}).rotate(270);
			$(obj).data("verticalTextObj", textObj);
		}
		else {
			$(obj).text(text);
		}
	};

	var getChangedPairs = function (src, oldPositions, newPositions) {
		var result = {
			src: [],
			dest: []
		};

		var id = $(src).attr("id");
		for (var i = 0; i < oldPositions.length; i++) {
			if (oldPositions[i] != newPositions[i]) {

				if (id == newPositions[i]) {
					result.src = [oldPositions[i], newPositions[i]];
				}
				else {
					result.dest.push([oldPositions[i], newPositions[i]]);
				}
			}
		}

		return result;
	};

	var makeSortableRows = function () {
		var oldPositions;
		var newPositions;

		$(self.rows).sortable({
			start: function (event, ui) {
				oldPositions = $(this).sortable("toArray");
				saveOldPositionY(ui.item, this);
			},
			update: function (event, ui) {
				newPositions = $(this).sortable("toArray");
				self.moveRows(getChangedPairs(ui.item, oldPositions, newPositions));
			}
		});

		$(self.rows).disableSelection();
	};

	var makeSortableColumns = function () {
		var oldPositions;
		var newPositions;

		$(self.columns).sortable({
			start: function (event, ui) {
				oldPositions = $(this).sortable("toArray");
				saveOldPositionX(ui.item, this);
			},
			update: function (event, ui) {
				newPositions = $(this).sortable("toArray");
				self.moveColumns(getChangedPairs(ui.item, oldPositions, newPositions));
			}
		});

		$(self.columns).disableSelection();
	};

	var makeEditable = function (n, parentType) {
		var pos;
		if (parentType == "row") {
			pos = "right";
		}
		else {
			pos = "bottom";
		}

		$(n).showBalloon({
			position: pos,
			contents: '<input class="editable_title p' + $(n).attr("id") + '" type="text" size="15" />'
		})
		.hideBalloon()
		// custom event for calling hide balloon
		.on("SwimlinePanel.hideballoon", function () {
			$(this).hideBalloon();
		})
		.click(function () {
			self.hideBalloons();
		})
		.dblclick(function () {
			self.hideBalloons();
			var titleObj = this;

			// show edit field
			$(titleObj).showBalloon();
			$("input.editable_title.p" + $(n).attr("id"))
			// get current value
			.val(getHeaderTitle($(titleObj).children(".title"), parentType))
			.change(function () {
				// store new value
				setHeaderTitle($(titleObj).children(".title"), $(this).val(), parentType);
				// hide edit field
				$(titleObj).hideBalloon();
			});
		});
	};

	var makeRow = function (i, n) {
		// set min row height
		$(n).height(Math.max($(n).height(), minRowHeight));

		//if ($(n).position().top + $(n).height() > self.getHeight()) {
		//	self.resizeHeight($(n).height() + 20);
		//}

		var titleObj = $(n).children(".title");
		$(titleObj).addClass("vertical_text");

		// create vertical text
		setHeaderTitle(titleObj, getHeaderTitle(titleObj, "row"), "row");

		// make resizable
		$(n).resizable({
			handles: 's',
			minHeight: minRowHeight,
			stop: function (event, ui) {
				// move all rows after this row
				self.moveItemsAfter($(n).attr("id"), {
					dx: 0,
					dy: ui.size.height - ui.originalSize.height
				}, "row");

				// move bottom line by height
				var h = $(n).height();
				$(n).children("div.bottom_line").css("top", (h + 1) + "px");

				// update header title
				setHeaderTitle(titleObj, getHeaderTitle(titleObj, "row"), "row");
			}
		});

		// create bound lines
		var w = $(n).width();
		var h = $(n).height();

		var line = document.createElement('div');
		$(line).addClass("horizontal_line").addClass("bottom_line")
		.css("left", w + "px").css("top", (h + 1) + "px");
		$(n).append(line);

		line = document.createElement('div');
		$(line).addClass("horizontal_line").addClass("top_line")
		.css("left", w + "px").css("top", -1 + "px");
		$(n).append(line);

		// make editable row
		makeEditable(n, "row");
	};

	var makeColumn = function (i, n) {
		// set min column width
		$(n).width(Math.max($(n).width(), minColumnWidth));

		//if ($(n).position().left + $(n).width() > self.getWidth()) {
		//	self.resizeWidth($(n).width() + 20);
		//}

		// make resizable
		$(n).resizable({
			handles: 'e',
			minWidth: minColumnWidth,
			stop: function (event, ui) {
				// move all columns after this column
				self.moveItemsAfter($(n).attr("id"), {
					dx: ui.size.width - ui.originalSize.width,
					dy: 0
				}, "column");

				// move right line by width
				$(n).children("div.right_line").css("left", ($(n).width() + 1) + "px");
			}
		});

		// create bound lines
		var w = $(n).width();
		var h = $(n).height();

		var line = document.createElement('div');
		$(line).addClass("vertical_line").addClass("right_line")
		.css("left", (w + 1) + "px").css("top", h + "px");
		$(n).append(line);

		line = document.createElement('div');
		$(line).addClass("vertical_line").addClass("left_line")
		.css("left", -1 + "px").css("top", h + "px");
		$(n).append(line);

		// make editable column
		makeEditable(n, "column");
	};

	var saveOldPositionX = function (srcItem, parent) {
		var x = 0;
		var w = 0;
		var src = null;

		$(parent).children("li").each(function (i, n) {
			// find srcItem
			if ($(srcItem).attr("id") == $(n).attr("id")) {
				src = n;
			}
			else if (src == null) {
				// save X of item before src item
				x = $(n).position().left;
				w = $(n).width();
			}
		});

		if (src != null) {
			$(src).data('oldPositionX', x + w);
		}
	};

	var saveOldPositionY = function (srcItem, parent) {
		var y = 0;
		var h = 0;
		var src = null;

		$(parent).children("li").each(function (i, n) {
			// find srcItem
			if ($(srcItem).attr("id") == $(n).attr("id")) {
				src = n;
			}
			else if (src == null) {
				// save Y of item before src item
				y = $(n).position().top;
				h = $(n).height();
			}
		});

		if (src != null) {
			$(src).data('oldPositionY', y + h);
		}
	};

	var createActiveCell = function () {
		var activeCell = document.createElement('div');
		$(activeCell).addClass("active_cell").css("display", "none");
		$(document.body).append(activeCell);
	};

	var cmdAppendRow = function (data) {
		var newRow = self.getNewRow();
		$("#" + data.id).after(newRow[0]);

		var newId = newRow[1];
		makeRow(0, $("#"+ newId)[0]);

		// move rest items
		var h = $("#"+ newId).height();
		self.moveItemsAfter(newId, {
			dx: 0,
			dy: h
		}, "row");
	};

	var cmdDeleteRow = function (data) {
		var nameRow = getHeaderTitle($("#" + data.id).children(".title"), "row");

		var figures = self.getItems(data.id, "row");
		var l = figures.length;
		if (l > 0) {
			if (!confirm(draw2d.bpmn.strReplaceNamed(i18n.SwimlanePanel.prompt_remove_row, {
				name: nameRow
			}))) {
				return;
			}
			// remove children figures
			for (var i = 0; i < l; i++) {
				var cmd = new draw2d.CommandDelete(figures[i]);
				cmd.execute();
			}
		}

		// move rest items
		var h = $("#" + data.id).height();
		self.moveItemsAfter(data.id, {
			dx: 0,
			dy: -h
		}, "row");

		// remove current row
		$("#" + data.id).remove();

		// check for current rows count
		if ($(self.rows).children("li").length == 0) {
			// add one row
			$(self.rows).append(self.getNewRow()[0]);
			$(self.rows).children("li").each(makeRow);
		}
	};

	var createRowsContextMenu = function () {

		var initMenu = function (roles) {
			var items = {
				"add": {
					name: i18n.SwimlanePanel.add_row,
					icon: "paste"
				},
				"delete": {
					name: i18n.SwimlanePanel.remove_row,
					icon: "delete"
				}
			};

			items["separator1"] = "-----";
			_.each(roles, function (role) {
				items["role" + role.id] = {
					name: role.name
				};
			});

			$.contextMenu({
				zIndex: 1000,
				selector: ".parent_row",

				items: items,

				events: {
					show: function (opt, x, y) {
						draw2d.bpmn.getSwimlanePanel().blocksMenu.hide();
					}
				},
				callback: function (key, options) {
					var data = {
						id: $(options.$trigger).attr("id")
					};

					if (key == "add") {
						cmdAppendRow(data);
					}
					else if (key == "delete") {
						cmdDeleteRow(data);
					}
					else if (key.indexOf("role") == 0) {
						var titleObj = $(options.$trigger).children(".title");
						setHeaderTitle($(titleObj), items[key].name, "row");
					}
				}
			});
		};

		$.get(TeamTime.getUrlForTask("loadRoles"),
			function (data) {
				initMenu(JSON.parse(data));
			});
	};

	var cmdAppendColumn = function (data) {
		var newCol = self.getNewColumn();
		$("#" + data.id).after(newCol[0]);

		var newId = newCol[1];
		makeColumn(0, $("#" + newId)[0]);

		// move rest items
		var w = $("#" + newId).width();
		self.moveItemsAfter(newId, {
			dx: w,
			dy: 0
		}, "column");
	};

	var cmdDeleteColumn = function (data) {
		var nameCol = getHeaderTitle($("#" + data.id).children(".title"), "column");

		var figures = self.getItems(data.id, "column");
		var l = figures.length;
		if (l > 0) {
			if (!confirm(draw2d.bpmn.strReplaceNamed(i18n.SwimlanePanel.prompt_remove_column, {
				name: nameCol
			}))) {
				return;
			}
			// remove children figures
			for (var i = 0; i < l; i++) {
				var cmd = new draw2d.CommandDelete(figures[i]);
				cmd.execute();
			}
		}

		// move rest items
		var w = $("#" + data.id).width();
		self.moveItemsAfter(data.id, {
			dx: -w,
			dy: 0
		}, "column");

		// remove current column
		$("#" + data.id).remove();

		// check for current columns count
		if ($(self.columns).children("li").length == 0) {
			// add one column
			$(self.columns).append(self.getNewColumn()[0]);
			$(self.columns).children("li").each(makeColumn);
		}
	};

	var createColumnsContextMenu = function () {
		$.contextMenu({
			zIndex: 1000,
			selector: ".parent_column",

			items: {
				"add": {
					name: i18n.SwimlanePanel.add_column,
					icon: "paste"
				},
				"delete": {
					name: i18n.SwimlanePanel.remove_column,
					icon: "delete"
				}
			},

			events: {
				show: function (opt, x, y) {
					draw2d.bpmn.getSwimlanePanel().blocksMenu.hide();
				}
			},
			callback: function (key, options) {
				var data = {
					id: $(options.$trigger).attr("id")
				};

				if (key == "add") {
					cmdAppendColumn(data);
				}
				else if (key == "delete") {
					cmdDeleteColumn(data);
				}
			}
		});
	};

	var getFreeId = function (parentType) {
		var i = 1;
		var id = "#" + self.name + "-" + (parentType == "row"? "row" : "col");
		// find not existing id
		while ($(id + i).length > 0) {
			i++;
		}

		return i;
	};

	var getDefaultParamsFigure = function () {
		return {
			date: "",
			plan: "0",
			fact: "0",
			price: "0",
			state: "",
			part: "0",
			userName: ""
		};
	}

	var getParamsFromFigure = function (figure) {
		var defaultParams = getDefaultParamsFigure();
		var result = _.clone(defaultParams);

		if (figure.parentRow) {
			result.userName = self.getTitle($(figure.parentRow), "row");
		}

		if (figure.paramsData) {
			result.plan = parseFloat(figure.paramsData.hoursPlan / 60)
			.toFixed(2).toString().replace(".", ",");
			result.price = parseFloat(
				figure.paramsData.hourlyRate * (figure.paramsData.hoursPlan / 60))
			.toFixed(2).toString().replace(".", ",");

			if (figure.paramsData.userName) {
				result.userName = figure.paramsData.userName;
			}
		}

		return result;
	};

	// public functions

	this.getNewColumn = function (title, id) {
		if (!id) {
			var i = getFreeId("column");
			id = this.name + "-col" + i;
		}

		title = title || ('new milestone ' + i);

		return ['<li class="ui-state-default parent_column" id="' + id + '">' +
		'<span class="ui-icon ui-icon-arrowthick-2-e-w"></span><div class="title">' +
		title + '</div></li>', id];
	};

	this.getNewRow = function (title, id) {
		if (!id) {
			var i = getFreeId("row");
			id = this.name + "-row" + i;
		}

		title = title || ('new participant ' + i);

		return ['<li class="ui-state-default parent_row" id="' + id + '">' +
		'<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' +
		'<div class="title"><span class="rowTitle">' + title + '</span></div></li>', id];
	};

	this.getTitle = function (obj, parentType) {
		return getHeaderTitle($(obj).children(".title"), parentType);
	};

	this.init = function (name) {
		this.name = name || "SwimlanePanel";

		this.changedFigures = new SwimlaneChangedFigures();

		this.workflow = new draw2d.Workflow(this.name + "-paintarea");
		this.workflow.setBackgroundImage(null, false);
		//this.workflow.html.style.backgroundImage = "url(js/draw2d/bpmn/resources/grid_10.png)";
		//this.workflow.setViewPort(this.name + "-scrollarea");

		this.workflow.getCommandStack().addCommandStackEventListener(new draw2d.bpmn.CommandListener());
		draw2d.bpmn.addWorkflowConextMenu(this.workflow);

		// add flow menu
		var menu = new draw2d.bpmn.FlowMenu(this.workflow);
		this.workflow.addSelectionListener(menu);
		this.workflow.addSelectionListener(new draw2d.bpmn.SelectionChangeListener());
		//this.workflow.setEnableSmoothFigureHandling(true);

		this.rows = $("#" + this.name + "-rows");
		this.columns = $("#" + this.name + "-cols");

		this.blocksMenu = SwimlanePanelBlocksMenu;

		this.createPanel();
	};

	this.createPanel = function () {
		makeSortableRows();
		makeSortableColumns();

		var i;
		// create default rows and columns
		for (i = 0; i < 4; i++) {
			$(this.columns).append(this.getNewColumn()[0]);
		}
		// make resizable created columns
		$(this.columns).children("li").each(makeColumn);

		for (i = 0; i < 4; i++) {
			$(this.rows).append(this.getNewRow()[0]);
		}
		// make resizable created rows
		$(this.rows).children("li").each(makeRow);

		createActiveCell();

		SwimlanePanelToolbar.init();
		this.blocksMenu.init();

		createRowsContextMenu();
		createColumnsContextMenu();

		// some key handlers
		$(document).keyup(function (e) {
			if (e.which == 27) {
				self.hideBalloons();
			}
		});
	};

	this.getColumn = function (coords) {
		var result = null;
		var offsetX = $(this.columns).position().left;

		$(this.columns).children("li").each(function (i, n) {
			var x = $(n).position().left - offsetX;

			if (coords.x >= x && coords.x <= x + $(n).width()) {
				result = n;
			}
		});

		return result;
	};

	this.getRow = function (coords) {
		var result = null;
		var offsetY = $(this.rows).position().top;

		$(this.rows).children("li").each(function (i, n) {
			var y = $(n).position().top - offsetY;

			if (coords.y >= y && coords.y <= y + $(n).height()) {
				result = n;
			}
		});

		return result;
	};

	this.getMaxX = function (parentId) {
		var result = 0;

		var figures = this.getItems(parentId, "column");
		var l = figures.length;
		for (var i = 0; i < l; i++) {
			var x = figures[i].getX() + figures[i].getWidth();
			if (x > result) {
				result = x;
			}
		}

		return result;
	};

	this.getMaxY = function (parentId) {
		var result = 0;

		var figures = this.getItems(parentId, "row");
		var l = figures.length;
		for (var i = 0; i < l; i++) {
			var y = figures[i].getY() + figures[i].getHeight();
			if (y > result) {
				result = y;
			}
		}

		return result;
	};

	this.setColumnMinWidth = function (parentCol) {
		if (parentCol == null) {
			return;
		}

		console.log("set column width" + $(parentCol).attr("id"));

		var offsetX = $(this.columns).position().top;
		var parentX = $(parentCol).position().left - offsetX;

		var minWidth = this.getMaxX($(parentCol).attr("id")) + 10 - parentX;
		$(parentCol).resizable("option", "minWidth", Math.max(minWidth, minColumnWidth));
	};

	this.setRowMinHeight = function (parentRow) {
		if (parentRow == null) {
			return;
		}

		console.log("set row height" + $(parentRow).attr("id"));

		var offsetY = $(this.rows).position().top;
		var parentY = $(parentRow).position().top - offsetY;
		var minHeight = this.getMaxY($(parentRow).attr("id")) + 10 - parentY;
		$(parentRow).resizable("option", "minHeight", Math.max(minHeight, minRowHeight));
	};

	this.setMinBounds = function (item) {
		if (item.parentCol == null || item.parentRow == null) {
			return;
		}

		// set parent row minHeight for resize
		var offsetY = $(this.rows).position().top;
		var parentY = $(item.parentRow).position().top - offsetY;
		var minHeight = item.getY() + item.getHeight() + 10 - parentY;
		var oldHeight = $(item.parentRow).resizable("option", "minHeight");

		if (minHeight > oldHeight) {
			$(item.parentRow).resizable("option", "minHeight", Math.max(minHeight, minRowHeight));
		}
		else {
			minHeight = this.getMaxY($(item.parentRow).attr("id")) + 10 - parentY;
			$(item.parentRow).resizable("option", "minHeight", Math.max(minHeight, minRowHeight));
		}

		// set parent col minWidth for resize
		var offsetX = $(this.columns).position().top;
		var parentX = $(item.parentCol).position().left - offsetX;
		var minWidth = item.getX() + item.getWidth() + 10 - parentX;
		var oldWidth = $(item.parentCol).resizable("option", "minWidth");

		if (minWidth > oldWidth) {
			$(item.parentCol).resizable("option", "minWidth", Math.max(minWidth, minColumnWidth));
		}
		else {
			minWidth = this.getMaxX($(item.parentCol).attr("id")) + 10 - parentX;
			$(item.parentCol).resizable("option", "minWidth", Math.max(minWidth, minColumnWidth));
		}
	};

	this.getItems = function (parentId, parentType, filterChildren) {
		parentType = parentType == "row"? "parentRow" : "parentCol";
		filterChildren = filterChildren || true;

		var parent = $("#" + parentId);
		var result = [];
		var figures = this.workflow.getFigures();
		var l = figures.getSize();

		for (var i = 0; i < l; i++) {
			var obj = figures.get(i);

			if (filterChildren) {
				// figure has parent - ignore
				if (obj.getParent() != null) {
					continue;
				}
			}

			if ($(obj[parentType]).attr("id") == $(parent).attr("id")) {
				result.push(obj);
			}
		}

		return result;
	};

	this.moveRows = function (pairs) {
		var l = pairs.dest.length;
		var i, j, fl;
		var figures;
		var dy, dh;

		// check direction
		dy = $("#" + pairs.src[0]).position().top - $("#" + pairs.src[1]).position().top;
		dh = $("#" + pairs.src[1]).height();
		if (dy < 0) {
			dh = -dh;
		}
		// move changed rows
		for (i = 0; i < l; i++) {
			figures = this.getItems(pairs.dest[i][1], "row");
			fl = figures.length;
			for (j = 0; j < fl; j++) {
				figures[j].setPosition(figures[j].getX(), figures[j].getY() + dh);
			}
		}

		// move dragged src row
		dy = $("#" + pairs.src[1]).position().top - $("#" + pairs.src[1]).data('oldPositionY');
		figures = this.getItems(pairs.src[1], "row");
		fl = figures.length;
		for (j = 0; j < fl; j++) {
			figures[j].setPosition(figures[j].getX(), figures[j].getY() + dy);
		}
	};

	this.moveColumns = function (pairs) {
		var l = pairs.dest.length;
		var i, j, fl;
		var figures;
		var dx, dw;

		// check direction
		dx = $("#" + pairs.src[0]).position().left - $("#" + pairs.src[1]).position().left;
		dw = $("#" + pairs.src[1]).width();
		if (dx < 0) {
			dw = -dw;
		}
		// move changed columns
		for (i = 0; i < l; i++) {
			figures = this.getItems(pairs.dest[i][1], "column");
			fl = figures.length;
			for (j = 0; j < fl; j++) {
				figures[j].setPosition(figures[j].getX() + dw, figures[j].getY());
			}
		}

		// move dragged src column
		dx = $("#" + pairs.src[1]).position().left - $("#" + pairs.src[1]).data('oldPositionX');
		figures = this.getItems(pairs.src[1], "column");
		fl = figures.length;
		for (j = 0; j < fl; j++) {
			figures[j].setPosition(figures[j].getX() + dx, figures[j].getY());
		}
	};

	this.moveItemsAfter = function (parentId, coord, parentType) {
		var isRest = false;
		var figures;
		var fl = 0;
		var j;

		if (parentType == "row") {
			$(this.rows).children("li").each(function (i, n) {
				// find src row
				if ($(n).attr("id") == parentId) {
					isRest = true;
					return;
				}

				// move rest rows by dy
				if (isRest) {
					figures = self.getItems($(n).attr("id"), parentType);
					fl = figures.length;
					for (j = 0; j < fl; j++) {
						figures[j].setPosition(figures[j].getX(), figures[j].getY() + coord.dy);
					}
				}
			});
		}
		else if (parentType == "column") {
			$(this.columns).children("li").each(function (i, n) {
				// find src column
				if ($(n).attr("id") == parentId) {
					isRest = true;
					return;
				}

				// move rest columns by dx
				if (isRest) {
					figures = self.getItems($(n).attr("id"), parentType);
					fl = figures.length;
					for (j = 0; j < fl; j++) {
						figures[j].setPosition(figures[j].getX() + coord.dx, figures[j].getY());
					}
				}
			});
		}
	};

	this.getOffset = function () {
		return {
			left: $(this.columns).offset().left,
			top: $(this.rows).offset().top
		};
	}

	this.hideActiveCell = function () {
		$("div.active_cell").css("display", "none");
	};

	this.showActiveCell = function (item) {
		if (item.parentCol == null || item.parentRow == null) {
			//this.hideActiveCell();
			return;
		}

		var x = $(item.parentCol).position().left + $(this.columns).offset().left;
		var y = $(item.parentRow).position().top + $(this.rows).offset().top;

		$("div.active_cell").css("top", y).css("left", x)
		.css("width", $(item.parentCol).width() + 2)
		.css("height", $(item.parentRow).height() + 2)
		.css("display", "");
	};

	this.hideBalloons = function () {
		// hide balloons for rows
		$(this.rows).children("li").each(function (i, n) {
			$(n).trigger("SwimlinePanel.hideballoon");
		});

		// hide balloons for columns
		$(this.columns).children("li").each(function (i, n) {
			$(n).trigger("SwimlinePanel.hideballoon");
		});
	};

	this.clear = function () {
		//this.workflow.clear();

		$(this.rows).children("li").each(function (i, n) {
			$(n).children("div.horizontal_line").remove();
			$(n).remove();
		});

		$(this.columns).children("li").each(function (i, n) {
			$(n).children("div.vertical_line").remove();
			$(n).remove();
		});
	};

	this.appendRow = function (title, id, size) {
		$(this.rows).append(this.getNewRow(title, id)[0]);
		$(this.rows).children("li#" + id).css("height", size);
		$(this.rows).children("li").each(makeRow);
	};

	this.appendColumn = function (title, id, size) {
		$(this.columns).append(this.getNewColumn(title, id)[0]);
		$(this.columns).children("li#" + id).css("width", size);
		$(this.columns).children("li").each(makeColumn);
	};

	this.setWidthForColumn = function (column, obj) {
		var newX = obj.getX() + obj.getWidth();
		var x = $(column).position().left - $(this.columns).position().left + $(column).width();
		var dx = newX - x;

		if (dx <= 0) {
			return;
		}

		//this.resizeWidth(dx + 20);
		$(column).width($(column).width() + dx + 10);

		// move all columns after this column
		this.moveItemsAfter($(column).attr("id"), {
			dx: dx + 10,
			dy: 0
		}, "column");
		$(column).resizable("option", "minWidth", $(column).width());

		// move right line by width
		$(column).children("div.right_line").css("left", ($(column).width() + 1) + "px");
	};

	this.getWidth = function () {
		return $("#SwimlanePanel-cols-container").width();
	};

	this.getHeight = function () {
		return $("#SwimlanePanel-rows-container").height();
	};

	/*this.resizeWidth = function (dx) {
		var obj;

		// expand columns container
		obj = $("#SwimlanePanel-cols-container");

		$(obj).width($(obj).width() + dx);

		obj = $("div.horizontal_line");
		$(obj).width($(obj).width() + dx);

		// expand workflow
		obj = $("#SwimlanePanel-paintarea");
		$(obj).width($(obj).width() + dx);

		obj = $("#SwimlanePanel-scrollarea");
		$(obj).width($(obj).width() + dx);
	};

	this.resizeHeight = function (dy) {
		var obj;

		// expand rows container
		obj = $("#SwimlanePanel-rows-container");
		$(obj).height($(obj).height() + dy);

		obj = $("div.vertical_line");
		$(obj).height($(obj).height() + dy);

		obj = $("#SwimlanePanel-paintarea");
		$(obj).height($(obj).height() + dy);

		obj = $("#SwimlanePanel-scrollarea");
		$(obj).height($(obj).height() + dy);
	};*/

	this.showInfo = function (typeInfo) {
		var figures = this.workflow.getFigures().asArray();
		var figuresNotSaved = [];
		var figuresForLoad = [];

		_.each(figures, function (figure) {
			if (!(figure instanceof draw2d.bpmn.Activity)) {
				return;
			}

			if (figure.paramsData && (figure.paramsData._id || figure.paramsData.linkedId)) {
				figuresForLoad.push({
					id: figure.id,
					_id: figure.paramsData._id,
					linkedId: figure.paramsData.linkedId
				});
			}
			else {
				figuresNotSaved.push({
					id: figure.id,
					params: getParamsFromFigure(figure)
				});
			}
		});

		var defaultParams = getDefaultParamsFigure();

		var showBlockInfo = function (data) {
			var figure = self.workflow.getFigure(data.id);
			var params = _.extend(defaultParams, data.params);

			if (figure.parentRow && !params.userName) {
				params.userName = self.getTitle($(figure.parentRow), "row");
			}

			figure.showInfo(typeInfo, params);
		};

		// load info for existing todos
		$.post(TeamTime.getUrlForTask("loadInfo"), {
			info: typeInfo,
			figures: JSON.stringify(figuresForLoad)
		},
		function (data) {
			_.each(JSON.parse(data), showBlockInfo);
		});

		_.each(figuresNotSaved, showBlockInfo);
	};

	this.checkMaxSizeDiagram = function (diagram) {
		var maxSize = 65535;

		var l = diagram.length;
		console.log("Current size: " + l);

		if (l >= maxSize) {
			alert(i18n.SwimlanePanel.diagram_is_too_big);
			return false;
		}

		return true;
	}

};