/*
 * gallery preset: controls-in-heading
*/
hs.outlineType = 'rounded-white';
hs.align = 'center';
hs.transitions = ['expand', 'crossfade'];
hs.fadeInOut = true;
hs.wrapperClassName = 'controls-in-heading';
hs.numberPosition= 'heading';

hs.addSlideshow( {
	repeat: false
	,useControls: true
	,fixedControls: false
	,overlayOptions: {
		position: 'top right'
    	,hideOnMouseOut: false
    	,opacity: 1
	}