/*
 * gallery preset: gallery-floating-thumbs
*/
hs.outlineType = 'rounded-white';
hs.align = 'center';
hs.transitions = ['expand', 'crossfade'];
hs.fadeInOut = true;
hs.headingEval = 'this.a.title';
hs.numberPosition = 'heading';
hs.useBox = true;
hs.width = 600;
hs.height = 400;

hs.addSlideshow( {
	repeat: false
	,useControls: true
	,fixedControls: 'fit'
	,thumbstrip: {
		position: 'rightpanel'
	    ,mode: 'float'
		,relativeTo: 'expander'
		,offsetX: -65
	}
	,overlayOptions: {
		 position: 'top right'
	    ,offsetX: 200
		,offsetY: -65
	}