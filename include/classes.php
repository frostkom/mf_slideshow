<?php

class mf_slideshow
{
	var $post_type = __CLASS__;
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
				'options' => array('setting_slideshow_show_controls', 'setting_slideshow_image_steps', 'setting_slideshow_image_columns', 'setting_slideshow_animate', 'setting_slideshow_open_links_in_new_tab', 'setting_slideshow_random', 'setting_slideshow_fade_duration', 'setting_slideshow_duration', 'setting_slideshow_autoplay', 'setting_slideshow_height_ratio', 'setting_slideshow_height_ratio_mobile', 'setting_slideshow_display_text_background', 'setting_slideshow_background_opacity', 'setting_slideshow_style', 'setting_slideshow_background_color', 'setting_slideshow_allow_widget_override', 'setting_slideshow_display_controls', 'setting_slideshow_image_fit', 'setting_slideshow_thumbnail_columns', 'setting_slideshow_thumbnail_rows'),
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
		if(!isset($attributes['slideshow_height_ratio'])){			$attributes['slideshow_height_ratio'] = '0.5';}
		if(!isset($attributes['slideshow_height_ratio_mobile'])){	$attributes['slideshow_height_ratio_mobile'] = '1';}
		if(!isset($attributes['slideshow_image_fit'])){				$attributes['slideshow_image_fit'] = '';}
		if(!isset($attributes['slideshow_display_controls'])){		$attributes['slideshow_display_controls'] = [];}
		if(!isset($attributes['slideshow_autoplay'])){				$attributes['slideshow_autoplay'] = 'no';}
		if(!isset($attributes['slideshow_duration'])){				$attributes['slideshow_duration'] = 5;}
		if(!isset($attributes['slideshow_fade_duration'])){			$attributes['slideshow_fade_duration'] = 400;}
		if(!isset($attributes['slideshow_random'])){				$attributes['slideshow_random'] = 'no';}
		if(!isset($attributes['slideshow_thumbnail_columns'])){		$attributes['slideshow_thumbnail_columns'] = 5;}
		if(!isset($attributes['slideshow_thumbnail_rows'])){		$attributes['slideshow_thumbnail_rows'] = '';}

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
					do_action('load_font_awesome');
					do_action('load_lightbox');

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

			// Find children
			if($count_slide_images == 0)
			{
				$result = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title, post_content FROM ".$wpdb->posts." WHERE post_type = %s AND post_status = %s AND post_parent = '%d' ORDER BY menu_order ASC", $this->post_type, 'publish', $attributes['parent']));

				foreach($result as $r)
				{
					$post_id = $r->ID;
					$post_title = $r->post_title;
					$post_content = $r->post_content;

					$img_src = get_post_meta_file_src(array('post_id' => $post_id, 'meta_key' => $this->meta_prefix.'images', 'single' => true));

					if($img_src != '')
					{
						$arr_slide_images[$post_id] = $img_src;
					}

					else
					{
						$arr_slide_images[$post_id] = apply_filters('get_image_fallback', "", 'url');
					}

					$arr_slide_texts[$post_id] = array(
						//'parent_id' => $attributes['parent'],
						'title' => $post_title,
						'content' => $post_content,
					);
				}
			}

			$count_slide_images = count($arr_slide_images);

