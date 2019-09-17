<?php

class mf_slideshow
{
	function __construct()
	{
		$this->meta_prefix = 'mf_slide_';
	}

	function replace_controls_for_select($option)
	{
		if($option == '' || $option == '0')
		{
			$option = 'none';
		}

		else if($option == '1')
		{
			$option = 'all';
		}

		return $option;
	}

	function get_controls_for_select()
	{
		return array(
			'none' => __("None", 'lang_slideshow'),
			'dots' => __("Dots", 'lang_slideshow'),
			'arrows' => __("Arrows", 'lang_slideshow'),
			'all' => __("All", 'lang_slideshow'),
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

		register_post_type('slideshow', $args);
	}

	function settings_slideshow()
	{
		$options_area = __FUNCTION__;

		add_settings_section($options_area, "", array($this, $options_area."_callback"), BASE_OPTIONS_PAGE);

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
		$option = get_option($setting_key, 'all');

		echo show_select(array('data' => $this->get_controls_for_select(), 'name' => $setting_key, 'value' => $this->replace_controls_for_select($option)));
	}

	function setting_slideshow_height_ratio_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, '1');

		echo show_textfield(array('name' => $setting_key, 'value' => $option, 'description' => sprintf(__("From %s to %s. %s means the slideshow will be presented in landscape, %s means square format and %s means the slideshow is presented in portrait", 'lang_slideshow'), "0.3", "2", "0.3", "1", "2")));
	}

	function setting_slideshow_height_ratio_mobile_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key);

		echo show_textfield(array('name' => $setting_key, 'value' => $option));
	}

	function setting_slideshow_open_links_in_new_tab_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 'no');

		$arr_data = array(
			'no' => __("No", 'lang_slideshow'),
			'yes' => __("Yes", 'lang_slideshow')." (".__("When link is external", 'lang_slideshow').")",
			//'yes_always' => __("Yes", 'lang_slideshow')." (".__("Always", 'lang_slideshow').")",
		);

		echo show_select(array('data' => $arr_data, 'name' => $setting_key, 'value' => $option));
	}

	function rwmb_meta_boxes($meta_boxes)
	{
		$meta_boxes[] = array(
			'id' => 'settings',
			'title' => __("Settings", 'lang_slideshow'),
			'post_types' => array('slideshow'),
			'context' => 'side',
			'priority' => 'low',
			'fields' => array(
				/*array(
					'name' => __("Height", 'lang_slideshow'),
					'id' => $this->meta_prefix.'height',
					'type' => 'number',
					'min' => 1,
				),*/
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
			'id' => 'images',
			'title' => __("Images", 'lang_slideshow'),
			'post_types' => array('slideshow'),
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

	/*function count_shortcode_button($count)
	{
		if($count == 0)
		{
			$templates = get_posts(array(
				'post_type' => 'slideshow',
				'posts_per_page' => 1,
				'post_status' => 'publish'
			));

			if(count($templates) > 0)
			{
				$count++;
			}
		}

		return $count;
	}

	function get_shortcode_output($out)
	{
		$arr_data = array();
		get_post_children(array('add_choose_here' => true, 'post_type' => 'slideshow'), $arr_data);

		if(count($arr_data) > 1)
		{
			$out .= "<h3>".__("Choose a Slideshow", 'lang_slideshow')."</h3>"
			.show_select(array('data' => $arr_data, 'xtra' => "rel='slideshow'"));
		}

		return $out;
	}

	function get_shortcode_list($data)
	{
		$post_id = $data[0];
		$content_list = $data[1];

		if($post_id > 0)
		{
			$post_content = mf_get_post_content($post_id);

			$arr_list_id = get_match_all("/\[mf_slideshow id=(.*?)\]/", $post_content, false);

			foreach($arr_list_id[0] as $list_id)
			{
				if($list_id > 0)
				{
					$content_list .= "<li><a href='".admin_url("post.php?post=".$list_id."&action=edit")."'>".get_post_title($list_id)."</a> <span class='grey'>[mf_slideshow id=".$list_id."]</span></li>";
				}
			}
		}

		return array($post_id, $content_list);
	}*/

	function wp_head()
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

	function widgets_init()
	{
		register_widget('widget_slideshow');
	}

	function show($data)
	{
		if(!isset($data['settings'])){		$data['settings'] = array('slideshow_style' => 'original');}
		if(!isset($data['images'])){		$data['images'] = array();}
		if(!isset($data['texts'])){			$data['texts'] = array();}
		if(!isset($data['height'])){		$data['height'] = 0;}

		$out = "";

		if(count($data['images']) > 0)
		{
			$setting_slideshow_style = isset($data['settings']['slideshow_style']) ? $data['settings']['slideshow_style'] : get_option('setting_slideshow_style', array('original'));
			$setting_background_color = isset($data['settings']['slideshow_background']) ? $data['settings']['slideshow_background'] : get_option('setting_slideshow_background_color', "#000");
			$setting_autoplay = isset($data['settings']['slideshow_autoplay']) ? $data['settings']['slideshow_autoplay'] : get_option('setting_slideshow_autoplay');
			$setting_duration = isset($data['settings']['slideshow_duration']) ? $data['settings']['slideshow_duration'] : get_option_or_default('setting_slideshow_duration', 5);
			$setting_fade_duration = isset($data['settings']['slideshow_fade_duration']) ? $data['settings']['slideshow_fade_duration'] : get_option_or_default('setting_slideshow_fade_duration', 400);
			$setting_random = isset($data['settings']['slideshow_random']) ? $data['settings']['slideshow_random'] : get_option('setting_slideshow_random');
			$setting_show_controls = isset($data['settings']['slideshow_show_controls']) ? $data['settings']['slideshow_show_controls'] : get_option('setting_slideshow_show_controls');
			$setting_height_ratio = isset($data['settings']['slideshow_height_ratio']) ? $data['settings']['slideshow_height_ratio'] : get_option('setting_slideshow_height_ratio');
			$setting_height_ratio_mobile = isset($data['settings']['slideshow_height_ratio_mobile']) ? $data['settings']['slideshow_height_ratio_mobile'] : get_option('setting_slideshow_height_ratio_mobile');

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
					if($data['settings']['slideshow_style'] == 'flickity')
					{
						$images .= "<div class='carousel-cell'".($i == $active_i ? " class='active'" : "")." rel='".$i."'><img src='".$image."'></div>";
					}

					else if($data['settings']['slideshow_style'] == 'carousel')
					{
						$images .= "<div class='item'".($i == $active_i ? " class='active'" : "")." rel='".$i."'><img src='".$image."'></div>";
					}

					else
					{
						$images .= "<div".($i == $active_i ? " class='active'" : "")." rel='".$i."'>
							<img src='".$image."'>";

							if(count($data['texts']) > 0 && isset($data['texts'][$key]))
							{
								$images .= "<div class='content".(isset($data['texts'][$key]['content_position']) && $data['texts'][$key]['content_position'] != '' ? " ".$data['texts'][$key]['content_position'] : '')."'>
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
					}

					$i++;
				}

			if($data['settings']['slideshow_style'] == 'carousel')
			{
				$images .= "</div>";
			}

			$slideshow_style = "";

			if($setting_background_color != '')
			{
				$slideshow_style .= "background-color: ".$setting_background_color.";";
			}

			if($data['height'] > 0)
			{
				$slideshow_style .= "height: ".$data['height']."px;";
			}

			$out = "<div"
				." class='slideshow ".$data['settings']['slideshow_style']."'"
				." style='".$slideshow_style."'"
				." data-autoplay='".$setting_autoplay."'"
				." data-duration='".($setting_duration * 1000)."'"
				." data-fade='".$setting_fade_duration."'"
				." data-random='".$setting_random."'"
				." data-show_controls='".$setting_show_controls."'"
				." data-height_ratio='".$setting_height_ratio."'"
				." data-height_ratio_mobile='".$setting_height_ratio_mobile."'"
			.">"
				.$images;

				if(count($data['images']) > 1)
				{
					if($data['settings']['slideshow_style'] == 'original')
					{
						$out .= "<i class='fa fa-chevron-left controls arrow_left'></i>
						<i class='fa fa-chevron-right controls arrow_right'></i>
						<ul class='controls'>"
							.$dots
						."</ul>";
					}

					else if($data['settings']['slideshow_style'] == 'carousel')
					{
						$out .= "<i class='fa fa-chevron-left controls prev'></i>
						<i class='fa fa-chevron-right controls next'></i>";
					}
				}

			$out .= "</div>";
		}

		return $out;
	}
}

class widget_slideshow extends WP_Widget
{
	function __construct()
	{
		$widget_ops = array(
			'classname' => 'slideshow_wrapper',
			'description' => __("Display a slideshow that you have created", 'lang_slideshow')
		);

		$this->arr_default = array(
			'slideshow_heading' => '',
			'parent' => '',
			'slideshow_style' => get_option_or_default('setting_slideshow_style', 'original'),
			'slideshow_autoplay' => 0,
			'slideshow_duration' => 5,
			'slideshow_fade_duration' => 400,
			'slideshow_background' => get_option_or_default('setting_slideshow_background_color', '#000000'),
			'slideshow_random' => 0,
			'slideshow_show_controls' => 'all',
			'slideshow_height_ratio' => get_option_or_default('setting_slideshow_height_ratio', '0.5'),
			'slideshow_height_ratio_mobile' => get_option_or_default('setting_slideshow_height_ratio_mobile', '1'),
		);

		parent::__construct('slideshow-widget', __("Slideshow", 'lang_slideshow'), $widget_ops);

		$this->obj_slideshow = new mf_slideshow();
	}

	function widget($args, $instance)
	{
		global $wpdb;

		extract($args);
		$instance = wp_parse_args((array)$instance, $this->arr_default);

		if($instance['parent'] > 0)
		{
			echo $before_widget;

				if($instance['slideshow_heading'] != '')
				{
					$instance['slideshow_heading'] = apply_filters('widget_title', $instance['slideshow_heading'], $instance, $this->id_base);

					echo $before_title
						.$instance['slideshow_heading']
					.$after_title;
				}

				$arr_slide_images = get_post_meta_file_src(array('post_id' => $instance['parent'], 'meta_key' => $this->obj_slideshow->meta_prefix.'images', 'single' => false));
				$arr_slide_texts = array();

				if(count($arr_slide_images) == 0)
				{
					$result = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title, post_content FROM ".$wpdb->posts." WHERE post_type = 'slideshow' AND post_status = 'publish' AND post_parent = '%d' ORDER BY menu_order ASC", $instance['parent']));

					foreach($result as $r)
					{
						$post_id = $r->ID;
						$post_title = $r->post_title;
						$post_content = $r->post_content;

						$arr_slide_images_child = get_post_meta_file_src(array('post_id' => $post_id, 'meta_key' => $this->obj_slideshow->meta_prefix.'images', 'single' => false));

						foreach($arr_slide_images_child as $child)
						{
							$post_content_position = get_post_meta($post_id, $this->obj_slideshow->meta_prefix.'content_position', true);
							$post_page = get_post_meta($post_id, $this->obj_slideshow->meta_prefix.'page', true);

							if(intval($post_page) > 0)
							{
								$post_url = get_permalink($post_page);
							}

							else
							{
								$post_url = get_post_meta($post_id, $this->obj_slideshow->meta_prefix.'link', true);
							}

							$arr_slide_images[] = $child;
							$arr_slide_texts[] = array(
								'title' => $post_title,
								'content' => $post_content,
								'content_position' => $post_content_position,
								'url' => $post_url,
							);
						}
					}
				}

				if(count($arr_slide_images) > 0)
				{
					/* Add settings to .slideshow to fetch in JS instead */
					$obj_slideshow = new mf_slideshow();
					echo $obj_slideshow->show(array('settings' => $instance, 'images' => $arr_slide_images, 'texts' => $arr_slide_texts));
				}

				else
				{
					echo "<p>".__("I could not find any images to load", 'lang_slideshow')."</p>";
				}

			echo $after_widget;
		}
	}

	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$new_instance = wp_parse_args((array)$new_instance, $this->arr_default);

		$instance['slideshow_heading'] = sanitize_text_field($new_instance['slideshow_heading']);
		$instance['parent'] = sanitize_text_field($new_instance['parent']);
		$instance['slideshow_style'] = sanitize_text_field($new_instance['slideshow_style']);
		$instance['slideshow_autoplay'] = sanitize_text_field($new_instance['slideshow_autoplay']);
		$instance['slideshow_duration'] = sanitize_text_field($new_instance['slideshow_duration']);
		$instance['slideshow_fade_duration'] = sanitize_text_field($new_instance['slideshow_fade_duration']);
		$instance['slideshow_background'] = sanitize_text_field($new_instance['slideshow_background']);
		$instance['slideshow_random'] = sanitize_text_field($new_instance['slideshow_random']);
		$instance['slideshow_show_controls'] = sanitize_text_field($new_instance['slideshow_show_controls']);
		$instance['slideshow_height_ratio'] = str_replace(",", ".", sanitize_text_field($new_instance['slideshow_height_ratio']));
		$instance['slideshow_height_ratio_mobile'] = str_replace(",", ".", sanitize_text_field($new_instance['slideshow_height_ratio_mobile']));

		return $instance;
	}

	function form($instance)
	{
		$obj_slideshow = new mf_slideshow();

		$instance = wp_parse_args((array)$instance, $this->arr_default);

		$arr_data_parents = array();
		get_post_children(array('add_choose_here' => true, 'post_type' => 'slideshow', 'allow_depth' => false), $arr_data_parents);

		$arr_data_styles = $obj_slideshow->get_slideshow_styles_for_select();

		echo "<div class='mf_form'>"
			.show_textfield(array('name' => $this->get_field_name('slideshow_heading'), 'text' => __("Heading", 'lang_slideshow'), 'value' => $instance['slideshow_heading'], 'xtra' => " id='slideshow-title'"))
			.show_select(array('data' => $arr_data_parents, 'name' => $this->get_field_name('parent'), 'text' => __("Parent", 'lang_slideshow'), 'value' => $instance['parent'], 'suffix' => get_option_page_suffix(array('post_type' => 'slideshow', 'value' => $instance['parent']))));

			if(count($arr_data_styles) > 1)
			{
				echo show_select(array('data' => $arr_data_styles, 'name' => $this->get_field_name('slideshow_style'), 'text' => __("Style", 'lang_slideshow'), 'value' => $instance['slideshow_style']));
			}

			else
			{
				echo input_hidden(array('name' => $this->get_field_name('slideshow_style'), 'value' => (is_array($instance['slideshow_style']) ? $instance['slideshow_style'][0] : $instance['slideshow_style'])));
			}

			echo "<div class='flex_flow'>"
				.show_textfield(array('type' => 'color', 'name' => $this->get_field_name('slideshow_background'), 'text' => __("Background Color", 'lang_slideshow'), 'value' => $instance['slideshow_background']))
				.show_textfield(array('name' => $this->get_field_name('slideshow_height_ratio'), 'text' => __("Height Ratio", 'lang_slideshow')." <i class='fa fa-info-circle' title='".sprintf(__("From %s to %s. %s means the slideshow will be presented in landscape, %s means square format and %s means the slideshow is presented in portrait", 'lang_slideshow'), "0.3", "2", "0.3", "1", "2")."'></i>", 'value' => $instance['slideshow_height_ratio']))
				.show_textfield(array('name' => $this->get_field_name('slideshow_height_ratio_mobile'), 'text' => __("Height Ratio", 'lang_slideshow')." (".__("Mobile", 'lang_slideshow').")", 'value' => $instance['slideshow_height_ratio_mobile']))
			."</div>
			<div class='flex_flow'>"
				.show_select(array('data' => $this->obj_slideshow->get_controls_for_select(), 'name' => $this->get_field_name('slideshow_show_controls'), 'text' => __("Show Controls", 'lang_slideshow'), 'value' => $this->obj_slideshow->replace_controls_for_select($instance['slideshow_show_controls'])))
				.show_select(array('data' => get_yes_no_for_select(array('return_integer' => true)), 'name' => $this->get_field_name('slideshow_autoplay'), 'text' => __("Autoplay", 'lang_slideshow'), 'value' => $instance['slideshow_autoplay']));

				if($instance['slideshow_autoplay'] == 1)
				{
					echo show_textfield(array('type' => 'number', 'name' => $this->get_field_name('slideshow_duration'), 'text' => __("Duration", 'lang_slideshow'), 'value' => $instance['slideshow_duration'], 'xtra' => "min='2'", 'suffix' => __("s", 'lang_slideshow')));
				}

			echo "</div>";

			if($instance['slideshow_style'] == 'original')
			{
				echo "<div class='flex_flow'>"
					.show_textfield(array('type' => 'number', 'name' => $this->get_field_name('slideshow_fade_duration'), 'text' => __("Fade Duration", 'lang_slideshow'), 'value' => $instance['slideshow_fade_duration'], 'xtra' => "min='400' max='2000'", 'suffix' => __("ms", 'lang_slideshow')))
					.show_select(array('data' => get_yes_no_for_select(array('return_integer' => true)), 'name' => $this->get_field_name('slideshow_random'), 'text' => __("Random", 'lang_slideshow'), 'value' => $instance['slideshow_random']))
				."</div>";
			}

		echo "</div>";
	}
}