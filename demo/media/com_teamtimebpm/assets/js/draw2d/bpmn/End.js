
// "End Event" element implementation

draw2d.bpmn.End = function () {
	draw2d.bpmn.Figure.call(this);

	this.inputPort = [null, null, null];

	this.setDimension(40, 40);
};

draw2d.bpmn.End.prototype = new draw2d.bpmn.Figure();
draw2d.bpmn.End.prototype.type = "bpmn.End";

draw2d.bpmn.End.prototype.createHTMLElement = function() {
	var $ = TeamTime.jQuery;
	var item = draw2d.Figure.prototype.createHTMLElement.call(this);

	$(item).html(
		'<div class="bpmn_end">\
			<div class="bpmn_end_content">End</div>\
		</div>');

	return item;
};

draw2d.bpmn.End.prototype.setWorkflow = function (/*:draw2d.Workflow*/ workflow) {
	draw2d.bpmn.Figure.prototype.setWorkflow.call(this, workflow);

	draw2d.bpmn.addConextMenu(this);

	// left port
	if (workflow !== null && this.inputPort[0] === null) {
		this.inputPort[0] = new draw2d.bpmn.InputPort();
		this.inputPort[0].setWorkflow(workflow);
		this.inputPort[0].setName("input");
		this.addPort(this.inputPort[0], 0, this.height / 2);
	}

	// top port
	if (workflow !== null && this.inputPort[1] === null) {
		this.inputPort[1] = new draw2d.bpmn.InputPort();
		this.inputPort[1].setWorkflow(workflow);
		this.inputPort[1].setName("input1");
		this.addPort(this.inputPort[1], this.width / 2, 0);
	}

	// bottom port
	if (workflow !== null && this.inputPort[2] === null) {
		this.inputPort[2] = new draw2d.bpmn.InputPort();
		this.inputPort[2].setWorkflow(workflow);
		this.inputPort[2].setName("input2");
		this.addPort(this.inputPort[2], this.width / 2, this.height);
	}
};

draw2d.bpmn.End.prototype.getContextMenuItems = function () {
	return {
		"delete": {
			name: i18n.bpmn.delete_item,
			icon: "delete"
		}
	}
};

draw2d.bpmn.End.prototype.onContextMenuCmd = function (data) {
	if (data.key == "delete") {		
		var cmd = new draw2d.CommandDelete(data.figure);
		cmd.execute();
	}

	console.log("end " + data.key);
};