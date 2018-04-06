
// "Activity Process" element implementation

draw2d.bpmn.Activity = function () {
	this.inputPort = [null, null, null];
	this.outputPort = [null, null, null];
	this.viewState = "simple";
	this.originalHeight = 0;
	this.originalWidth = 0;

	draw2d.CompartmentFigure.call(this);

	this.setLineWidth(0);
	this.setDimension(this.defaultWidth, this.defaultHeight);

	draw2d.bpmn.initInstance(this);

	// make editable content if element exists
	var contentElement = this.getContentElement();
	if (contentElement) {
		this.initEditableContent(contentElement);
	}
};

draw2d.bpmn.Activity.prototype = new draw2d.CompartmentFigure();
draw2d.bpmn.Activity.prototype.type = "bpmn.Activity";

draw2d.bpmn.Activity.prototype.defaultWidth = 140;
draw2d.bpmn.Activity.prototype.defaultHeight = 60;

draw2d.bpmn.Activity.prototype.createHTMLElement = function () {
	var $ = TeamTime.jQuery;
	var self = this;
	var item = draw2d.Figure.prototype.createHTMLElement.call(this);

	$(item).html(
		'<div class="bpmn_activity simple_activity">\
			<div class="bpmn_linked_process_icon" src="linked_process_icon.gif"></div>\
			<div class="bpmn_content_hidden">\
				<div class="bpmn_activity_container">\
					<span class="bpmn_activity_content">Activity</span>\
					<span class="bpmn_activity_iefix">&nbsp;</span>\
				</div>\
			</div>\
			<div class="bpmn_collapse_icon"></div>\
			<div class="bpmn_activity_backlight" style="display:none;"></div>\
			<div class="bpmn_activity_aditional_info" style="display:none;"></div>\
		</div>');

	$('.bpmn_collapse_icon', item).click(function () {
		self.toggle(self);
	});

	return item;
};

draw2d.bpmn.Activity.prototype.setViewState = function (state) {
	var $ = TeamTime.jQuery;

	var image = "";
	var imageLinked = "";
	this.viewState = state;

	var zOrd = parseInt(this.getZOrder());

	if (this.viewState == "collapsed") {
		$('.bpmn_activity', this.html).removeClass("simple_activity")
		.removeClass("subprocess_expanded_activity").addClass("subprocess_collapsed_activity");
		image = "expand_icon.gif";

		if (zOrd > draw2d.Figure.ZOrderBaseIndex) {
			$('.bpmn_activity', this.html).parent().css('z-index', zOrd - 1);
		}
	}
	else if (this.viewState == "expanded") {
		$('.bpmn_activity', this.html).removeClass("simple_activity")
		.removeClass("subprocess_collapsed_activity").addClass("subprocess_expanded_activity");
		image = "collapse_icon.gif";

		$('.bpmn_activity', this.html).parent().css('z-index', zOrd + 1);
	}
	else if (this.viewState == "linked") {
		imageLinked = "linked_process_icon.gif";
		$('.bpmn_activity', this.html)
		.removeClass("subprocess_collapsed_activity").removeClass("subprocess_expanded_activity")
		.addClass("simple_activity").addClass("linked_process");
	}
	else {
		$('.bpmn_activity', this.html).addClass("simple_activity");
		image = "";
	}

	$(".bpmn_collapse_icon", this.html)
	.css("background-image", draw2d.bpmn.getCssImage(image));

	$(".bpmn_linked_process_icon", this.html)
	.css("background-image", draw2d.bpmn.getCssImage(imageLinked));
};

draw2d.bpmn.Activity.prototype.setViewMode = function (self, mode) {
	var l = self.getChildren().getSize();
	var i;

	if (mode == "collapsed") {
		for (i = 0; i < l; i++) {
			self.getChildren().get(i).setVisibility(false);
		}
		self.originalWidth = self.width;
		self.originalHeight = self.height;
		self.setViewState("collapsed");
		self.setDimension(self.defaultWidth, self.defaultHeight);
	//self.setResizeable(false);
	}
	else if (mode == "expanded") {
		self.setViewState("expanded");
		self.setDimension(self.originalWidth, self.originalHeight);
		//self.setResizeable(true);
		for (i = 0; i < l; i++) {
			self.getChildren().get(i).setVisibility(true);
		}
	}
};

