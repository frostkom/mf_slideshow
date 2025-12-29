(function()
{
	var el = wp.element.createElement,
		registerBlockType = wp.blocks.registerBlockType,
		SelectControl = wp.components.SelectControl,
		TextControl = wp.components.TextControl,
		InspectorControls = wp.blockEditor.InspectorControls;

	registerBlockType('mf/slideshow',
	{
		title: script_slideshow_block_wp.block_title,
		description: script_slideshow_block_wp.block_description,
		icon: 'format-gallery',
		category: 'widgets',
		'attributes':
		{
			'align':
			{
				'type': 'string',
				'default': ''
			},
			'parent':
			{
				'type': 'string',
				'default': ''
			},
			'slideshow_style':
			{
				'type': 'string',
				'default': ''
			},
			'slideshow_height_ratio':
			{
				'type': 'string',
				'default': '0.5'
			},
			'slideshow_height_ratio_mobile':
			{
				'type': 'string',
				'default': '1'
			},
			'slideshow_image_fit':
			{
				'type': 'string',
				'default': ''
			},
			'slideshow_display_controls':
			{
				'type': 'array',
				'default': ''
			},
			'slideshow_autoplay':
			{
				'type': 'string',
				'default': 'no'
			},
			'slideshow_duration':
			{
				'type': 'string',
				'default': '5'
			},
			'slideshow_fade_duration':
			{
				'type': 'string',
				'default': '400'
			},
			'slideshow_random':
			{
				'type': 'string',
				'default': ''
			},
			'slideshow_thumbnail_columns':
			{
				'type': 'string',
				'default': '5'
			},
			'slideshow_thumbnail_rows':
			{
				'type': 'string',
				'default': ''
			}
		},
		'supports':
		{
			'html': false,
			'multiple': true,
			'align': true,
			'spacing':
			{
				'margin': true,
				'padding': true
			},
			'color':
			{
				'background': true,
				'gradients': false,
				'text': true
			},
			'defaultStylePicker': true,
			'typography':
			{
				'fontSize': true,
				'lineHeight': true
			},
			"__experimentalBorder":
			{
				"radius": true
			}
		},
		edit: function(props)
		{
			var inspectorControlsChildren = [
				el(
					SelectControl,
					{
						label: script_slideshow_block_wp.parent_label,
						value: props.attributes.parent,
						options: convert_php_array_to_block_js(script_slideshow_block_wp.arr_parents),
						onChange: function(value)
						{
							props.setAttributes({parent: value});
						}
					}
				),
				el(
					SelectControl,
					{
						label: script_slideshow_block_wp.slideshow_style_label,
						value: props.attributes.slideshow_style,
						options: convert_php_array_to_block_js(script_slideshow_block_wp.arr_slideshow_style),
						onChange: function(value)
						{
							props.setAttributes({slideshow_style: value});
						}
					}
				),
				el(
					SelectControl,
					{
						label: script_slideshow_block_wp.slideshow_random_label,
						value: props.attributes.slideshow_random,
						options: convert_php_array_to_block_js(script_slideshow_block_wp.yes_no_for_select),
						onChange: function(value)
						{
							props.setAttributes({slideshow_random: value});
						}
					}
				),
			];

			if(props.attributes.slideshow_style == 'original')
			{
				inspectorControlsChildren.push(
					el(
						TextControl,
						{
							label: script_slideshow_block_wp.slideshow_height_ratio_label,
							type: 'text',
							value: props.attributes.slideshow_height_ratio,
							onChange: function(value)
							{
								props.setAttributes({slideshow_height_ratio: value});
							}
						}
					),
					el(
						TextControl,
						{
							label: script_slideshow_block_wp.slideshow_height_ratio_mobile_label,
							type: 'text',
							value: props.attributes.slideshow_height_ratio_mobile,
							onChange: function(value)
							{
								props.setAttributes({slideshow_height_ratio_mobile: value});
							}
						}
					),
					el(
						SelectControl,
						{
							label: script_slideshow_block_wp.slideshow_image_fit_label,
							value: props.attributes.slideshow_image_fit,
							options: convert_php_array_to_block_js(script_slideshow_block_wp.arr_slideshow_image_fit),
							onChange: function(value)
							{
								props.setAttributes({slideshow_image_fit: value});
							}
						}
					),
					el(
						SelectControl,
						{
							label: script_slideshow_block_wp.slideshow_display_controls_label,
							value: props.attributes.slideshow_display_controls,
							options: convert_php_array_to_block_js(script_slideshow_block_wp.arr_slideshow_display_controls),
							multiple: true,
							onChange: function(value)
							{
								props.setAttributes({slideshow_display_controls: value});
							}
						}
					),
					el(
						SelectControl,
						{
							label: script_slideshow_block_wp.slideshow_autoplay_label,
							value: props.attributes.slideshow_autoplay,
							options: convert_php_array_to_block_js(script_slideshow_block_wp.yes_no_for_select),
							onChange: function(value)
							{
								props.setAttributes({slideshow_autoplay: value});
							}
						}
					),
				);

				if(props.attributes.slideshow_autoplay == 'yes')
				{
					inspectorControlsChildren.push(
						el(
							TextControl,
							{
								label: script_slideshow_block_wp.slideshow_duration_label,
								type: 'number',
								value: props.attributes.slideshow_duration,
								onChange: function(value)
								{
									props.setAttributes({slideshow_duration: value});
								}
							}
						),
					);
				}

				inspectorControlsChildren.push(
					el(
						TextControl,
						{
							label: script_slideshow_block_wp.slideshow_fade_duration_label,
							type: 'number',
							value: props.attributes.slideshow_fade_duration,
							onChange: function(value)
							{
								props.setAttributes({slideshow_fade_duration: value});
							}
						}
					),
					el(
						TextControl,
						{
							label: script_slideshow_block_wp.slideshow_thumbnail_columns_label,
							type: 'number',
							value: props.attributes.slideshow_thumbnail_columns,
							onChange: function(value)
							{
								props.setAttributes({slideshow_thumbnail_columns: value});
							},
							min: 2,
							max: 10,
						}
					),
					el(
						SelectControl,
						{
							label: script_slideshow_block_wp.slideshow_thumbnail_rows_label,
							value: props.attributes.slideshow_thumbnail_rows,
							options: convert_php_array_to_block_js(script_slideshow_block_wp.arr_slideshow_thumbnail_rows),
							multiple: false,
							onChange: function(value)
							{
								props.setAttributes({slideshow_thumbnail_rows: value});
							}
						}
					)
				);
			}

			return el(
				'div',
				{className: 'wp_mf_block_container'},
				[
					el(
						InspectorControls,
						'div',
						inspectorControlsChildren,
					),
					el(
						'strong',
						{className: props.className},
						script_slideshow_block_wp.block_title
					)
				]
			);
		},
		save: function()
		{
			return null;
		}
	});
})();