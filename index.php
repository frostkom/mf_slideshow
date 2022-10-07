<?php
/*
Plugin Name: MF Slideshow
Plugin URI: https://github.com/frostkom/mf_slideshow
Description:
Version: 4.8.15
Licence: GPLv2 or later
Author: Martin Fors
Author URI: https://frostkom.se
Text Domain: lang_slideshow
Domain Path: /lang

Depends: Meta Box, MF Base
GitHub Plugin URI: frostkom/mf_slideshow
*/

if(!function_exists('is_plugin_active') || function_exists('is_plugin_active') && is_plugin_active("mf_base/index.php"))
{
	include_once("include/classes.php");

	$obj_slideshow = new mf_slideshow();

	add_action('cron_base', 'activate_slideshow', mt_rand(1, 10));

	add_action('init', array($obj_slideshow, 'init'));

	if(is_admin())
	{
		register_activation_hook(__FILE__, 'activate_slideshow');
		register_uninstall_hook(__FILE__, 'uninstall_slideshow');

		add_action('admin_init', array($obj_slideshow, 'settings_slideshow'));
		add_action('rwmb_meta_boxes', array($obj_slideshow, 'rwmb_meta_boxes'));
	}

	else
	{
		add_action('wp_head', array($obj_slideshow, 'wp_head'), 0);

		add_shortcode('mf_slideshow', array($obj_slideshow, 'shortcode_slideshow'));
	}

	add_filter('filter_is_file_used', array($obj_slideshow, 'filter_is_file_used'));

	add_action('widgets_init', array($obj_slideshow, 'widgets_init'));

	load_plugin_textdomain('lang_slideshow', false, dirname(plugin_basename(__FILE__))."/lang/");

	function activate_slideshow()
	{
		require_plugin("meta-box/meta-box.php", "Meta Box");

		// Add later
		/*mf_uninstall_plugin(array(
			'options' => array('setting_slideshow_show_controls', 'setting_slideshow_display_thumbnails'),
		));*/
	}

	function uninstall_slideshow()
	{
		include_once("include/classes.php");

		$obj_slideshow = new mf_slideshow();

		mf_uninstall_plugin(array(
			'options' => array('setting_slideshow_style', 'setting_slideshow_allow_widget_override', 'setting_slideshow_background_color', 'setting_slideshow_display_text_background', 'setting_slideshow_image_columns', 'setting_slideshow_image_steps', 'setting_slideshow_height_ratio', 'setting_slideshow_height_ratio_mobile', 'setting_slideshow_display_controls', 'setting_slideshow_thumbnail_columns', 'setting_slideshow_thumbnail_rows', 'setting_slideshow_autoplay', 'setting_slideshow_animate', 'setting_slideshow_duration', 'setting_slideshow_fade_duration', 'setting_slideshow_random', 'setting_slideshow_open_links_in_new_tab', 'setting_slideshow_show_controls', 'setting_slideshow_display_thumbnails'),
			'post_types' => array($obj_slideshow->post_type),
		));
	}
}