// Toggle the expand/collapse state of the figure.
draw2d.bpmn.Activity.prototype.toggle = function (self) {
	if (self.viewState == "expanded") {
		self.setViewMode(self, "collapsed");
	}
	else if (self.viewState == "collapsed") {
		self.setViewMode(self, "expanded");
	}
};

draw2d.bpmn.Activity.prototype.showInfo = function (typeInfo, params) {
	var $ = TeamTime.jQuery;
	var content = "";
	var s = "";

	if (typeInfo == "status") {
		$('.bpmn_activity_backlight', this.html).hide();

		var state = "defaut";
		if (params.state != "") {
			state = params.state;
			content = params.part;
		}

		$('.bpmn_activity_aditional_info', this.html)
		.removeClass("bpmn_activity_error")
		.removeClass("bpmn_activity_done")
		.removeClass("bpmn_activity_done-part")
		.addClass("bpmn_activity_" + state).html(content).show();
	}
	else {
		$('.bpmn_activity_aditional_info', this.html).hide();

		switch (typeInfo) {
			case "date":
				s = params.date;
				content =
				'<div class="bpmn_activity_logo bpmn_activity_date-logo"></div>\
				<div class="bpmn_activity_title">' + s + '</div>';
				break;

			case "time":
			case "plan":
				if (typeInfo == "time") {
					s = params.fact + " / " + params.plan;
				}
				else {
					typeInfo = "time";
					s = params.plan + " чел-часов";
				}
				content =
				'<div class="bpmn_activity_logo bpmn_activity_time-logo"></div>\
				<div class="bpmn_activity_title">' + s + '</div>';
				break;

			case "price":
				s = params.price + "  руб.";
				content =
				'<div class="bpmn_activity_logo bpmn_activity_price-logo"></div>\
				<div class="bpmn_activity_title bpmn_activity_title-price">' + s + '</div>';
				break;

			case "performer":
				s = params.userName;
				content =
				'<div class="bpmn_activity_logo bpmn_activity_performer-logo"></div>\
				<div class="bpmn_activity_title">' + s + '</div>';
				break;

			default:
				typeInfo = "default";
				content = "";
				break;
		}

		$('.bpmn_activity_backlight', this.html)
		.removeClass("bpmn_activity_date")
		.removeClass("bpmn_activity_time")
		.removeClass("bpmn_activity_price")
		.removeClass("bpmn_activity_performer")
		.addClass("bpmn_activity_" + typeInfo).html(content).show();
	}
};

