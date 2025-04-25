
draw2d.bpmn.ArrowConnectionDecorator = function () {
	draw2d.ArrowConnectionDecorator.call(this, 10, 8);

	this.setColor(new draw2d.Color("#0076aa"));
	this.setBackgroundColor(new draw2d.Color("#0076aa"));
};

draw2d.bpmn.ArrowConnectionDecorator.prototype = new draw2d.ArrowConnectionDecorator();

/*
draw2d.ArrowConnectionDecorator.prototype.paint = function (g) {
  // draw the background
  //
  if(this.backgroundColor!==null)
  {
     g.setColor(this.backgroundColor);
     //g.fillPolygon([3,20,20,3],[0,5,-5,0]);
     g.fillPolygon([3, this.lenght, this.lenght, 3], [0, (this.width/2), -(this.width/2), 0]);
  }

  // draw the border
  g.setColor(this.color);
  g.setStroke(1);
  // g.drawPolygon([3,20,20,3],[0,5,-5,0]);
  g.drawPolygon([3, this.lenght, this.lenght, 3], [0, (this.width/2), -(this.width/2), 0]);
};

draw2d.ArrowConnectionDecorator.prototype.setDimension=function (l, width) {
    this.width=w;
    this.lenght=l;
};
*/
