(function() {
	tinymce.PluginManager.requireLangPack('hsexpander');
	tinymce.create('tinymce.plugins.HighslideExpanderPlugin', {
		init : function(ed, url) {
			this.editor = ed;

			// Register commands
			ed.addCommand('mceHsExpander', function() {
				var se = ed.selection;

				ed.windowManager.open({
					file : ed.getParam('site_url') + 'index.php?option=com_jce&task=plugin&plugin=hsexpander&file=hsexpander',
					width : 580 + ed.getLang('hsexpander.delta_width', 0),
					height : 640 + ed.getLang('hsexpander.delta_height', 0),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('hsexpander', {
				title : 'hsexpander.desc',
				cmd : 'mceHsExpander',
				image : url + '/img/hsexpander.gif'			});

			ed.onNodeChange.add(function(ed, cm, n, co) {
				//cm.setDisabled('hsexpander', co && n.nodeName != 'A');
				cm.setActive('hsexpander', (n.nodeName == 'A' || n.nodeName == 'IMG') && !n.name);
			});
		},

		getInfo : function() {
			return {
				longname : 'Highslide Expander',
				author : 'Moxiecode Systems AB',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/advlink',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('hsexpander', tinymce.plugins.HighslideExpanderPlugin);
})();