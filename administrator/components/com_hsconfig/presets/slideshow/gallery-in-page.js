/*
 * gallery preset: gallery-in-page
*/
hs.transitions = ['fade', 'crossfade'];
hs.restoreCursor = null;
hs.lang.restoreTitle = 'Click for next image';
hs.outlineType = null;
hs.allowSizeReduction = false;
hs.wrapperClassName = 'in-page controls-in-heading';
hs.useBox = true;
hs.width = 600;
hs.height = 400;
hs.targetX = 'gallery-area 10px';
hs.targetY = 'gallery-area';
hs.captionEval = 'this.a.title';
hs.numberPosition = 'caption';

/* Open the first thumb on page load */
hs.addEventListener(window, 'load', function() {
	document.getElementById('in-page-thumb1').onclick();
});

/* Cancel the default action fo image click and do next instead */
hs.Expander.prototype.onImageClick = function() {
	if (/in-page/.test(this.wrapper.className))	return hs.next();
}

/* Under no circumstances should the static popup be closed */
hs.Expander.prototype.onBeforeClose = function() {
	if (/in-page/.test(this.wrapper.className))	return false;
}

/*	.. nor dragged */
hs.Expander.prototype.onDrag = function() {
	if (/in-page/.test(this.wrapper.className))	return false;
}

/* Keep the position after window resize */
hs.addEventListener(window, 'resize', function() {
	var i, exp;
	hs.page = hs.getPageSize();

	for (i = 0; i < hs.expanders.length; i++) {
		exp = hs.expanders[i];
		if (exp) {
			var x = exp.x,
				y = exp.y;
			// get new thumb positions
			exp.tpos = hs.getPosition(exp.el);
			x.calcThumb();
			y.calcThumb();
			// calculate new popup position
	 		x.pos = x.tpos - x.cb + x.tb;
			x.scroll = hs.page.scrollLeft;
			x.clientSize = hs.page.width;
			y.pos = y.tpos - y.cb + y.tb;
			y.scroll = hs.page.scrollTop;
			y.clientSize = hs.page.height;
			exp.justify(x, true);
			exp.justify(y, true);
			// set new left and top to wrapper and outline
			exp.moveTo(x.pos, y.pos);
		}
	}
});

hs.addSlideshow( {
	useControls: true
	,thumbstrip: {
		position: 'above'
	    ,mode: 'horizontal'
		,relativeTo: 'expander'
	}
	,overlayOptions: {
		 position: 'bottom right'
		,offsetY: 50
	}