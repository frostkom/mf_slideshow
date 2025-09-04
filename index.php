<?php
/*
Plugin Name: MF Slideshow
Plugin URI: https://github.com/frostkom/mf_slideshow
Description:
Version: 4.11.6
Licence: GPLv2 or later
Author: Martin Fors
Author URI: https://martinfors.se
Text Domain: lang_slideshow
Domain Path: /lang

Requires Plugins: meta-box
*/

if(!function_exists('is_plugin_active') || function_exists('is_plugin_active') && is_plugin_active("mf_base/index.php"))
{
	include_once("include/classes.php");

	$obj_slideshow = new mf_slideshow();

	add_action('cron_base', array($obj_slideshow, 'cron_base'), mt_rand(1, 10));

	add_action('enqueue_block_editor_assets', array($obj_slideshow, 'enqueue_block_editor_assets'));
	add_action('init', array($obj_slideshow, 'init'));

	if(is_admin())
	{
		register_uninstall_hook(__FILE__, 'uninstall_slideshow');

		add_action('admin_init', array($obj_slideshow, 'settings_slideshow'));

		add_filter('filter_sites_table_pages', array($obj_slideshow, 'filter_sites_table_pages'));

		add_action('rwmb_meta_boxes', array($obj_slideshow, 'rwmb_meta_boxes'));

		add_filter('manage_'.$obj_slideshow->post_type.'_posts_columns', array($obj_slideshow, 'column_header'), 5);
		add_action('manage_'.$obj_slideshow->post_type.'_posts_custom_column', array($obj_slideshow, 'column_cell'), 5, 2);

		add_filter('page_row_actions', array($obj_slideshow, 'row_actions'), 10, 2);
	}

	add_filter('filter_is_file_used', array($obj_slideshow, 'filter_is_file_used'));

	add_action('widgets_init', array($obj_slideshow, 'widgets_init'));

	function uninstall_slideshow()
	{
		include_once("include/classes.php");

		$obj_slideshow = new mf_slideshow();

		mf_uninstall_plugin(array(
			'options' => array('setting_slideshow_thumbnail_columns', 'setting_slideshow_thumbnail_rows'),
			'post_types' => array($obj_slideshow->post_type),
		));
	}
}