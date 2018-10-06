<?php
/*
Plugin Name: MF Slideshow
Plugin URI: https://github.com/frostkom/mf_slideshow
Description: 
Version: 4.3.5
Licence: GPLv2 or later
Author: Martin Fors
Author URI: https://frostkom.se
Text Domain: lang_slideshow
Domain Path: /lang

Depends: Meta Box, MF Base
GitHub Plugin URI: frostkom/mf_slideshow
*/

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

	/*add_filter('count_shortcode_button', array($obj_slideshow, 'count_shortcode_button'));
	add_filter('get_shortcode_output', array($obj_slideshow, 'get_shortcode_output'));
	add_filter('get_shortcode_list', array($obj_slideshow, 'get_shortcode_list'));*/
}

else
{
	add_action('wp_head', array($obj_slideshow, 'wp_head'), 0);
}

add_action('widgets_init', array($obj_slideshow, 'widgets_init'));

load_plugin_textdomain('lang_slideshow', false, dirname(plugin_basename(__FILE__)).'/lang/');

function activate_slideshow()
{
	require_plugin("meta-box/meta-box.php", "Meta Box");

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