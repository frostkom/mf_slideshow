<?php

if(!defined('ABSPATH'))
{
	header("Content-Type: text/css; charset=utf-8");

	$folder = str_replace("/wp-content/plugins/mf_slideshow/include", "/", dirname(__FILE__));

	require_once($folder."wp-load.php");
}

/*else
{
	global $wpdb;
}

if(!isset($obj_slideshow))
{
	$obj_slideshow = new mf_slideshow();
}*/

echo ".slideshow.original .slideshow_container
{
	overflow: hidden;
	position: relative;
	text-align: center;
	transition: all .5s ease;
	width: 100%;
}

	.full_width > div > .widget > .slideshow.original
	{
		padding-left: 0;
		padding-right: 0;
	}

	.widget .slideshow.original .slideshow_container
	{
		padding: 0 !important;
		max-width: 100% !important; /* If .full_width, then this has to be specified */
	}

		.slideshow.original .slideshow_container .slide_item
		{
			display: block;
			height: 100%;
			padding: inherit; /* This will respect the parents padding */
			width: 100%;
		}

			.slideshow.original .slideshow_container .slide_item:not(.active)
			{
				display: none;
			}

			/* This will allow animate to fade in/out correctly */
			.slideshow.original .slideshow_container .columns_1 .slide_item
			{
				position: absolute;
			}

			.slideshow.original .slideshow_container .slide_item img
			{
				height: 100%;
				width: 100%;
				transition: transform 20s ease;
				transform: scale(1);
			}

				.slideshow.original .slideshow_container .slide_item img, .slideshow.original .slideshow_container .slideshow_image_fit_contain .slide_item img
				{
					object-fit: contain;
				}

				.slideshow.original .slideshow_container .slideshow_image_fit_cover .slide_item img
				{
					object-fit: cover;
				}";

/*$result = $wpdb->get_results($wpdb->prepare("SELECT ID, post_parent, meta_value FROM ".$wpdb->posts." INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id WHERE post_type = %s AND post_status = %s AND post_parent > '0' ORDER BY post_parent ASC", $obj_slideshow->post_type, 'publish'));

$post_parent_temp = 0;

foreach($result as $r)
{
	$post_id = $r->ID;
	$post_parent = $r->post_parent;
	$post_content_style = $r->meta_value;

	if($post_parent > 0 && $post_parent != $post_parent_temp)
	{
		$post_parent_content_style = get_post_meta($post_parent, $obj_slideshow->meta_prefix.'content_style', true);

		if($post_parent_content_style != '')
		{
			if(preg_match("/\[slide_parent_id]/", $post_parent_content_style))
			{
				echo str_replace("[slide_parent_id]", ".slide_parent_".$post_parent, $post_parent_content_style);
			}

			else
			{
				echo ".slide_parent_".$post_parent."
				{"
					.$post_parent_content_style
				."}";
			}
		}

		$post_parent_temp = $post_parent;
	}

	if($post_content_style != '')
	{
		if(preg_match("/\[slide_id]/", $post_content_style))
		{
			echo str_replace("[slide_id]", "#slide_".$post_id, $post_content_style);
		}

		else
		{
			echo "#slide_".$post_id."
			{"
				.$post_content_style
			."}";
		}
	}
}*/