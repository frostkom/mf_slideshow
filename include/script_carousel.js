function on_load_carousel()
{
	jQuery(".slideshow.carousel").each(function()
	{
		var dom_obj = jQuery(this),
			carousel = dom_obj.children("div"),
			currdeg = 0;

		/*var initialIndex = dom_obj.children(".gallery-img div.active").attr('rel'),
			autoPlay = dom_obj.attr('data-autoplay') || script_slideshow_carousel.autoplay,
			duration = dom_obj.attr('data-duration') || script_slideshow_carousel.duration,
			showControls = dom_obj.attr('data-show_controls') || script_slideshow_carousel.show_controls;*/

		function rotate(e)
		{
			if(e.data.d == "n")
			{
				currdeg = currdeg - 60;
			}

			if(e.data.d == "p")
			{
				currdeg = currdeg + 60;
			}

			carousel.css(
			{
				"-webkit-transform": "rotateY(" + currdeg + "deg)",
				"-moz-transform": "rotateY(" + currdeg + "deg)",
				"-o-transform": "rotateY(" + currdeg + "deg)",
				"transform": "rotateY(" + currdeg + "deg)"
			});
		}

		carousel.children(".item").each(function()
		{
			console.log("Item");
		});

		jQuery(".next").on("click", { d: "n" }, rotate);
		jQuery(".prev").on("click", { d: "p" }, rotate);
	});
}

jQuery(function($)
{
	on_load_carousel();

	if(typeof collect_on_load == 'function')
	{
		collect_on_load('on_load_carousel');
	}
});