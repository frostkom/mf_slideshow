<?php

class mf_slideshow
{
	var $post_type = 'mf_slideshow';
	var $meta_prefix;
	var $allow_widget_override_default = array('background', 'height_ratio', 'display_controls', 'thumbnail_columns', 'autoplay');

	function __construct()
	{
		$this->meta_prefix = $this->post_type.'_';
	}

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

	function get_slideshow_styles_for_select()
	{
		$arr_data = array(
			'original' => __("Default", 'lang_slideshow'),
			'mosaic' => __("Mosaic", 'lang_slideshow'),
		);

		return $arr_data;
	}

	function get_allow_widget_override_for_select()
	{
		return array(
			'background' => __("Background", 'lang_slideshow'),
			'height_ratio' => __("Height Ratio", 'lang_slideshow'),
			'display_controls' => __("Display", 'lang_slideshow'),
			'thumbnail_columns' => __("Thumbnails", 'lang_slideshow'),
			'autoplay' => __("Autoplay", 'lang_slideshow'),
		);
	}

	function cron_base()
	{
		global $wpdb;

		$obj_cron = new mf_cron();
		$obj_cron->start(__CLASS__);

		if($obj_cron->is_running == false)
		{
			replace_post_type(array('old' => 'slideshow', 'new' => $this->post_type));
			replace_post_meta(array('old' => 'mf_slide_content_position', 'new' => $this->meta_prefix.'content_position'));
			replace_post_meta(array('old' => 'mf_slide_content_style', 'new' => $this->meta_prefix.'content_style'));
			replace_post_meta(array('old' => 'mf_slide_page', 'new' => $this->meta_prefix.'page'));
			replace_post_meta(array('old' => 'mf_slide_link', 'new' => $this->meta_prefix.'link'));
			replace_post_meta(array('old' => 'mf_slide_images', 'new' => $this->meta_prefix.'images'));

			mf_uninstall_plugin(array(
				'options' => array('setting_slideshow_show_controls', 'setting_slideshow_image_steps', 'setting_slideshow_image_columns', 'setting_slideshow_animate', 'setting_slideshow_open_links_in_new_tab', 'setting_slideshow_random', 'setting_slideshow_fade_duration', 'setting_slideshow_duration', 'setting_slideshow_autoplay', 'setting_slideshow_height_ratio', 'setting_slideshow_height_ratio_mobile', 'setting_slideshow_display_text_background', 'setting_slideshow_background_opacity', 'setting_slideshow_style', 'setting_slideshow_background_color', 'setting_slideshow_allow_widget_override', 'setting_slideshow_display_controls'),
			));
		}

		$obj_cron->end();
	}

	function shuffle_assoc($array)
	{
		$keys = array_keys($array);
		shuffle($keys);
		$new = [];

		foreach($keys as $key)
		{
			$new[$key] = $array[$key];
		}

		return $new;
	}

