<?php

elgg_import_esm('admin/administer_utilities/csv_exporter');

// add tab menu
echo elgg_view_menu('csv_exporter', [
	'class' => 'elgg-tabs',
	'sort_by' => 'priority',
]);

echo elgg_view_form('csv_exporter/edit', [
	'id' => 'csv-exporter-export',
	'action' => 'admin/administer_utilities/csv_exporter#preview',
	'sticky_enabled' => true,
]);

// preview
$type_subtype = get_input('type_subtype');
$exportable_values = get_input('exportable_values');
$preview = (bool) get_input('preview', false);
if ($preview && !empty($type_subtype) && !empty($exportable_values)) {
	list($type, $subtype) = explode(':', $type_subtype);
	
	$params = [
		'type' => $type,
		'subtype' => $subtype,
		'exportable_values' => $exportable_values,
		'time' => get_input('time'),
		'time_field' => get_input('time_field'),
		'created_time_lower' => get_input('created_time_lower'),
		'created_time_upper' => get_input('created_time_upper'),
		'owner_guid' => get_input('owner_guid'),
		'container_guid' => get_input('container_guid'),
	];
	$params = $params + $vars;
	echo elgg_view('csv_exporter/preview', $params);
}
