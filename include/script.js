jQuery.fn.mf_slideshow = function(o)
{
	var dom_obj = this,
		slide_now = parseInt(dom_obj.children('div.active').attr('rel')),
		slide_timeout,
		autoplay = dom_obj.attr('data-autoplay') || script_slideshow.autoplay,
		duration = dom_obj.attr('data-duration') || script_slideshow.duration,
		fade = parseInt(dom_obj.attr('data-fade') || script_slideshow.fade),
		show_controls = dom_obj.attr('data-show_controls') || script_slideshow.show_controls,
		random = dom_obj.attr('data-random') || script_slideshow.random,
		height_ratio = dom_obj.attr('data-height_ratio') || script_slideshow.height_ratio,
		height_ratio_mobile = dom_obj.attr('data-height_ratio_mobile') || script_slideshow.height_ratio_mobile;

	function change_slide(slide_new)
	{
		if(autoplay == 1)
		{
			clearTimeout(slide_timeout);
		}

		slide_new = parseInt(slide_new);

		if(slide_new > slider_amount){		slide_new = 1;}
		else if(slide_new < 1){				slide_new = slider_amount;}

		if(slide_new != slide_now)
		{
			dom_obj.children('div[rel=' + slide_now + ']').fadeOut(fade);
			dom_obj.children('div[rel=' + slide_new + ']').fadeIn(fade);

			dom_obj.find('li[rel=' + slide_new + ']').addClass('active').siblings('li').removeClass('active');
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
		if(jQuery("body.is_mobile, body.is_tablet, body.is_desktop").length > 0 || retry == false)
		{
			var dom_obj_width = parseInt(dom_obj.outerWidth()),
				dom_height = jQuery("body.is_mobile").length > 0 ? dom_obj_width * height_ratio_mobile : dom_obj_width * height_ratio;

			dom_obj.css(
			{
				'height': dom_height + 'px'
			});

			dom_obj.children('div').css(
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

	var slider_amount = dom_obj.children('div').size();

	if(slider_amount > 1)
	{
		if(show_controls != true && show_controls != 'yes')
		{
			dom_obj.find(".controls").addClass('hide');
		}

		dom_obj.children('div').each(function()
		{
			preload(jQuery(this).children('img').attr('src'));
		});

		if(autoplay == 1)
		{
			change_slide(random == 1 ? Math.round(Math.random() * slider_amount) : slide_now);
		}
	}

	dom_obj.find('li').on('click', function()
	{
		change_slide(jQuery(this).attr('rel'));

		return false;
	});

	dom_obj.children('.arrow_left').on('click', function()
	{
		change_slide(slide_now - 1);
	});

	dom_obj.children('.arrow_right').on('click', function()
	{
		change_slide(slide_now + 1);
	});

	jQuery(window).on('resize', function()
	{
		set_slide_sizes(false);
	});
};

function on_load_slideshow()
{
	jQuery('.slideshow.original').mf_slideshow();
}

jQuery(function($)
{
	on_load_slideshow();

	if(typeof collect_on_load == 'function')
	{
		collect_on_load('on_load_slideshow');
	}
});