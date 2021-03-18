jQuery(function($)
{
	function set_slide_sizes_flickity(retry)
	{
		if($("body.is_mobile, body.is_tablet, body.is_desktop").length > 0 || retry == false)
		{
			$(".slideshow.flickity").each(function()
			{
				var dom_obj = $(this),
					dom_obj_width = parseInt(dom_obj.outerWidth()),
					height_ratio = dom_obj.attr('data-height_ratio') || script_slideshow_flickity.height_ratio,
					height_ratio_mobile = dom_obj.attr('data-height_ratio_mobile') || script_slideshow_flickity.height_ratio_mobile,
					dom_height = $("body").hasClass('is_mobile') ? dom_obj_width * height_ratio_mobile : dom_obj_width * height_ratio;

				dom_obj.css(
				{
					'height': dom_height + 'px'
				});

				dom_obj.find('.carousel-cell img').css(
				{
					'height': dom_height + 'px',
					'max-height': dom_height + 'px'
				});
			});
		}

		else
		{
			setTimeout(function()
			{
				set_slide_sizes_flickity(false);
			}, 100);
		}
	}

	if($(".slideshow.flickity").length > 0)
	{
		set_slide_sizes_flickity(true);

		$(".slideshow.flickity").each(function()
		{
			var dom_obj = $(this),
				slide_now = dom_obj.children(".carousel-cell div.active").attr('rel'),
				autoplay = dom_obj.attr('data-autoplay') || script_slideshow_flickity.autoplay,
				duration = dom_obj.attr('data-duration') || script_slideshow_flickity.duration,
				display_controls = dom_obj.attr('data-display_controls') || script_slideshow_flickity.display_controls;

			$(this).flickity(
			{
				wrapAround: true,
				/*,cellAlign: 'left'*/
				initialIndex: slide_now,
				/*,contain: true
				,freeScroll: true*/
				autoPlay: (autoplay == 1 ? duration : false),
				prevNextButtons: (display_controls.indexOf('arrows') !== -1),
				pageDots: (display_controls.indexOf('dots') !== -1)
			});
		});

		$(window).on('resize', function()
		{
			set_slide_sizes_flickity(false);
		});
	}
});