/*
 
 eval(function(p,a,c,k,e,d){e=function(c){return(c<a?"":e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)d[e(c)]=k[c]||e(c);k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1;};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p;}(';(c($){j($.19){h}$.1e=c(Z){6 3=a.1X(\'3\');3.1Y(a.1Z(Z));h 3.1W};$.1p=c(f){f=$(f);f.k({12:\'15\',1B:F.G((a.x.1C-f.m())/2+a.x.1T,0)+\'v\',1D:F.G((a.x.1E-f.q())/2+a.x.1U,0)+\'v\'})};$.13=c(e,17){6 f=W(e);6 t=f.k(\'1V\')||\'\';6 r=f.k(\'20\')||\'\';6 b=f.k(\'25\')||\'\';6 l=f.k(\'26\')||\'\';j(17)h{t:I(t)||0,r:I(r)||0,b:I(b)||0,l:I(l)};1b h{t:t,r:r,b:b,l:l}};c H(X,Y){h X.24(/\\$\\{([\\w]+)\\}/g,c(18,1k){6 s=Y[1k];j(22(s)!="23"){h s}1b{h 18}})}6 D=p;$.19=c(u,8){j(D){h}D=B;8=$.1I({m:1F,q:1K,C:\'\',1r:B,E:T},8);6 i=(1l 1n()).1m();8.i=i;8.C=$.1e(8.C);6 n=$("<3 z=\'1Q"+i+"\' 9=\'4-7 4-7-1P\'></3>");6 1j="<3 z=\'1S${i}\' 9=\'4-7-1R\'><3 9=\'4-7-1M\'><3 9=\'4-7-1L\'><3 L=\'1O: M; 1N: M\' 9=\'4-7-1c\' 1H=\'28\'><3 9=\'4-V 4-V-11\'>&2u;</3><1d 9=\'4-7-1c-2t\'>${C}</1d></3></3></3></3>";6 10="<3 9=\'4-7-2s\'><3 9=\'4-7-2p\'><3 9=\'4-7-2o\'><3 9=\'4-7-2r\'><3 z=\'N${i}\' L=\'m: ${m}v; q: ${q}v\' 9=\'4-7-o\'>${1h}</3></3></3></3><3 9=\'4-7-2q\'><3 9=\'4-7-2A\'><3 9=\'4-7-2x\'><3 9=\'4-7-2y\'></3></3></3></3></3>";6 1g=\'<1a z="1q${i}" 1f="0" 2e="0" 1u="${u}" L="1f:M;m:${m}v;q:${q}v"></1a>\';8.u=u+(u.2g(\'?\')>-1?\'&\':\'?\')+\'2l=\'+(1l 1n()).1m();6 y=[];8.1h=H(1g,8);y.1i(H(1j,8));y.1i(H(10,8));n.k({m:8.m+14}).y(y.2z(""));6 Q=n.2w("3.4-V-11").J(c(e){$(16).U("J")},c(e){$(16).1w("J")}).21(1y);6 K=$.13(a.o,B);6 O=$(\'<3></3>\').k({12:\'15\',1B:0,1D:0,m:F.G(a.x.1C,a.o.27),q:F.G(a.x.1E,a.o.1J+K.t+K.b),29:\'2j\',2i:\'#2h\',2k:\'0.5\'}).2n(\'2m\',c(){h p}).1o(a.o);6 A=p;j(8.1r){j($.2b.1s){n.U("4-7-2a").1s(p).2c(c(e){j(A==p){A=B;$("#N"+i).k("1t","2f")}}).2d(c(e){A=p;$("#N"+i).k("1t","2v")})}}n.1o(a.o);$.1p(n);j($.1A.1v){$(a.o).U("1x");a.1G("1q"+i).1u=8.u}c 1y(e){$.R()}c 1z(){h p}$.R=c(P,d){$.R=1z;j($.1A.1v){$(a.o).1w("1x")}O.S();Q.S();n.S();D=p;Q=O=n=T;P&&P();j(d&&8.E){8.E();8.E=T}}}})(W);',62,161,'|||div|bbit||var|window|options|class|document||function|||el||return|newid|if|css||width|box|body|false|height||||url|px||documentElement|html|id|isdrag|true|caption|opening|onclose|Math|max|Tp|parseInt|hover|margins|style|none|dailog_body_|overlayer|callback|closebtn|closeIfrm|remove|null|addClass|tool|jQuery|temp|dataarry|string|bodytemplete|close|position|getMargins||absolute|this|toInteger|s1|ShowIfrmDailog|iframe|else|header|span|escapeHTML|border|iframetemplete|iframehtml|push|headtemplete|s2|new|valueOf|Date|appendTo|documentCenter|dailog_iframe_|enabledrag|easydrag|visibility|src|msie6|removeClass|hiddenselect|closedialog|returnfalse|browser|left|clientWidth|top|clientHeight|600|getElementById|unselectable|extend|scrollHeight|400|tc|tr|khtmluserselect|mozuserselect|plain|dailog_|tl|dailog_head_|scrollLeft|scrollTop|marginTop|innerHTML|createElement|appendChild|createTextNode|marginRight|click|typeof|undefined|replace|marginBottom|marginLeft|scrollWidth|on|zIndex|draggable|fn|ondrag|ondrop|frameBorder|hidden|indexOf|fff|background|998|opacity|_|contextmenu|bind|mr|ml|bl|mc|bwrap|text|nbsp|visible|find|bc|footer|join|br'.split('|'),0,{}))

*/

