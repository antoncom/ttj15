/*
 * gallery preset: gallery-in-box
*/
hs.outlineType = 'rounded-white';
hs.align = 'center';
hs.transitions = ['expand', 'crossfade'];
hs.fadeInOut = true;
hs.dimmingOpacity = .75;
hs.useBox = true;
hs.width = 640;
hs.height = 480;

hs.addSlideshow( {
	repeat: false
	,useControls: true
	,fixedControls: 'fit'
	,overlayOptions: {
		 position: 'bottom center'
    	,hideOnMouseOut: true
    	,opacity: 1
	}