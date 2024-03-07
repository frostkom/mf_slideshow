<?php

class mf_slideshow
{
	var $post_type = 'slideshow';
	var $meta_prefix = 'mf_slide_';
	var $allow_widget_override_default = array('background', 'image_columns', 'height_ratio', 'display_controls', 'thumbnail_columns', 'autoplay');

	function __construct(){}

	function get_display_controls_for_select()
	{
		$arr_data = array(
			'arrows' => __("Arrows", 'lang_slideshow'),
			'dots' => __("Dots", 'lang_slideshow'),
			'magnifying_glass' => __("Magnifying Glass", 'lang_slideshow'),
			'thumbnails' => __("Thumbnails", 'lang_slideshow'),
		);

		return $arr_data;
	}

	function get_thumbnail_rows_for_select()
	{
		return array(
			'one' => __("One", 'lang_slideshow'),
			'multiple' => __("Multiple", 'lang_slideshow'),
		);
	}

	function get_slideshow_styles_for_select($data = array())
	{
		if(!isset($data['styles'])){	$data['styles'] = get_option('setting_slideshow_style', array('original'));}

		$arr_data = array(
			'original' => __("Default", 'lang_slideshow'),
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

	function get_allow_widget_override_for_select()
	{
		return array(
			'background' => __("Background", 'lang_slideshow'),
			'image_columns' => __("Image Columns", 'lang_slideshow'),
			'height_ratio' => __("Height Ratio", 'lang_slideshow'),
			'display_controls' => __("Display", 'lang_slideshow'),
			'thumbnail_columns' => __("Thumbnails", 'lang_slideshow'),
			'autoplay' => __("Autoplay", 'lang_slideshow'),
		);
	}

	function init()
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
			'capability_type' => 'page',
			'menu_position' => 21,
			'menu_icon' => 'dashicons-format-gallery',
			'supports' => array('title', 'editor', 'page-attributes'),
			'hierarchical' => true,
			'has_archive' => false,
		);

		register_post_type($this->post_type, $args);
	}

	function settings_slideshow()
	{
		$options_area = __FUNCTION__;

		add_settings_section($options_area, "", array($this, $options_area."_callback"), BASE_OPTIONS_PAGE);

		$arr_settings = array(
			'setting_slideshow_style' => __("Style", 'lang_slideshow'),
		);

		$arr_settings['setting_slideshow_allow_widget_override'] = __("Allow Widget Override", 'lang_slideshow');
		$arr_settings['setting_slideshow_background_color'] = __("Background Color", 'lang_slideshow');
		$arr_settings['setting_slideshow_background_opacity'] = " - ".__("Opacity", 'lang_slideshow');
		$arr_settings['setting_slideshow_display_text_background'] = __("Display Text Background", 'lang_slideshow');
		$arr_settings['setting_slideshow_image_columns'] = __("Image Columns", 'lang_slideshow');

		if(get_option('setting_slideshow_image_columns') > 1)
		{
			$arr_settings['setting_slideshow_image_steps'] = __("Image Steps", 'lang_slideshow');
		}

		$arr_settings['setting_slideshow_height_ratio'] = __("Height Ratio", 'lang_slideshow');
		$arr_settings['setting_slideshow_height_ratio_mobile'] = __("Height Ratio", 'lang_slideshow')." (".__("Mobile", 'lang_slideshow').")";
		$arr_settings['setting_slideshow_image_fit'] = __("Image Fit", 'lang_slideshow');
		$arr_settings['setting_slideshow_display_controls'] = __("Display", 'lang_slideshow');

		$setting_slideshow_display_controls = get_option('setting_slideshow_display_controls');

		if(is_array($setting_slideshow_display_controls) && in_array('thumbnails', $setting_slideshow_display_controls))
		{
			$arr_settings['setting_slideshow_thumbnail_columns'] = __("Thumbnail Columns", 'lang_slideshow');
			$arr_settings['setting_slideshow_thumbnail_rows'] = __("Thumbnail Rows", 'lang_slideshow');
		}

		$arr_settings['setting_slideshow_autoplay'] = __("Autoplay", 'lang_slideshow');

		if(get_option('setting_slideshow_autoplay') == 1 || get_option('setting_slideshow_autoplay') == 'yes')
		{
			$arr_settings['setting_slideshow_animate'] = __("Animate", 'lang_slideshow');
			$arr_settings['setting_slideshow_duration'] = __("Duration", 'lang_slideshow');
		}

		if(in_array('original', get_option('setting_slideshow_style', array('original'))))
		{
			$arr_settings['setting_slideshow_fade_duration'] = __("Fade Duration", 'lang_slideshow');
			$arr_settings['setting_slideshow_random'] = __("Random", 'lang_slideshow');
		}

		$arr_settings['setting_slideshow_open_links_in_new_tab'] = __("Open Links in new Tabs", 'lang_slideshow');

		show_settings_fields(array('area' => $options_area, 'object' => $this, 'settings' => $arr_settings));
	}

