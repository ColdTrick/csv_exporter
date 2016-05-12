<?php

// add tab menu
echo elgg_view_menu('csv_exporter', [
	'class' => 'elgg-menu-hz elgg-tabs',
	'sort_by' => 'priority',
]);

echo elgg_list_entities_from_metadata([
	'type' => 'object',
	'subtype' => CSVExport::SUBTYPE,
	'metadata_name' => 'completed',
	'order_by_metadata' => [
		'name' => 'completed',
		'direction' => 'desc',
		'as' => 'integer',
	],
	'no_results' => elgg_echo('csv_exporter:download:none'),
]);