draw2d.bpmn.Activity.prototype.setWorkflow = function (/*:draw2d.Workflow*/ workflow) {
	draw2d.CompartmentFigure.prototype.setWorkflow.call(this, workflow);

	draw2d.bpmn.addConextMenu(this);
	this.setViewState("simple");

	// input ports

	if (workflow !== null && this.inputPort[0] === null) {
		this.inputPort[0] = new draw2d.bpmn.InputPort();
		this.inputPort[0].setWorkflow(workflow);
		this.inputPort[0].setName("input");
		this.addPort(this.inputPort[0], 0, this.height / 2);
	}

	if (workflow !== null && this.inputPort[1] === null) {
		this.inputPort[1] = new draw2d.bpmn.InputPort();
		this.inputPort[1].setWorkflow(workflow);
		this.inputPort[1].setName("input1");
		this.addPort(this.inputPort[1], 20, 0);
	}

	if (workflow !== null && this.inputPort[2] === null) {
		this.inputPort[2] = new draw2d.bpmn.InputPort();
		this.inputPort[2].setWorkflow(workflow);
		this.inputPort[2].setName("input2");
		this.addPort(this.inputPort[2], 20, this.height);
	}

	// output ports

	if (workflow !== null && this.outputPort[0] === null) {
		this.outputPort[0] = new draw2d.bpmn.OutputPort();
		// It is possible to add "5" Connector to this port
		this.outputPort[0].setMaxFanOut(5);
		this.outputPort[0].setWorkflow(workflow);
		this.outputPort[0].setName("output");
		this.addPort(this.outputPort[0], this.width, this.height / 2);
	}

	if (workflow !== null && this.outputPort[1] === null) {
		this.outputPort[1] = new draw2d.bpmn.OutputPort();
		// It is possible to add "5" Connector to this port
		this.outputPort[1].setMaxFanOut(5);
		this.outputPort[1].setWorkflow(workflow);
		this.outputPort[1].setName("output1");
		this.addPort(this.outputPort[1], this.width - 20, 0);
	}

	if (workflow !== null && this.outputPort[2] === null) {
		this.outputPort[2] = new draw2d.bpmn.OutputPort();
		// It is possible to add "5" Connector to this port
		this.outputPort[2].setMaxFanOut(5);
		this.outputPort[2].setWorkflow(workflow);
		this.outputPort[2].setName("output2");
		this.addPort(this.outputPort[2], this.width - 20, this.height);
	}
};

draw2d.bpmn.Activity.prototype.setDimension = function (/*:int*/ w, /*:int*/ h ) {
	var $ = TeamTime.jQuery;

	draw2d.CompartmentFigure.prototype.setDimension.call(this, w, h);

	// change size for block
	var activity = $('.bpmn_activity', this.html);
	$(activity).css("width", w).css("height", h);
	$('.bpmn_content_hidden', this.html).css("width", w - 6).css("height", h - 6);
	//$('.bpmn_activity_container', this.html).css("line-height", h - 6);

	var content = this.getContentElement();
	// for - simple activity - change top of content to middle
	/*
	if ($(activity).hasClass("simple_activity") ||
		$(activity).hasClass("subprocess_collapsed_activity")) {
		$(content).css("margin-top", h / 2 - (($(content).height() / 2) || 12));
	}
	// for subprocess - change top of content to top
	else {
	*/
	$(content).css("margin-top", 0);
	/*}*/

	// adjust the Output ports to the new dimension
	if (this.outputPort[0] !== null) {
		this.outputPort[0].setPosition(this.width, this.height / 2);
	}

	if (this.outputPort[1] !== null) {
		this.outputPort[1].setPosition(this.width - 20, 0);
	}

	if (this.outputPort[2] !== null) {
		this.outputPort[2].setPosition(this.width - 20, this.height);
	}

	// adjust the Input ports to the new dimension
	if (this.inputPort[0] !== null) {
		this.inputPort[0].setPosition(0, this.height / 2);
	}

	if (this.inputPort[1] !== null) {
		this.inputPort[1].setPosition(20, 0);
	}

	if (this.inputPort[2] !== null) {
		this.inputPort[2].setPosition(20, this.height);
	}
};

draw2d.bpmn.Activity.prototype.onDragstart = function (x, y) {
	draw2d.bpmn.onDragstart(this);

	return draw2d.CompartmentFigure.prototype.onDragstart.call(this, x, y);
};

draw2d.bpmn.Activity.prototype.onDragend = function () {
	draw2d.CompartmentFigure.prototype.onDragend.call(this);
	draw2d.bpmn.onDragend(this);
};

draw2d.bpmn.Activity.prototype.onDrag = function () {
	draw2d.CompartmentFigure.prototype.onDrag.call(this);
	draw2d.bpmn.onDrag(this);
};

draw2d.bpmn.Activity.prototype.setVisibility = function (visible) {
	draw2d.bpmn.setVisibility(this, visible);
};

draw2d.bpmn.Activity.prototype.isSubProcess = function () {
	return this.viewState != "simple";
};

