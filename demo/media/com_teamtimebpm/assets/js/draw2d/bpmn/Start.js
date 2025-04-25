
// "Start Event" element implementation

draw2d.bpmn.Start = function () {
	draw2d.bpmn.Figure.call(this);

	this.outputPort = null;

	this.setDimension(40, 40);
};

draw2d.bpmn.Start.prototype = new draw2d.bpmn.Figure();
draw2d.bpmn.Start.prototype.type = "bpmn.Start";

draw2d.bpmn.Start.prototype.createHTMLElement = function() {
	var $ = TeamTime.jQuery;
	var item = draw2d.Figure.prototype.createHTMLElement.call(this);

	$(item).html(
		'<div class="bpmn_start">\
			<div class="bpmn_start_content">Start</div>\
		</div>');

	return item;
};

draw2d.bpmn.Start.prototype.setWorkflow = function (/*:draw2d.Workflow*/ workflow) {
	draw2d.bpmn.Figure.prototype.setWorkflow.call(this, workflow);

	draw2d.bpmn.addConextMenu(this);

	// right port
	if (workflow !== null && this.outputPort === null) {
		this.outputPort = new draw2d.bpmn.OutputPort();
		// It is possible to add "5" Connector to this port
		this.outputPort.setMaxFanOut(5);
		this.outputPort.setWorkflow(workflow);
		this.outputPort.setName("output");
		this.addPort(this.outputPort, this.width, this.height / 2);
	}
};

draw2d.bpmn.Start.prototype.getContextMenuItems = function () {
	return {
		"delete": {
			name: i18n.bpmn.delete_item,
			icon: "delete"
		}
	}
};

draw2d.bpmn.Start.prototype.onContextMenuCmd = function (data) {
	if (data.key == "delete") {		
		var cmd = new draw2d.CommandDelete(data.figure);
		cmd.execute();
	}

	console.log("start " + data.key);
};
