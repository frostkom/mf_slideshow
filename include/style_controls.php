<?php

if(!defined('ABSPATH'))
{
	header("Content-Type: text/css; charset=utf-8");

	$folder = str_replace("/wp-content/plugins/mf_slideshow/include", "/", dirname(__FILE__));

	require_once($folder."wp-load.php");
}

$plugin_images_url = str_replace("/include/", "/images/", plugin_dir_url(__FILE__));

echo ".slideshow.original .controls_arrows:not(.hide)
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
		}";