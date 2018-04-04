
draw2d.bpmn.ButtonShowBlocksMenu = function (/*:draw2d.PaletteWindow*/ palette) {
	draw2d.Button.call(this, palette, 16, 16);
};

draw2d.bpmn.ButtonShowBlocksMenu.prototype = new draw2d.Button();

draw2d.bpmn.ButtonShowBlocksMenu.prototype.execute = function () {	
	var blocksMenu = draw2d.bpmn.getBlocksMenu();
	blocksMenu.show(this);

	draw2d.ToolGeneric.prototype.execute.call(this);
};

draw2d.bpmn.ButtonShowBlocksMenu.prototype.getImageUrl = function () {
	return draw2d.bpmn.getCssImage("showmenublocks.png", true);
};