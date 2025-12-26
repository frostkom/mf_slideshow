<?php

if(!defined('ABSPATH'))
{
	header("Content-Type: text/css; charset=utf-8");

	$folder = str_replace("/wp-content/plugins/mf_slideshow/include", "/", dirname(__FILE__));

	require_once($folder."wp-load.php");
}

else
{
	global $wpdb;
}

if(!isset($obj_slideshow))
{
	$obj_slideshow = new mf_slideshow();
}

$plugin_images_url = str_replace("/include/", "/images/", plugin_dir_url(__FILE__));

$column_gap = 0.3;
$columns_2_width = (50 - ($column_gap / 2));
$columns_3_width = (33 - ($column_gap / 2));

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
				}

	/* Content */
	/* ##################### */
	.slideshow.original .content
	{
		display: none;
		margin-left: 10%;
		margin-right: 10%;
		position: absolute;
		top: 50%;
		transform: translateY(-50%);
		z-index: 1;
	}

		.slideshow.original .slideshow_container .slide_item.active_init .content
		{
			display: block;
		}

		.slideshow.original .content p
		{
			margin: 0;
		}

			.slideshow.original .content:not(.bottom) > div
			{
				transform: translateY(-50%);
			}

			.slideshow.original .content > div > a
			{
				display: block;
				margin-top: 1em;
				text-align: right;
			}
	/* ##################### */

	/* Controls */
	/* ##################### */
	.slideshow.original .controls_arrows:not(.hide)
	{
		display: block;
		height: 85%;
		left: 0;
		position: absolute;
		right: 0;
		top: 0;
	}

		.slideshow.original .controls_arrows .panel_arrow_left
		{
			cursor: url(".$plugin_images_url."arrow_left.png), e-resize;
			float: left;
			height: 100%;
			width: 50%;
		}

			.slideshow.original .controls_arrows .panel_arrow_left:hover .arrow_left
			{
				opacity: .1;
			}

		.slideshow.original .controls_arrows .panel_arrow_right
		{
			cursor: url(".$plugin_images_url."arrow_right.png), e-resize;
			float: right;
			height: 100%;
			width: 50%;
		}

			.slideshow.original .controls_arrows .panel_arrow_right:hover .arrow_right
			{
				opacity: .1;
			}

		.slideshow.original .controls_arrows .fa
		{
			font-size: 2em;
			opacity: .2;
			position: absolute;
			top: 50%;
			transform: translateY(-50%);
			transition: all 2s ease;
		}

			.slideshow.original:hover .controls_arrows .fa
			{
				opacity: .5;
				font-size: 3em;
			}

			.slideshow.original .controls_arrows .fa:hover
			{
				opacity: 1;
				font-size: 3.5em;
			}

			.slideshow.original .controls_arrows .arrow_left
			{
				left: 2%;
				padding: .35em .6em .35em .45em;
			}

			.slideshow.original .controls_arrows .arrow_right
			{
				padding: .35em .45em .35em .6em;
				right: 2%;
			}

	.slideshow.original .controls_dots
	{
		bottom: 1em;
		left: 10%;
		opacity: .2;
		padding: 0;
		position: absolute;
		right: 10%;
		text-align: center;
		transition: all 2s ease;
		max-width: 80%;
	}

		.slideshow.original:hover .controls_dots
		{
			opacity: .8;
			transform: scale(1.2);
		}

		.slideshow.original .controls_dots li
		{
			background: #fff;
			border: .2em solid #fff !important;
			border-radius: 50%;
			cursor: pointer;
			display: inline-block;
			height: 1.2em;
			margin: 0 .5em;
			outline: none;
			width: 1.2em;
		}

			.slideshow.original .controls_dots li:hover
			{
				background: #bbb;
			}

			.slideshow.original .controls_dots li.active
			{
				background: #666;
			}
	/* ##################### */

	/* Thumbnails */
	/* ##################### */
	.slideshow .slideshow_thumbnails
	{
		display: flex;
		flex-wrap: wrap;
		list-style: none;
		margin-top: ".$column_gap."%;
		overflow: hidden;
	}

		.slideshow .slideshow_thumbnails li
		{
			cursor: pointer;
			margin: 0;
			opacity: .3;
		}";

			for($i = 2; $i <= 10; $i++)
			{
				$thumbnail_width = (100 / $i);

				echo ".slideshow .slideshow_thumbnails.thumbnail_columns_".$i." li
				{
					flex: 0 0 ".$thumbnail_width."%;
				}";
			}

			echo ".slideshow .slideshow_thumbnails.thumbnail_rows_one
			{
				flex-wrap: unset;
				overflow: auto;
			}

			.slideshow .slideshow_thumbnails li.active, .slideshow .slideshow_thumbnails li:hover
			{
				opacity: 1;
			}

			.slideshow .slideshow_thumbnails li img
			{
				display: block;
			}
	/* ##################### */";

$result = $wpdb->get_results($wpdb->prepare("SELECT ID, post_parent, meta_value FROM ".$wpdb->posts." INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id WHERE post_type = %s AND post_status = %s AND post_parent > '0' ORDER BY post_parent ASC", $obj_slideshow->post_type, 'publish'));

if($wpdb->num_rows > 0)
{
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
	}
}