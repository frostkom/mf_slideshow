(function()
{
	var __ = wp.i18n.__,
		el = wp.element.createElement,
		registerBlockType = wp.blocks.registerBlockType,
		SelectControl = wp.components.SelectControl,
		TextControl = wp.components.TextControl;

	registerBlockType('mf/slideshow',
	{
		title: __("Slideshow", 'lang_slideshow'),
		description: __("Display Slideshow", 'lang_slideshow'),
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
			var arr_out = [];

			/* Text */
			/* ################### */
			arr_out.push(el(
				'div',
				{className: "wp_mf_block " + props.className},
				el(
					TextControl,
					{
						label: __("Heading", 'lang_slideshow'),
						type: 'text',
						value: props.attributes.slideshow_heading,
						/*help: __("Description...", 'lang_slideshow'),*/
						onChange: function(value)
						{
							props.setAttributes({slideshow_heading: value});
						}
					}
				)
			));
			/* ################### */

			/* Select */
			/* ################### */
			arr_out.push(el(
				'div',
				{className: "wp_mf_block " + props.className},
				el(
					SelectControl,
					{
						label: __("Parent", 'lang_slideshow'),
						value: props.attributes.parent,
						options: convert_php_array_to_block_js(script_slideshow_block_wp.parent),
						onChange: function(value)
						{
							props.setAttributes({parent: value});
						}
					}
				)
			));
			/* ################### */

			/* Settings */
			/* ################### */
			arr_out.push(el(
				'a',
				{
					className: "wp_mf_block " + props.className,
					href: "/wp-admin/options-general.php?page=settings_mf_base#settings_slideshow"
				},
				__("Settings", 'lang_slideshow')
			));
			/* ################### */

			return arr_out;
		},
		save: function()
		{
			return null;
		}
	});
})();