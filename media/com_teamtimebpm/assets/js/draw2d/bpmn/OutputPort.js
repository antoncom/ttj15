
// "OutputPort" element implementation

draw2d.bpmn.OutputPort = function (/*:draw2d.Figure*/ uiRepresentation) {
	draw2d.OutputPort.call(this, uiRepresentation);

	this.setBackgroundColor(new draw2d.Color(245, 115, 115));
};

draw2d.bpmn.OutputPort.prototype = new draw2d.OutputPort();
draw2d.bpmn.OutputPort.prototype.type = "bpmn.OutputPort";

draw2d.bpmn.OutputPort.prototype.onDrop = function (/*:draw2d.Port*/ port) {
	if (this.getMaxFanOut() <= this.getFanOut()) {
		return;
	}

	// same node
	if (this.parentNode.id == port.parentNode.id) {
		return;
	}
	
	// same type
	if (port.getName().indexOf("output") >= 0) {
		return;
	}

	var command = new draw2d.CommandConnect(this.parentNode.workflow, this, port);

	// set custom defined connection
	command.setConnection(new draw2d.bpmn.Connection());

	this.parentNode.workflow.getCommandStack().execute(command);
};
