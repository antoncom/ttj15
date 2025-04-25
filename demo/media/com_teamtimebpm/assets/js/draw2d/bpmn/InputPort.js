
// "InputPort" element implementation

draw2d.bpmn.InputPort = function (/*:draw2d.Figure*/ uiRepresentation) {
	draw2d.InputPort.call(this, uiRepresentation);

	this.setBackgroundColor(new draw2d.Color(115, 115, 245));
};

draw2d.bpmn.InputPort.prototype = new draw2d.InputPort();
draw2d.bpmn.InputPort.prototype.type = "bpmn.InputPort";

draw2d.bpmn.InputPort.prototype.onDrop = function (/*:draw2d.Port*/ port) {
	if (port.getMaxFanOut && port.getMaxFanOut() <= port.getFanOut()) {
		return;
	}

	// same node
	if (this.parentNode.id == port.parentNode.id) {
		return;
	}
	
	// same type
	if (port.getName().indexOf("input") >= 0) {
		return;
	}

	var command = new draw2d.CommandConnect(this.parentNode.workflow, port, this);

	// set custom defined connection
	command.setConnection(new draw2d.bpmn.Connection());

	this.parentNode.workflow.getCommandStack().execute(command);
};
