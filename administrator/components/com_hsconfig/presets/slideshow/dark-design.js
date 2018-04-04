/*
 * gallery preset: dark-design
*/
hs.outlineType = 'glossy-dark';
hs.align = 'center';
hs.transitions = ['expand', 'crossfade'];
hs.fadeInOut = true;
hs.wrapperClassName = 'dark';

hs.addSlideshow( {
	repeat: false
	,useControls: true
	,fixedControls: 'fit'
	,overlayOptions: {
		fade: 2
		,position: 'bottom center'
    	,hideOnMouseOut: true
    	,opacity: .6
	}