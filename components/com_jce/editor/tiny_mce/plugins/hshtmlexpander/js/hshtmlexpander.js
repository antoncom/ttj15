var HsHtmlExpanderDialog = {

	preInit : function() {
		tinyMCEPopup.requireLangPack();
	},
	init : function() {
		var ed = tinyMCEPopup.editor, n = ed.selection.getNode(), action = 'insert', rel = "";
		tinyMCEPopup.resizeToInnerSize();

		dom.html('hrefbrowsercontainer', TinyMCE_Utils.getBrowserHTML('hrefbrowser','href','file','hshtmlexpander'));
		dom.html('psrcbrowsercontainer', TinyMCE_Utils.getBrowserHTML('psrcbrowser','psrc','file','hshtmlexpander'));
		dom.html('swfexpressinstallurlbrowsercontainer', TinyMCE_Utils.getBrowserHTML('swfexpressinstallurlbrowser','swfexpressinstallurl','file','hshtmlexpander'));

		el = ed.dom.getParent(n, "A");
		if (el != null && el.nodeName == "A"){
			action = "update";
		}

		// Init plugin
		this.HsHtmlExpander = initHsHtmlExpander();

		var target = ''; /*HsHtmlExpander.getParam('target');*/
		if(target == 'default') target = '';

		dom.value('insert', tinyMCEPopup.getLang(action, 'Insert', true));

		if (action == "update") {
			var href = ed.documentBaseURI.toRelative(ed.dom.getAttrib(el, 'href'));
			// Setup form data
			dom.value('href', href);
			dom.value('title', ed.dom.getAttrib(el, 'title'));
			dom.value('id', ed.dom.getAttrib(el, 'id'));
			dom.value('expanderid',ed.dom.getAttrib(el,'id' ));
			dom.value('style', ed.dom.getAttrib(el, "style"));
			dom.value('onclick', ed.dom.getAttrib(el, 'onclick'));
			dom.value('onmouseover', ed.dom.getAttrib(el, 'onmouseover'));
			rel = ed.dom.getAttrib(el,'rel');
			if ( rel == 'highslide-ajax'
			   ||rel == 'highslide-swf'
			   ||rel == 'highslide-iframe'
			   )
			{
				dom.check( 'unobtrusive', true );
				dom.value('objecttype', rel.substr( 10 ) );
			}
		}

		if (dom.ischecked( 'unobtrusive'))
		{
			this.setTabs();
		}
		else
		{
			var isExpander = this.setHsValues();
			if (action == 'update')
			{
				if (! isExpander
				   || rel == 'highslide'
				   )
				{
					new Alert(tinyMCEPopup.getLang('hshtmlexpander_dlg.is_expander', 'This expander was created by the HsExpander plugin. It must be used for updates.'));
					dom.hide('insert' );
					return;
				}
			}
		}

		if (dom.value('contentid') != '') {
			var divobj = ed.dom.get(dom.value('contentid'));
			if (divobj != null && divobj.nodeName == "DIV" && ed.dom.getAttrib( divobj, 'class') == 'highslide-html-content') {
				dom.value('content', divobj.innerHTML );
				dom.value('contentstyle', ed.dom.getAttrib( divobj, 'style' ));
				dom.value('origcontentid', dom.value('contentid'));
			}
		}

		TinyMCE_EditableSelects.init();
		window.focus();
	},

	mirrorValue : function(ele, destid)
	{
		var el = document.getElementById( destid );
		if (el != null)
		{
			el.value = ele.value;
		}
		return true;
	},

	setTabs : function(){
		if (dom.ischecked('unobtrusive'))
		{
			if (dom.value('objecttype') == "")
			{
				new Alert(tinyMCEPopup.getLang('hshtmlexpander_dlg.need_objecttype', 'Object type must be set to ajax, iframe or flash for unobtrusive markup.'));
				dom.check('unobtrusive', false );
				return;
			}
			dom.hide( 'options_tab' );
			dom.hide( 'html_tab' );
			dom.hide( 'flash_tab' );
			dom.hide( 'caption_tab' );
			dom.hide( 'heading_tab' );
			dom.hide( 'overlay_tab' );
		}
		else
		{
			dom.show( 'options_tab' );
			dom.show( 'html_tab' );
			dom.show( 'flash_tab' );
			dom.show( 'caption_tab' );
			dom.show( 'heading_tab' );
			dom.show( 'overlay_tab' );
		}
	},

	setHsValues : function(){
		var onclick = dom.value('onclick');
		var onmouseover = dom.value('onmouseover');

		if (onclick != null && onclick.indexOf('return hs.htmlExpand') != -1)
		{
			var ndx = onclick.indexOf('{');
			var lndx = onclick.lastIndexOf('}');

			if (ndx != -1 && lndx != -1)
			{
				var argsstr = onclick.substring( ndx, lndx+1 );
				try
				{
					eval( "var onclickprop = [" + argsstr + "]" );
				}
				catch(ex)
				{
					//	ignore
				}
			}

			if (typeof onclickprop != 'undefined')
			{
				for (var i = 0; i < onclickprop.length; i++ )
				{
					var propobj = onclickprop[i];
					this.setValues( propobj );
				}
			}
			if (onmouseover != null && onmouseover.indexOf('return this.onclick()') != -1)
			{
				dom.check( 'openonhover', true );
			}
			else
			{
				dom.check( 'openonhover', false );
			}
			return true;
		}
		if ( onclick != null && onclick.indexOf( 'return hs.expand' ) != -1)
		{
			return false;
		}
		return true;
	},

	setValues : function( propobj ) {
		for ( var prop in propobj )
		{
			if (typeof prop == 'object')
			{
				this.setValues( prop );
			}
			else
			{
				var propvalu = propobj[prop];
				switch( prop )
				{
					case "align":
						dom.value('align', propvalu );
						break;
					case "anchor":
						dom.value('anchor', propvalu );
						break;
					case "easing":
						dom.value('easing', propvalu );
						break;
					case "easingClose":
						dom.value('easingclose', propvalu );
						break;
					case "allowSizeReduction":
						dom.value('allowsizereduction', (propvalu ? 'true' : 'false') );
						break;
					case "fadeInOut":
						dom.value('fadeinout', ( propvalu ? 'true' : 'false' ) );
						break;
					case "outlineWhileAnimating":
						dom.value('outlinewhileanimating', (propvalu ? 'true' : 'false') );
						break;
					case "outlineType":
						if (propvalu == null)
						{
							dom.value('outlinetype', 'no-border' );
						}
						else
						{
							dom.value('outlinetype', propvalu );
						}
						break;
					case "minWidth":
						dom.value('minwidth', propvalu );
						break;
					case "minHeight":
						dom.value('minheight', propvalu );
						break;
					case "targetX":
						dom.value('targetx', propvalu );
						break;
					case "targetY":
						dom.value('targety', propvalu );
						break;
					case "wrapperClassName":
						dom.value('wrapperclass', propvalu );
						break;
					case "thumbnailId":
						dom.value('thumbnailid', propvalu );
						break;
					case "contentId":
						dom.value('contentid', propvalu );
						break;
					case "slideshowGroup":
						dom.value('slideshowgroup', propvalu );
						break;
					case "src":
						dom.value('psrc', propvalu );
						break;
					case "width":
						dom.value('width', propvalu );
						break;
					case "height":
						dom.value('height', propvalu );
						break;
					case "allowWidthReduction":
						dom.value('allowwidthreduction', (propvalu ? 'true' : 'false') );
						break;
					case "allowHeightReduction":
						dom.value('allowheightwidthreduction', (propvalu ? 'true' : 'false') );
						break;
					case "objectType":
						dom.value('objecttype', propvalu );
						break;
					case "objectWidth":
						dom.value('objectwidth', propvalu );
						break;
					case "objectHeight":
						dom.value('objectheight', propvalu );
						break;
					case "preserveContent":
						dom.value('preservecontent', (propvalu ? 'true' : 'false') );
						break;
					case "cacheAjax":
						dom.value('cacheajax', (propvalu ? 'true' : 'false') );
						break;
					case "objectLoadTime":
						dom.value('objectloadtime', propvalu );
						break;
					case "swfOptions":
						this.setSwfOptions( propvalu );
						break;
					/* the following overlay elements remain here for compatability with previous version */
					case "overlayId":
						dom.value('overlayid', propvalu );
						break;
					case "fade":
						dom.value('ovfade', propvalu );
						break;
					case "position":
						var posar = propvalu.split( ' ' );
						for ( var i = 0; i < posar.length; i++ )
						{
							switch( this.positionType( posar[i] ))
							{
								case 'vertical':
									dom.value( 'ovvposition', posar[i] );
									break;
								case 'horizontal':
									dom.value( 'ovhposition', posar[i] );
									break;
								default:
									break;
							} // switch
						}
						break;
					case "hideOnMouseOut":
						dom.value('ovhideonmouseout', propvalu );
						break;
					case "opacity":
						dom.value('ovopacity', propvalu );
						break;
					/* end of compatability elements */

					case "dragByHeading":
						dom.value('dragbyheading', ( propvalu ? 'true' : 'false' ) );
						break;
					case "numberPosition":
						if (propvalu == null)
						{
							dom.value( 'numberposition', 'null' );
						}
						else
						{
							dom.value('numberposition', propvalu );
						}
						break;
					case "dimmingOpacity":
						dom.value('dimmingopacity', propvalu );
						break;
					case "captionId":
						dom.value('captionid', propvalu );
						break;
					case "hsjcaption":
						dom.value( 'caption', unescape( propvalu ));
						break;
					case "hsjcaptionstyle":
						dom.value( 'captionstyle', propvalu );
						break;
					case "headingId":
						dom.value('headingid', propvalu );
						break;
					case "hsjheading":
						dom.value( 'heading', unescape( propvalu ));
						break;
					case "hsjheadingstyle":
						dom.value( 'headingstyle', propvalu );
						break;
					case "creditsPosition":
						var posar = propvalu.split( ' ' );
						for ( var i = 0; i < posar.length; i++ )
						{
							switch( this.positionType( posar[i] ))
							{
								case 'vertical':
									dom.value( 'crvposition', posar[i] );
									break;
								case 'horizontal':
									dom.value( 'crhposition', posar[i] );
									break;
								default:
									break;
							} // switch
						}
						break;
					case "transitions":
						var str = "";
						var cm = "";
						for ( var i = 0; i < propvalu.length; i++ )
						{
							str += cm + "'" + propvalu[i] + "'";
							cm = ", ";
						}
						dom.value('transitions', str );
						break;
					case "captionText":
						dom.value('captiontext', propvalu );
						break;
					case "headingText":
						dom.value('headingtext', propvalu );
						break;
					case "captionOverlay":
						dom.check( 'coenableoverlay', true );
						this.setCaptionOverlayValues( propvalu );
						break;
					case "headingOverlay":
						dom.check( 'hoenableoverlay', true );
						this.setHeadingOverlayValues( propvalu );
						break;
					case "customOverlay":
						this.setCustomOverlayValues( propvalu );
						break;
					case "hsjcustomOverlay":
						dom.value( 'overlay', unescape( propvalu ));
						break;
					case "hsjcustomOverlayStyle":
						dom.value( 'overlaystyle', propvalu );
						break;
					case "hsjcontent":
						dom.value( 'content', unescape( propvalu ));
						break;
					case "hsjcontentstyle":
						dom.value( 'contentstyle', propvalu );
						break;
					default:
						break;
				}
			}
		}
	},

	setSwfOptions : function( propobj ) {
		for ( var prop in propobj )
		{
			var propvalu = propobj[prop];
			switch( prop )
			{
				case "version":
					dom.value('swfversion', propvalu );
					break;
				case "expressInstallSwfurl":
					dom.value('swfexpressinstallurl', propvalu );
					break;
				case "flashvars":
					dom.value('swfflashvars', this.dumpSwfVars( propvalu ));
					break;
				case "params":
					dom.value('swfparams', this.dumpSwfVars( propvalu ));
					break;
				case "attributes":
					dom.value('swfattributes', this.dumpSwfVars( propvalu ));
					break;
				default:
					break;
			}
		}
	},

	dumpSwfVars : function( propobj ) {
		var vars = "";
		var $c = "";

		for (var prop in propobj )
		{
			if (typeof prop != 'object')
			{
				vars += $c + prop + ": '" + propobj[prop] + "'"
				$c = ", ";
			}
		}
		return vars;
	},

	setCaptionOverlayValues : function( propobj ) {
		for ( var prop in propobj )
		{
			if (typeof prop == 'object')
			{
				this.setValues( prop );
			}
			else
			{
				var propvalu = propobj[prop];
				switch( prop )
				{
					case "fade":
						dom.value('cofade', propvalu );
						break;
					case "position":
						var posar = propvalu.split( ' ' );
						for ( var i = 0; i < posar.length; i++ )
						{
							switch( this.positionType( posar[i] ))
							{
								case 'vertical':
									dom.value( 'covposition', posar[i] );
									break;
								case 'horizontal':
									dom.value( 'cohposition', posar[i] );
									break;
								default:
									break;
							} // switch
						}
						break;
					case "hideOnMouseOut":
						dom.value('cohideonmouseout', (propvalu ? 'true' : 'false') );
						break;
					case "opacity":
						dom.value('coopacity', propvalu );
						break;
					case "width":
						dom.value('cowidth', propvalu );
						break;
					case "offsetX":
						dom.value('cooffsetx', propvalu );
						break;
					case "offsetY":
						dom.value('cooffsety', propvalu );
						break;
					case "relativeTo":
						dom.value('corelativeto', propvalu );
						break;
					case "className":
						dom.value('coclassname', propvalu );
						break;
					default:
						break;
				}
			}
		}
	},

	setHeadingOverlayValues : function( propobj ) {
		for ( var prop in propobj )
		{
			if (typeof prop == 'object')
			{
				this.setValues( prop );
			}
			else
			{
				var propvalu = propobj[prop];
				switch( prop )
				{
					case "fade":
						dom.value('hofade', propvalu );
						break;
					case "position":
						var posar = propvalu.split( ' ' );
						for ( var i = 0; i < posar.length; i++ )
						{
							switch( this.positionType( posar[i] ))
							{
								case 'vertical':
									dom.value( 'hovposition', posar[i] );
									break;
								case 'horizontal':
									dom.value( 'hohposition', posar[i] );
									break;
								default:
									break;
							} // switch
						}
						break;
					case "hideOnMouseOut":
						dom.value('hohideonmouseout', (propvalu ? 'true' : 'false') );
						break;
					case "opacity":
						dom.value('hoopacity', propvalu );
						break;
					case "width":
						dom.value('howidth', propvalu );
						break;
					case "offsetX":
						dom.value('hooffsetx', propvalu );
						break;
					case "offsetY":
						dom.value('hooffsety', propvalu );
						break;
					case "relativeTo":
						dom.value('horelativeto', propvalu );
						break;
					case "className":
						dom.value('hoclassname', propvalu );
						break;
					default:
						break;
				}
			}
		}
	},

	setCustomOverlayValues : function( propobj ) {
		for ( var prop in propobj )
		{
			if (typeof prop == 'object')
			{
				this.setValues( prop );
			}
			else
			{
				var propvalu = propobj[prop];
				switch( prop )
				{
					case "fade":
						dom.value('ovfade', propvalu );
						break;
					case "position":
						var posar = propvalu.split( ' ' );
						for ( var i = 0; i < posar.length; i++ )
						{
							switch( this.positionType( posar[i] ))
							{
								case 'vertical':
									dom.value( 'ovvposition', posar[i] );
									break;
								case 'horizontal':
									dom.value( 'ovhposition', posar[i] );
									break;
								default:
									break;
							} // switch
						}
						break;
					case "hideOnMouseOut":
						dom.value('ovhideonmouseout', (propvalu ? 'true' : 'false') );
						break;
					case "opacity":
						dom.value('ovopacity', propvalu );
						break;
					case "width":
						dom.value('ovwidth', propvalu );
						break;
					case "offsetX":
						dom.value('ovoffsetx', propvalu );
						break;
					case "offsetY":
						dom.value('ovoffsety', propvalu );
						break;
					case "relativeTo":
						dom.value('ovrelativeto', propvalu );
						break;
					case "className":
						dom.value('ovclassname', propvalu );
						break;
					default:
						break;
				}
			}
		}
	},

	positionType : function( position )
	{
		switch( position )
		{
			case 'above':
			case 'top':
			case 'middle':
			case 'bottom':
			case 'below':
				return "vertical";
				break;
			case 'leftpanel':
			case 'left':
			case 'center':
			case 'right':
			case 'rightpanel':
				return "horizontal";
				break;
			default:
				break;
		}
		return "";
	},

	buildHsOnMouseOver : function(){
		if (dom.ischecked('openonhover'))
		{
			dom.value('onmouseover', 'return this.onclick()' );
		}
		else
		{
			dom.value('onmouseover', '' );
		}
	},

	buildHsOnClick : function(){
		var ed = tinyMCEPopup.editor;
		var onclick = "return hs.htmlExpand(this";
		var onclickopts = "";
		var onclickswfopts = "";
		var v;

		if ((v=dom.value('align')) != "")
		{
			onclickopts += "align: ";
			onclickopts += "'" + v + "'";
		}
		if ((v=dom.value('anchor')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "anchor: '" + v + "'";
		}
		if ((v=dom.value('easing')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "easing: '" + v + "'";
		}
		if ((v=dom.value('easingclose')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "easingClose: '" + v + "'";
		}
		if ((v=dom.value('allowsizereduction')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "allowSizeReduction: " + v;
		}
		if ((v=dom.value('fadeinout')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "fadeInOut: " + v;
		}
		if ((v=dom.value('dragbyheading')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "dragByHeading: " + v;
		}
		if ((v=dom.value('numberposition')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			if (v == 'null')
			{
				onclickopts += "numberPosition: " + v;
			}
			else
			{
				onclickopts += "numberPosition: '" + v + "'";
			}
		}
		if ((v=dom.value('outlinewhileanimating')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "outlineWhileAnimating: " + v;
		}
		if ((v=dom.value('outlinetype')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			if (v == 'no-border')
			{
				onclickopts += "outlineType: null";
			}
			else
			{
				onclickopts += "outlineType: '" + v + "'";
			}
		}
		if ((v=dom.value('minwidth')) != "" && !isNaN(v))
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "minWidth: " + v;
		}
		if ((v=dom.value('minheight')) != "" && !isNaN(v))
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "minHeight: " + v;
		}
		if ((v=dom.value('targetx')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "targetX: '" + v + "'";
		}
		if ((v=dom.value('targety')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "targetY: '" + v + "'";
		}
		if ((v=dom.value('wrapperclass')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "wrapperClassName: '" + v + "'";
		}
		if ((v=dom.value('thumbnailid')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "thumbnailId: '" + v + "'";
		}
		if ((v=dom.value('contentid')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "contentId: '" + v + "'";
			if (dom.value('objecttype') == 'ajax')
			{
				dom.value( 'cacheajax', 'false' );
			}
		}
		if ((v=dom.value('content')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "hsjcontent: '" + this.escapeText(v) + "'";
		}
		if ((v=dom.value('contentstyle')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "hsjcontentstyle: '" + v + "'";
		}
		if ((v=dom.value('slideshowgroup')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "slideshowGroup: '" + v + "'";
		}
		if ((v=dom.value('psrc')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			v	= new tinymce.util.URI(ed.getParam('document_base_url')).toAbsolute(v,true);
			onclickopts += "src: '" + v + "'";
		}
		if ((v=dom.value('width')) != "" && !isNaN(v))
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "width: " + v;
		}
		if ((v=dom.value('height')) != ""  && !isNaN(v))
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "height: " + v;
		}
		if ((v=dom.value('allowwidthreduction')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "allowWidthReduction: " + v;
		}
		if ((v=dom.value('allowheightreduction')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "allowHeightReduction: " + v;
		}
		if ((v=dom.value('objecttype')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "objectType: '" + v + "'";
		}
		if ((v=dom.value('objectwidth')) != "" && !isNaN(v))
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "objectWidth: " + v;
		}
		if ((v=dom.value('objectheight')) != "" && !isNaN(v))
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "objectHeight: " + v;
		}
		if ((v=dom.value('preservecontent')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "preserveContent: " + v;
		}
		if ((v=dom.value('cacheajax')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "cacheAjax: " + v;
		}
		if ((v=dom.value('objectloadtime')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "objectLoadTime: '" + v + "'";
		}
		if ((v=dom.value('dimmingopacity')) != ""  && !isNaN(v))
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "dimmingOpacity: " + v;
		}
		if (dom.value('crvposition') != "" || dom.value('crhposition') != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "creditsPosition: '";
			var spc = "";
			if ((v=dom.value('crvposition')) != "")
			{
				onclickopts += v;
				spc = " ";
			}
			if ((v=dom.value('crhposition')) != "")
			{
				onclickopts += spc + v;
			}
			onclickopts += "'";
		}
		if ((v=dom.value('transitions')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "transitions: [ " + v + " ]";
		}
		if ((v=dom.value('captiontext')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "captionText: '" + v + "'";
		}
		if ((v=dom.value('headingtext')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "headingText: '" + v + "'";
		}
		if ((v=dom.value('captionid')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "captionId: '" + v + "'";
		}
		if ((v=dom.value('caption')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "hsjcaption: '" + this.escapeText(v) + "'";
		}
		if ((v=dom.value('captionstyle')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "hsjcaptionstyle: '" + v + "'";
		}
		if (dom.ischecked('coenableoverlay'))
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "captionOverlay: {";
			onclickopts += " fade: ";
			if ((v=dom.value('cofade')) != "")
			{
				onclickopts += v;
			}
			else
			{
				onclickopts += 0;
			}
			if (dom.value('covposition') != "" || dom.value('cohposition') != "")
			{
				onclickopts += ", position: '";
				var spc = "";
				if ((v=dom.value('covposition')) != "")
				{
					onclickopts += v;
					spc = " ";
				}
				if ((v=dom.value('cohposition')) != "")
				{
					onclickopts += spc + v;
				}
				onclickopts += "'";
			}

			if ((v=dom.value('cohideonmouseout')) != "")
			{
				onclickopts += ", hideOnMouseOut: ";
				onclickopts += v;
			}

			if ((v=dom.value('coopacity')) != "" && !isNaN(v))
			{
				onclickopts += ", opacity: ";
				onclickopts += v;
			}
			if ((v=dom.value('cowidth')) != "")
			{
				onclickopts += ", width: ";
				onclickopts += "'" + v + "'";
			}
			if ((v=dom.value('cooffsetx')) != "" && !isNaN(v))
			{
				onclickopts += ", offsetX: ";
				onclickopts += v;
			}
			if ((v=dom.value('cooffsety')) != "" && !isNaN(v))
			{
				onclickopts += ", offsetY: ";
				onclickopts += v;
			}
			if ((v=dom.value('corelativeto')) != "")
			{
				onclickopts += ", relativeTo: ";
				onclickopts += "'" + v + "'";
			}
			if ((v=dom.value('coclassname')) != "")
			{
				onclickopts += ", className: ";
				onclickopts += "'" + v + "'";
			}
			onclickopts += " }";
		}

		if ((v=dom.value('headingid')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "headingId: '" + v + "'";
		}
		if ((v=dom.value('heading')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "hsjheading: '" + this.escapeText(v) + "'";
		}
		if ((v=dom.value('headingstyle')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "hsjheadingstyle: '" + v + "'";
		}
		if (dom.ischecked('hoenableoverlay'))
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "headingOverlay: {";
			onclickopts += " fade: ";
			if ((v=dom.value('hofade')) != "")
			{
				onclickopts += v;
			}
			else
			{
				onclickopts += 0;
			}
			if (dom.value('hovposition') != "" || dom.value('hohposition') != "")
			{
				onclickopts += ", position: '";
				var spc = "";
				if ((v=dom.value('hovposition')) != "")
				{
					onclickopts += v;
					spc = " ";
				}
				if ((v=dom.value('hohposition')) != "")
				{
					onclickopts += spc + v;
				}
				onclickopts += "'";
			}

			if ((v=dom.value('hohideonmouseout')) != "")
			{
				onclickopts += ", hideOnMouseOut: ";
				onclickopts += v;
			}

			if ((v=dom.value('hoopacity')) != "" && !isNaN(v))
			{
				onclickopts += ", opacity: ";
				onclickopts += v;
			}
			if ((v=dom.value('howidth')) != "")
			{
				onclickopts += ", width: ";
				onclickopts += "'" + v + "'";
			}
			if ((v=dom.value('hooffsetx')) != "" && !isNaN(v))
			{
				onclickopts += ", offsetX: ";
				onclickopts += v;
			}
			if ((v=dom.value('hooffsety')) != "" && !isNaN(v))
			{
				onclickopts += ", offsetY: ";
				onclickopts += v;
			}
			if ((v=dom.value('horelativeto')) != "")
			{
				onclickopts += ", relativeTo: ";
				onclickopts += "'" + v + "'";
			}
			if ((v=dom.value('hoclassname')) != "")
			{
				onclickopts += ", className: ";
				onclickopts += "'" + v + "'";
			}
			onclickopts += " }";
		}

		if ((v=dom.value('overlay')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "hsjcustomOverlay: '" + this.escapeText(v) + "'";
		}
		if ((v=dom.value('overlaystyle')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "hsjcustomOverlayStyle: '" + v + "'";
		}
		if ((v=dom.value('overlayid')) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "overlayId: '" + v + "'";

			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "customOverlay: {";
			onclickopts += " useOnHtml: true";
			if ((v=dom.value('ovfade')) != "")
			{
				onclickopts += ",fade: ";
				onclickopts += v;
			}
			if (dom.value('ovvposition') != "" || dom.value('ovhposition') != "")
			{
				onclickopts += ", position: '";
				var spc = "";
				if ((v=dom.value('ovvposition')) != "")
				{
					onclickopts += v;
					spc = " ";
				}
				if ((v=dom.value('ovhposition')) != "")
				{
					onclickopts += spc + v;
				}
				onclickopts += "'";
			}

			if ((v=dom.value('ovhideonmouseout')) != "")
			{
				onclickopts += ", hideOnMouseOut: ";
				onclickopts += v;
			}

			if ((v=dom.value('ovopacity')) != "" && !isNaN(v))
			{
				onclickopts += ", opacity: ";
				onclickopts += v;
			}
			if ((v=dom.value('ovwidth')) != "")
			{
				onclickopts += ", width: ";
				onclickopts += "'" + v + "'";
			}
			if ((v=dom.value('ovoffsetx')) != "" && !isNaN(v))
			{
				onclickopts += ", offsetX: ";
				onclickopts += v;
			}
			if ((v=dom.value('ovoffsety')) != "" && !isNaN(v))
			{
				onclickopts += ", offsetY: ";
				onclickopts += v;
			}
			if ((v=dom.value('ovrelativeto')) != "")
			{
				onclickopts += ", relativeTo: ";
				onclickopts += "'" + v + "'";
			}
			if ((v=dom.value('ovclassname')) != "")
			{
				onclickopts += ", className: ";
				onclickopts += "'" + v + "'";
			}
			onclickopts += " }";
		}

		if ( dom.value('swfversion') != ""
		   ||dom.value('swfexpressinstallurl') != ""
		   ||dom.value('swfflashvars') != ""
		   ||dom.value('swfparams') != ""
		   ||dom.value('swfattributes') != ""
		   )
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "swfOptions: {";

			if ((v=dom.value('swfversion')) != "")
			{
				onclickswfopts += (onclickswfopts.length > 0) ? ", " : "";
				onclickswfopts += "version: '" + v + "'";
			}
			if ((v=dom.value('swfexpressinstallurl')) != "")
			{
				onclickswfopts += (onclickswfopts.length > 0) ? ", " : "";
				onclickswfopts += "expressInstallSwfurl: '" + v + "'";
			}

			if ((v=dom.value('swfflashvars')) != "")
			{
				onclickswfopts += (onclickswfopts.length > 0) ? ", " : "";
				onclickswfopts += "flashvars: { " + this.filterVars(v) + "} ";
			}
			if ((v=dom.value('swfparams')) != "")
			{
				onclickswfopts += (onclickswfopts.length > 0) ? ", " : "";
				onclickswfopts += "params: { " + this.filterVars(v) + "} ";
			}
			if ((v=dom.value('swfattributes')) != "")
			{
				onclickswfopts += (onclickswfopts.length > 0) ? ", " : "";
				onclickswfopts += "attributes: { " + this.filterVars(v) + " }";
			}
			onclickopts += onclickswfopts + " }";
		}

		if (onclickopts != "")
			onclick += ",{" + onclickopts + "}";

		onclick += ")"
		dom.value('onclick', onclick );
	},

	filterVars : function( vars )
	{
		var arr = vars.match(/([^ :]*):\s*'([^\']*)'/g );
		var c = "";
		var str = "";
		for (var i = 0; i < arr.length; i++)
		{
			str += c + arr[i];
			c = ", ";
		}
		return str;
	},

	escapeText : function( text )
	{
		if ( text.test( /[&<>'\\"%\n\r]/ ))
		{
			return escape(text);
		}
		return text;
	},

	insert : function(){
		var ed = tinyMCEPopup.editor, el = null, elementArray, i, args = {};
		var n = ed.selection.getNode(), br = '';
		var hsrel = '';

		if (dom.ischecked('unobtrusive'))
		{
			hsrel = 'highslide-' + dom.value('objecttype' );
			dom.value('onclick', '' );
			dom.value('onmouseover', '' );
		}
		else
		{
			this.buildHsOnClick();
			this.buildHsOnMouseOver();
		}

		tinymce.extend(args, {
			href 		: dom.value('href'),
			title 		: dom.value('title'),
			id 			: dom.value('id'),
			style 		: dom.value('style'),
			'class' 	: 'highslide',
			rel         : hsrel,
			onclick 	: dom.value('onclick'),
			onmouseover : dom.value('onmouseover')
		});

		el = ed.dom.getParent(ed.selection.getNode(), "A");
		tinyMCEPopup.execCommand("mceBeginUndoLevel");

		// Create new anchor elements
		if (el == null)
		{
			tinyMCEPopup.execCommand("CreateLink", false, "#mce_temp_url#");
			elementArray = tinymce.grep(ed.dom.select("a"), function(n) {return ed.dom.getAttrib(n, 'href') == '#mce_temp_url#';});
			for (i=0; i<elementArray.length; i++)
			{
				el = elementArray[i];

				// Move cursor to end
				try
				{
					tinyMCEPopup.editor.selection.collapse(false);
				}
				catch (ex)
				{
					// Ignore
				}
				ed.dom.setAttribs(el, args);
			}
		}
		else
		{
			ed.dom.setAttribs(el, args);
		}

		if (dom.value('contentid') != '') {
			var divobj = ed.dom.get(dom.value('contentid'));
			if (divobj != null && divobj.nodeName == "DIV" && ed.dom.getAttrib( divobj, 'class') == 'highslide-html-content') {
				var contentid = dom.value('contentid' );
				ed.dom.remove( contentid );
			}
		}
		tinyMCEPopup.execCommand("mceEndUndoLevel");
		tinyMCEPopup.close();
	}
}

var HsHtmlExpander = Plugin.extend({
	moreOptions : function(){
		return {};
	},
	initialize : function(options){
		this.setOptions(this.moreOptions(), options);
		this.parent('HsHtmlExpander', this.options);
	}
});
HsHtmlExpander.implement(new Events, new Options);
HsHtmlExpanderDialog.preInit();
tinyMCEPopup.onInit.add(HsHtmlExpanderDialog.init, HsHtmlExpanderDialog);