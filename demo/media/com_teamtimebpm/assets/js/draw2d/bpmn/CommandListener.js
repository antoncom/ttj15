
// Command listener for handle some actions

draw2d.bpmn.CommandListener = function () {
	draw2d.CommandStackEventListener.call(this);
};

draw2d.bpmn.CommandListener.prototype = new draw2d.CommandStackEventListener;

/**
 * Sent when an event occurs on the command stack. draw2d.CommandStackEvent.getDetail()
 * can be used to identify the type of event which has occurred.
 *
 **/
draw2d.bpmn.CommandListener.prototype.stackChanged = function (/*:draw2d.CommandStackEvent*/ event) {
	/*
	 Is the Event a PRE of POST event?

	if (event.isPostChangeEvent())
		log.innerHTML="POST:";
	else
		log.innerHTML="PRE:";


	// EXECUTE, UNDO or REDO?
	//
	var details = event.getDetails();
	if (0 != (details & (draw2d.CommandStack.PRE_EXECUTE | draw2d.CommandStack.POST_EXECUTE))) {
		// "EXECUTE";
	}
	else if (0 != (details & (draw2d.CommandStack.PRE_UNDO | draw2d.CommandStack.POST_UNDO))) {
		log.innerHTML = log.innerHTML+" UNDO";
	}
	else if(0 != (details & (draw2d.CommandStack.PRE_REDO | draw2d.CommandStack.POST_REDO))) {
		log.innerHTML = log.innerHTML+" REDO";
	}
	*/

	var command = event.getCommand();
	//var details = event.getDetails();

	if (event.isPostChangeEvent()) {
		if (command instanceof draw2d.CommandDelete) {
			var swPanel = draw2d.bpmn.getSwimlanePanel();
			swPanel.setMinBounds(command.figure);

			// mark figure as deleted
			if (command.figure.paramsData && command.figure.paramsData._id) {
				swPanel.changedFigures.setItem(command.figure.id, {
					_id: command.figure.paramsData._id,
					_action: "delete"
				});
			}
			else {
				swPanel.changedFigures.removeItem(command.figure.id);
			}
		}
		else if (command instanceof draw2d.CommandMove) {
			var parent = command.figure.getParent();
			if (parent && parent.viewState == "collapsed") {
				console.log("disable drop on collapsed");
				command.undo();
			}
		}
	}
	else if (event.isPreChangeEvent()) {
	//...
	}

/*
	if (command instanceof draw2d.CommandAdd)
		log.innerHTML = log.innerHTML+" => ADD Element";
	else if(command instanceof draw2d.CommandConnect)
		log.innerHTML = log.innerHTML+" => Connect two Ports";
	else if(command instanceof draw2d.CommandDelete)
		log.innerHTML = log.innerHTML+" => Delete Element";
	else if(command instanceof draw2d.CommandMove)
		log.innerHTML = log.innerHTML+" => Moving Element";
	else if(command instanceof draw2d.CommandResize) {
		console.log(command.figure);
		log.innerHTML = log.innerHTML+" => Resize Element";
	}
	*/
};