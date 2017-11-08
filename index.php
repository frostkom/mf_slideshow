<?php
/*
Plugin Name: MF Slideshow
Plugin URI: https://github.com/frostkom/mf_slideshow
Description: 
Version: 4.1.3
Author: Martin Fors
Author URI: http://frostkom.se
Text Domain: lang_slideshow
Domain Path: /lang

GitHub Plugin URI: frostkom/mf_slideshow
*/

include_once("include/classes.php");
include_once("include/functions.php");

add_action('cron_base', 'activate_slideshow', mt_rand(1, 10));

add_action('widgets_init', 'widgets_slideshow');

add_action('init', 'init_slideshow');

if(is_admin())
{
	register_activation_hook(__FILE__, 'activate_slideshow');
	register_uninstall_hook(__FILE__, 'uninstall_slideshow');

	add_action('admin_init', 'settings_slideshow');
	add_action('rwmb_meta_boxes', 'meta_boxes_slideshow');
}

load_plugin_textdomain('lang_slideshow', false, dirname(plugin_basename(__FILE__)).'/lang/');

function activate_slideshow()
{
	if(is_admin())
	{
		require_plugin("meta-box/meta-box.php", "Meta Box");
	}

	replace_option(array('old' => 'settings_random', 'new' => 'setting_slideshow_random'));
	replace_option(array('old' => 'settings_background_color', 'new' => 'setting_slideshow_background_color'));
	replace_option(array('old' => 'settings_autoplay', 'new' => 'setting_slideshow_autoplay'));
	replace_option(array('old' => 'setting_height_ratio', 'new' => 'setting_slideshow_height_ratio'));
	replace_option(array('old' => 'setting_show_controls', 'new' => 'setting_slideshow_show_controls'));
}

function uninstall_slideshow()
{
	mf_uninstall_plugin(array(
		'options' => array('setting_slideshow_style', 'setting_slideshow_background_color', 'setting_slideshow_height_ratio', 'setting_slideshow_height_ratio_mobile', 'setting_slideshow_show_controls', 'setting_slideshow_autoplay', 'setting_slideshow_duration', 'setting_slideshow_fade_duration', 'setting_slideshow_random'),
		'post_types' => array('slideshow'),
	));
}