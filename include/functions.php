<?php

function init_slideshow()
{
	$labels = array(
		'name' => _x(__("Slideshows", 'lang_slideshow'), 'post type general name'),
		'singular_name' => _x(__("Slideshow", 'lang_slideshow'), 'post type singular name'),
		'menu_name' => __("Slideshow", 'lang_slideshow')
	);

	$args = array(
		'labels' => $labels,
		'public' => false,
		'show_ui' => true,
		'exclude_from_search' => true,
		'menu_position' => 99,
		'menu_icon' => 'dashicons-format-gallery',
		'supports' => array('title', 'editor', 'page-attributes'),
		'hierarchical' => true,
		'has_archive' => false,
	);

	register_post_type('slideshow', $args);

	if(!is_admin())
	{
		$setting_slideshow_style = get_option_or_default('setting_slideshow_style', array('original'));
		$setting_autoplay = get_option('setting_slideshow_autoplay');
		$setting_duration = get_option_or_default('setting_slideshow_duration', 5);
		$setting_fade_duration = get_option_or_default('setting_slideshow_fade_duration', 400);
		$setting_random = get_option('setting_slideshow_random');
		$setting_height_ratio = get_option('setting_slideshow_height_ratio', '0.5');
		$setting_height_ratio_mobile = get_option('setting_slideshow_height_ratio_mobile', '1');
		$setting_show_controls = get_option('setting_slideshow_show_controls');

		$setting_height_ratio = str_replace(",", ".", $setting_height_ratio);
		$setting_height_ratio_mobile = str_replace(",", ".", $setting_height_ratio_mobile);

		if($setting_height_ratio > 2 || $setting_height_ratio < 0.2)
		{
			$setting_height_ratio = 1;
		}

		if($setting_height_ratio_mobile > 2 || $setting_height_ratio_mobile < 0.2)
		{
			$setting_height_ratio_mobile = 1;
		}

		$arr_settings = array(
			'autoplay' => $setting_autoplay,
			'duration' => ($setting_duration * 1000),
			'fade' => $setting_fade_duration,
			'show_controls' => $setting_show_controls,
			'random' => $setting_random,
			'height_ratio' => $setting_height_ratio,
			'height_ratio_mobile' => $setting_height_ratio_mobile,
		);

		$plugin_include_url = plugin_dir_url(__FILE__);
		$plugin_version = get_plugin_version(__FILE__);

		if(in_array('original', $setting_slideshow_style))
		{
			mf_enqueue_style('style_slideshow', $plugin_include_url."style.css", $plugin_version);
			mf_enqueue_script('script_swipe', $plugin_include_url."lib/jquery.touchSwipe.min.js", $plugin_version);
			mf_enqueue_script('script_slideshow', $plugin_include_url."script.js", $arr_settings, $plugin_version);
		}

		if(in_array('flickity', $setting_slideshow_style))
		{
			mf_enqueue_style('style_flickity', $plugin_include_url."lib/flickity.min.css", $plugin_version);
			mf_enqueue_style('style_slideshow_flickity', $plugin_include_url."style_flickity.css", $plugin_version);
			mf_enqueue_script('script_flickity', $plugin_include_url."lib/flickity.pkgd.min.js", $plugin_version);
			mf_enqueue_script('script_slideshow_flickity', $plugin_include_url."script_flickity.js", $arr_settings, $plugin_version);
		}

		if(in_array('carousel', $setting_slideshow_style))
		{
			mf_enqueue_style('style_slideshow_carousel', $plugin_include_url."style_carousel.css", $plugin_version);
			mf_enqueue_script('script_slideshow_carousel', $plugin_include_url."script_carousel.js", $arr_settings, $plugin_version);
		}
	}
}

function settings_slideshow()
{
	$options_area = __FUNCTION__;

	add_settings_section($options_area, "", $options_area."_callback", BASE_OPTIONS_PAGE);

	$arr_settings = array(
		'setting_slideshow_style' => __("Style", 'lang_slideshow'),
	);

	$arr_settings['setting_slideshow_background_color'] = __("Background Color", 'lang_slideshow');
	$arr_settings['setting_slideshow_height_ratio'] = __("Height Ratio", 'lang_slideshow');
	$arr_settings['setting_slideshow_height_ratio_mobile'] = __("Height Ratio", 'lang_slideshow')." (".__("Mobile", 'lang_slideshow').")";

	$arr_settings['setting_slideshow_show_controls'] = __("Show Controls", 'lang_slideshow');
	$arr_settings['setting_slideshow_autoplay'] = __("Autoplay", 'lang_slideshow');

	if(get_option('setting_slideshow_autoplay') == 1)
	{
		$arr_settings['setting_slideshow_duration'] = __("Duration", 'lang_slideshow');
	}

	if(in_array('original', get_option('setting_slideshow_style', array('original'))))
	{
		$arr_settings['setting_slideshow_fade_duration'] = __("Fade Duration", 'lang_slideshow');
		$arr_settings['setting_slideshow_random'] = __("Random", 'lang_slideshow');
	}

	show_settings_fields(array('area' => $options_area, 'settings' => $arr_settings));
}