(function ($) {
	if ($.ShowIfrmDailog) {
		return
	}
	$.escapeHTML = function (string) {
		var div = document.createElement('div');
		div.appendChild(document.createTextNode(string));
		return div.innerHTML
	};
	$.documentCenter = function (el) {
		el = $(el);
		el.css({
			position: 'absolute',
			left: Math.max((document.documentElement.clientWidth - el.width()) / 2 + document.documentElement.scrollLeft, 0) + 'px',
			top: Math.max((document.documentElement.clientHeight - el.height()) / 2 + document.documentElement.scrollTop, 0) + 'px'
		})
	};
	$.getMargins = function (e, toInteger) {
		var el = jQuery(e);
		var t = el.css('marginTop') || '';
		var r = el.css('marginRight') || '';
		var b = el.css('marginBottom') || '';
		var l = el.css('marginLeft') || '';
		if (toInteger) return {
			t: parseInt(t) || 0,
			r: parseInt(r) || 0,
			b: parseInt(b) || 0,
			l: parseInt(l)
		};
		else
			return {
				t: t,
				r: r,
				b: b,
				l: l
			}
	};

	function Tp(temp, dataarry) {
		return temp.replace(/\$\{([\w]+)\}/g, function (s1, s2) {
			var s = dataarry[s2];
			if (typeof(s) != "undefined") {
				return s
			} else {
				return s1
			}
		})
	}
	var opening = false;
	$.ShowIfrmDailog = function (url, options) {
		if (opening) {
			return
		}
		opening = true;
		options = $.extend({
			width: 600,
			height: 400,
			caption: '',
			enabledrag: true,
			onclose: null
		}, options);
		var newid = (new Date()).valueOf();
		options.newid = newid;
		options.caption = $.escapeHTML(options.caption);
		var box = $("<div id='dailog_" + newid + "' class='bbit-window bbit-window-plain'></div>");
		var headtemplete = "<div id='dailog_head_${newid}' class='bbit-window-tl'><div class='bbit-window-tr'><div class='bbit-window-tc'><div style='mozuserselect: none; khtmluserselect: none' class='bbit-window-header' unselectable='on'><div class='bbit-tool bbit-tool-close'>&nbsp;</div><span class='bbit-window-header-text'>${caption}</span></div></div></div></div>";
		var bodytemplete = "<div class='bbit-window-bwrap'><div class='bbit-window-ml'><div class='bbit-window-mr'><div class='bbit-window-mc'><div id='dailog_body_${newid}' style='width: ${width}px; height: ${height}px' class='bbit-window-body'>${iframehtml}</div></div></div></div><div class='bbit-window-bl'><div class='bbit-window-br'><div class='bbit-window-bc'><div class='bbit-window-footer'></div></div></div></div></div>";
		var iframetemplete = '<iframe id="dailog_iframe_${newid}" border="0" frameBorder="0" src="${url}" style="border:none;width:${width}px;height:${height}px"></iframe>';
		options.url = url + (url.indexOf('?') > -1 ? '&' : '?') + '_=' + (new Date()).valueOf();
		var html = [];
		options.iframehtml = Tp(iframetemplete, options);
		html.push(Tp(headtemplete, options));
		html.push(Tp(bodytemplete, options));
		box.css({
			width: options.width + 14
		}).html(html.join(""));
		var closebtn = box.find("div.bbit-tool-close").hover(function (e) {
			$(this).addClass("hover")
		}, function (e) {
			$(this).removeClass("hover")
		}).click(closedialog);
		var margins = $.getMargins(document.body, true);
		
		/*alert([
			$(document).height(),
			document.documentElement.clientHeight, 
			document.body.scrollHeight + margins.t + margins.b
		]);*/
		
		var overlayer = $('<div></div>').css({
			position: 'absolute',
			left: 0,
			top: 0,
			width: Math.max(document.documentElement.clientWidth, document.body.scrollWidth),			
			//height: Math.max(document.documentElement.clientHeight, document.body.scrollHeight + margins.t + margins.b),
			height: Math.max($(document).height(), document.body.scrollHeight + margins.t + margins.b),
			zIndex: '998',
			background: '#fff',
			opacity: '0.5'
		}).bind('contextmenu', function () {
			return false
		}).appendTo(document.body);
		var isdrag = false;
		if (options.enabledrag) {
			if ($.fn.easydrag) {
				box.addClass("bbit-window-draggable").easydrag(false).ondrag(function (e) {
					if (isdrag == false) {
						isdrag = true;
						$("#dailog_body_" + newid).css("visibility", "hidden")
					}
				}).ondrop(function (e) {
					isdrag = false;
					$("#dailog_body_" + newid).css("visibility", "visible")
				})
			}
		}
		box.appendTo(document.body);
		$.documentCenter(box);
		if ($.browser.msie6) {
			$(document.body).addClass("hiddenselect");
			document.getElementById("dailog_iframe_" + newid).src = options.url
		}
		function closedialog(e) {
			$.closeIfrm()
		}
		function returnfalse() {
			return false
		}
		$.closeIfrm = function (callback, d) {
			$.closeIfrm = returnfalse;
			if ($.browser.msie6) {
				$(document.body).removeClass("hiddenselect")
			}
			overlayer.remove();
			closebtn.remove();
			box.remove();
			opening = false;
			closebtn = overlayer = box = null;
			callback && callback();
			if (d && options.onclose) {
				options.onclose();
				options.onclose = null
			}
		}
	}
})(jQuery);