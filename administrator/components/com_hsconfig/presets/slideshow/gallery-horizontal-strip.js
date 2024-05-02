/*
 * gallery preset: gallery-horizontal-strip
*/
hs.outlineType = 'rounded-white';
hs.align = 'center';
hs.transitions = ['expand', 'crossfade'];
hs.fadeInOut = true;
hs.dimmingOpacity = .8;
hs.captionEval = 'this.a.title';
hs.marginBottom = 105;
hs.numberPosition = 'caption';

hs.addSlideshow( {
	repeat: false
	,useControls: true
	,thumbstrip: {
		position: 'bottom center'
	    ,mode: 'horizontal'
		,relativeTo: 'viewport'
	}
	,overlayOptions: {
		 position: 'bottom center'
		,className: 'text-controls'
		,relativeTo: 'viewport'
		,offsetY: -60
	}