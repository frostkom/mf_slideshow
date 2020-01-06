jQuery(function($)
{
	$.fn.slideshow = function()
	{
		var dom_obj = this,
			slide_now = parseInt(dom_obj.children("div.active").attr('rel')),
			slide_timeout,
			autoplay = dom_obj.attr('data-autoplay') || script_slideshow.autoplay,
			animate = dom_obj.attr('data-animate') || 'no', /*script_slideshow.animate*/
			duration = dom_obj.attr('data-duration') || script_slideshow.duration,
			fade_duration = parseInt(dom_obj.attr('data-fade_duration') || script_slideshow.fade_duration),
			show_controls = dom_obj.attr('data-show_controls') || script_slideshow.show_controls,
			random = dom_obj.attr('data-random') || script_slideshow.random,
			height_ratio = dom_obj.attr('data-height_ratio') || script_slideshow.height_ratio,
			height_ratio_mobile = dom_obj.attr('data-height_ratio_mobile') || script_slideshow.height_ratio_mobile;

		function preload(url)
		{
			var img = new Image();
			img.src = url;
		}

		function disable_autoplay()
		{
			autoplay = 0;
			clearTimeout(slide_timeout);
		}

		function change_slide(slide_new)
		{
			slide_new = parseInt(slide_new);

			if(slide_new > slider_amount){		slide_new = 1;}
			else if(slide_new < 1){				slide_new = slider_amount;}

			var dom_old = dom_obj.children("div[rel=" + slide_now + "]"),
				dom_new = dom_obj.children("div[rel=" + slide_new + "]");

			if(autoplay == 1)
			{
				clearTimeout(slide_timeout);
			}

			if(slide_new != slide_now)
			{
				/* Fade Out Content */
				dom_old.children(".content").fadeOut(fade_duration / 2, function()
				{
					/* Fade Out Container */
					dom_old.fadeOut(fade_duration, function()
					{
						if(animate == 'yes')
						{
							dom_old.addClass("animate");
						}
					});

					/* Fade In Container */
					dom_new.fadeIn(fade_duration, function()
					{
						/* Fade In Content */
						dom_new.children(".content").fadeIn(fade_duration / 2);

						if(animate == 'yes')
						{
							dom_new.removeClass("animate");
						}
					});

					dom_obj.find("li[rel=" + slide_new + "]").addClass('active').siblings("li").removeClass('active');
				});
			}

			slide_now = slide_new;

			if(autoplay == 1)
			{
				slide_timeout = setTimeout(function()
				{
					change_slide(slide_now + 1);
				}, duration);
			}
		}

		function set_slide_sizes(retry)
		{
			if($("body.is_mobile, body.is_tablet, body.is_desktop").length > 0 || retry == false)
			{
				var dom_obj_width = parseInt(dom_obj.outerWidth()),
					dom_height = $("body.is_mobile").length > 0 ? dom_obj_width * height_ratio_mobile : dom_obj_width * height_ratio;

				dom_obj.css(
				{
					'height': dom_height + 'px'
				});

				dom_obj.children("div").css(
				{
					'height': dom_height + 'px'
				});
			}

			else
			{
				setTimeout(function()
				{
					set_slide_sizes(false);
				}, 100);
			}
		}

		set_slide_sizes(true);

		var slider_amount = dom_obj.children("div").size();

		if(slider_amount > 1)
		{
			switch(show_controls)
			{
				case 'all':
				case 1:
				case '1':
					/* Don't hide controls */
				break;

				case 'arrows':
					dom_obj.find("ul.controls").addClass('hide');
				break;

				case 'dots':
					dom_obj.find("i.controls").addClass('hide');
				break;

				case 'none':
				default:
					dom_obj.find(".controls").addClass('hide');
				break;
			}

			if(dom_obj.is(":visible"))
			{
				dom_obj.children("div").each(function()
				{
					preload($(this).children("img").attr('src'));
				});
			}

			if(autoplay == 1)
			{
				change_slide(random == 1 ? Math.round(Math.random() * slider_amount) : slide_now);
			}
		}

		dom_obj.find("li").on('click', function()
		{
			disable_autoplay();

			change_slide($(this).attr('rel'));

			return false;
		});

		dom_obj.children(".arrow_left").on('click', function()
		{
			disable_autoplay();

			change_slide(slide_now - 1);
		});

		dom_obj.children(".arrow_right").on('click', function()
		{
			disable_autoplay();

			change_slide(slide_now + 1);
		});

		$(window).on('resize', function()
		{
			set_slide_sizes(false);
		});

		dom_obj.swipe("destroy").swipe(
		{
			swipeStatus: function(event, phase, direction, distance)
			{
				if(direction == 'left' || direction == 'right')
				{
					switch(phase)
					{
						/*case 'start': break;
						case 'move': break;*/

						case 'end':
							if(direction == 'left')
							{
								change_slide(slide_now - 1);
							}

							else
							{
								change_slide(slide_now + 1);
							}
						break;
					}
				}
			},
			/*threshold: threshold,*/
			fingers: 1,
			allowPageScroll: 'vertical'
		});
	};

	$(".slideshow.original").each(function()
	{
		$(this).slideshow();
	});
});