	function block_render_callback($attributes)
	{
		global $wpdb;

		if(!isset($attributes['parent'])){							$attributes['parent'] = '';}
		if(!isset($attributes['slideshow_style'])){					$attributes['slideshow_style'] = 'original';}
		//if(!isset($attributes['slideshow_background'])){			$attributes['slideshow_background'] = "";}
		//if(!isset($attributes['slideshow_background_opacity'])){	$attributes['slideshow_background_opacity'] = "";}
		if(!isset($attributes['slideshow_height_ratio'])){			$attributes['slideshow_height_ratio'] = '0.5';}
		if(!isset($attributes['slideshow_height_ratio_mobile'])){	$attributes['slideshow_height_ratio_mobile'] = '1';}
		if(!isset($attributes['slideshow_display_controls'])){		$attributes['slideshow_display_controls'] = [];}
		if(!isset($attributes['slideshow_autoplay'])){				$attributes['slideshow_autoplay'] = 'no';}
		if(!isset($attributes['slideshow_duration'])){				$attributes['slideshow_duration'] = 5;}
		if(!isset($attributes['slideshow_fade_duration'])){			$attributes['slideshow_fade_duration'] = 400;}
		if(!isset($attributes['slideshow_random'])){				$attributes['slideshow_random'] = 'no';}

		if($attributes['parent'] > 0)
		{
			$attributes['slideshow_height_ratio'] = str_replace(",", ".", $attributes['slideshow_height_ratio']);
			$attributes['slideshow_height_ratio_mobile'] = str_replace(",", ".", $attributes['slideshow_height_ratio_mobile']);

			if($attributes['slideshow_height_ratio'] > 2 || $attributes['slideshow_height_ratio'] < 0.2)
			{
				$attributes['slideshow_height_ratio'] = 1;
			}

			if($attributes['slideshow_height_ratio_mobile'] > 2 || $attributes['slideshow_height_ratio_mobile'] < 0.2)
			{
				$attributes['slideshow_height_ratio_mobile'] = 1;
			}

			$plugin_include_url = plugin_dir_url(__FILE__);

			switch($attributes['slideshow_style'])
			{
				case 'mosaic':
					global $obj_base;

					if(!isset($obj_base))
					{
						$obj_base = new mf_base();
					}

					$plugin_base_include_url = plugins_url()."/mf_base/include/";

					$obj_base->load_font_awesome(array(
						'type' => 'public',
						'plugin_include_url' => $plugin_base_include_url,
					));

					mf_enqueue_script('script_slideshow_mosaic', $plugin_include_url."script_mosaic.js");
					mf_enqueue_style('style_slideshow_mosaic', $plugin_include_url."style_mosaic.css");
				break;

				default:
				case 'original';
					$arr_settings = array(
						'height_ratio' => $attributes['slideshow_height_ratio'],
						'height_ratio_mobile' => $attributes['slideshow_height_ratio_mobile'],
						'display_controls' => $attributes['slideshow_display_controls'],
						'autoplay' => $attributes['slideshow_autoplay'],
						'duration' => $attributes['slideshow_duration'],
						'random' => $attributes['slideshow_random'],
					);

					$arr_settings['fade_duration'] = 400;

					mf_enqueue_style('style_slideshow', $plugin_include_url."style.php");
					mf_enqueue_script('script_swipe', $plugin_include_url."jquery.touchSwipe.min.js");
					mf_enqueue_script('script_slideshow', $plugin_include_url."script.js", $arr_settings);
				break;
			}

			$out = "";

			$arr_slide_images = get_post_meta_file_src(array('post_id' => $attributes['parent'], 'meta_key' => $this->meta_prefix.'images', 'single' => false));
			$arr_slide_texts = [];

			$count_slide_images = count($arr_slide_images);

			if($count_slide_images > 0)
			{
				if(!isset($attributes['slideshow_style'])){						$attributes['slideshow_style'] = 'original';}
				//if($attributes['slideshow_background'] == ''){				$attributes['slideshow_background'] = "#000000";}
				//if($attributes['slideshow_background_opacity'] == ''){		$attributes['slideshow_background_opacity'] = 100;}
				if(!isset($attributes['slideshow_display_text_background'])){	$attributes['slideshow_display_text_background'] = get_option_or_default('setting_slideshow_slideshow_display_text_background', 'yes');}
				if(!isset($attributes['slideshow_display_controls'])){			$attributes['slideshow_display_controls'] = [];} //get_option('setting_slideshow_display_controls')
				if(!isset($attributes['slideshow_random'])){					$attributes['slideshow_random'] = 'no';}

				//$setting_slideshow_open_links_in_new_tab = get_option('setting_slideshow_open_links_in_new_tab');

				/*if(is_array($attributes['slideshow_style']))
				{
					$attributes['slideshow_style'] = $attributes['slideshow_style'][0];
				}*/

				$images_html = $dots_html = "";
				$i = $active_i = 1;

				if($attributes['slideshow_random'] == 'yes')
				{
					//shuffle($arr_slide_images);
					$arr_slide_images = $this->shuffle_assoc($arr_slide_images);
				}

				foreach($arr_slide_images as $key => $image)
				{
					switch($attributes['slideshow_style'])
					{
						case 'mosaic':
							$images_html .= "<div rel='".$key."'>"
								.render_image_tag(array('id' => $key, 'src' => $image, 'size' => 'full'))
							."</div>";
						break;

						default:
						case 'original':
							$container_class = "slide_item";

							$has_texts = (count($arr_slide_texts) > 0 && isset($arr_slide_texts[$key]));

							if($has_texts)
							{
								$container_class .= ($container_class != '' ? " " : "")."slide_parent_".$arr_slide_texts[$key]['parent_id'];
							}

							if($i == $active_i)
							{
								$container_class .= ($container_class != '' ? " " : "")."active active_init";
							}

							$images_html .= "<div"
								.($has_texts ? " id='slide_".$arr_slide_texts[$key]['id']."'" : "")
								.($container_class != '' ? " class='".$container_class."'" : "")
								." rel='".$i."'"
							.">
								<img src='".$image."'>";

								if($has_texts)
								{
									$content_class = "content";

									if($arr_slide_texts[$key]['content_position'] != '')
									{
										$content_class .= ($content_class != '' ? " " : "").$arr_slide_texts[$key]['content_position'];
									}

									$images_html .= "<div class='".$content_class."'>
										<div>
											<h4>".$arr_slide_texts[$key]['title']."</h4>"
											.apply_filters('the_content', $arr_slide_texts[$key]['content']);

											if($arr_slide_texts[$key]['url'] != '')
											{
												$images_html .= "<a href='".$arr_slide_texts[$key]['url']."'";

													/*switch($setting_slideshow_open_links_in_new_tab)
													{
														case 'yes':
															if(strpos($arr_slide_texts[$key]['url'], get_site_url()) === false)
															{
																$images_html .= " rel='external'";
															}
														break;

														default:
															//Do nothing
														break;
													}*/

												$images_html .= ">".__("Read More", 'lang_slideshow')."&hellip;</a>";
											}

										$images_html .= "</div>
									</div>";
								}

							$images_html .= "</div>";

							$dots_html .= "<li".($i == $active_i ? " class='active'" : "")." rel='".$i."'></li>";
						break;
					}

					$i++;
				}

				$slideshow_classes = "widget slideshow ".$attributes['slideshow_style'];
				$slideshow_style = $slideshow_attributes = "";

				if($attributes['slideshow_display_text_background'] == 'yes')
				{
					$slideshow_classes .= " display_text_background";
				}

				/*if($attributes['slideshow_background'] != '')
				{
					if($attributes['slideshow_background_opacity'] != '')
					{
						list($r, $g, $b) = sscanf($attributes['slideshow_background'], "#%02x%02x%02x");

						$attributes['slideshow_background'] = "rgba(".$r.", ".$g.", ".$b.", ".($attributes['slideshow_background_opacity'] / 100).")";
					}

					$slideshow_style .= "background-color: ".$attributes['slideshow_background'].";";
				}*/

				$slideshow_attributes .= " data-random='".$attributes['slideshow_random']."'";

				$arr_attributes = array('autoplay', 'animate', 'duration', 'fade_duration', 'display_text_background', 'height_ratio', 'height_ratio_mobile');

				foreach($arr_attributes as $attribute)
				{
					if(isset($attributes['slideshow_'.$attribute]))
					{
						switch($attribute)
						{
							case 'duration':
								$attributes['slideshow_'.$attribute] *= 1000;
							break;
						}

						$slideshow_attributes .= " data-".$attribute."='".$attributes['slideshow_'.$attribute]."'";
					}
				}

				$out = "<div"
					.parse_block_attributes(array('class' => $slideshow_classes, 'attributes' => $attributes, 'style' => $slideshow_style))
					.$slideshow_attributes
				.">";

					switch($attributes['slideshow_style'])
					{
						case 'mosaic':
							// Add nothing
						break;

						default:
						case 'original':
							$out .= "<div class='slideshow_container'>
								<div class='slideshow_images columns_1'>";
						break;
					}

						$out .= $images_html;

					switch($attributes['slideshow_style'])
					{
						case 'mosaic':
							// Add nothing
						break;

						default:
						case 'original':
								$out .= "</div>";

								if($count_slide_images > 1)
								{
									$out .= "<div class='controls_arrows'>
										<div class='panel_arrow_left'>";

											if(is_array($attributes['slideshow_display_controls']) && in_array('arrows', $attributes['slideshow_display_controls']))
											{
												$out .= "<i class='fa fa-chevron-left arrow_left'></i>";
											}

										$out .= "</div>
										<div class='panel_arrow_right'>";

											if(is_array($attributes['slideshow_display_controls']) && in_array('arrows', $attributes['slideshow_display_controls']))
											{
												$out .= "<i class='fa fa-chevron-right arrow_right'></i>";
											}

										$out .= "</div>
									</div>";

									if(is_array($attributes['slideshow_display_controls']))
									{
										if(in_array('magnifying_glass', $attributes['slideshow_display_controls']))
										{
											$out .= "<i class='fa fa-search controls_magnifying_glass'></i>";
										}

										if(in_array('dots', $attributes['slideshow_display_controls']))
										{
											$out .= "<ul class='controls_dots'>".$dots_html."</ul>";
										}
									}
								}

							$out .= "</div>";

							if($count_slide_images > 1 && is_array($attributes['slideshow_display_controls']) && in_array('thumbnails', $attributes['slideshow_display_controls']))
							{
								$site_url = get_site_url();
								$i = 1;

								$setting_slideshow_thumbnail_columns = get_option('setting_slideshow_thumbnail_columns', 5);
								$setting_slideshow_thumbnail_rows = get_option('setting_slideshow_thumbnail_rows');

								$out .= "<ul class='slideshow_thumbnails thumbnail_columns_".$setting_slideshow_thumbnail_columns." thumbnail_rows_".$setting_slideshow_thumbnail_rows."'>";

									foreach($arr_slide_images as $key => $image)
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
											.render_image_tag(array('id' => $key, 'src' => $image, 'size' => 'thumbnail'))
										."</li>";

										$i++;
									}

								$out .= "</ul>";
							}
						break;
					}

				$out .= "</div>";
			}

			else
			{
				$out .= "<p>".__("I could not find any images to load", 'lang_slideshow')."</p>";
			}
		}

