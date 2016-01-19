<?php

elgg_require_js('csv_exporter/admin');

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
$form_body .= elgg_view('input/dropdown', [
	'name' => 'type_subtype',
	'value' => $type_subtype,
	'options_values' => $type_subtype_options,
	'id' => 'csv-exporter-type-subtype',
	'class' => 'mls',
]);
$form_body .= '</div>';

// additional fields
if (!empty($type_subtype)) {
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
	$form_body .= elgg_view('input/button', [
		'value' => elgg_echo('csv_exporter:admin:download'),
		'class' => 'elgg-button-action float-alt',
		'id' => 'csv-exporter-download',
	]);
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
	'action' => 'admin/administer_utilities/csv_exporter#preview',
	'body' => $form_body,
]);

echo $preview;
