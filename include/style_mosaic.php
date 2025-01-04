<?php

if(!defined('ABSPATH'))
{
	header("Content-Type: text/css; charset=utf-8");

	$folder = str_replace("/wp-content/plugins/mf_slideshow/include", "/", dirname(__FILE__));

	require_once($folder."wp-load.php");
}

echo "@media all
{
	.slideshow.mosaic
	{
		display: grid;
		gap: 1rem;
		grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
		grid-auto-rows: 240px;
	}

		.slideshow.mosaic > div
		{
			display: flex;
			justify-content: center;
			align-items: center;
			border-radius: .3em;
			overflow: hidden;
		}

			.slideshow.mosaic > div img
			{
				height: 100%;
				object-fit: cover;
				width: 100%;
			}
		
			.slideshow.mosaic > div:nth-child(3n + 1)
			{
				grid-row: span 2 / auto;
			}

			.slideshow.mosaic > div:nth-child(9n + 1)
			{
				grid-column: span 2 / auto;
			}
}";