		else
		{
			$out .= "<p>".__("You have to choose a gallery to be displayed here", 'lang_slideshow')."</p>";
		}

		return $out;
	}

	function enqueue_block_editor_assets()
	{
		$plugin_include_url = plugin_dir_url(__FILE__);
		$plugin_version = get_plugin_version(__FILE__);

		wp_register_script('script_slideshow_block_wp', $plugin_include_url."block/script_wp.js", array('wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-block-editor'), $plugin_version, true);

		$arr_data_parents = [];
		get_post_children(array('add_choose_here' => true, 'post_type' => $this->post_type, 'allow_depth' => false), $arr_data_parents);

		wp_localize_script('script_slideshow_block_wp', 'script_slideshow_block_wp', array(
			'block_title' => __("Slideshow", 'lang_slideshow'),
			'block_description' => __("Display Slideshow", 'lang_slideshow'),
			'parent_label' => __("Parent", 'lang_slideshow'),
			'arr_parents' => $arr_data_parents,
			'slideshow_style_label' => __("Style", 'lang_slideshow'),
			'arr_slideshow_style' => $this->get_slideshow_styles_for_select(),
			'slideshow_display_text_background_label' => __("Display Text Background", 'lang_slideshow'),
			'yes_no_for_select' => get_yes_no_for_select(),
			'slideshow_height_ratio_label' => __("Height Ratio", 'lang_slideshow'),
			'slideshow_height_ratio_mobile_label' => " - ".__("Mobile", 'lang_slideshow'),
			'slideshow_image_fit_label' => __("Image Fit", 'lang_slideshow'),
			'arr_slideshow_image_fit' => $this->get_image_fit_for_select(),
			'slideshow_display_controls_label' => __("Display", 'lang_slideshow'),
			'arr_slideshow_display_controls' => $this->get_display_controls_for_select(),
			'slideshow_autoplay_label' => __("Autoplay", 'lang_slideshow'),
			'slideshow_duration_label' => __("Duration", 'lang_slideshow')." (s)",
			'slideshow_fade_duration_label' => __("Fade Duration", 'lang_slideshow')." (ms)",
			'slideshow_random_label' => __("Random", 'lang_slideshow'),
		));
	}

	function init()
	{
		load_plugin_textdomain('lang_slideshow', false, str_replace("/include", "", dirname(plugin_basename(__FILE__)))."/lang/");

		register_post_type($this->post_type, array(
			'labels' => array(
				'name' => __("Slideshows", 'lang_slideshow'),
				'singular_name' => __("Slideshow", 'lang_slideshow'),
				'menu_name' => __("Slideshow", 'lang_slideshow')
			),
			'public' => false,
			'show_ui' => true,
			'show_in_rest' => true,
			'exclude_from_search' => true,
			'capability_type' => 'page',
			'menu_position' => 21,
			'menu_icon' => 'dashicons-format-gallery',
			'supports' => array('title', 'editor', 'page-attributes'),
			'hierarchical' => true,
			'has_archive' => false,
		));

		register_block_type('mf/slideshow', array(
			'editor_script' => 'script_slideshow_block_wp',
			'editor_style' => 'style_base_block_wp',
			'render_callback' => array($this, 'block_render_callback'),
			//'style' => 'style_base_block_wp',
		));
	}

	function settings_slideshow()
	{
		$options_area = __FUNCTION__;

		add_settings_section($options_area, "", array($this, $options_area."_callback"), BASE_OPTIONS_PAGE);

		$arr_settings = [];
		//$arr_settings['setting_slideshow_style'] = __("Style", 'lang_slideshow');
		//$arr_settings['setting_slideshow_allow_widget_override'] = __("Allow Widget Override", 'lang_slideshow');
		//$arr_settings['setting_slideshow_background_color'] = __("Background Color", 'lang_slideshow');
		//$arr_settings['setting_slideshow_background_opacity'] = " - ".__("Opacity", 'lang_slideshow');
		//$arr_settings['setting_slideshow_display_text_background'] = __("Display Text Background", 'lang_slideshow');
		//$arr_settings['setting_slideshow_height_ratio'] = __("Height Ratio", 'lang_slideshow');
		//$arr_settings['setting_slideshow_height_ratio_mobile'] = __("Height Ratio", 'lang_slideshow')." (".__("Mobile", 'lang_slideshow').")";
		$arr_settings['setting_slideshow_image_fit'] = __("Image Fit", 'lang_slideshow');
		/*$arr_settings['setting_slideshow_display_controls'] = __("Display", 'lang_slideshow');

		$setting_slideshow_display_controls = get_option('setting_slideshow_display_controls');

		if(is_array($setting_slideshow_display_controls) && in_array('thumbnails', $setting_slideshow_display_controls))
		{*/
			$arr_settings['setting_slideshow_thumbnail_columns'] = __("Thumbnail Columns", 'lang_slideshow');
			$arr_settings['setting_slideshow_thumbnail_rows'] = __("Thumbnail Rows", 'lang_slideshow');
		//}

		//$arr_settings['setting_slideshow_autoplay'] = __("Autoplay", 'lang_slideshow');

		/*if(get_option('setting_slideshow_autoplay') == 1 || get_option('setting_slideshow_autoplay') == 'yes')
		{
			$arr_settings['setting_slideshow_duration'] = __("Duration", 'lang_slideshow');
		}*/

		/*if(in_array('original', get_option('setting_slideshow_style', array('original'))))
		{
			$arr_settings['setting_slideshow_fade_duration'] = __("Fade Duration", 'lang_slideshow');

			if(wp_is_block_theme() == false)
			{
				$arr_settings['setting_slideshow_random'] = __("Random", 'lang_slideshow');
			}
		}*/

		//$arr_settings['setting_slideshow_open_links_in_new_tab'] = __("Open Links in new Tabs", 'lang_slideshow');

		show_settings_fields(array('area' => $options_area, 'object' => $this, 'settings' => $arr_settings));
	}

	function settings_slideshow_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);

		echo settings_header($setting_key, __("Slideshow", 'lang_slideshow'));
	}

	/*function setting_slideshow_style_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, array('original'));

		echo show_select(array('data' => $this->get_slideshow_styles_for_select(), 'name' => $setting_key."[]", 'value' => $option));
	}*/

	/*function setting_slideshow_allow_widget_override_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, $this->allow_widget_override_default);

		echo show_select(array('data' => $this->get_allow_widget_override_for_select(), 'name' => $setting_key."[]", 'value' => $option));
	}*/

	/*function setting_slideshow_background_color_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, "#000000");

		echo show_textfield(array('type' => 'color', 'name' => $setting_key, 'value' => $option));
	}*/

		/*function setting_slideshow_background_opacity_callback()
		{
			$setting_key = get_setting_key(__FUNCTION__);
			$option = get_option($setting_key, 100);

			echo show_textfield(array('type' => 'number', 'name' => $setting_key, 'value' => $option, 'suffix' => "%"));
		}*/

	/*function setting_slideshow_display_text_background_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 'yes');

		echo show_select(array('data' => get_yes_no_for_select(), 'name' => $setting_key, 'value' => $option));
	}*/

	/*function setting_slideshow_height_ratio_callback()
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
	}*/

	function get_image_fit_for_select()
	{
		return array(
			'none' => "-- ".__("None", 'lang_slideshow')." --",
			'cover' => __("Cover", 'lang_slideshow'),
			'contain' => __("Contain", 'lang_slideshow'),
		);
	}

	function setting_slideshow_image_fit_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 'cover');

		echo show_select(array('data' => $this->get_image_fit_for_select(), 'name' => $setting_key, 'value' => $option));
	}

	/*function setting_slideshow_display_controls_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, array('thumbnails'));

		echo show_select(array('data' => $this->get_display_controls_for_select(), 'name' => $setting_key."[]", 'value' => $option));
	}*/

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

	/*function setting_slideshow_autoplay_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 'no');

		echo show_select(array('data' => get_yes_no_for_select(), 'name' => $setting_key, 'value' => $option)); //array('return_integer' => true)
	}*/

	/*function setting_slideshow_duration_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 5);

		echo show_textfield(array('type' => 'number', 'name' => $setting_key, 'value' => $option, 'xtra' => "min='2'", 'suffix' => __("s", 'lang_slideshow')));
	}*/

	/*function setting_slideshow_fade_duration_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 400);

		echo show_textfield(array('type' => 'number', 'name' => $setting_key, 'value' => $option, 'xtra' => "min='400' max='2000'", 'suffix' => __("ms", 'lang_slideshow')));
	}*/

	/*function setting_slideshow_random_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 'no');

		echo show_select(array('data' => get_yes_no_for_select(array('return_integer' => true)), 'name' => $setting_key, 'value' => $option));
	}*/

	/*function setting_slideshow_open_links_in_new_tab_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 'no');

		$arr_data = array(
			'no' => __("No", 'lang_slideshow'),
			'yes' => __("Yes", 'lang_slideshow')." (".__("When link is external", 'lang_slideshow').")",
		);

		echo show_select(array('data' => $arr_data, 'name' => $setting_key, 'value' => $option));
	}*/

	function filter_sites_table_pages($arr_pages)
	{
		$arr_pages[$this->post_type] = array(
			'icon' => "fas fa-images",
			'title' => __("Slideshows", 'lang_slideshow'),
		);

		return $arr_pages;
	}

	function rwmb_meta_boxes($meta_boxes)
	{
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

		if(wp_is_block_theme() == false)
		{
			$arr_data_pages = [];
			get_post_children(array('add_choose_here' => true, 'post_type' => $this->post_type), $arr_data_pages);

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
						'options' => $arr_data_pages,
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
		}

		return $meta_boxes;
	}

	function column_header($columns)
	{
		global $post_type;

		unset($columns['date']);

		switch($post_type)
		{
			case $this->post_type:
				$columns['images'] = __("Images", 'lang_slideshow');
			break;
		}

		return $columns;
	}

	function column_cell($column, $post_id)
	{
		global $wpdb, $post;

		switch($post->post_type)
		{
			case $this->post_type:
				switch($column)
				{
					case 'images':
						$arr_images = get_post_meta($post_id, $this->meta_prefix.$column);

						echo count($arr_images);
					break;
				}
			break;
		}
	}

	function filter_actions($data = [])
	{
		global $post;

		if(!isset($data['actions'])){	$data['actions'] = [];}
		if(!isset($data['class'])){		$data['class'] = "";}

		$block_code = '<!-- wp:mf/slideshow {"parent":"'.$post->ID.'"} /-->';
		$arr_ids = apply_filters('get_page_from_block_code', [], $block_code);

		if(count($arr_ids) > 0)
		{
			foreach($arr_ids as $post_id_temp)
			{
				$data['actions']['edit_page'] = "<a href='".admin_url("post.php?post=".$post_id_temp."&action=edit")."'".($data['class'] != '' ? " class='".$data['class']."'" : "").">".__("Edit Page", 'lang_slideshow')."</a>";
				$data['actions']['view_page'] = "<a href='".get_permalink($post_id_temp)."'".($data['class'] != '' ? " class='".$data['class']."'" : "").">".__("View", 'lang_slideshow')."</a>";
			}
		}

		return $data['actions'];
	}

	function row_actions($arr_actions, $post)
	{
		if($post->post_type == $this->post_type)
		{
			$arr_actions = $this->filter_actions(array('actions' => $arr_actions));
		}

		return $arr_actions;
	}

	function filter_is_file_used($arr_used)
	{
		global $wpdb;

		$result = $wpdb->get_results($wpdb->prepare("SELECT ID FROM ".$wpdb->posts." INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id WHERE post_status = %s AND meta_key = %s AND meta_value LIKE %s", 'publish', $this->meta_prefix.'images', "%".$arr_used['id']."%"));
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

				$arr_used['example'] = admin_url("post.php?action=edit&post=".$r->ID);
			}
		}

		return $arr_used;
	}

	function widgets_init()
	{
		if(wp_is_block_theme() == false)
		{
			register_widget('widget_slideshow');
		}
	}
}

