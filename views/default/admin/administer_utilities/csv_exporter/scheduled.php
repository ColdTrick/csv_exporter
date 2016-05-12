<?php

// add tab menu
echo elgg_view_menu('csv_exporter', [
	'class' => 'elgg-menu-hz elgg-tabs',
	'sort_by' => 'priority',
]);

echo elgg_list_entities_from_metadata([
	'type' => 'object',
	'subtype' => CSVExport::SUBTYPE,
	'metadata_name' => 'scheduled',
	'order_by_metadata' => [
		'name' => 'scheduled',
		'direction' => 'asc',
		'as' => 'integer',
	],
	'no_results' => elgg_echo('csv_exporter:scheduled:none'),
]);