draw2d.bpmn.Activity.prototype.makeSubProcess = function (figure) {
	this.setViewState("expanded");
	this.setDimension(300, 140);

	// ant commented this
/*	var sx, sy;

	// make start, end elements
	var startObj = new draw2d.bpmn.Start();
	sy = this.getY() + this.height / 2 - startObj.getHeight() / 2;
	this.workflow.addFigure(startObj, this.getX() + 10, sy);
	draw2d.CompartmentFigure.prototype.addChild.call(this, startObj);

	var endObj = new draw2d.bpmn.End();
	this.workflow.addFigure(endObj,	this.getX() + this.getWidth() - endObj.getWidth() - 10, sy);
	draw2d.CompartmentFigure.prototype.addChild.call(this, endObj);

	if (figure == null) {
		endObj.setPosition(this.getX() + this.getWidth() - endObj.getWidth() - 10, endObj.getY());

		figure = new draw2d.bpmn.Activity();
		this.workflow.addFigure(figure, this.getX() + 50, this.getY() + 30);
		draw2d.CompartmentFigure.prototype.addChild.call(this, figure);
	}

	sx = this.getX() + this.width / 2 - figure.getWidth() / 2;
	sy = this.getY() + this.height / 2 - figure.getHeight() / 2;
	figure.setPosition(sx, sy);

	// make connections
	var c = new draw2d.bpmn.Connection();
	c.setSource(startObj.getPort("output"));
	c.setTarget(figure.getPort("input"));
	this.workflow.addFigure(c);

	c = new draw2d.bpmn.Connection();
	c.setSource(figure.getPort("output"));
	c.setTarget(endObj.getPort("input"));
	this.workflow.addFigure(c);
*/
};

draw2d.bpmn.Activity.prototype.isConnectedWith = function (figure) {
	var result = false;

	// collect all parent connections
	var conns = [];
	_.each(this.getPorts().asArray(), function (p) {
		_.each(p.getConnections().asArray(), function (c) {
			conns.push(c);
		});
	});

	// collect all figure connections
	var figureConns = [];
	_.each(figure.getPorts().asArray(), function (p) {
		_.each(p.getConnections().asArray(), function (c) {
			figureConns.push(c);
		});
	});

	// check connections
	var i;
	var j;
	var l = conns.length;
	var lf = figureConns.length;

	var found = false;
	for (i = 0; i < l; i++) {
		for (j = 0; j < lf; j++) {
			if (conns[i] == figureConns[j]) {
				found = true;
				break;
			}
		}

		if (found) {
			break;
		}
	}
	result = found;

	return result;
}

draw2d.bpmn.Activity.prototype.onFigureDrop = function (figure) {
	if (this.isSubProcess()) {
		return;
	}

	var t = figure.type;
	if (!t || t.toString().indexOf("bpmn.") < 0) {
		return;
	}

	if (t == "bpmn.InputPort" || t == "bpmn.OutputPort") {
		return;
	}

	if (this.isConnectedWith(figure)) {
		return;
	}

	// ant
	// this.makeSubProcess(figure);
};

draw2d.bpmn.Activity.prototype.getContextMenuItems = function () {
	var result = {};

	if (this.viewState == "linked") {
		result["linkto"] = {
			name: i18n.bpmn.item_linkto,
			icon: "edit"
		};
	}
	else {
		result["details"] = {
			name: i18n.bpmn.item_details,
			icon: "edit"
		};
	}

	result["delete"] = {
		name: i18n.bpmn.delete_item,
		icon: "delete"
	};

	return result;
};