class widget_slideshow extends WP_Widget
{
	var $obj_slideshow;
	var $widget_ops;
	var $arr_default;
	//var $setting_slideshow_allow_widget_override;

	function __construct()
	{
		$this->obj_slideshow = new mf_slideshow();

		$this->widget_ops = array(
			'classname' => 'slideshow_wrapper',
			'description' => __("Display a slideshow that you have created", 'lang_slideshow'),
		);

		//$this->setting_slideshow_allow_widget_override = get_option('setting_slideshow_allow_widget_override', $this->obj_slideshow->allow_widget_override_default);

		$this->arr_default = array(
			'slideshow_heading' => '',
			'parent' => '',
			'slideshow_style' => 'original',
		);

		//$this->arr_default['slideshow_background'] = "#000000";
		//$this->arr_default['slideshow_background_opacity'] = 100;
		$this->arr_default['slideshow_display_text_background'] = 'yes';
		$this->arr_default['slideshow_height_ratio'] = '0.5';
		$this->arr_default['slideshow_height_ratio_mobile'] = '1';
		$this->arr_default['slideshow_display_controls'] = []; //get_option('setting_slideshow_display_controls')
		$this->arr_default['slideshow_autoplay'] = 'no';
		$this->arr_default['slideshow_duration'] = 5;
		$this->arr_default['slideshow_fade_duration'] = 400;
		$this->arr_default['slideshow_random'] = 'no';

		parent::__construct('slideshow-widget', __("Slideshow", 'lang_slideshow'), $this->widget_ops);
	}

