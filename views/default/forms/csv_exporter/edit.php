<?php
/**
 * Configure CSV Export
 */

// prepare type/subtype selector
$type_subtypes = csv_exporter_get_allowed_entity_types();
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

echo elgg_view_field([
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
]);

echo elgg_view_field([
	'#type' => 'fieldset',
	'#class' => (elgg_extract('time', $vars) === 'range') ? '' : 'hidden',
	'id' => 'csv-exporter-range',
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
	'align' => 'horizontal',
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('csv_exporter:admin:title'),
	'name' => 'title',
	'value' => elgg_extract('title', $vars),
]);

// get exportable values
list($type, $subtype) = explode(':', $type_subtype);

$exportable_values_options = csv_exporter_get_exportable_values($type, $subtype, true);
uksort($exportable_values_options, 'strcasecmp');
echo elgg_view_field([
	'#type' => 'checkboxes',
	'#label' => elgg_echo('csv_exporter:admin:exportable_values'),
	'name' => 'exportable_values',
	'options' => $exportable_values_options,
	'value' => elgg_extract('exportable_values', $vars),
]);

$footer = elgg_view_field([
	'#type' => 'fieldset',
	'fields' => [
		[
			'#type' => 'submit',
			'value' => elgg_echo('csv_exporter:admin:preview'),
			'icon' => 'eye',
		],
		[
			'#type' => 'button',
			'title' => elgg_echo('csv_exporter:admin:schedule:description'),
			'value' => elgg_echo('csv_exporter:admin:schedule'),
			'class' => 'elgg-button-action',
			'id' => 'csv-exporter-schedule',
			'icon' => 'clock-o',
		],
	],
	'align' => 'horizontal',
]);
elgg_set_form_footer($footer);
