<?php

class mf_slideshow
{
	function __construct()
	{
		$this->meta_prefix = "mf_slide_";
	}

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
										<h3>".$data['texts'][$key]['title']."</h3>"
										.apply_filters('the_content', $data['texts'][$key]['content']);

										if($data['texts'][$key]['url'] != '')
										{
											$images .= "<a href='".$data['texts'][$key]['url']."'>".__("Read More", 'lang_slideshow')."&hellip;</a>";
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
						$out .= "<i class='controls arrow_left fa fa-chevron-left'></i>
						<i class='controls arrow_right fa fa-chevron-right'></i>
						<ul class='controls'>"
							.$dots
						."</ul>";
					}

					else if($data['settings']['slideshow_style'] == 'carousel')
					{
						$out .= "<i class='controls prev fa fa-chevron-left'></i>
						<i class='controls next fa fa-chevron-right'></i>";
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
			'description' => __("Display a slideshow that you've created", 'lang_slideshow')
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
			'slideshow_show_controls' => 1,
			'slideshow_height_ratio' => get_option_or_default('setting_slideshow_height_ratio', '0.5'),
			'slideshow_height_ratio_mobile' => get_option_or_default('setting_slideshow_height_ratio_mobile', '1'),
		);

		parent::__construct('slideshow-widget', __("Slideshow", 'lang_slideshow'), $widget_ops);

		$this->meta_prefix = "mf_slide_";
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
					echo $before_title
						.$instance['slideshow_heading']
					.$after_title;
				}

				$arr_slide_images = get_post_meta_file_src(array('post_id' => $instance['parent'], 'meta_key' => $this->meta_prefix.'images', 'single' => false));
				$arr_slide_texts = array();

				if(count($arr_slide_images) == 0)
				{
					$result = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title, post_content FROM ".$wpdb->posts." WHERE post_type = 'slideshow' AND post_status = 'publish' AND post_parent = '%d' ORDER BY menu_order ASC", $instance['parent']));

					foreach($result as $r)
					{
						$post_id = $r->ID;
						$post_title = $r->post_title;
						$post_content = $r->post_content;

						$arr_slide_images_child = get_post_meta_file_src(array('post_id' => $post_id, 'meta_key' => $this->meta_prefix.'images', 'single' => false));

						foreach($arr_slide_images_child as $child)
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
		$instance['slideshow_height_ratio'] = sanitize_text_field($new_instance['slideshow_height_ratio']);
		$instance['slideshow_height_ratio_mobile'] = sanitize_text_field($new_instance['slideshow_height_ratio_mobile']);

		return $instance;
	}

	function form($instance)
	{
		global $wpdb;

		$instance = wp_parse_args((array)$instance, $this->arr_default);

		$arr_data = array();
		get_post_children(array('add_choose_here' => true, 'post_type' => 'slideshow', 'allow_depth' => false), $arr_data);

		echo "<div class='mf_form'>"
			.show_textfield(array('name' => $this->get_field_name('slideshow_heading'), 'text' => __("Heading", 'lang_slideshow'), 'value' => $instance['slideshow_heading']))
			.show_select(array('data' => $arr_data, 'name' => $this->get_field_name('parent'), 'text' => __("Parent", 'lang_slideshow'), 'value' => $instance['parent']))
			.show_select(array('data' => get_slideshow_styles_for_select(), 'name' => $this->get_field_name('slideshow_style'), 'text' => __("Style", 'lang_slideshow'), 'value' => $instance['slideshow_style']))
			."<div class='flex_flow'>"
				.show_textfield(array('type' => 'color', 'name' => $this->get_field_name('slideshow_background'), 'text' => __("Background Color", 'lang_slideshow'), 'value' => $instance['slideshow_background']))
				.show_textfield(array('name' => $this->get_field_name('slideshow_height_ratio'), 'text' => __("Height Ratio", 'lang_slideshow')." <i class='fa fa-info-circle' title='".__("From 0,3 to 2. 0,3 means the slideshow will be presented in landscape, 1 means square format and 2 means the slideshow i presented in portrait", 'lang_slideshow')."'></i>", 'value' => $instance['slideshow_height_ratio']))
				.show_textfield(array('name' => $this->get_field_name('slideshow_height_ratio_mobile'), 'text' => __("Height Ratio", 'lang_slideshow')." (".__("Mobile", 'lang_slideshow').")", 'value' => $instance['slideshow_height_ratio_mobile']))
			."</div>"
			.show_select(array('data' => get_yes_no_for_select(array('return_integer' => true)), 'name' => $this->get_field_name('slideshow_show_controls'), 'text' => __("Show Controls", 'lang_slideshow'), 'value' => $instance['slideshow_show_controls']))
			."<div class='flex_flow'>"
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