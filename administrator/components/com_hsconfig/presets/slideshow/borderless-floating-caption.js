/*
 * gallery preset: borderless-floating-caption
*/
hs.outlineType = null;
hs.numberPosition = 'caption';
hs.dimmingOpacity = .75;
hs.align = 'center';
hs.transitions = ['expand', 'crossfade'];
hs.wrapperClassName = 'borderless floating-caption';

hs.addSlideshow( {
	repeat: false
	,useControls: true
	,fixedControls: 'fit'
	,overlayOptions: {
		fade: 2
		,position: 'bottom center'
    	,offsetY: -60
    	,relativeTo: 'viewport'
    	,hideOnMouseOut: false
    	,opacity: .75
    	,className: 'text-controls'
	}
	,thumbstrip: {
		fade: 2
		,position: 'bottom center'
    	,mode: 'horizontal'
    	,relativeTo: 'viewport'
    	,hideOnMouseOut: false
	}