jQuery(function($)
{
	$(document).on('click', ".slideshow.mosaic > div", function()
	{
		var dom_obj = $(this);

		dom_obj.toggleClass('active').siblings("div").toggleClass('hide');
		dom_obj.parent(".slideshow.mosaic").toggleClass('has_active');

		$("html, body").animate({scrollTop: (dom_obj.offset().top - 50)}, 800);
	});
});