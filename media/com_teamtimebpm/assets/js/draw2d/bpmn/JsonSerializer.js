
// Json serializer for swimlane panel structure

draw2d.bpmn.JsonSerializer = function () { };

draw2d.bpmn.JsonSerializer.prototype.getChildren = function (parent) {
	var result = [];

	var figures = parent.getChildren();
	var l = figures.getSize();
	for (var i = 0; i < figures.getSize(); i++) {
		result.push(this.getData(figures.get(i)));
	}

	return result;
};

draw2d.bpmn.JsonSerializer.prototype.getData = function (figure) {
	var $ = TeamTime.jQuery;

	var result = {};
	result.id = figure.getId();
	result.type = figure.type;
	result.x = figure.getX();
	result.y = figure.getY();
	result.width = figure.getWidth();
	result.height = figure.getHeight();

	if (!(figure instanceof draw2d.bpmn.Start) && !(figure instanceof draw2d.bpmn.End)) {
		result.text = figure.getText();
	}

	// parent row link
	if (figure.parentRow != null) {
		result.parentRow = $(figure.parentRow).attr("id");
	}

	// parent col link
	if (figure.parentCol != null) {
		result.parentCol = $(figure.parentCol).attr("id");
	}

	// get activity data
	if (figure instanceof draw2d.bpmn.Activity) {
		result.viewState = figure.viewState;
		result.originalWidth = figure.originalWidth;
		result.originalHeight = figure.originalHeight;
		result.children = this.getChildren(figure);

		if (figure.paramsData) {
			result.paramsData = figure.paramsData;
		}
	}

	return result;
};

draw2d.bpmn.JsonSerializer.prototype.initData = function (f, panel) {
	var $ = TeamTime.jQuery;

	var n = f.type.replace("bpmn.", "");
	var obj = new draw2d.bpmn[n]();

	obj.setId(f.id);
	panel.workflow.addFigure(obj, f.x, f.y);
	obj.setDimension(f.width, f.height);
	obj.parentRow = $(panel.rows).children("li#" + f.parentRow);
	obj.parentCol = $(panel.columns).children("li#" + f.parentCol);

	if (!(obj instanceof draw2d.bpmn.Start) && !(obj instanceof draw2d.bpmn.End)) {
		if ("text" in f) {
			obj.setText(f.text);
		}
	}

	if (obj instanceof draw2d.bpmn.Activity) {
		if (f.viewState == "expanded") {
			obj.originalWidth = f.width;
			obj.originalHeight = f.height;
		}
		else if (f.viewState == "collapsed") {
			obj.setDimension(f.originalWidth, f.originalHeight)
		}

		if (f.children && f.children.length > 0) {
			for (var i = 0; i < f.children.length; i++) {
				var ch = this.initData(f.children[i], panel);
				if (ch) {
					obj.addChild(ch);
				}
			}
		}

		//obj.setDimension(f.width, f.height);
		if (f.viewState == "linked") {
			obj.setViewState(f.viewState);
		}
		else {
			obj.setViewMode(obj, f.viewState);
		}

		if (f.paramsData) {
			obj.paramsData = f.paramsData;
		}
	}

	return obj;
};

draw2d.bpmn.JsonSerializer.prototype.serialize = function (panel) {
	var $ = TeamTime.jQuery;

	var result = {};

	// save rows data
	result.rows = [];
	$(panel.rows).children("li").each(function (i, n) {
		result.rows.push({
			id: $(n).attr("id"),
			size: $(n).height(),
			title: panel.getTitle($(n), "row")
		});
	});

	// save columns data
	result.columns = [];
	$(panel.columns).children("li").each(function (i, n) {
		result.columns.push({
			id: $(n).attr("id"),
			size: $(n).width(),
			title: panel.getTitle($(n), "column")
		});
	});

	// save figures
	result.figures = [];
	var figures = panel.workflow.getFigures();
	var i, l;
	l = figures.getSize();
	for (i = 0; i < l; i++) {
		var figure = figures.get(i);

		if (figure.type == "draw2d.ToolPalette") {
			continue;
		}

		if (figure.getParent() == null) {
			result.figures.push(this.getData(figures.get(i)));
		}
	}

	// save connections
	result.connections = [];
	var conns = panel.workflow.getLines();
	l = conns.getSize();
	for (i = 0; i < l; i++) {
		var c = conns.get(i);

		result.connections.push({
			label: c.label.getText(),
			source: {
				figureId: c.sourcePort.getParent().getId(),
				name: c.sourcePort.getName()
			},
			target: {
				figureId: c.targetPort.getParent().getId(),
				name: c.targetPort.getName()
			}
		});
	}

	// save changed figures
	result.changed = panel.changedFigures.getItems();

	return JSON.stringify(result, null, "\t");
};

draw2d.bpmn.JsonSerializer.prototype.unserialize = function (panel, jsonStr) {
	var $ = TeamTime.jQuery;

	var data = JSON.parse(jsonStr);

	// load rows data
	var i;
	for (i = 0; i < data.rows.length; i++) {
		var row = data.rows[i];
		panel.appendRow(row.title, row.id, row.size);
	}

	// load columns data
	for (i = 0; i < data.columns.length; i++) {
		var col = data.columns[i];
		panel.appendColumn(col.title, col.id, col.size);
	}

	// load figures
	for (i = 0; i < data.figures.length; i++) {
		this.initData(data.figures[i], panel);
	}

	// load connections
	for (i = 0; i < data.connections.length; i++) {
		var c = data.connections[i];
		var conn = new draw2d.bpmn.Connection();
		var sourcePort = panel.workflow.getFigure(c.source.figureId);
		var targetPort = panel.workflow.getFigure(c.target.figureId);

		if (sourcePort && targetPort) {
			conn.setSource(sourcePort.getPort(c.source.name));
			conn.setTarget(targetPort.getPort(c.target.name));
			panel.workflow.addFigure(conn);
		}
	}

	// hide connections lines for collapsed blocks
	var figures = panel.workflow.getFigures();
	var l = panel.workflow.getFigures().getSize();
	for (i = 0; i < l; i++) {
		if (figures.get(i) instanceof draw2d.bpmn.Activity && figures.get(i).viewState == "collapsed") {
			var children = figures.get(i).getChildren();
			var lch = children.getSize();
			for (var j = 0; j < lch; j++) {
				children.get(j).setVisibility(false);
			}
		}
	}
};