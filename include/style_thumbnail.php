<?php

if(!defined('ABSPATH'))
{
	header("Content-Type: text/css; charset=utf-8");

	$folder = str_replace("/wp-content/plugins/mf_slideshow/include", "/", dirname(__FILE__));

	require_once($folder."wp-load.php");
}

$column_gap = 0.3;
$columns_2_width = (50 - ($column_gap / 2));
$columns_3_width = (33 - ($column_gap / 2));

echo ".slideshow .slideshow_thumbnails
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