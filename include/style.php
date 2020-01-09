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

$obj_slideshow = new mf_slideshow();

echo "@media all
{
	.slideshow.original
	{
		font-size: 1em;
		overflow: hidden;
		position: relative;
		text-align: center;
		transition: all .5s ease;
		width: 100%;
	}

		.is_size_lap .slideshow.original
		{
			font-size: .8em;
		}

		.is_size_palm .slideshow.original
		{
			font-size: .6em;
		}

		.widget .slideshow.original
		{
			padding: 0 !important;
			max-width: 100% !important; /* If .full_width, then this has to be specified */
		}

		.slideshow.original > div
		{
			display: none;
			left: 0;
			padding: inherit; /* This will respect the parents padding */
			position: absolute;
			right: 0;
			top: 0;
		}

			.slideshow.original > div.active
			{
				display: block;
			}

				.slideshow.original > div > img
				{
					height: 100%;
					object-fit: cover;
					width: 100%;
					transition: transform 20s ease;
					-webkit-transform: scale(1);
					transform: scale(1);
				}

					.slideshow.original > div.animate > img
					{
						-webkit-transform: scale(1.2);
						transform: scale(1.2);
					}

			.slideshow.original .content
			{
				display: none;
				position: absolute;
				top: 50%;
			}

				.slideshow.original > div.active .content
				{
					display: block;
				}

				.slideshow.original .content.left
				{
					left: 1em;
					right: 40%;
				}

				.slideshow.original .content.center
				{
					left: 20%;
					right: 20%;
				}

				.slideshow.original .content.bottom
				{
					bottom: 1em;
					left: 1em;
					right: 1em;
					top: auto;
				}

				.slideshow.original .content.right
				{
					left: 40%;
					right: 1em;
				}

				.slideshow.original .content > div
				{
					color: #fff;
					padding: 1em;
				}

					.slideshow.original.display_text_background .content > div
					{
						background: rgba(0, 0, 0, .4);
					}

					.slideshow.original .content:not(.bottom) > div
					{
						-webkit-transform: translateY(-50%);
						transform: translateY(-50%);
					}

					.slideshow.original .content > div > a
					{
						display: block;
						margin-top: 1em;
						text-align: right;
					}

		.slideshow.original .controls.fa
		{
			background: #000;
			border-radius: 50%;
			color: #fff;
			font-size: 2em;
			opacity: .2;
			position: absolute;
			top: 50%;
			-webkit-transform: translateY(-50%);
			transform: translateY(-50%);
			transition: all 2s ease;
		}

			.slideshow.original:hover .controls.fa
			{
				opacity: .5;
				font-size: 3em;
			}

			.slideshow.original .controls.fa:hover
			{
				opacity: 1;
				font-size: 3.5em;
			}

			.slideshow.original .controls.arrow_left
			{
				left: 2%;
				padding: .35em .6em .35em .45em;
			}

			.slideshow.original .controls.arrow_right
			{
				padding: .35em .45em .35em .6em;
				right: 2%;
			}

		.slideshow.original ul.controls
		{
			bottom: 1em;
			left: 0;
			opacity: .2;
			position: absolute;
			right: 0;
			text-align: center;
			transition: all 2s ease;
		}

			.slideshow.original:hover ul.controls
			{
				opacity: .8;
				-webkit-transform: scale(1.2);
				transform: scale(1.2);
			}

			.slideshow.original .controls li
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

				.slideshow.original .controls li:hover
				{
					background: #bbb;
				}

				.slideshow.original .controls li.active
				{
					background: #666;
				}";

	$result = $wpdb->get_results($wpdb->prepare("SELECT ID, post_parent, meta_value FROM ".$wpdb->posts." INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id WHERE post_type = %s AND post_status = %s AND post_parent > '0' ORDER BY post_parent ASC", $obj_slideshow->post_type, 'publish', $obj_slideshow->meta_prefix.'content_style')); //(post_parent > '0' OR meta_key = %s AND meta_value != '') // This will load [slide_parent_id] into CSS because parent style is also loaded

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

echo "}";