			if($count_slide_images > 0)
			{
				$images_html = $dots_html = "";
				$i = $active_i = 1;

				if($attributes['slideshow_random'] == 'yes')
				{
					$arr_slide_images = $this->shuffle_assoc($arr_slide_images);
				}

				foreach($arr_slide_images as $key => $image)
				{
					switch($attributes['slideshow_style'])
					{
						case 'mosaic':
							$images_html .= "<figure class='wp-block-image'>"
								.render_image_tag(array('id' => $key, 'size' => 'large')) //, 'src' => $image
							."</figure>";
						break;

						default:
						case 'original':
							$container_class = "slide_item";

							if($i == $active_i)
							{
								$container_class .= ($container_class != '' ? " " : "")."active active_init";
							}

							$images_html .= "<div class='".$container_class."' rel='".$i."'>
								<img src='".$image."' alt='".__("Slideshow Image", 'lang_slideshow')."'>";

								if(count($arr_slide_texts) > 0 && isset($arr_slide_texts[$key]))
								{
									$images_html .= "<div class='content'>";

										if($arr_slide_texts[$key]['title'] != '')
										{
											$images_html .= "<h4>".$arr_slide_texts[$key]['title']."</h4>";
										}

										if($arr_slide_texts[$key]['content'] != '')
										{
											$images_html .= apply_filters('the_content', $arr_slide_texts[$key]['content']);
										}

									$images_html .= "</div>";
								}

							$images_html .= "</div>";

							$dots_html .= "<li".($i == $active_i ? " class='active'" : "")." rel='".$i."'></li>";
						break;
					}

					$i++;
				}

				$slideshow_attributes = " data-random='".$attributes['slideshow_random']."'";

				$arr_attributes = array('autoplay', 'animate', 'duration', 'fade_duration', 'height_ratio', 'height_ratio_mobile');

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
					.parse_block_attributes(array('class' => "widget slideshow ".$attributes['slideshow_style'], 'attributes' => $attributes)) //, 'style' => $slideshow_style
					.$slideshow_attributes
				.">";

					/*if(IS_SUPER_ADMIN)
					{
						$out .= var_export($attributes, true);
					}*/

					switch($attributes['slideshow_style'])
					{
						case 'mosaic':
							// Add nothing
						break;

						default:
						case 'original':
							$out .= "<div class='slideshow_container'>
								<div class='slideshow_images".($attributes['slideshow_image_fit'] != '' && $attributes['slideshow_image_fit'] != 'none' ? " slideshow_image_fit_".$attributes['slideshow_image_fit'] : "")."'>"; // columns_1
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
												do_action('load_font_awesome');

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

									if(is_array($attributes['slideshow_display_controls']) && in_array('dots', $attributes['slideshow_display_controls']))
									{
										$out .= "<ul class='controls_dots'>".$dots_html."</ul>";
									}
								}

							$out .= "</div>";

							if($count_slide_images > 1 && is_array($attributes['slideshow_display_controls']) && in_array('thumbnails', $attributes['slideshow_display_controls']))
							{
								$site_url = get_site_url();
								$i = 1;

								$ul_class = "";

								if($attributes['slideshow_thumbnail_columns'] != '')
								{
									$ul_class .= " thumbnail_columns_".$attributes['slideshow_thumbnail_columns'];
								}

								if($attributes['slideshow_thumbnail_columns'] != '')
								{
									$ul_class .= " thumbnail_rows_".$attributes['slideshow_thumbnail_rows'];
								}

								$out .= "<ul class='slideshow_thumbnails".$ul_class."'>";

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
			'slideshow_thumbnail_columns_label' => __("Thumbnail Columns", 'lang_slideshow'),
			'slideshow_thumbnail_rows_label' => __("Thumbnail Rows", 'lang_slideshow'),
			'arr_slideshow_thumbnail_rows' => $this->get_thumbnail_rows_for_select(),
		));
	}

	function init()
	{
		load_plugin_textdomain('lang_slideshow', false, str_replace("/include", "", dirname(plugin_basename(__FILE__)))."/lang/");

		register_post_type($this->post_type, array(
			'labels' => array(
				'name' => __("Slideshows", 'lang_slideshow'),
				'singular_name' => __("Slideshow", 'lang_slideshow'),
				'menu_name' => __("Slideshow", 'lang_slideshow'),
				'all_items' => __("List", 'lang_slideshow'),
				'edit_item' => __("Edit", 'lang_slideshow'),
				'view_item' => __("View", 'lang_slideshow'),
				'add_new_item' => __("Add New", 'lang_slideshow'),
			),
			'public' => false,
			'show_ui' => true,
			'show_in_rest' => true,
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
		));
	}

	function get_image_fit_for_select()
	{
		return array(
			//'none' => "-- ".__("None", 'lang_slideshow')." --",
			'cover' => __("Cover", 'lang_slideshow'),
			'contain' => __("Contain", 'lang_slideshow'),
		);
	}

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

		$arr_ids = apply_filters('get_page_from_block_code', [], '<!-- wp:mf/slideshow {"parent":"'.$post->ID.'"} /-->');

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

	function page_row_actions($arr_actions, $post)
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

		$result = $wpdb->get_results($wpdb->prepare("SELECT ID FROM ".$wpdb->posts." INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id WHERE post_status = %s AND meta_key = %s AND meta_value LIKE %s GROUP BY ID", 'publish', $this->meta_prefix.'images', "%".$arr_used['id']."%"));
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
}