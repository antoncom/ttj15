
// Base Figure element implementation

draw2d.bpmn.Figure = function () {
	draw2d.Node.call(this);

	this.setDimension(40, 40);

	draw2d.bpmn.initInstance(this);

	// make editable content if element exists
	var contentElement = this.getContentElement();
	if (contentElement) {
		this.initEditableContent(contentElement);
	}
};

draw2d.bpmn.Figure.prototype = new draw2d.Node();

// base figure not resizable
draw2d.bpmn.Figure.prototype.isResizeable = function () {
	return false;
};

// event handlers

draw2d.bpmn.Figure.prototype.onDragstart = function (x, y) {
	draw2d.bpmn.onDragstart(this);

	return draw2d.Node.prototype.onDragstart.call(this, x, y);
};

draw2d.bpmn.Figure.prototype.onDragend = function () {
	draw2d.Node.prototype.onDragend.call(this);
	draw2d.bpmn.onDragend(this);
};

draw2d.bpmn.Figure.prototype.onDrag = function () {
	draw2d.Node.prototype.onDrag.call(this);
	draw2d.bpmn.onDrag(this);
};

draw2d.bpmn.Figure.prototype.setVisibility = function (visible) {
	draw2d.bpmn.setVisibility(this, visible);
};

// editable content

draw2d.bpmn.Figure.prototype.getContentElement = function () {
	return null;
};

draw2d.bpmn.Figure.prototype.getText = function () {
	var $ = TeamTime.jQuery;
	var contentElement = this.getContentElement();

	if (!contentElement) {
		return "";
	}

	return $(contentElement).text();
};

draw2d.bpmn.Figure.prototype.setText = function (text) {
	var $ = TeamTime.jQuery;
	var contentElement = this.getContentElement();

	if (!contentElement) {
		return;
	}

	$(contentElement).text(text);
};

draw2d.bpmn.Figure.prototype.isContentEditable = function () {
	var $ = TeamTime.jQuery;

	return $(this.getContentElement()).attr("contentEditable");
};

draw2d.bpmn.Figure.prototype.setContentEditable = function (flag) {
	var $ = TeamTime.jQuery;

	var contentElement = this.getContentElement();
	if (!contentElement) {
		return;
	}

	if (flag) {
		$(contentElement).attr("contentEditable", "true").focus();
	}
	else {
		$(contentElement).removeAttr("contentEditable");
	}
};

draw2d.bpmn.Figure.prototype.initEditableContent = function (contentElement) {
	var $ = TeamTime.jQuery;
	var self = this;

	$(contentElement).blur(function () {
		self.setContentEditable(false);
	});
};

draw2d.bpmn.Figure.prototype.onDoubleClick = function () {
	this.setContentEditable(true);
	draw2d.bpmn.selectText(this);
};

draw2d.bpmn.Figure.prototype.onKeyDown = function (keyCode, ctrl) {
	// disable del command in edit mode

	if (!this.isContentEditable()) {
		draw2d.Node.prototype.onKeyDown.call(this, keyCode, ctrl);
	//if (keyCode == 46) {
	//  this.workflow.getCommandStack().execute(
	//    this.createCommand(new draw2d.EditPolicy(draw2d.EditPolicy.DELETE)));
	//}
	}

// redirect any CTRL key strokes to the parent workflow/canvas
//
//if (ctrl) {
//  this.workflow.onKeyDown(keyCode,ctrl);
//}
};

// get parent row / column
draw2d.bpmn.Figure.prototype.getParentRow = function () {
	if (this.getParent()) {
		return this.getParent().getParentRow();
	}
	else {
		return this.parentRow;
	}
};

draw2d.bpmn.Figure.prototype.getParentCol = function () {
	if (this.getParent()) {
		return this.getParent().getParentCol();
	}
	else {
		return this.parentCol;
	}
};