jQuery(function($)
{
	$.fn.slideshow = function()
	{
		var dom_obj = this,
			dom_obj_container = dom_obj.children(".slideshow_container"),
			dom_obj_slide_items = dom_obj_container.find(".slide_item"),
			slider_amount = dom_obj_slide_items.length,
			slide_now = parseInt(dom_obj_container.find(".slide_item.active").attr('rel')),
			slide_timeout,
			setting_autoplay = dom_obj.attr('data-autoplay') || script_slideshow.autoplay,
			setting_animate = dom_obj.attr('data-animate') || 'no',
			setting_duration = dom_obj.attr('data-duration') || script_slideshow.duration,
			setting_fade_duration = parseInt(dom_obj.attr('data-fade_duration') || script_slideshow.fade_duration),
			setting_random = dom_obj.attr('data-random') || script_slideshow.random,
			setting_image_columns = dom_obj.attr('data-image_columns') || script_slideshow.image_columns,
			setting_image_columns_orig = setting_image_columns,
			setting_image_steps = parseInt(dom_obj.attr('data-image_steps') || script_slideshow.image_steps),
			setting_height_ratio = dom_obj.attr('data-height_ratio') || script_slideshow.height_ratio,
			setting_height_ratio_mobile = dom_obj.attr('data-height_ratio_mobile') || script_slideshow.height_ratio_mobile;

		/*function preload(url)
		{
			var img = new Image();
			img.src = url;
		}*/

		function disable_autoplay()
		{
			setting_autoplay = 0;
			clearTimeout(slide_timeout);
		}

		function get_slide_no(slide_no)
		{
			slide_no = parseInt(slide_no);

			if(slide_no > slider_amount)
			{
				slide_no = (slide_no - slider_amount);
			}

			else if(slide_no < 1)
			{
				slide_no = (slider_amount + slide_no);
			}

			return slide_no;
		}

		function find_slide_item(slide_no)
		{
			return dom_obj_container.find(".slide_item[rel=" + slide_no + "]");
		}

		function fade_in_content(dom_new, slide_no)
		{
			dom_new.children(".content").fadeIn(setting_fade_duration / 2);

			display_container(dom_new, slide_no);
		}

		function fade_out_container(dom_old)
		{
			dom_old.fadeOut(setting_fade_duration, function()
			{
				if(setting_animate == 'yes')
				{
					dom_old.addClass("animate");
				}
			});
		}

		function fade_in_container(dom_new, slide_no)
		{
			dom_new.fadeIn(setting_fade_duration, function()
			{
				fade_in_content(dom_new, slide_no);
			});
		}

		function display_container(dom_new, slide_no)
		{
			if(setting_animate == 'yes')
			{
				dom_new.removeClass("animate");
			}

			dom_obj_container.children(".slideshow_images").children(".slide_item").removeClass('active active_init').css({'order': 'unset'});

			for(var j = 0; j < setting_image_columns; j++)
			{
				slide_next = get_slide_no(slide_no + j);

				find_slide_item(slide_next).addClass('active').css({'order': (1 + j)});
			}

			slide_now = slide_no;
		}

		function highlight_controls(type_selector, slide_no)
		{
			var dom_obj_parent = dom_obj.find(type_selector);

			if(dom_obj_parent.length > 0)
			{
				dom_obj_parent.children("li").removeClass('active');

				for(var j = 0; j < setting_image_columns; j++)
				{
					var slide_next = get_slide_no(slide_no + j);

					dom_obj_parent.children("li[rel=" + slide_next + "]").addClass('active');
				}
			}
		}

		function change_slide(slide_new)
		{
			slide_new = get_slide_no(slide_new);

			if(typeof filter_slide_no === 'function')
			{
				var filter_result = filter_slide_no(slide_new, setting_image_columns);

				slide_new = filter_result.slide_new;
				setting_image_columns = filter_result.setting_image_columns;
			}

			var dom_old = find_slide_item(slide_now),
				dom_new = find_slide_item(slide_new);

			if(setting_animate == 'yes')
			{
				dom_old.removeClass("animate");
			}

			if(setting_autoplay == 1)
			{
				clearTimeout(slide_timeout);
			}

			if(slide_new != slide_now)
			{
				if(dom_old.children(".content").length > 0)
				{
					/* Fade Out Content */
					dom_old.children(".content").fadeOut((setting_fade_duration / 2), function()
					{
						if(setting_image_columns_orig == 1)
						{
							fade_out_container(dom_old);

							fade_in_container(dom_new, slide_new);
						}

						else
						{
							fade_in_content(dom_new, slide_new);
						}
					});
				}

				else
				{
					if(setting_image_columns_orig == 1)
					{
						fade_out_container(dom_old);

						fade_in_container(dom_new, slide_new);
					}

					else
					{
						display_container(dom_new, slide_new);
					}
				}

				highlight_controls(".controls_dots", slide_new);
				highlight_controls(".slideshow_thumbnails", slide_new);
			}

			if(setting_autoplay == 1)
			{
				slide_timeout = setTimeout(function()
				{
					change_slide(slide_now + setting_image_steps);
				}, setting_duration);
			}
		}

		function set_slide_sizes(retry)
		{
			if($("body.is_mobile, body.is_tablet, body.is_desktop").length > 0 || retry == false)
			{
				var dom_obj_width = parseInt(dom_obj_container.outerWidth()),
					dom_height = (dom_obj_width * ($("body.is_mobile").length > 0 ? setting_height_ratio_mobile : setting_height_ratio));

				if(setting_image_columns_orig > 0)
				{
					dom_height /= setting_image_columns_orig;
				}

				dom_obj_container.css(
				{
					'height': dom_height + 'px'
				});

				dom_obj_slide_items.css(
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

		if(slider_amount > 1)
		{
			/*if(dom_obj_container.is(":visible"))
			{
				dom_obj_slide_items.each(function()
				{
					preload($(this).children("img").attr('src'));
				});
			}*/

			if(setting_autoplay == 1)
			{
				change_slide(setting_random == 1 ? Math.round(Math.random() * slider_amount) : slide_now);
			}
		}

		dom_obj.find(".slideshow_thumbnails, .controls_dots").children("li").on('click', function()
		{
			disable_autoplay();

			change_slide($(this).attr('rel'));

			$("html, body").animate({scrollTop: dom_obj_container.offset().top}, 800);

			return false;
		});

		dom_obj_container.find(".panel_arrow_left").on('click', function()
		{
			disable_autoplay();

			change_slide(slide_now - setting_image_steps);
		});

		dom_obj_container.find(".panel_arrow_right").on('click', function()
		{
			disable_autoplay();

			change_slide(slide_now + setting_image_steps);
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
								change_slide(slide_now - setting_image_steps);
							}

							else
							{
								change_slide(slide_now + setting_image_steps);
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

				dom_overlay.html("<i class='fa fa-times fa-2x'></i>" + dom_obj.html()).parent("#overlay_slideshow").fadeIn();
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