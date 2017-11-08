function set_slide_sizes_carousel(retry)
{
	if(jQuery("body.is_mobile, body.is_tablet, body.is_desktop").length > 0 || retry == false)
	{
		jQuery(".slideshow.carousel").each(function()
		{
			var dom_obj = jQuery(this),
				dom_obj_parent = jQuery(this).parent(".slideshow_wrapper"),
				dom_obj_width = parseInt(dom_obj_parent.outerWidth()),
				height_ratio = dom_obj.attr('data-height_ratio') || script_slideshow_carousel.height_ratio,
				height_ratio_mobile = dom_obj.attr('data-height_ratio_mobile') || script_slideshow_carousel.height_ratio_mobile,
				dom_height = jQuery("body").hasClass('is_mobile') ? dom_obj_width * height_ratio_mobile : dom_obj_width * height_ratio;

			dom_obj_parent.css(
			{
				'height': dom_height + 'px'
			});			

			dom_obj.css(
			{
				'height': dom_height + 'px',
				'perspective': (dom_obj_width * 4) + 'px',
				'width': dom_obj_width + 'px'
			});

			var size_factor = 1;

			dom_obj.find('.item').css(
			{
				'height': (dom_height * size_factor) + 'px',
				/*'line-height': (dom_height) + 'px',*/
				'width': (dom_obj_width * size_factor) + 'px'
			});
		});
	}

	else
	{
		setTimeout(function()
		{
			set_slide_sizes_carousel(false);
		}, 100);
	}
}

function on_load_carousel()
{
	if(jQuery(".slideshow.carousel").length > 0)
	{
		set_slide_sizes_carousel(true);

		jQuery(".slideshow.carousel").each(function()
		{
			var dom_obj = jQuery(this),
				carousel = dom_obj.children("div"),
				currdeg = 0,
				item_amount = carousel.children(".item").length,
				item_separation = (360 / item_amount),
				height_ratio = dom_obj.attr('data-height_ratio') || script_slideshow_carousel.height_ratio,
				height_ratio_mobile = dom_obj.attr('data-height_ratio_mobile') || script_slideshow_carousel.height_ratio_mobile;

			/*var slide_now = dom_obj.children(".gallery-img div.active").attr('rel'),
				show_controls = dom_obj.attr('data-show_controls') || script_slideshow_carousel.show_controls;*/

			var count_deg = 0;

			carousel.children(".item").each(function()
			{
				jQuery(this).css(
				{
					'-webkit-transform': "rotateY(" + count_deg + "deg) translateZ(-750px)",
					'-moz-transform': "rotateY(" + count_deg + "deg) translateZ(-750px)",
					'-o-transform': "rotateY(" + count_deg + "deg) translateZ(-750px)",
					'transform': "rotateY(" + count_deg + "deg) translateZ(-750px)",
				});

				count_deg += item_separation;
			});

			function rotate(e)
			{
				if(e.data.d == "n")
				{
					currdeg -= item_separation;
				}

				if(e.data.d == "p")
				{
					currdeg += item_separation;
				}

				carousel.css(
				{
					'-webkit-transform': "rotateY(" + currdeg + "deg)",
					'-moz-transform': "rotateY(" + currdeg + "deg)",
					'-o-transform': "rotateY(" + currdeg + "deg)",
					'transform': "rotateY(" + currdeg + "deg)"
				});
			}

			jQuery(".next").on("click", { d: "n" }, rotate);
			jQuery(".prev").on("click", { d: "p" }, rotate);
		});

		jQuery(window).on('resize', function()
		{
			set_slide_sizes_carousel(false);
		});
	}
}

jQuery(function($)
{
	on_load_carousel();

	if(typeof collect_on_load == 'function')
	{
		collect_on_load('on_load_carousel');
	}
});