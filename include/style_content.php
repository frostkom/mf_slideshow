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