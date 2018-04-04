
draw2d.bpmn.FlowMenu = function (/*:draw2d.Workflow*/ workflow) {
	this.actionShowBlocksMenu = new draw2d.bpmn.ButtonShowBlocksMenu(this);

	draw2d.ToolPalette.call(this);

	this.setDropShadow(0);
	this.setDimension(16, 16);
	this.currentFigure = null;
	this.myworkflow = workflow;
	this.added = false;
	this.setDeleteable(false);
	this.setCanDrag(false);
	this.setResizeable(false);
	this.setSelectable(false);
	this.setBackgroundColor(null);
	this.setColor(null);
	this.scrollarea.style.borderBottom = "0px";

	this.actionShowBlocksMenu.setPosition(0, 0);
	this.addChild(this.actionShowBlocksMenu);
};

/** base class of my example double click figure
 * You can use circle, oval,.....too
 **/
draw2d.bpmn.FlowMenu.prototype = new draw2d.ToolPalette();

/**
 * Reenable the setAlpha method. This has been disabled in the Window class.
 *
 **/
draw2d.bpmn.FlowMenu.prototype.setAlpha = function (/*:float 0-1*/ percent) {
	draw2d.Figure.prototype.setAlpha.call(this, percent);
};

/**
 * The FlowMenu has no title bar => return false.
 *
 * @returns Returns [true] if the window has a title bar
 * @type boolean
 **/
draw2d.bpmn.FlowMenu.prototype.hasTitleBar = function () {
	return false;
};

/**
 * Call back method of the framework if the selected object has been changed.
 *
 * @param {draw2d.Figure} figure the object which has been selected.
 **/
draw2d.bpmn.FlowMenu.prototype.onSelectionChanged = function (/*:draw2d.Figure*/ figure) {
	if (figure == this.currentFigure) {
		return;
	}

	if (figure instanceof draw2d.Line || figure instanceof draw2d.bpmn.End) {
		return;
	}

	if (this.added == true) {
		this.myworkflow.removeFigure(this);
		this.added = false;

		// hide blocks menu
		draw2d.bpmn.getBlocksMenu().hide();
	}

	if (figure !== null && this.added == false) {
		// The figure has been changed. Hide the FlowMenu. The addFigure(..) will increase the alpha
		// with an internal timer. But only if the the smooth handling is enabled.
		//
		if (this.myworkflow.getEnableSmoothFigureHandling() == true) {
			this.setAlpha(0.01);
		}

		this.myworkflow.addFigure(this, 100, 100);
		this.added = true;
	}

	// deregister the moveListener from the old figure
	//
	if (this.currentFigure !== null) {
		this.currentFigure.detachMoveListener(this);
	}

	this.currentFigure = figure;
	// deregister the moveListener from the old figure
	//
	if (this.currentFigure !== null) {
		this.currentFigure.attachMoveListener(this);
		this.onOtherFigureMoved(this.currentFigure);
	}
};


draw2d.bpmn.FlowMenu.prototype.setWorkflow = function (/*:draw2d.Workflow*/ workflow) {
	// Call the Figure.setWorkflow(...) and NOT the ToolPalette!
	// Reson: the ToolPalette deregister the selectionListener from the workflow. But we need
	// the selection listener event.
	draw2d.Figure.prototype.setWorkflow.call(this, workflow);
};


/**
 * Move the FlowMenu in synch with the corresponding figure.
 *
 * @param {draw2d.Figure} figure The figure which has changed its position
 * @private
 */
draw2d.bpmn.FlowMenu.prototype.onOtherFigureMoved = function (/*:draw2d.Figure*/ figure) {
	var pos = figure.getPosition();
	var dx = 8;

	if (figure.type && figure.type.toString().indexOf("bpmn.Activity") < 0)  {
		dx = -2;
	}

	this.setPosition(pos.x + figure.getWidth() + dx, pos.y + figure.getHeight() / 2 - 18);
};

// set min size for toolbar
draw2d.bpmn.FlowMenu.prototype.getMinWidth = function () {
	return 18;
};

draw2d.bpmn.FlowMenu.prototype.getMinHeight = function () {
	return 18;
};