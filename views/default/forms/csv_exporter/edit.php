<?php
/**
 * Configure CSV Export
 */

elgg_require_css('forms/csv_exporter/edit');

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'preview',
	'value' => '1',
]);

// prepare type/subtype selector
$type_subtypes = csv_exporter_get_allowed_entity_types();
$type_subtype_options = [];
foreach ($type_subtypes as $type => $subtypes) {
	if (!empty($subtypes)) {
		foreach ($subtypes as $subtype) {
			$label = $subtype;
			if (elgg_language_key_exists("collection:{$type}:{$subtype}")) {
				$label = elgg_echo("collection:{$type}:{$subtype}");
			} elseif (elgg_language_key_exists("item:{$type}:{$subtype}")) {
				$label = elgg_echo("item:{$type}:{$subtype}");
			}
			
			$type_subtype_options["{$type}:{$subtype}"] = $label;
		}
	} else {
		$type_subtype_options[$type] = elgg_echo("item:{$type}");
	}
}

natcasesort($type_subtype_options);
$type_subtype_options = array_merge(array_reverse($type_subtype_options), ['' => elgg_echo('csv_exporter:admin:type_subtype:choose')]);
$type_subtype_options = array_reverse($type_subtype_options);

$type_subtype = elgg_extract('type_subtype', $vars);
echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('csv_exporter:admin:type_subtype'),
	'name' => 'type_subtype',
	'value' => $type_subtype,
	'options_values' => $type_subtype_options,
	'id' => 'csv-exporter-type-subtype',
	'required' => true,
]);

if (empty($type_subtype)) {
	echo elgg_view_message('notice', elgg_echo('csv_exporter:admin:exportable_values:choose'));
	return;
}

$result = elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('csv_exporter:admin:title'),
	'name' => 'title',
	'value' => elgg_extract('title', $vars),
]);

$show_filter = false;
if (!empty(elgg_extract('time', $vars))) {
	$show_filter = true;
}

$filter_fields = [
	[
		'#type' => 'fieldset',
		'fields' => [
			[
				'#type' => 'select',
				'#label' => elgg_echo('csv_exporter:admin:time'),
				'#help' => elgg_echo('csv_exporter:admin:time:description'),
				'id' => 'csv-exporter-time',
				'name' => 'time',
				'value' => elgg_extract('time', $vars),
				'options_values' => [
					'' => elgg_echo('csv_exporter:admin:time:select'),
					'today' => elgg_echo('csv_exporter:admin:time:today'),
					'yesterday' => elgg_echo('csv_exporter:admin:time:yesterday'),
					'this_week' => elgg_echo('csv_exporter:admin:time:this_week'),
					'last_week' => elgg_echo('csv_exporter:admin:time:last_week'),
					'this_month' => elgg_echo('csv_exporter:admin:time:this_month'),
					'last_month' => elgg_echo('csv_exporter:admin:time:last_month'),
					'range' => elgg_echo('csv_exporter:admin:time:range'),
				],
			],
			[
				'#type' => 'fieldset',
				'#class' => (elgg_extract('time', $vars) === 'range') ? '' : 'hidden',
				'id' => 'csv-exporter-range',
				'align' => 'horizontal',
				'fields' => [
					[
						'#type' => 'date',
						'#label' => elgg_echo('csv_exporter:admin:time:range:created_time_lower'),
						'name' => 'created_time_lower',
						'value' => elgg_extract('created_time_lower', $vars),
						'timestamp' => true,
						'datepicker_options' => [
							'maxDate' => '-1d',
						],
					],
					[
						'#type' => 'date',
						'#label' => elgg_echo('csv_exporter:admin:time:range:created_time_upper'),
						'name' => 'created_time_upper',
						'value' => elgg_extract('created_time_upper', $vars),
						'timestamp' => true,
						'datepicker_options' => [
							'maxDate' => '+1d',
						],
					],
				],
			],
		],
	],
];

