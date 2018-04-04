/*
 * gallery preset: gallery-vertical-strip
*/
hs.outlineType = 'drop-shadow';
hs.align = 'center';
hs.transitions = ['expand', 'crossfade'];
hs.fadeInOut = true;
hs.dimmingOpacity = .8;
hs.wrapperClassName = 'borderless floating-caption';
hs.captionEval = 'this.a.title';
hs.marginLeft = 100;
hs.marginBottom = 80;
hs.numberPosition = 'caption';
hs.lang.number = '%1/%2';

hs.registerOverlay({
	html: hs.replaceLang('<div class="closebutton" onclick="return hs.close(this)" title="{hs.lang.closeText}"></div>'),
	position: 'top right',
	fade: 2
});

hs.addSlideshow( {
	repeat: false
	,useControls: true
	,fixedControls: 'fit'
	,thumbstrip: {
		position: 'middle left'
	    ,mode: 'vertical'
		,relativeTo: 'viewport'
	}
	,overlayOptions: {
		 position: 'bottom center'
		,className: 'text-controls'
		,relativeTo: 'viewport'
		,offsetX: 50
		,offsetY: -5
	}