	function widget($args, $instance)
	{
		do_log(__CLASS__."->".__FUNCTION__."(): Add a block instead", 'publish', false);
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
		$instance['slideshow_random'] = sanitize_text_field($new_instance['slideshow_random']);
		//$instance['slideshow_background'] = sanitize_text_field($new_instance['slideshow_background']);
		//$instance['slideshow_background_opacity'] = sanitize_text_field($new_instance['slideshow_background_opacity']);
		$instance['slideshow_display_text_background'] = sanitize_text_field($new_instance['slideshow_display_text_background']);
		$instance['slideshow_display_controls'] = is_array($new_instance['slideshow_display_controls']) ? $new_instance['slideshow_display_controls'] : [];
		$instance['slideshow_height_ratio'] = str_replace(",", ".", sanitize_text_field($new_instance['slideshow_height_ratio']));
		$instance['slideshow_height_ratio_mobile'] = str_replace(",", ".", sanitize_text_field($new_instance['slideshow_height_ratio_mobile']));

		return $instance;
	}

	function form($instance)
	{
		$instance = wp_parse_args((array)$instance, $this->arr_default);

		$arr_data_parents = [];
		get_post_children(array('add_choose_here' => true, 'post_type' => $this->obj_slideshow->post_type, 'allow_depth' => false), $arr_data_parents);

		echo "<div class='mf_form'>"
			.show_textfield(array('name' => $this->get_field_name('slideshow_heading'), 'text' => __("Heading", 'lang_slideshow'), 'value' => $instance['slideshow_heading'], 'xtra' => " id='".$this->widget_ops['classname']."-title'"))
			.show_select(array('data' => $arr_data_parents, 'name' => $this->get_field_name('parent'), 'text' => __("Parent", 'lang_slideshow'), 'value' => $instance['parent'], 'suffix' => get_option_page_suffix(array('post_type' => $this->obj_slideshow->post_type, 'value' => $instance['parent']))))
			.show_select(array('data' => $this->obj_slideshow->get_slideshow_styles_for_select(), 'name' => $this->get_field_name('slideshow_style'), 'text' => __("Style", 'lang_slideshow'), 'value' => $instance['slideshow_style']));

			/*if(is_array($this->setting_slideshow_allow_widget_override) && in_array('background', $this->setting_slideshow_allow_widget_override))
			{*/
				echo "<div class='flex_flow'>"
					//.show_textfield(array('type' => 'color', 'name' => $this->get_field_name('slideshow_background'), 'text' => __("Background Color", 'lang_slideshow'), 'value' => $instance['slideshow_background']))
					//.show_textfield(array('type' => 'number', 'name' => $this->get_field_name('slideshow_background_opacity'), 'text' => " - ".__("Opacity", 'lang_slideshow'), 'value' => $instance['slideshow_background_opacity']))
					.show_select(array('data' => get_yes_no_for_select(), 'name' => $this->get_field_name('slideshow_display_text_background'), 'text' => __("Display Text Background", 'lang_slideshow'), 'value' => $instance['slideshow_display_text_background']))
				."</div>";
			//}

			/*if(is_array($this->setting_slideshow_allow_widget_override) && (in_array('image_columns', $this->setting_slideshow_allow_widget_override) || in_array('height_ratio', $this->setting_slideshow_allow_widget_override)))
			{
				if(in_array('height_ratio', $this->setting_slideshow_allow_widget_override))
				{*/
					echo "<div class='flex_flow'>";

						echo show_textfield(array('name' => $this->get_field_name('slideshow_height_ratio'), 'text' => __("Height Ratio", 'lang_slideshow')." <i class='fa fa-info-circle' title='".sprintf(__("From %s to %s. %s means the slideshow will be presented in landscape, %s means square format and %s means the slideshow is presented in portrait", 'lang_slideshow'), "0.3", "2", "0.3", "1", "2")."'></i>", 'value' => $instance['slideshow_height_ratio']))
						.show_textfield(array('name' => $this->get_field_name('slideshow_height_ratio_mobile'), 'text' => __("Height Ratio", 'lang_slideshow')." (".__("Mobile", 'lang_slideshow').")", 'value' => $instance['slideshow_height_ratio_mobile']));

					echo "</div>";
				/*}
			}*/

			/*if(is_array($this->setting_slideshow_allow_widget_override) && in_array('display_controls', $this->setting_slideshow_allow_widget_override) || in_array('autoplay', $this->setting_slideshow_allow_widget_override))
			{*/
				echo "<div class='flex_flow'>";

					/*if(in_array('display_controls', $this->setting_slideshow_allow_widget_override))
					{*/
						echo show_select(array('data' => $this->obj_slideshow->get_display_controls_for_select(), 'name' => $this->get_field_name('slideshow_display_controls')."[]", 'text' => __("Display", 'lang_slideshow'), 'value' => $instance['slideshow_display_controls']));
					//}

					/*if(in_array('autoplay', $this->setting_slideshow_allow_widget_override))
					{*/
						if($instance['slideshow_autoplay'] == '') // Backwards compatibility
						{
							$instance['slideshow_autoplay'] = 'no';
						}

						echo show_select(array('data' => get_yes_no_for_select(), 'name' => $this->get_field_name('slideshow_autoplay'), 'text' => __("Autoplay", 'lang_slideshow'), 'value' => $instance['slideshow_autoplay']));
					//}

				echo "</div>";
			//}

			/*if(is_array($this->setting_slideshow_allow_widget_override) && in_array('autoplay', $this->setting_slideshow_allow_widget_override))
			{*/
				if($instance['slideshow_autoplay'] == 1 || $instance['slideshow_autoplay'] == 'yes')
				{
					echo show_textfield(array('type' => 'number', 'name' => $this->get_field_name('slideshow_duration'), 'text' => __("Duration", 'lang_slideshow'), 'value' => $instance['slideshow_duration'], 'xtra' => "min='2'", 'suffix' => __("s", 'lang_slideshow')));
				}

				if($instance['slideshow_style'] == 'original')
				{
					if($instance['slideshow_random'] == '') // Backwards compatibility
					{
						$instance['slideshow_random'] = 'no';
					}

					echo "<div class='flex_flow'>"
						.show_textfield(array('type' => 'number', 'name' => $this->get_field_name('slideshow_fade_duration'), 'text' => __("Fade Duration", 'lang_slideshow'), 'value' => $instance['slideshow_fade_duration'], 'xtra' => "min='400' max='4000'", 'suffix' => __("ms", 'lang_slideshow')))
						.show_select(array('data' => get_yes_no_for_select(), 'name' => $this->get_field_name('slideshow_random'), 'text' => __("Random", 'lang_slideshow'), 'value' => $instance['slideshow_random']))
					."</div>";
				}
			//}

		echo "</div>";
	}
}