if(str_starts_with($type_subtype, 'object:')) {
	$owner_guid = elgg_extract('owner_guid', $vars);
	$filter_fields[] = [
		'#type' => 'userpicker',
		'#label' => elgg_echo('csv_exporter:admin:owner_guid'),
		'name' => 'owner_guid',
		'show_friends' => false,
		'value' => $owner_guid,
	];
	
	$container_guid = elgg_extract('container_guid', $vars);
	$filter_fields[] = [
		'#type' => 'grouppicker',
		'#label' => elgg_echo('csv_exporter:admin:container_guid'),
		'name' => 'container_guid',
		'value' => $container_guid,
	];
	
	if (!empty($owner_guid) || !empty($container_guid)) {
		$show_filter = true;
	}
}

$result .= elgg_view_field([
	'#type' => 'fieldset',
	'legend' => elgg_echo('filter'),
	'id' => 'csv-exporter-filter',
	'class' => !$show_filter ? 'hidden' : null,
	'fields' => $filter_fields,
]);

// get exportable values
list($type, $subtype) = explode(':', $type_subtype);

$exportable_values_options = csv_exporter_get_exportable_values($type, $subtype, true);

// filter out group only values
$postfix = elgg_echo('csv_exporter:exportable_value:group:postfix');
$exportable_values_options = array_filter($exportable_values_options, function($value, $label) use ($postfix) {
	return stristr($label, $postfix) === false;
}, ARRAY_FILTER_USE_BOTH);

uksort($exportable_values_options, 'strnatcasecmp');

// init with empty keys to force order
$grouped = [
	'attributes' => [],
	'owner' => [],
	'container' => [],
	'timestamps' => [],
	'metadata' => [],
	'icon' => [],
	'counters' => [],
	'state' => [],
];

foreach ($exportable_values_options as $label => $option) {
	if (str_contains($option, '|')) {
		list($category, $option) = explode('|', $option);
	} else {
		$category = 'misc';
	}
	
	$grouped[$category][$label] = $option;
}

$grouped = array_filter($grouped);

$categories = '';
foreach ($grouped as $category => $fields) {
	$legend = $category;
	if (elgg_language_key_exists("csv_exporter:category:{$category}:title")) {
		$legend = elgg_echo("csv_exporter:category:{$category}:title");
	}
	
	$categories .= elgg_view_field([
		'#type' => 'fieldset',
		'legend' => $legend,
		'fields' => [
			[
				'#type' => 'checkboxes',
				'#label' => elgg_echo('csv_exporter:admin:exportable_values'),
				'name' => 'exportable_values',
				'options' => $fields,
				'default' => false,
				'value' => elgg_extract('exportable_values', $vars),
			],
		],
	]);
}

$result .= elgg_format_element('div', ['class' => 'csv-exporter-categories'], $categories);

echo elgg_view_module('info', elgg_echo('csv_exporter:admin'), $result, ['menu' => elgg_view('output/url', [
	'icon' => 'filter',
	'text' => elgg_echo('csv_exporter:admin:show_filter'),
	'href' => false,
	'class' => ['elgg-toggle', 'elgg-button', 'elgg-button-action'],
	'data-toggle-selector' => '#csv-exporter-filter',
])]);

$footer = elgg_view_field([
	'#type' => 'fieldset',
	'fields' => [
		[
			'#type' => 'submit',
			'text' => elgg_echo('csv_exporter:admin:preview'),
			'icon' => 'eye',
		],
		[
			'#type' => 'submit',
			'title' => elgg_echo('csv_exporter:admin:schedule:description'),
			'text' => elgg_echo('csv_exporter:admin:schedule'),
			'class' => 'elgg-button-action',
			'formaction' => elgg_generate_action_url('csv_exporter/edit', [], false),
			'icon' => 'clock',
		],
	],
	'align' => 'horizontal',
]);
elgg_set_form_footer($footer);
