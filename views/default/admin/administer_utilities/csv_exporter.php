<?php

elgg_require_js('csv_exporter/admin');

// add tab menu
echo elgg_view_menu('csv_exporter', [
	'class' => 'elgg-menu-hz elgg-tabs',
	'sort_by' => 'priority',
]);

// prepare type/subtype selector
$type_subtypes = get_registered_entity_types();
$type_subtype_options = [];
foreach ($type_subtypes as $type => $subtypes) {
	if (!empty($subtypes)) {
		foreach ($subtypes as $subtype) {
			$type_subtype_options["{$type}:{$subtype}"] = elgg_echo("item:{$type}:{$subtype}");
		}
	} else {
		$type_subtype_options[$type] = elgg_echo("item:{$type}");
	}
}

natcasesort($type_subtype_options);
$type_subtype_options = array_merge(array_reverse($type_subtype_options), ['' => elgg_echo('csv_exporter:admin:type_subtype:choose')]);
$type_subtype_options = array_reverse($type_subtype_options);

$form_body = '';
$preview = '';

// type/subtype selector
$type_subtype = elgg_get_sticky_value('csv_exporter', 'type_subtype', get_input('type_subtype'));
$form_body .= '<div>';
$form_body .= elgg_format_element('label', ['for' => 'csv-exporter-type-subtype'], elgg_echo('csv_exporter:admin:type_subtype'));
$form_body .= elgg_view('input/select', [
	'name' => 'type_subtype',
	'value' => $type_subtype,
	'options_values' => $type_subtype_options,
	'id' => 'csv-exporter-type-subtype',
	'class' => 'mls',
]);
$form_body .= '</div>';

// additional fields
if (!empty($type_subtype)) {
	
	// time options
	$time_value = elgg_get_sticky_value('csv_exporter', 'time', get_input('time'));
	$time_options = [
		'' => elgg_echo('csv_exporter:admin:time:select'),
		'today' => elgg_echo('csv_exporter:admin:time:today'),
		'yesterday' => elgg_echo('csv_exporter:admin:time:yesterday'),
		'this_week' => elgg_echo('csv_exporter:admin:time:this_week'),
		'last_week' => elgg_echo('csv_exporter:admin:time:last_week'),
		'this_month' => elgg_echo('csv_exporter:admin:time:this_month'),
		'last_month' => elgg_echo('csv_exporter:admin:time:last_month'),
		'range' => elgg_echo('csv_exporter:admin:time:range'),
	];
	$time = elgg_format_element('label', ['for' => 'csv-exporter-time'], elgg_echo('csv_exporter:admin:time'));
	$time .= elgg_view('input/select', [
		'name' => 'time',
		'value' => $time_value,
		'options_values' => $time_options,
		'class' => 'mls',
		'id' => 'csv-exporter-time',
	]);
	$time .= elgg_format_element('div', ['class' => 'elgg-subtext'], elgg_echo('csv_exporter:admin:time:description'));
	$range = elgg_echo('csv_exporter:admin:time:range:created_time_lower');
	$range .= elgg_view('input/date', [
		'name' => 'created_time_lower',
		'value' => elgg_get_sticky_value('csv_exporter', 'created_time_lower', get_input('created_time_lower')),
		'timestamp' => true,
		'datepicker_options' => [
			'maxDate' => '-1d',
		],
		'class' => 'mhs',
	]);
	$range .= elgg_echo('csv_exporter:admin:time:range:created_time_upper');
	$range .= elgg_view('input/date', [
		'name' => 'created_time_upper',
		'value' => elgg_get_sticky_value('csv_exporter', 'created_time_upper', get_input('created_time_upper')),
		'timestamp' => true,
		'datepicker_options' => [
			'maxDate' => '+1d',
		],
		'class' => 'mls',
	]);
	$time .= elgg_format_element('div', [
		'id' => 'csv-exporter-range',
		'class' => ($time_value === 'range') ? '' : 'hidden',
	], $range);
	$form_body .= elgg_format_element('div', [], $time);
	
	// optional title for export
	$title = elgg_format_element('label', ['for' => 'csv-exporter-title'], elgg_echo('csv_exporter:admin:title'));
	$title .= elgg_view('input/text', [
		'id' => 'csv-exporter-title',
		'name' => 'title',
		'value' => elgg_get_sticky_value('csv_exporter', 'title', get_input('title')),
	]);
	$form_body .= elgg_format_element('div', [], $title);
	
	// get exportable values
	list($type, $subtype) = explode(':', $type_subtype);
	
	$exportable_values_options = csv_exporter_get_exportable_values($type, $subtype, true);
	uksort($exportable_values_options, 'strcasecmp');
	$exportable_values = elgg_get_sticky_value('csv_exporter', 'exportable_values', get_input('exportable_values'));
	
	$form_body .= '<div>';
	$form_body .= elgg_echo('csv_exporter:admin:exportable_values') . '<br />';
	$form_body .= elgg_view('input/checkboxes', [
		'name' => 'exportable_values',
		'options' => $exportable_values_options,
		'value' => $exportable_values,
	]);
	$form_body .= '</div>';
	
	$form_body .= '<div class="elgg-foot">';
	$form_body .= '<div class="float-alt elgg-discover csv-exporter-align-right">';
	$form_body .= elgg_view('input/button', [
		'value' => elgg_echo('csv_exporter:admin:schedule'),
		'class' => 'elgg-button-action',
		'id' => 'csv-exporter-schedule',
	]);
	$form_body .= elgg_format_element('div', ['class' => 'elgg-subtext elgg-discoverable'], elgg_echo('csv_exporter:admin:schedule:description'));
	$form_body .= '</div>';
	$form_body .= elgg_view('input/submit', ['value' => elgg_echo('csv_exporter:admin:preview')]);
	$form_body .= '</div>';
	
	if (!empty($exportable_values)) {
		$preview = elgg_view('csv_exporter/preview', [
			'type' => $type,
			'subtype' => $subtype,
			'exportable_values' => $exportable_values,
		]);
	}
} else {
	$form_body .= elgg_view('output/longtext', [
		'value' => elgg_echo('csv_exporter:admin:exportable_values:choose'),
	]);
}

elgg_clear_sticky_form('csv_exporter');

echo elgg_view('input/form', [
	'id' => 'csv-exporter-export',
	'action' => 'admin/administer_utilities/csv_exporter#preview',
	'body' => $form_body,
]);

echo $preview;
