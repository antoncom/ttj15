/**
 * $Id: editor_plugin_src.js 763 2008-04-03 13:25:45Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	var each = tinymce.each;
	tinymce.PluginManager.requireLangPack('iframe');
	tinymce.create('tinymce.plugins.IFramePlugin', {
		init : function(ed, url) {
			var t = this;
			
			t.editor 	= ed;
			t.url 		= url;

			// Register commands
			ed.addCommand('mceIFrame', function() {
				ed.windowManager.open({
					file : ed.getParam('site_url') + 'index.php?option=com_jce&task=plugin&plugin=iframe&file=iframe',
					width : 700 + parseInt(ed.getLang('iframe.delta_width', 0)),
					height : 320 + parseInt(ed.getLang('iframe.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('iframe', {title : 'iframe.desc', cmd : 'mceIFrame', image : url + '/img/iframe.gif'});

			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('iframe', n.nodeName == 'IMG' && /mceItemIFrame/.test(n.className));
			});

			ed.onPreInit.add(function() {
				// Add iframe to valid elements
				ed.serializer.addRules('iframe[id|src|width|height|align<bottom?left?middle?right?top|frameborder|scrolling<auto?no?yes|longdesc|name|allowtransparency|*]');
			});

			ed.onInit.add(function() {
				ed.dom.loadCSS(url + "/css/content.css");
			});
			
			ed.onBeforeSetContent.add(function(ed, o) {
				// Convert to span
				o.content = o.content.replace(/<iframe([^>]*)>([\s\S]*?)<\/iframe>/g, '<span class="mceItemIFrame" $1></span>');
			});
			
			ed.onSetContent.add(function(ed, o){
				var dom = ed.dom;
				each(dom.select('span.mceItemIFrame', ed.getBody()), function(n) {
					dom.replace(t._createImg(n), n);
				});
			});

			ed.onPreProcess.add(function(ed, o) {
				var dom = ed.dom;

				if (o.set) {
					each(dom.select('span.mceItemIFrame', o.node), function(n) {
						dom.replace(t._createImg(n), n);
					});
				}				
				if (o.get) {
					each(dom.select('img.mceItemIFrame', o.node), function(n) {
						dom.replace(t._buildIframe(n), n);
					});
				}
			});			
		},

		getInfo : function() {
			return {
				longname : 'IFrame',
				author : 'Moxiecode Systems AB / Ryan Demmer',
				authorurl : 'http://www.joomlacontenteditor.net',
				infourl : 'http://www.joomlacontenteditor.net',
				version : '1.5.2'
			};
		},
		
		// Private methods

		_buildIframe : function(n) {
			var ed = this.editor, dom = ed.dom, p = this._parse(n.title), ob;			
			
			// Convert src to relative if local
			p.src = ed.convertURL(p.src, 'src', n);
			// Cleanup name attribute
			if(p.name){
				p.name = p.name.replace(/[^a-z0-9_:\-\.]/gi, '');
			}
			// Setup base parameters
			each(['id', 'class', 'style', 'width', 'height', 'longdesc'], function(na) {
				var v = dom.getAttrib(n, na);				
				if (v)
					p[na] = v;
			});
			// Create iframe element
			ob = dom.create('iframe', p);
			// Remove identifier
			dom.removeClass(ob, 'mceItemIFrame');
			// Remove additional width / height styles
			each(['width', 'height'], function(na){
				if(p[na] && dom.getStyle(n, na)){
					dom.setStyle(ob, na, '');	
				}
			});		
			return ob;
		},

		_createImg : function(n) {
			var img, dom = this.editor.dom, pa = {}, t = this;
			
			// Setup base image
			img = dom.create('img', {
				src		: t.url + '/img/trans.gif',
				width	: dom.getAttrib(n, 'width'),
				height	: dom.getAttrib(n, 'height'),
				'class' : dom.getAttrib(n, 'class'),
				id		: dom.getAttrib(n, 'id'),
				style	: dom.getAttrib(n, 'style'),
				longdesc: dom.getAttrib(n, 'longdesc')
			});
			// Add identifier class
			dom.addClass(img, 'mceItemIFrame');
			// Sort out IE problems with % dimensions
			if(tinymce.isIE){
				each(['width', 'height'], function(na) {
					var s = dom.getAttrib(n, na); 
					if(/%/.test(s) && !dom.getStyle(n, na)){
						dom.setStyle(img, na, s);	
					}
				});	
			}
			// Setup iframe parameters
			each(['frameborder', 'src', 'marginheight', 'marginwidth', 'scrolling', 'title', 'name', 'allowtransparency'], function(na) {
				var v = dom.getAttrib(n, na);
				
				v = t._encode(v);
				
				if(na == 'name')
					v = v.replace(/[^a-z0-9_:\-\.]/gi, '');	
				
				if (v)
					pa[na] = v;
			});
			img.title = this._serialize(pa);
			
			return img;
		},
		
		_encode : function(s){
			s = s.replace(new RegExp('\\\\', 'g'), '\\\\');
			s = s.replace(new RegExp('"', 'g'), '\\"');
			s = s.replace(new RegExp("'", 'g'), "\\'");
	
			return s;
		},

		_parse : function(s) {
			return tinymce.util.JSON.parse('{' + s + '}');
		},

		_serialize : function(o) {
			return tinymce.util.JSON.serialize(o).replace(/[{}]/g, '').replace(/"/g, "'");
		}
	});

	// Register plugin
	tinymce.PluginManager.add('iframe', tinymce.plugins.IFramePlugin);
})();