	function settings_slideshow_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);

		echo settings_header($setting_key, __("Slideshow", 'lang_slideshow'));
	}

	function setting_slideshow_style_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, array('original'));

		echo show_select(array('data' => $this->get_slideshow_styles_for_select(array('styles' => array('original', 'flickity', 'carousel'))), 'name' => $setting_key."[]", 'value' => $option));
	}

	function setting_slideshow_allow_widget_override_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, $this->allow_widget_override_default);

		echo show_select(array('data' => $this->get_allow_widget_override_for_select(), 'name' => $setting_key."[]", 'value' => $option));
	}

	function setting_slideshow_background_color_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, "#000000");

		echo show_textfield(array('type' => 'color', 'name' => $setting_key, 'value' => $option));
	}

		function setting_slideshow_background_opacity_callback()
		{
			$setting_key = get_setting_key(__FUNCTION__);
			$option = get_option($setting_key, 100);

			echo show_textfield(array('type' => 'number', 'name' => $setting_key, 'value' => $option, 'suffix' => "%"));
		}

	function setting_slideshow_display_text_background_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 'yes');

		echo show_select(array('data' => get_yes_no_for_select(), 'name' => $setting_key, 'value' => $option));
	}

	function setting_slideshow_image_columns_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 1);

		echo show_textfield(array('type' => 'number', 'name' => $setting_key, 'value' => $option, 'xtra' => " min='1' max='3'"));
	}

	function setting_slideshow_image_steps_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 1);

		$option_max = get_option('setting_slideshow_image_columns');

		echo show_textfield(array('type' => 'number', 'name' => $setting_key, 'value' => $option, 'xtra' => " min='1' max='".$option_max."'", 'description' => __("How many images do you want to switch everytime you go forward or backwards?", 'lang_slideshow')));
	}

	function setting_slideshow_height_ratio_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, '0.5');

		echo show_textfield(array('name' => $setting_key, 'value' => $option, 'description' => sprintf(__("From %s to %s. %s means the slideshow will be presented in landscape, %s means square format and %s means the slideshow is presented in portrait", 'lang_slideshow'), "0.3", "2", "0.3", "1", "2")));
	}

	function setting_slideshow_height_ratio_mobile_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, '1');

		echo show_textfield(array('name' => $setting_key, 'value' => $option));
	}

	function setting_slideshow_image_fit_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 'cover');

		$arr_data = array(
			'none' => "-- ".__("None", 'lang_slideshow')." --",
			'cover' => __("Cover", 'lang_slideshow'),
			'contain' => __("Contain", 'lang_slideshow'),
		);

		echo show_select(array('data' => $arr_data, 'name' => $setting_key, 'value' => $option));
	}

	function setting_slideshow_display_controls_callback()
	{
		switch(get_option('setting_slideshow_show_controls'))
		{
			case 'none':
				$arr_default = array('magnifying_glass');
			break;

			case 'dots':
				$arr_default = array('dots', 'magnifying_glass');
			break;

			case 'arrows':
				$arr_default = array('arrows', 'magnifying_glass');
			break;

			default:
			case 'all':
				$arr_default = array('dots', 'arrows', 'magnifying_glass');
			break;
		}

		if(in_array('original', get_option('setting_slideshow_style', array('original'))) && get_option('setting_slideshow_display_thumbnails') == 'yes')
		{
			$arr_default[] = 'thumbnails';
		}

		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, $arr_default);

		echo show_select(array('data' => $this->get_display_controls_for_select(), 'name' => $setting_key."[]", 'value' => $option));
	}

	function setting_slideshow_thumbnail_columns_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 5);

		echo show_textfield(array('type' => 'number', 'name' => $setting_key, 'value' => $option, 'xtra' => " min='2' max='10'"));
	}

	function setting_slideshow_thumbnail_rows_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 'one');

		echo show_select(array('data' => $this->get_thumbnail_rows_for_select(), 'name' => $setting_key, 'value' => $option));
	}

	function setting_slideshow_autoplay_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 'no');

		echo show_select(array('data' => get_yes_no_for_select(), 'name' => $setting_key, 'value' => $option)); //array('return_integer' => true)
	}

	function setting_slideshow_animate_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 'no');

		echo show_select(array('data' => get_yes_no_for_select(), 'name' => $setting_key, 'value' => $option));
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

	function setting_slideshow_random_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 'no');

		echo show_select(array('data' => get_yes_no_for_select(array('return_integer' => true)), 'name' => $setting_key, 'value' => $option));
	}

	function setting_slideshow_open_links_in_new_tab_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 'no');

		$arr_data = array(
			'no' => __("No", 'lang_slideshow'),
			'yes' => __("Yes", 'lang_slideshow')." (".__("When link is external", 'lang_slideshow').")",
		);

		echo show_select(array('data' => $arr_data, 'name' => $setting_key, 'value' => $option));
	}

	function admin_menu()
	{
		$menu_start = "edit.php?post_type=".$this->post_type;
		$menu_capability = override_capability(array('page' => $menu_start, 'default' => 'edit_pages'));

		$menu_title = __("Settings", 'lang_slideshow');
		add_submenu_page($menu_start, $menu_title, $menu_title, $menu_capability, admin_url("options-general.php?page=settings_mf_base#settings_slideshow"));
	}

	function rwmb_meta_boxes($meta_boxes)
	{
		$meta_boxes[] = array(
			'id' => $this->meta_prefix.'settings',
			'title' => __("Settings", 'lang_slideshow'),
			'post_types' => array($this->post_type),
			'context' => 'side',
			'priority' => 'low',
			'fields' => array(
				array(
					'name' => __("Content Position", 'lang_slideshow'),
					'id' => $this->meta_prefix.'content_position',
					'type' => 'select',
					'options' => array(
						'' => "-- ".__("Choose Here", 'lang_slideshow')." --",
						'left' => __("Left", 'lang_slideshow'),
						'center' => __("Center", 'lang_slideshow'),
						'bottom' => __("Bottom", 'lang_slideshow'),
						'right' => __("Right", 'lang_slideshow'),
					),
					'std' => 'center',
				),
				array(
					'name' => __("Content Style", 'lang_slideshow'),
					'id' => $this->meta_prefix.'content_style',
					'type' => 'textarea',
				),
				array(
					'name' => __("Page", 'lang_slideshow'),
					'id' => $this->meta_prefix.'page',
					'type' => 'select',
					'options' => get_posts_for_select(array('add_choose_here' => true, 'optgroup' => false)),
					'attributes' => array(
						'condition_type' => 'show_if',
						'condition_field' => $this->meta_prefix.'link',
					),
				),
				array(
					'name' => __("External Link", 'lang_slideshow'),
					'id' => $this->meta_prefix.'link',
					'type' => 'url',
					'attributes' => array(
						'condition_type' => 'show_if',
						'condition_field' => $this->meta_prefix.'page',
					),
				),
			)
		);

		$meta_boxes[] = array(
			'id' => $this->meta_prefix.'images',
			'title' => __("Images", 'lang_slideshow'),
			'post_types' => array($this->post_type),
			//'context' => 'side',
			'priority' => 'high',
			'fields' => array(
				array(
					'id' => $this->meta_prefix.'images',
					'type' => 'file_advanced',
					'mime_type' => 'image',
				)
			)
		);

		return $meta_boxes;
	}

	function column_header($cols)
	{
		global $post_type;

		unset($cols['date']);

		switch($post_type)
		{
			case $this->post_type:
				$cols['images'] = __("Images", 'lang_slideshow');
				$cols['shortcode'] = __("Shortcode", 'lang_slideshow');
			break;
		}

		return $cols;
	}

	function column_cell($col, $id)
	{
		global $wpdb, $post;

		switch($post->post_type)
		{
			case $this->post_type:
				switch($col)
				{
					case 'images':
						$arr_images = get_post_meta($id, $this->meta_prefix.$col);

						echo count($arr_images);
					break;

					case 'shortcode':
						$shortcode = "[mf_slideshow id=".$id."]";

						echo show_textfield(array('value' => $shortcode, 'readonly' => true, 'xtra' => "onclick='this.select()'"))
						."<div class='row-actions'>
							<a href='".admin_url("post-new.php?post_type=page&content=".$shortcode)."'>".__("Add New Page", 'lang_slideshow')."</a>
						</div>";
					break;
				}
			break;
		}
	}

	function wp_head()
	{
		$setting_slideshow_style = get_option_or_default('setting_slideshow_style', array('original'));
		$setting_slideshow_image_columns = get_option_or_default('setting_slideshow_image_columns', 1);
		$setting_slideshow_image_steps = get_option_or_default('setting_slideshow_image_steps', 1);
		$setting_slideshow_height_ratio = get_option_or_default('setting_slideshow_height_ratio', '0.5');
		$setting_slideshow_height_ratio_mobile = get_option_or_default('setting_slideshow_height_ratio_mobile', '1');

		$setting_slideshow_height_ratio = str_replace(",", ".", $setting_slideshow_height_ratio);
		$setting_slideshow_height_ratio_mobile = str_replace(",", ".", $setting_slideshow_height_ratio_mobile);

		if($setting_slideshow_height_ratio > 2 || $setting_slideshow_height_ratio < 0.2)
		{
			$setting_slideshow_height_ratio = 1;
		}

		if($setting_slideshow_height_ratio_mobile > 2 || $setting_slideshow_height_ratio_mobile < 0.2)
		{
			$setting_slideshow_height_ratio_mobile = 1;
		}

		$arr_settings = array(
			'image_columns' => $setting_slideshow_image_columns,
			'image_steps' => $setting_slideshow_image_steps,
			'height_ratio' => $setting_slideshow_height_ratio,
			'height_ratio_mobile' => $setting_slideshow_height_ratio_mobile,
			'display_controls' => get_option('setting_slideshow_display_controls'),
			'autoplay' => get_option_or_default('setting_slideshow_autoplay', 'no'),
			'duration' => (get_option_or_default('setting_slideshow_duration', 5) * 1000),
		);

		$plugin_include_url = plugin_dir_url(__FILE__);
		$plugin_version = get_plugin_version(__FILE__);

		if(in_array('original', $setting_slideshow_style))
		{
			$arr_settings['fade_duration'] = get_option_or_default('setting_slideshow_fade_duration', 400);
			$arr_settings['random'] = get_option('setting_slideshow_random', 'no');

			mf_enqueue_style('style_slideshow', $plugin_include_url."style.php", $plugin_version);
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

	function shortcode_slideshow($atts)
	{
		global $wpdb, $has_image;

		extract(shortcode_atts(array(
			'id' => '',
			'style' => get_option_or_default('setting_slideshow_style', 'original'),
			'background' => get_option('setting_slideshow_background_color'),
			'background_opacity' => get_option('setting_slideshow_background_opacity'),
			'display_text_background' => get_option_or_default('setting_slideshow_display_text_background', 'yes'),
			'image_columns' => get_option_or_default('setting_slideshow_image_columns', 1),
			'image_steps' => get_option_or_default('setting_slideshow_image_steps', 1),
			'height_ratio' => get_option_or_default('setting_slideshow_height_ratio', '0.5'),
			'height_ratio_mobile' => get_option_or_default('setting_slideshow_height_ratio_mobile', '1'),
			'display_controls' => get_option('setting_slideshow_display_controls'),
			'autoplay' => get_option_or_default('setting_slideshow_autoplay', 'no'),
			'animate' => get_option_or_default('setting_slideshow_animate', 'no'),
			'duration' => get_option_or_default('setting_slideshow_duration', 5),
			'fade_duration' => get_option_or_default('setting_slideshow_fade_duration', 400),
			'random' => get_option_or_default('setting_slideshow_random', 'no'),
		), $atts));

		return $this->get_slideshow(array(
			'parent' => $id,
			'slideshow_style' => $style,
			'slideshow_background' => $background,
			'slideshow_background_opacity' => $background_opacity,
			'slideshow_display_text_background' => $display_text_background,
			'slideshow_image_columns' => $image_columns,
			'slideshow_image_steps' => $image_steps,
			'slideshow_height_ratio' => $height_ratio,
			'slideshow_height_ratio_mobile' => $height_ratio_mobile,
			'slideshow_display_controls' => $display_controls,
			'slideshow_autoplay' => $autoplay,
			'slideshow_animate' => $animate,
			'slideshow_duration' => $duration,
			'slideshow_fade_duration' => $fade_duration,
			'slideshow_random' => $random,
		));
	}

	function get_slideshow($data)
	{
		global $wpdb;

		$out = "";

		$arr_slide_images = get_post_meta_file_src(array('post_id' => $data['parent'], 'meta_key' => $this->meta_prefix.'images', 'single' => false));
		$arr_slide_texts = array();

		if(count($arr_slide_images) == 0)
		{
			$result = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title, post_content FROM ".$wpdb->posts." WHERE post_type = %s AND post_status = %s AND post_parent = '%d' ORDER BY menu_order ASC", $this->post_type, 'publish', $data['parent']));

			foreach($result as $r)
			{
				$post_id = $r->ID;
				$post_title = $r->post_title;
				$post_content = $r->post_content;

				$arr_slide_images_child = get_post_meta_file_src(array('post_id' => $post_id, 'meta_key' => $this->meta_prefix.'images', 'single' => false));

				if(count($arr_slide_images_child) > 0)
				{
					$post_content_position = get_post_meta($post_id, $this->meta_prefix.'content_position', true);
					$post_page = get_post_meta($post_id, $this->meta_prefix.'page', true);

					if(intval($post_page) > 0)
					{
						$post_url = get_permalink($post_page);
					}

					else
					{
						$post_url = get_post_meta($post_id, $this->meta_prefix.'link', true);
					}

					foreach($arr_slide_images_child as $child)
					{
						$arr_slide_images[] = $child;
						$arr_slide_texts[] = array(
							'parent_id' => $data['parent'],
							'id' => $post_id,
							'title' => $post_title,
							'content' => $post_content,
							'content_position' => $post_content_position,
							'url' => $post_url,
						);
					}
				}
			}
		}

		if(count($arr_slide_images) > 0)
		{
			/* Add settings to .slideshow to fetch in JS instead */
			$obj_slideshow = new mf_slideshow();

			$out .= $obj_slideshow->render_slides(array(
				'settings' => $data,
				'images' => $arr_slide_images,
				'texts' => $arr_slide_texts,
			));
		}

		else
		{
			$out .= "<p>".__("I could not find any images to load", 'lang_slideshow')."</p>";
		}

		return $out;
	}

	function filter_is_file_used($arr_used)
	{
		global $wpdb;

		$result = $wpdb->get_results($wpdb->prepare("SELECT post_id FROM ".$wpdb->postmeta." WHERE meta_key = %s AND meta_value LIKE %s", $this->meta_prefix.'images', "%".$arr_used['id']."%"));
		$rows = $wpdb->num_rows;

		if($rows > 0)
		{
			$arr_used['amount'] += $rows;

			foreach($result as $r)
			{
				if($arr_used['example'] != '')
				{
					break;
				}

				$arr_used['example'] = admin_url("post.php?action=edit&post=".$r->post_id);
			}
		}

		return $arr_used;
	}

	function widgets_init()
	{
		register_widget('widget_slideshow');
	}

	function render_slides($data)
	{
		if(!isset($data['settings'])){		$data['settings'] = array();}
		if(!isset($data['images'])){		$data['images'] = array();}
		if(!isset($data['texts'])){			$data['texts'] = array();}
		if(!isset($data['height'])){		$data['height'] = 0;}

		$out = "";

		if(count($data['images']) > 0)
		{
			if(!isset($data['settings']['slideshow_style'])){						$data['settings']['slideshow_style'] = get_option_or_default('setting_slideshow_style', 'original');}
			if(!isset($data['settings']['slideshow_background']) || $data['settings']['slideshow_background'] == ''){	$data['settings']['slideshow_background'] = get_option_or_default('setting_slideshow_background_color', "#000000");}
			if(!isset($data['settings']['slideshow_background_opacity']) || $data['settings']['slideshow_background_opacity'] == ''){	$data['settings']['slideshow_background_opacity'] = get_option_or_default('setting_slideshow_background_opacity', 100);}
			if(!isset($data['settings']['slideshow_display_text_background'])){		$data['settings']['slideshow_display_text_background'] = get_option_or_default('setting_slideshow_slideshow_display_text_background', 'yes');}
			if(!isset($data['settings']['slideshow_image_columns'])){				$data['settings']['slideshow_image_columns'] = get_option_or_default('setting_slideshow_image_columns', 1);}
			if(!isset($data['settings']['slideshow_image_steps'])){					$data['settings']['slideshow_image_steps'] = get_option_or_default('setting_slideshow_image_steps', 1);}
			if(!isset($data['settings']['slideshow_display_controls'])){			$data['settings']['slideshow_display_controls'] = get_option('setting_slideshow_display_controls');}
			if(!isset($data['settings']['slideshow_animate'])){						$data['settings']['slideshow_animate'] = get_option_or_default('setting_slideshow_animate', 'no');}

			$setting_slideshow_style = (isset($data['settings']['slideshow_style']) ? $data['settings']['slideshow_style'] : get_option('setting_slideshow_style', array('original')));
			$setting_random = (isset($data['settings']['slideshow_random']) ? $data['settings']['slideshow_random'] : get_option('setting_slideshow_random', 'no'));

			$setting_slideshow_open_links_in_new_tab = get_option('setting_slideshow_open_links_in_new_tab');

			if(is_array($setting_slideshow_style) && !in_array($data['settings']['slideshow_style'], $setting_slideshow_style))
			{
				$data['settings']['slideshow_style'] = $setting_slideshow_style[0];
			}

			$images = $dots = "";
			$i = 1;

			$active_i = $setting_random == true ? mt_rand(1, count($data['images'])) : 1;

			if($data['settings']['slideshow_style'] == 'carousel')
			{
				$images .= "<div>";
			}

				foreach($data['images'] as $key => $image)
				{
					switch($data['settings']['slideshow_style'])
					{
						case 'flickity':
							$images .= "<div class='carousel-cell'".($i == $active_i ? " class='active'" : "")." rel='".$i."'><img src='".$image."'></div>";
						break;

						case 'carousel':
							$images .= "<div class='item'".($i == $active_i ? " class='active'" : "")." rel='".$i."'><img src='".$image."'></div>";
						break;

						default:
							$container_class = "slide_item";

							$has_texts = (count($data['texts']) > 0 && isset($data['texts'][$key]));

							if($has_texts)
							{
								$container_class .= ($container_class != '' ? " " : "")."slide_parent_".$data['texts'][$key]['parent_id'];
							}

							if($i == $active_i)
							{
								$container_class .= ($container_class != '' ? " " : "")."active active_init";
							}

							if($data['settings']['slideshow_image_columns'] > 1)
							{
								for($j = 1; $j < $data['settings']['slideshow_image_columns']; $j++)
								{
									if($i == ($active_i + $j))
									{
										$container_class .= ($container_class != '' ? " " : "")."active";
									}
								}
							}

							if($data['settings']['slideshow_animate'] == 'yes')
							{
								$container_class .= ($container_class != '' ? " " : "")."animate";
							}

							$images .= "<div"
								.($has_texts ? " id='slide_".$data['texts'][$key]['id']."'" : "")
								.($container_class != '' ? " class='".$container_class."'" : "")
								." rel='".$i."'"
							.">
								<img src='".$image."'>";

								if($has_texts)
								{
									$content_class = "content";

									if($data['texts'][$key]['content_position'] != '')
									{
										$content_class .= ($content_class != '' ? " " : "").$data['texts'][$key]['content_position'];
									}

									$images .= "<div class='".$content_class."'>
										<div>
											<h4>".$data['texts'][$key]['title']."</h4>"
											.apply_filters('the_content', $data['texts'][$key]['content']);

											if($data['texts'][$key]['url'] != '')
											{
												$images .= "<a href='".$data['texts'][$key]['url']."'";

													switch($setting_slideshow_open_links_in_new_tab)
													{
														case 'yes':
															if(strpos($data['texts'][$key]['url'], get_site_url()) === false)
															{
																$images .= " rel='external'";
															}
														break;

														default:
															//Do nothing
														break;
													}

												$images .= ">".__("Read More", 'lang_slideshow')."&hellip;</a>";
											}

										$images .= "</div>
									</div>";
								}

							$images .= "</div>";

							$dots .= "<li".($i == $active_i ? " class='active'" : "")." rel='".$i."'></li>";
						break;
					}

					$i++;
				}

			if($data['settings']['slideshow_style'] == 'carousel')
			{
				$images .= "</div>";
			}

			$arr_attributes = array('autoplay', 'animate', 'duration', 'fade_duration', 'display_text_background', 'image_columns', 'image_steps', 'height_ratio', 'height_ratio_mobile'); //, 'display_controls'

			$slideshow_classes = "slideshow ".$data['settings']['slideshow_style'];
			$slideshow_style = $slideshow_attributes = "";

			if($data['settings']['slideshow_display_text_background'] == 'yes')
			{
				$slideshow_classes .= " display_text_background";
			}

			if($data['settings']['slideshow_background'] != '') // && $data['settings']['slideshow_background'] != '#435355'
			{
				if($data['settings']['slideshow_background_opacity'] != '')
				{
					list($r, $g, $b) = sscanf($data['settings']['slideshow_background'], "#%02x%02x%02x");

					$data['settings']['slideshow_background'] = "rgba(".$r.", ".$g.", ".$b.", ".($data['settings']['slideshow_background_opacity'] / 100).")";
				}

				$slideshow_style .= ($slideshow_style != '' ? " " : "")."background-color: ".$data['settings']['slideshow_background'].";";
			}

			if($data['height'] > 0)
			{
				$slideshow_style .= ($slideshow_style != '' ? " " : "")."height: ".$data['height']."px;";
			}

			if($slideshow_style != '')
			{
				$slideshow_attributes .= " style='".$slideshow_style."'";
			}

			$slideshow_attributes .= " data-random='".$setting_random."'";

			foreach($arr_attributes as $attribute)
			{
				if(isset($data['settings']['slideshow_'.$attribute]))
				{
					switch($attribute)
					{
						case 'duration':
							$data['settings']['slideshow_'.$attribute] *= 1000;
						break;
					}

					$slideshow_attributes .= " data-".$attribute."='".$data['settings']['slideshow_'.$attribute]."'";
				}
			}

			$out = "<div"
				." class='".$slideshow_classes."'"
				.$slideshow_attributes
			.">
				<div class='slideshow_container'>
					<div class='slideshow_images columns_".$data['settings']['slideshow_image_columns'].($data['settings']['slideshow_image_columns'] > 1 ? " has_columns" : "")."'>"
						.$images
					."</div>";

					if(count($data['images']) > 1)
					{
						switch($data['settings']['slideshow_style'])
						{
							case 'original':
								$display_controls_exists = (isset($data['settings']['slideshow_display_controls']) && is_array($data['settings']['slideshow_display_controls']));

								$out .= "<div class='controls_arrows'>
									<div class='panel_arrow_left'>";

										if($display_controls_exists && in_array('arrows', $data['settings']['slideshow_display_controls']))
										{
											$out .= "<i class='fa fa-chevron-left arrow_left'></i>";
										}

									$out .= "</div>
									<div class='panel_arrow_right'>";

										if($display_controls_exists && in_array('arrows', $data['settings']['slideshow_display_controls']))
										{
											$out .= "<i class='fa fa-chevron-right arrow_right'></i>";
										}

									$out .= "</div>
								</div>";

								if($display_controls_exists)
								{
									if(in_array('magnifying_glass', $data['settings']['slideshow_display_controls']))
									{
										$out .= "<i class='fa fa-search controls_magnifying_glass'></i>";
									}

									if(in_array('dots', $data['settings']['slideshow_display_controls']))
									{
										$out .= "<ul class='controls_dots'>".$dots."</ul>";
									}
								}
							break;

							case 'carousel':
								$out .= "<i class='fa fa-chevron-left controls prev'></i>
								<i class='fa fa-chevron-right controls next'></i>";
							break;
						}
					}

				$out .= "</div>";

				if(count($data['images']) > 1 && $data['settings']['slideshow_style'] == 'original' && is_array($data['settings']['slideshow_display_controls']) && in_array('thumbnails', $data['settings']['slideshow_display_controls']))
				{
					$site_url = get_site_url();
					$i = 1;

					$setting_slideshow_thumbnail_columns = get_option('setting_slideshow_thumbnail_columns', 5);
					$setting_slideshow_thumbnail_rows = get_option('setting_slideshow_thumbnail_rows');

					$out .= "<ul class='slideshow_thumbnails thumbnail_columns_".$setting_slideshow_thumbnail_columns." thumbnail_rows_".$setting_slideshow_thumbnail_rows."'>";

						foreach($data['images'] as $key => $image)
						{
							$thumbnail_class = "";

							if($i == $active_i)
							{
								$thumbnail_class .= ($thumbnail_class != '' ? " " : "")."active";
							}

							$out .= "<li"
								.($thumbnail_class != '' ? " class='".$thumbnail_class."'" : "")
								." rel='".$i."'"
							.">"
								.render_image_tag(array('src' => str_replace($site_url, "", $image), 'size' => 'thumbnail'))
							."</li>";

							$i++;
						}

					$out .= "</ul>";
				}

			$out .= "</div>";
		}

		return $out;
	}
}

class widget_slideshow extends WP_Widget
{
	var $obj_slideshow = "";

	var $widget_ops = array();

	var $arr_default = array();

	var $setting_slideshow_allow_widget_override = "";

	function __construct()
	{
		$this->obj_slideshow = new mf_slideshow();

		$this->widget_ops = array(
			'classname' => 'slideshow_wrapper',
			'description' => __("Display a slideshow that you have created", 'lang_slideshow'),
		);

		$this->setting_slideshow_allow_widget_override = get_option('setting_slideshow_allow_widget_override', $this->obj_slideshow->allow_widget_override_default);

		$this->arr_default = array(
			'slideshow_heading' => '',
			'parent' => '',
			'slideshow_style' => get_option_or_default('setting_slideshow_style', 'original'),
		);

		if(1 == 1 || in_array('background', $this->setting_slideshow_allow_widget_override))
		{
			$this->arr_default['slideshow_background'] = get_option('setting_slideshow_background_color');
			$this->arr_default['slideshow_background_opacity'] = get_option('setting_slideshow_background_opacity');
			$this->arr_default['slideshow_display_text_background'] = get_option_or_default('setting_slideshow_display_text_background', 'yes');
		}

		if(1 == 1 || in_array('image_columns', $this->setting_slideshow_allow_widget_override))
		{
			$this->arr_default['slideshow_image_columns'] = get_option_or_default('setting_slideshow_image_columns', 1);
			$this->arr_default['slideshow_image_steps'] = get_option_or_default('setting_slideshow_image_steps', 1);
		}

		if(1 == 1 || in_array('height_ratio', $this->setting_slideshow_allow_widget_override))
		{
			$this->arr_default['slideshow_height_ratio'] = get_option_or_default('setting_slideshow_height_ratio', '0.5');
			$this->arr_default['slideshow_height_ratio_mobile'] = get_option_or_default('setting_slideshow_height_ratio_mobile', '1');
		}

		if(1 == 1 || in_array('display_controls', $this->setting_slideshow_allow_widget_override))
		{
			$this->arr_default['slideshow_display_controls'] = get_option('setting_slideshow_display_controls');
		}

		if(1 == 1 || in_array('autoplay', $this->setting_slideshow_allow_widget_override))
		{
			$this->arr_default['slideshow_autoplay'] = get_option_or_default('setting_slideshow_autoplay', 'no');
			$this->arr_default['slideshow_animate'] = get_option_or_default('setting_slideshow_animate', 'no');
			$this->arr_default['slideshow_duration'] = get_option_or_default('setting_slideshow_duration', 5);
			$this->arr_default['slideshow_fade_duration'] = get_option_or_default('setting_slideshow_fade_duration', 400);
			$this->arr_default['slideshow_random'] = get_option('setting_slideshow_random', 'no');
		}

		parent::__construct('slideshow-widget', __("Slideshow", 'lang_slideshow'), $this->widget_ops);
	}

	function widget($args, $instance)
	{
		global $wpdb;

		extract($args);
		$instance = wp_parse_args((array)$instance, $this->arr_default);

		if($instance['parent'] > 0)
		{
			echo apply_filters('filter_before_widget', $before_widget);

				if($instance['slideshow_heading'] != '')
				{
					$instance['slideshow_heading'] = apply_filters('widget_title', $instance['slideshow_heading'], $instance, $this->id_base);

					echo $before_title
						.$instance['slideshow_heading']
					.$after_title;
				}

				echo $this->obj_slideshow->get_slideshow($instance)
			.$after_widget;
		}
	}

	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$new_instance = wp_parse_args((array)$new_instance, $this->arr_default);

		$instance['slideshow_heading'] = sanitize_text_field($new_instance['slideshow_heading']);
		$instance['parent'] = sanitize_text_field($new_instance['parent']);
		$instance['slideshow_style'] = sanitize_text_field($new_instance['slideshow_style']);

		if(1 == 1 || in_array('autoplay', $this->setting_slideshow_allow_widget_override))
		{
			$instance['slideshow_autoplay'] = sanitize_text_field($new_instance['slideshow_autoplay']);
			$instance['slideshow_animate'] = sanitize_text_field($new_instance['slideshow_animate']);
			$instance['slideshow_duration'] = sanitize_text_field($new_instance['slideshow_duration']);
			$instance['slideshow_fade_duration'] = sanitize_text_field($new_instance['slideshow_fade_duration']);
			$instance['slideshow_random'] = sanitize_text_field($new_instance['slideshow_random']);
		}

		if(1 == 1 || in_array('background', $this->setting_slideshow_allow_widget_override))
		{
			$instance['slideshow_background'] = sanitize_text_field($new_instance['slideshow_background']);
			$instance['slideshow_background_opacity'] = sanitize_text_field($new_instance['slideshow_background_opacity']);
			$instance['slideshow_display_text_background'] = sanitize_text_field($new_instance['slideshow_display_text_background']);
		}

		if(1 == 1 || in_array('display_controls', $this->setting_slideshow_allow_widget_override))
		{
			$instance['slideshow_display_controls'] = is_array($new_instance['slideshow_display_controls']) ? $new_instance['slideshow_display_controls'] : array();
		}

		if(1 == 1 || in_array('image_columns', $this->setting_slideshow_allow_widget_override))
		{
			$instance['slideshow_image_columns'] = sanitize_text_field($new_instance['slideshow_image_columns']);
			$instance['slideshow_image_steps'] = sanitize_text_field($new_instance['slideshow_image_steps']);
		}

		if(1 == 1 || in_array('height_ratio', $this->setting_slideshow_allow_widget_override))
		{
			$instance['slideshow_height_ratio'] = str_replace(",", ".", sanitize_text_field($new_instance['slideshow_height_ratio']));
			$instance['slideshow_height_ratio_mobile'] = str_replace(",", ".", sanitize_text_field($new_instance['slideshow_height_ratio_mobile']));
		}

		return $instance;
	}

	function form($instance)
	{
		$instance = wp_parse_args((array)$instance, $this->arr_default);

		$arr_data_parents = array();
		get_post_children(array('add_choose_here' => true, 'post_type' => $this->obj_slideshow->post_type, 'allow_depth' => false), $arr_data_parents);

		$arr_data_styles = $this->obj_slideshow->get_slideshow_styles_for_select();

		echo "<div class='mf_form'>"
			.show_textfield(array('name' => $this->get_field_name('slideshow_heading'), 'text' => __("Heading", 'lang_slideshow'), 'value' => $instance['slideshow_heading'], 'xtra' => " id='".$this->widget_ops['classname']."-title'"))
			.show_select(array('data' => $arr_data_parents, 'name' => $this->get_field_name('parent'), 'text' => __("Parent", 'lang_slideshow'), 'value' => $instance['parent'], 'suffix' => get_option_page_suffix(array('post_type' => $this->obj_slideshow->post_type, 'value' => $instance['parent']))));

			if(count($arr_data_styles) > 1)
			{
				echo show_select(array('data' => $arr_data_styles, 'name' => $this->get_field_name('slideshow_style'), 'text' => __("Style", 'lang_slideshow'), 'value' => $instance['slideshow_style']));
			}

			else
			{
				echo input_hidden(array('name' => $this->get_field_name('slideshow_style'), 'value' => (is_array($instance['slideshow_style']) ? $instance['slideshow_style'][0] : $instance['slideshow_style'])));
			}

			if(is_array($this->setting_slideshow_allow_widget_override) && in_array('background', $this->setting_slideshow_allow_widget_override))
			{
				echo "<div class='flex_flow'>"
					.show_textfield(array('type' => 'color', 'name' => $this->get_field_name('slideshow_background'), 'text' => __("Background Color", 'lang_slideshow'), 'value' => $instance['slideshow_background']))
					.show_textfield(array('type' => 'number', 'name' => $this->get_field_name('slideshow_background_opacity'), 'text' => " - ".__("Opacity", 'lang_slideshow'), 'value' => $instance['slideshow_background_opacity']))
					.show_select(array('data' => get_yes_no_for_select(), 'name' => $this->get_field_name('slideshow_display_text_background'), 'text' => __("Display Text Background", 'lang_slideshow'), 'value' => $instance['slideshow_display_text_background']))
				."</div>";
			}

			if(is_array($this->setting_slideshow_allow_widget_override) && (in_array('image_columns', $this->setting_slideshow_allow_widget_override) || in_array('height_ratio', $this->setting_slideshow_allow_widget_override)))
			{
				echo "<div class='flex_flow'>";

					if(in_array('image_columns', $this->setting_slideshow_allow_widget_override))
					{
						echo show_textfield(array('type' => 'number', 'name' => $this->get_field_name('slideshow_image_columns'), 'text' => __("Image Columns", 'lang_slideshow'), 'value' => $instance['slideshow_image_columns'], 'xtra' => " min='1' max='3'"));

						if($instance['slideshow_image_columns'] > 1)
						{
							$option_max = $instance['slideshow_image_columns'];

							echo show_textfield(array('type' => 'number', 'name' => $this->get_field_name('slideshow_image_steps'), 'text' => __("Image Steps", 'lang_slideshow'), 'value' => $instance['slideshow_image_steps'], 'xtra' => " min='1' max='".$option_max."'"));
						}
					}

					if(in_array('height_ratio', $this->setting_slideshow_allow_widget_override))
					{
						echo show_textfield(array('name' => $this->get_field_name('slideshow_height_ratio'), 'text' => __("Height Ratio", 'lang_slideshow')." <i class='fa fa-info-circle' title='".sprintf(__("From %s to %s. %s means the slideshow will be presented in landscape, %s means square format and %s means the slideshow is presented in portrait", 'lang_slideshow'), "0.3", "2", "0.3", "1", "2")."'></i>", 'value' => $instance['slideshow_height_ratio']))
						.show_textfield(array('name' => $this->get_field_name('slideshow_height_ratio_mobile'), 'text' => __("Height Ratio", 'lang_slideshow')." (".__("Mobile", 'lang_slideshow').")", 'value' => $instance['slideshow_height_ratio_mobile']));
					}

				echo "</div>";
			}

			if(is_array($this->setting_slideshow_allow_widget_override) && in_array('display_controls', $this->setting_slideshow_allow_widget_override) || in_array('autoplay', $this->setting_slideshow_allow_widget_override))
			{
				echo "<div class='flex_flow'>";

					if(in_array('display_controls', $this->setting_slideshow_allow_widget_override))
					{
						echo show_select(array('data' => $this->obj_slideshow->get_display_controls_for_select(), 'name' => $this->get_field_name('slideshow_display_controls')."[]", 'text' => __("Display", 'lang_slideshow'), 'value' => $instance['slideshow_display_controls']));
					}

					if(in_array('autoplay', $this->setting_slideshow_allow_widget_override))
					{
						if($instance['slideshow_autoplay'] == '') // Backwards compatibility
						{
							$instance['slideshow_autoplay'] = 'no';
						}

						echo show_select(array('data' => get_yes_no_for_select(), 'name' => $this->get_field_name('slideshow_autoplay'), 'text' => __("Autoplay", 'lang_slideshow'), 'value' => $instance['slideshow_autoplay'])); //array('return_integer' => true)
					}

				echo "</div>";
			}

			if(is_array($this->setting_slideshow_allow_widget_override) && in_array('autoplay', $this->setting_slideshow_allow_widget_override))
			{
				if($instance['slideshow_autoplay'] == 1 || $instance['slideshow_autoplay'] == 'yes')
				{
					echo "<div class='flex_flow'>"
						.show_select(array('data' => get_yes_no_for_select(), 'name' => $this->get_field_name('slideshow_animate'), 'text' => __("Animate", 'lang_slideshow'), 'value' => $instance['slideshow_animate']))
						.show_textfield(array('type' => 'number', 'name' => $this->get_field_name('slideshow_duration'), 'text' => __("Duration", 'lang_slideshow'), 'value' => $instance['slideshow_duration'], 'xtra' => "min='2'", 'suffix' => __("s", 'lang_slideshow')))
					."</div>";
				}

				if($instance['slideshow_style'] == 'original')
				{
					if($instance['slideshow_random'] == '') // Backwards compatibility
					{
						$instance['slideshow_random'] = 'no';
					}

					echo "<div class='flex_flow'>"
						.show_textfield(array('type' => 'number', 'name' => $this->get_field_name('slideshow_fade_duration'), 'text' => __("Fade Duration", 'lang_slideshow'), 'value' => $instance['slideshow_fade_duration'], 'xtra' => "min='400' max='4000'", 'suffix' => __("ms", 'lang_slideshow')))
						.show_select(array('data' => get_yes_no_for_select(), 'name' => $this->get_field_name('slideshow_random'), 'text' => __("Random", 'lang_slideshow'), 'value' => $instance['slideshow_random'])) //array('return_integer' => true)
					."</div>";
				}
			}

		echo "</div>";
	}
}