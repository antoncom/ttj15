
TeamTime.jQuery(function ($) {

	$(".fancybox").fancybox({
		type: 'iframe',
		width: 800,
		//height: 750,
		autoSize: false,
		padding : 1,
		openEffect: 'none',
		closeEffect: 'none',
		helpers : {
			overlay : {
				css : {
					'background' : 'rgba(0, 0, 0, 0.4)'
				}
			}
		}
	});

});
