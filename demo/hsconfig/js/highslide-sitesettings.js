/*
*  Highslide site settings
*  DO NOT EDIT. Generated on 2011-02-14 07:03:16 (GMT) by the Highslide Configuration Component 2.0.1
*/

hs.showCredits = false;
hs.graphicsDir = 'plugins/content/highslide/graphics/';
hs.outlineStartOffset = 3;
hs.outlineWhileAnimating = 2;
hs.loadingOpacity = 0.75;
hs.dimmingOpacity = 0.3;
hs.padToMinWidth = false;
hs.padToMinWidth = false;
hs.expandCursor = 'zoomin.cur';
hs.restoreCursor = 'zoomout.cur';
hs.creditsHref = 'http://highslide.com';
hs.easing = 'easeInQuad';
hs.easingClose = hs.easing;
hs.fullExpandOpacity = 1.0;
hs.marginTop = 10;
hs.marginBottom = 15;
hs.marginLeft = 10;
hs.marginRight = 15;
hs.minHeight = 200;
hs.minWidth = 200;
hs.numberOfImagesToPreload = 5;
hs.transitions = ['expand'];
hs.fullExpandPosition = 'bottom right';
hs.objectLoadTime = 'before';
hs.align = '';
hs.anchor = '';
hs.allowSizeReduction = true;
hs.fadeInOut = false;
hs.allowMultipleInstances = true;
hs.allowWidthReduction = false;
hs.allowHeightReduction = true;
hs.blockRightClick = true;
hs.enableKeyListener = true;
hs.dynamicallyUpdateAnchors = true;
hs.useBox = false;
hs.cacheAjax = true;
hs.preserveContent = true;
hs.dragByHeading = false;
hs.dragSensitivity = 5;
hs.dimmingDuration = 50;
hs.expandDuration = 250;
hs.transitionDuration = 500;
hs.expandSteps = 10;
hs.zIndexCounter = 1001;
hs.Expander.prototype.onBeforeGetCaption = function(sender)
{
	if (typeof sender.captionId != 'undefined' && sender.captionId != null)
	{
		if (document.getElementById( sender.captionId ) == null && sender.a.onclick != null)
		{
			var onclick = sender.a.onclick.toString();
			var onclickprop = onclick.match(/(hsjcaption:)+\s*('|")([^'"]*)/);
			if (onclickprop != null)
			{
				var text = unescape( onclickprop[3] );
				var div = document.createElement('div');
				div['innerHTML'] = hs.replaceLang( text );
				div['id'] = sender.captionId;
				div['className'] = 'highslide-caption';
				var onclickstyle = onclick.match(/(hsjcaptionstyle:)+\s*('|")([^'"]*)/);
				if (onclickstyle != null)
				{
					var styles = onclickstyle[3].match(/([^:; ])*:\s*([^,;}])*/g);
					if (styles != null)
					{
						for (var i = 0; i < styles.length; i++)
						{
							var arr;
							arr = styles[i].split(":");
							div.style[arr[0]] = arr[1].replace( " ", "");
						}
					}
				}
				sender.a.appendChild( div );
			}
		}
	}
}
hs.Expander.prototype.onBeforeGetHeading = function(sender)
{
	if (typeof sender.headingId != 'undefined' && sender.headingId != null)
	{
		if (document.getElementById( sender.headingId ) == null && sender.a.onclick != null)
		{
			var onclick = sender.a.onclick.toString();
			var onclickprop = onclick.match(/(hsjheading:)+\s*('|")([^'"]*)/);
			if (onclickprop != null)
			{
				var text = unescape( onclickprop[3] );
				var div = document.createElement('div');
				div['innerHTML'] = hs.replaceLang( text );
				div['id'] = sender.headingId;
				div['className'] = 'highslide-heading';
				var onclickstyle = onclick.match(/(hsjheadingstyle:)+\s*('|")([^'"]*)/);
				if (onclickstyle != null)
				{
					var styles = onclickstyle[3].match(/([^:; ])*:\s*([^,;}])*/g);
					if (styles != null)
					{
						for (var i = 0; i < styles.length; i++)
						{
							var arr;
							arr = styles[i].split(":");
							div.style[arr[0]] = arr[1].replace( " ", "");
						}
					}
				}
				sender.a.appendChild( div );
			}
		}
	}
	return true;
}
hs.Expander.prototype.onBeforeGetContent = function(sender)
{
	if (typeof sender.contentId != 'undefined' && sender.contentId != null)
	{
		if (document.getElementById( sender.contentId ) == null && sender.a.onclick != null)
		{
			var onclick = sender.a.onclick.toString();
			var onclickprop = onclick.match(/(hsjcontent:)+\s*('|")([^'"]*)/);
			if (onclickprop != null)
			{
				var text = unescape( onclickprop[3] );
				var div = document.createElement('div');
				div['innerHTML'] = hs.replaceLang( text );
				div['id'] = sender.contentId;
				div['className'] = 'highslide-html-content';
				var onclickstyle = onclick.match(/(hsjcontentstyle:)+\s*('|")([^'"]*)/);
				if (onclickstyle != null)
				{
					var styles = onclickstyle[3].match(/([^:; ])*:\s*([^,;}])*/g);
					if (styles != null)
					{
						for (var i = 0; i < styles.length; i++)
						{
							var arr;
							arr = styles[i].split(":");
							div.style[arr[0]] = arr[1].replace( " ", "");
						}
					}
				}
				sender.a.appendChild( div );
			}
		}
	}
	return true;
}
hs.Expander.prototype.onBeforeExpand = function (sender) {
	if (this.custom != null
       &&this.custom['overlayId'] != null)
    {
		sender.createOverlay( this.custom );
	}
	return true;
}
hs.Expander.prototype.onInit = function (sender)
{
	if (sender.a.onclick != null)
	{
		var onclick = sender.a.onclick.toString();
		var onclickprop = onclick.match(/(overlayId:)+\s*('|")([^'"]*)/);
		if (onclickprop != null)
		{
			var overlayId = onclickprop[3];
			if (document.getElementById( overlayId ) == null)
			{
				var onclickprop = onclick.match(/(hsjcustomOverlay:)+\s*('|")([^'"]*)/);
				if (onclickprop != null)
				{
					var text = unescape( onclickprop[3] );
					var div = document.createElement('div');
					div['innerHTML'] = hs.replaceLang( text );
					div['id'] = overlayId;
					div['className'] = 'highslide-overlay';
					var onclickstyle = onclick.match(/(hsjcustomOverlayStyle:)+\s*('|")([^'"]*)/);
					if (onclickstyle != null)
					{
						var styles = onclickstyle[3].match(/([^:; ])*:\s*([^,;}])*/g);
						if (styles != null)
						{
							for (var i = 0; i < styles.length; i++)
							{
								var arr;
								arr = styles[i].split(":");
								div.style[arr[0]] = arr[1].replace( " ", "");
							}
						}
					}
					sender.a.appendChild( div );
					var overlayExists = false;
					for (var i = 0; i < hs.overlays.length; i++)
					{
						if (hs.overlays[i].overlayId == overlayId)
						{
							overlayExists = true;
						}
					}
					if (! overlayExists)
					{
						onclickprop = onclick.match(/(customOverlay:)+\s*{\s*([^}]*)}/);
						if (onclickprop != null)
						{
							try
							{
								eval( "var opts = {" + onclickprop[2] + "}" );
								opts.overlayId = overlayId;
								if (typeof sender.thumb.id != "undefined" && sender.thumb.id != "")
								{
									opts.thumbnailId = sender.thumb.id;
									hs.registerOverlay( opts );
								}
								else
								if (typeof sender.a.id != "undefined" && sender.a.id != "")
								{
									opts.thumbnailId = sender.a.id;
									hs.registerOverlay( opts );
								}
							}
							catch(e)
							{
								//	ignore
							}
						}
					}
				}
			}
		}
	}
	return true;
}
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
	,interval: 5000
	,repeat: false
});
hs.autoplay = false