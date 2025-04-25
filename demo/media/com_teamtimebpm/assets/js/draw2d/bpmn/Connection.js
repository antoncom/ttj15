
// "Connection" element implementation

draw2d.bpmn.Connection = function () {
	draw2d.Connection.call(this);

	this.sourcePort = null;
	this.targetPort = null;
	this.lineSegments = [];
	this.setTargetDecorator(new draw2d.bpmn.ArrowConnectionDecorator());

	this.setColor(new draw2d.Color("#0076aa"));
	this.setLineWidth(2);

	// create label
	this.label = new draw2d.Label("");
	this.label.setBackgroundColor(new draw2d.Color(230, 230, 250));
	this.label.setBorder(new draw2d.LineBorder(1));

	draw2d.bpmn.setVisibility(this.label, false);

	var $ = TeamTime.jQuery;
	var self = this;

	// make editable label
	$(self.label.html).showBalloon({
		//position: ,
		contents: '<input class="editable_label p' + self.label.id + '" type="text" size="15" />'
	})
	.hideBalloon()
	// custom event for calling hide/show balloon
	.on("bpmn.Label.hideballoon", function () {
		$(this).hideBalloon();
	})
	.on("bpmn.Label.showballoon", function () {
		draw2d.bpmn.setVisibility(self.label, true);

		var titleObj = this;
		$(titleObj).showBalloon();

		$("input.editable_label.p" + self.label.id).val(self.label.getText()).focus()
		.change(function () {
			self.label.setText($(this).val());
			$(titleObj).hideBalloon();
		})
		.blur(function () {
			$(titleObj).hideBalloon();
		});
	});

	this.addFigure(this.label, new draw2d.ManhattanMidpointLocator(this));

	draw2d.bpmn.addConextMenu(this);
};

draw2d.bpmn.Connection.prototype = new draw2d.Connection();

draw2d.bpmn.Connection.prototype.setWorkflow = function (/*:draw2d.Workflow*/ workflow) {
	draw2d.Connection.prototype.setWorkflow.call(this, workflow);

	if (this.targetPort) {
		var f = this.targetPort.getParent();
		this.setZOrder(parseInt(f.getZOrder()) - 1);
	}

	draw2d.bpmn.addConnectionConextMenu(this);
};

draw2d.bpmn.Connection.prototype.getContextMenuItems = function () {
	return {
		"delete": {
			name: i18n.bpmn.delete_item,
			icon: "delete"
		},
		"set_label": {
			name: i18n.bpmn.add_label,
			icon: "paste"
		},
		"remove_label": {
			name: i18n.bpmn.remove_label,
			icon: "delete"
		}
	}
};

draw2d.bpmn.Connection.prototype.onContextMenuCmd = function (data) {
	var $ = TeamTime.jQuery;

	if (data.key == "delete") {
		var cmd = new draw2d.CommandDelete(data.figure);
		cmd.execute();
	}
	else if (data.key == "set_label") {
		$(data.figure.label.html).trigger("bpmn.Label.showballoon");
	}
	else if (data.key == "remove_label") {
		draw2d.bpmn.setVisibility(data.figure.label, false);
	}

	console.log("Connection " + data.key);
};