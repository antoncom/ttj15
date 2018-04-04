/*
 * gallery preset: white-design
*/
hs.outlineType = 'rounded-white';
hs.align = 'center';
hs.transitions = ['expand', 'crossfade'];
hs.fadeInOut = true;

hs.addSlideshow( {
	repeat: false
	,useControls: true
	,fixedControls: 'fit'
	,overlayOptions: {
		fade: 2
		,position: 'bottom center'
    	,hideOnMouseOut: true
    	,opacity: .75
	}