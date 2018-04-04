(function() {
	tinymce.PluginManager.requireLangPack('hshtmlexpander');
	tinymce.create('tinymce.plugins.HighslideHtmlExpanderPlugin', {
		init : function(ed, url) {
			this.editor = ed;

			// Register commands
			ed.addCommand('mceHsHtmlExpander', function() {
				var se = ed.selection;

				ed.windowManager.open({
					file : ed.getParam('site_url') + 'index.php?option=com_jce&task=plugin&plugin=hshtmlexpander&file=hshtmlexpander',
					width : 580 + ed.getLang('hshtmlexpander.delta_width', 0),
					height : 640 + ed.getLang('hshtmlexpander.delta_height', 0),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('hshtmlexpander', {
				title : 'hshtmlexpander.desc',
				cmd : 'mceHsHtmlExpander',
				image : url + '/img/hshtmlexpander.gif'			});

			ed.onNodeChange.add(function(ed, cm, n, co) {
				cm.setDisabled('hshtmlexpander', co && n.nodeName != 'A');
				cm.setActive('hshtmlexpander', n.nodeName == 'A' && !n.name);
			});
		},

		getInfo : function() {
			return {
				longname : 'Highslide HTML Expander',
				author : 'Moxiecode Systems AB',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/advlink',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('hshtmlexpander', tinymce.plugins.HighslideHtmlExpanderPlugin);
})();