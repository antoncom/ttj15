/*
 * gallery preset: no-border-floating-caption
*/
hs.outlineType = 'drop-shadow';
hs.dimmingOpacity = .75;
hs.align = 'center';
hs.transitions = ['expand', 'crossfade'];
hs.wrapperClassName = 'dark borderless floating-caption';
hs.fadeInOut = true;

hs.addSlideshow( {
	repeat: false
	,useControls: true
	,fixedControls: 'fit'
	,overlayOptions: {
		position: 'bottom center'
    	,hideOnMouseOut: true
    	,opacity: .6
	}