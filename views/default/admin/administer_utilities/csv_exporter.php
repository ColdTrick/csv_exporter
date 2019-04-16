<?php

elgg_require_js('csv_exporter/admin');

// add tab menu
echo elgg_view_menu('csv_exporter', [
	'class' => 'elgg-tabs',
	'sort_by' => 'priority',
]);

$form_vars = [
	'id' => 'csv-exporter-export',
	'action' => 'admin/administer_utilities/csv_exporter#preview',
];
$body_vars = csv_exporter_prepare_edit_form_vars();

echo elgg_view_form('csv_exporter/edit', $form_vars, $body_vars);

// preview
$type_subtype = elgg_extract('type_subtype', $body_vars);
$exportable_values = elgg_extract('exportable_values', $body_vars);
if (!empty($type_subtype) && !empty($exportable_values)) {
	list($type, $subtype) = explode(':', $type_subtype);
	
	$params = [
		'type' => $type,
		'subtype' => $subtype,
		'exportable_values' => $exportable_values,
	];
	$params = $params  + $vars;
	
	echo elgg_view('csv_exporter/preview', $params);
}
