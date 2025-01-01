(function()
{
	var el = wp.element.createElement,
		registerBlockType = wp.blocks.registerBlockType,
		SelectControl = wp.components.SelectControl,
		TextControl = wp.components.TextControl;

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
			'slideshow_heading':
			{
                'type': 'string',
                'default': ''
            },
			'parent':
			{
                'type': 'string',
                'default': ''
            }
		},
		'supports':
		{
			'html': false,
			'multiple': false,
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
			return el(
				'div',
				{className: 'wp_mf_block_container'},
				[
					el(
						InspectorControls,
						'div',
						el(
							TextControl,
							{
								label: script_slideshow_block_wp.slideshow_heading_label,
								type: 'text',
								value: props.attributes.slideshow_heading,
								onChange: function(value)
								{
									props.setAttributes({slideshow_heading: value});
								}
							}
						),
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
							'a',
							{
								className: "wp_mf_block " + props.className,
								href: "/wp-admin/options-general.php?page=settings_mf_base#settings_slideshow"
							},
							script_slideshow_block_wp.settings_label
						)
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