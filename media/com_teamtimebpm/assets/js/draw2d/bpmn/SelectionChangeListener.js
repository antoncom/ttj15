
draw2d.bpmn.SelectionChangeListener = function () {
	this.currentFigure = null;
}

draw2d.bpmn.SelectionChangeListener.prototype.onSelectionChanged = function (figure) {
	if (figure == this.currentFigure) {
		return;
	}

	if (figure instanceof draw2d.Line) {
		return;
	}

	// code for disabling editable text
	var $ = TeamTime.jQuery;

	if (this.currentFigure) {
		var contentElement = this.currentFigure.getContentElement();
		if (contentElement) {
			$(contentElement).blur();
		}
	}

	this.currentFigure = figure;
};