jQuery(function($)
{
	$.fn.slideshow = function()
	{
		var dom_obj = this,
			dom_obj_container = dom_obj.children(".slideshow_container"),
			slide_now = parseInt(dom_obj_container.find(".slide_item.active").attr('rel')),
			slide_timeout,
			autoplay = dom_obj.attr('data-autoplay') || script_slideshow.autoplay,
			animate = dom_obj.attr('data-animate') || 'no',
			duration = dom_obj.attr('data-duration') || script_slideshow.duration,
			fade_duration = parseInt(dom_obj.attr('data-fade_duration') || script_slideshow.fade_duration),
			random = dom_obj.attr('data-random') || script_slideshow.random,
			image_columns = dom_obj.attr('data-image_columns') || script_slideshow.image_columns,
			height_ratio = dom_obj.attr('data-height_ratio') || script_slideshow.height_ratio,
			height_ratio_mobile = dom_obj.attr('data-height_ratio_mobile') || script_slideshow.height_ratio_mobile;

		/*function preload(url)
		{
			var img = new Image();
			img.src = url;
		}*/

		function disable_autoplay()
		{
			autoplay = 0;
			clearTimeout(slide_timeout);
		}

		function display_next(slide_next)
		{
			for(var j = 1; j < image_columns; j++)
			{
				slide_next++;

				if(slide_next > slider_amount){		slide_next = 1;}
				else if(slide_next < 1){			slide_next = slider_amount;}

				dom_obj_container.find(".slide_item[rel=" + slide_next + "]").addClass('active').css({'order': (1 + j)});
			}
		}

		function change_slide(slide_new)
		{
			slide_new = parseInt(slide_new);

			if(slide_new > slider_amount){		slide_new = 1;}
			else if(slide_new < 1){				slide_new = slider_amount;}

			var dom_old = dom_obj_container.find(".slide_item[rel=" + slide_now + "]"),
				dom_new = dom_obj_container.find(".slide_item[rel=" + slide_new + "]");

			if(autoplay == 1)
			{
				clearTimeout(slide_timeout);
			}

			if(slide_new != slide_now)
			{
				/* Fade Out Content */
				if(dom_old.children(".content").length > 0)
				{
					dom_old.children(".content").fadeOut(fade_duration / 2, function()
					{
						if(image_columns == 1)
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

								dom_new.addClass('active').siblings(".active").removeClass('active');
							});
						}

						else
						{
							/* Fade In Content */
							dom_new.children(".content").fadeIn(fade_duration / 2);

							/* Display Container */
							dom_new.addClass('active').css({'order': 1}).siblings(".active").removeClass('active').css({'order': 'unset'});

							display_next(slide_new);
						}
					});
				}

				else
				{
					if(image_columns == 1)
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
							if(animate == 'yes')
							{
								dom_new.removeClass("animate");
							}

							/* Display Container */
							dom_new.addClass('active').siblings(".active").removeClass('active');
						});
					}

					else
					{
						/* Display Container */
						dom_new.addClass('active').css({'order': 1}).siblings(".active").removeClass('active').css({'order': 'unset'});

						display_next(slide_new);
					}
				}

				/* Highlight Dot */
				dom_obj.find("li[rel=" + slide_new + "]").addClass('active').siblings("li").removeClass('active');
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
				var dom_obj_width = parseInt(dom_obj_container.outerWidth()),
					dom_height = ($("body.is_mobile").length > 0 ? dom_obj_width * height_ratio_mobile : dom_obj_width * height_ratio);

				if(image_columns > 0)
				{
					dom_height /= image_columns;
				}

				dom_obj_container.css(
				{
					'height': dom_height + 'px'
				});

				dom_obj_container.find(".slide_item").css(
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

		$(window).on('resize', function()
		{
			set_slide_sizes(false);
		});

		var slider_amount = dom_obj_container.find(".slide_item").length;

		if(slider_amount > 1)
		{
			/*if(dom_obj_container.is(":visible"))
			{
				dom_obj_container.find(".slide_item").each(function()
				{
					preload($(this).children("img").attr('src'));
				});
			}*/

			if(autoplay == 1)
			{
				change_slide(random == 1 ? Math.round(Math.random() * slider_amount) : slide_now);
			}
		}

		dom_obj.find("li").on('click', function()
		{
			disable_autoplay();

			change_slide($(this).attr('rel'));

			if($(this).parent(".slideshow_thumbnails").length > 0)
			{
				$("html, body").animate({scrollTop: dom_obj_container.offset().top}, 800);
			}

			return false;
		});

		dom_obj_container.find(".panel_arrow_left").on('click', function()
		{
			disable_autoplay();

			change_slide(slide_now - 1);
		});

		dom_obj_container.find(".panel_arrow_right").on('click', function()
		{
			disable_autoplay();

			change_slide(slide_now + 1);
		});

		dom_obj_container.swipe("destroy").swipe(
		{
			swipeStatus: function(event, phase, direction, distance)
			{
				if(direction == 'left' || direction == 'right')
				{
					switch(phase)
					{
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
			fingers: 1,
			allowPageScroll: 'vertical'
		});

		dom_obj_container.children(".controls_magnifying_glass").on('click', function()
		{
			disable_autoplay();

			if($("#wrapper").length > 0)
			{
				var dom_obj = $(this).siblings("div.active"),
					dom_overlay = $("#overlay_slideshow > div");

				if(dom_overlay.length == 0)
				{
					$("#wrapper").append("<div id='overlay_slideshow' class='overlay_container modal'><div></div></div>");

					dom_overlay = $("#overlay_slideshow > div");
				}

				/*if(dom_overlay.children().length > 0)
				{
					dom_overlay.html('').parent("#overlay_slideshow").fadeOut();
				}

				else
				{*/
					dom_overlay.html("<i class='fa fa-times fa-2x'></i>" + dom_obj.html()).parent("#overlay_slideshow").fadeIn();
				/*}*/
			}

			return false;
		});

		function hide_form_overlay()
		{
			$("#overlay_slideshow").fadeOut().children("div").html('');
		}

		$(document).on('click', "#overlay_slideshow", function(e)
		{
			if(e.target == e.currentTarget)
			{
				hide_form_overlay();
			}
		});

		$(document).on('click', "#overlay_slideshow .fa-times", function()
		{
			hide_form_overlay();
		});
	};

	$(".slideshow.original").each(function()
	{
		$(this).slideshow();
	});
});