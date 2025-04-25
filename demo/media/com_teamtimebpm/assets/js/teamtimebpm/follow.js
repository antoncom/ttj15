
TeamTime.jQuery(function ($) {

	$(".followingButton").click(function () {
		var follow = $(this).hasClass("followingButton-isFollowed")? 0 : 1;
		var self = this;

		$.post(TeamTime.getUrlForTask("follow"), {
			follow: follow,
			id: $(this).closest(".itemSummary").attr("data-id")
		},
		function (data) {
			if (follow) {
				$(self).addClass("followingButton-isFollowed")
				.removeClass("followingButton-isNotFollowed");
			}
			else {
				$(self).addClass("followingButton-isNotFollowed")
				.removeClass("followingButton-isFollowed");
			}
		});
	});

});