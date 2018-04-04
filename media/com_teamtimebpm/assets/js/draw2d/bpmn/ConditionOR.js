
// "Condition OR" element implementation

draw2d.bpmn.ConditionOR = function () {
	draw2d.bpmn.Figure.call(this);

	this.inputPort = [null, null];
	this.outputPort = [null, null];
};

draw2d.bpmn.ConditionOR.prototype = new draw2d.bpmn.Figure();
draw2d.bpmn.ConditionOR.prototype.type = "bpmn.ConditionOR";

draw2d.bpmn.ConditionOR.prototype.createHTMLElement = function () {
	var $ = TeamTime.jQuery;
	var item = draw2d.Figure.prototype.createHTMLElement.call(this);

	$(item).html(
		"<div class='bpmn_condition_or'>\
			<div class='bpmn_icon'></div>\
			<div class='bpmn_event_content'>OR</div>\
		</div>");
	$(".bpmn_icon", item).css("background-image", draw2d.bpmn.getCssImage("condition_or.gif"));

	return item;
};

draw2d.bpmn.ConditionOR.prototype.setWorkflow = function (/*:draw2d.Workflow*/ workflow) {
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

	// right port
	if (workflow !== null && this.outputPort[0] === null) {
		this.outputPort[0] = new draw2d.bpmn.OutputPort();
		this.outputPort[0].setWorkflow(workflow);
		this.outputPort[0].setName("output");
		this.addPort(this.outputPort[0], this.width, this.height / 2);
	}

	// bottom port
	if (workflow !== null && this.outputPort[1] === null) {
		this.outputPort[1] = new draw2d.bpmn.OutputPort();
		this.outputPort[1].setWorkflow(workflow);
		this.outputPort[1].setName("output1");
		this.addPort(this.outputPort[1], this.width / 2, this.height);
	}
};

draw2d.bpmn.ConditionOR.prototype.getContentElement = function () {
	var $ = TeamTime.jQuery;

	return $(".bpmn_event_content", this.html);
};

draw2d.bpmn.ConditionOR.prototype.getContextMenuItems = function () {
	return {
		"delete": {
			name: i18n.bpmn.delete_item,
			icon: "delete"
		}
	}
};

draw2d.bpmn.ConditionOR.prototype.onContextMenuCmd = function (data) {
	if (data.key == "delete") {		
		var cmd = new draw2d.CommandDelete(data.figure);
		cmd.execute();
	}

	console.log("ConditionOR " + data.key);
};