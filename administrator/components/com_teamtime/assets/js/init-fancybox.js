jQuery(function ($) {

	//hs.graphicsDir = "<?= JURI::root() ?>components/com_teamtime/assets/highslide/graphics/";
	//hs.outlineType = "rounded-white";
	//hs.wrapperClassName = "draggable-header";
	//hs.showCredits = false;
	//hs.width = 740;
	/*hs.dimmingOpacity = 0.4;
        hs.maxWidth = 800;
        hs.maxHeight = 200;
        hs.maxHeight = 600;
        hs.align = "auto";
        hs.allowWidthReduction = true;*/

	$(".fancybox").fancybox({
		type: 'iframe',
		width: 800,
		//height: 750,
		autoSize: false,
		padding : 1
	});

});