function settings_slideshow_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);

	echo settings_header($setting_key, __("Slideshow", 'lang_slideshow'));
}

function get_slideshow_styles_for_select($data = array())
{
	if(!isset($data['styles'])){	$data['styles'] = get_option('setting_slideshow_style', array('original'));}

	$arr_data = array(
		'original' => __("Original", 'lang_slideshow'),
	);

	if(in_array('flickity', $data['styles']))
	{
		$arr_data['flickity'] = __("Flickity", 'lang_slideshow')." (".__("beta", 'lang_slideshow').")";
	}

	if(in_array('carousel', $data['styles']))
	{
		$arr_data['carousel'] = __("Carousel", 'lang_slideshow')." (".__("beta", 'lang_slideshow').")";
	}

	return $arr_data;
}

function setting_slideshow_style_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);
	$option = get_option($setting_key, array('original'));

	echo show_select(array('data' => get_slideshow_styles_for_select(array('styles' => array('original', 'flickity', 'carousel'))), 'name' => $setting_key."[]", 'value' => $option));
}

function setting_slideshow_autoplay_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);
	$option = get_option($setting_key);

	echo show_select(array('data' => get_yes_no_for_select(array('return_integer' => true)), 'name' => $setting_key, 'value' => $option));
}

function setting_slideshow_duration_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);
	$option = get_option($setting_key, 5);

	echo show_textfield(array('type' => 'number', 'name' => $setting_key, 'value' => $option, 'xtra' => "min='2'", 'suffix' => __("s", 'lang_slideshow')));
}

function setting_slideshow_fade_duration_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);
	$option = get_option($setting_key, 400);

	echo show_textfield(array('type' => 'number', 'name' => $setting_key, 'value' => $option, 'xtra' => "min='400' max='2000'", 'suffix' => __("ms", 'lang_slideshow')));
}

function setting_slideshow_background_color_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);
	$option = get_option($setting_key, "#000000");

	echo show_textfield(array('name' => $setting_key, 'value' => $option, 'type' => 'color'));
}

function setting_slideshow_random_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);
	$option = get_option($setting_key);

	echo show_select(array('data' => get_yes_no_for_select(array('return_integer' => true)), 'name' => $setting_key, 'value' => $option));
}

function setting_slideshow_show_controls_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);
	$option = get_option($setting_key, 1);

	echo show_select(array('data' => get_yes_no_for_select(array('return_integer' => true)), 'name' => $setting_key, 'value' => $option));
}

function setting_slideshow_height_ratio_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);
	$option = get_option($setting_key, '1');

	echo show_textfield(array('name' => $setting_key, 'value' => $option, 'description' => __("From 0,2 to 2. 0,2 means the slideshow will be presented in landscape, 1 means square format and 2 means the slideshow i presented in portrait", 'lang_slideshow')));
}

function setting_slideshow_height_ratio_mobile_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);
	$option = get_option($setting_key);

	echo show_textfield(array('name' => $setting_key, 'value' => $option));
}

function widgets_slideshow()
{
	register_widget('widget_slideshow');
}

function meta_boxes_slideshow($meta_boxes)
{
	$obj_slideshow = new mf_slideshow();

	$meta_boxes[] = array(
		'id' => 'settings',
		'title' => __("Settings", 'lang_slideshow'),
		'post_types' => array('slideshow'),
		'context' => 'side',
		'priority' => 'low',
		'fields' => array(
			/*array(
				'name' => __("Height", 'lang_slideshow'),
				'id' => $obj_slideshow->meta_prefix.'height',
				'type' => 'number',
				'min'  => 1,
			),*/
			array(
				'name' => __("Content Position", 'lang_slideshow'),
				'id' => $obj_slideshow->meta_prefix.'content_position',
				'type' => 'select',
				'options' => array(
					'' => "-- ".__("Choose here", 'lang_slideshow')." --",
					'left' => __("Left", 'lang_slideshow'),
					'center' => __("Center", 'lang_slideshow'),
					'right' => __("Right", 'lang_slideshow'),
				),
				'std' => 'center',
			),
			array(
				'name' => __("Page", 'lang_slideshow'),
				'id' => $obj_slideshow->meta_prefix.'page',
				'type' => 'select',
				'options' => get_posts_for_select(array('add_choose_here' => true, 'optgroup' => false)),
				'attributes' => array(
					'condition_type' => 'show_if',
					'condition_field' => $obj_slideshow->meta_prefix.'link',
				),
			),
			array(
				'name' => __("External Link", 'lang_slideshow'),
				'id' => $obj_slideshow->meta_prefix.'link',
				'type' => 'url',
				'attributes' => array(
					'condition_type' => 'show_if',
					'condition_field' => $obj_slideshow->meta_prefix.'page',
				),
			),
		)
	);

	$meta_boxes[] = array(
		'id' => 'images',
		'title' => __("Images", 'lang_slideshow'),
		'post_types' => array('slideshow'),
		//'context' => 'side',
		'priority' => 'high',
		'fields' => array(
			array(
				'id' => $obj_slideshow->meta_prefix.'images',
				'type' => 'file_advanced',
			)
		)
	);

	return $meta_boxes;
}