draw2d.bpmn.Activity.prototype.onContextMenuCmd = function (data) {
	var $ = TeamTime.jQuery;

	var swPanel = draw2d.bpmn.getSwimlanePanel();
	var url = "";
	var isTemplate = purl().param("is_template");

	if (data.key == "delete") {
		var cmd = new draw2d.CommandDelete(data.figure);
		cmd.execute();
	}
	else if (data.key == "details") {
		var role = swPanel.getTitle($(data.figure.getParentRow()), "row");

		url = TeamTime.getUrlForController() +
		"&tmpl=component&view=processdetails" +
		"&id=" + data.figure.id +
		"&process_id=" + purl().param("id") +
		"&role=" + role;

		if (typeof(isTemplate) != "undefined") {
			url += "&is_template=" + isTemplate;
		}

		if (data.figure.paramsData && data.figure.paramsData._id) {
			url += "&_id=" + data.figure.paramsData._id;
		}

		var parentFigure = data.figure.getParent();
		if (parentFigure) {
			if (parentFigure.paramsData && parentFigure.paramsData._id) {
				url += "&_parent_id=" + parentFigure.paramsData._id;
			}
		}
		console.log(url);

		parent.TeamTime.jQuery.fancybox({
			href: url,
			type: 'iframe',
			width: 800,
			//height: 750,
			autoSize: false,
			padding : 1,
			openEffect: 'none',
			closeEffect: 'none',
			helpers : {
				overlay : {
					css : {
						'background' : 'rgba(0, 0, 0, 0.4)'
					}
				}
			}
		});
	}
	else if (data.key == "linkto") {
		url = TeamTime.getUrlForController() +
		"&tmpl=component&view=processlinkto" +
		"&id=" + data.figure.id +
		"&process_id=" + purl().param("id");

		if (typeof(isTemplate) != "undefined") {
			/*url += "&is_template=" + isTemplate;*/
			return;
		}

		parent.TeamTime.jQuery.fancybox({
			href: url,
			type: 'iframe',
			width: 440,
			height: 472,
			autoSize: false,
			scrolling: "no",
			padding : 1,
			openEffect: 'none',
			closeEffect: 'none',
			helpers : {
				overlay : {
					css : {
						'background' : 'rgba(0, 0, 0, 0.4)'
					}
				}
			}
		});
	}
};

// editable content

draw2d.bpmn.Activity.prototype.getContentElement = function () {
	var $ = TeamTime.jQuery;

	return $(".bpmn_activity_content", this.html);
};

draw2d.bpmn.Activity.prototype.getText = function () {
	var $ = TeamTime.jQuery;
	var contentElement = this.getContentElement();

	if (!contentElement) {
		return "";
	}

	return $(contentElement).text();
};

draw2d.bpmn.Activity.prototype.setText = function (text) {
	var $ = TeamTime.jQuery;
	var contentElement = this.getContentElement();

	if (!contentElement) {
		return;
	}

	$(contentElement).text(text);
};

draw2d.bpmn.Activity.prototype.isContentEditable = function () {
	var $ = TeamTime.jQuery;

	return $(this.getContentElement()).attr("contentEditable");
};

draw2d.bpmn.Activity.prototype.setContentEditable = function (flag) {
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

draw2d.bpmn.Activity.prototype.initEditableContent = function (contentElement) {
	var $ = TeamTime.jQuery;
	var self = this;

	$(contentElement).blur(function () {
		self.setContentEditable(false);
		self.setDimension(self.width, self.height);
	});
};

draw2d.bpmn.Activity.prototype.onDoubleClick = function () {
	this.setContentEditable(true);
	draw2d.bpmn.selectText(this);
};

draw2d.bpmn.Activity.prototype.onKeyDown = function (keyCode, ctrl) {
	// disable del command in edit mode

	if (!this.isContentEditable()) {
		draw2d.CompartmentFigure.prototype.onKeyDown.call(this, keyCode, ctrl);
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
draw2d.bpmn.Activity.prototype.getParentRow = function () {
	if (this.getParent()) {
		return this.getParent().getParentRow();
	}
	else {
		return this.parentRow;
	}
};

draw2d.bpmn.Activity.prototype.getParentCol = function () {
	if (this.getParent()) {
		return this.getParent().getParentCol();
	}
	else {
		return this.parentCol;
	}
};
