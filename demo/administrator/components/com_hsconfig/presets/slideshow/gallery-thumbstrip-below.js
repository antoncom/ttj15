/*
 * gallery preset: gallery-thumbstrip-above
*/
hs.outlineType = 'glossy-dark';
hs.align = 'center';
hs.transitions = ['expand', 'crossfade'];
hs.fadeInOut = true;
hs.wrapperClassName = 'dark';
hs.captionEval = 'this.a.title';
hs.numberPosition = 'caption';
hs.captionOverlay.position = 'above';
hs.useBox = true;
hs.width = 600;
hs.height = 400;

hs.addSlideshow( {
	repeat: false
	,useControls: true
	,fixedControls: 'fit'
	,thumbstrip: {
		position: 'below'
	    ,mode: 'horizontal'
		,relativeTo: 'expander'
	}
	,overlayOptions: {
		 position: 'bottom center'
		,hideOnMouseOut: true
		,opacity: .75
	}