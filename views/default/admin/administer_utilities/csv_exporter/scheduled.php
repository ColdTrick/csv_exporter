<?php

// add tab menu
echo elgg_view_menu('csv_exporter', [
	'class' => 'elgg-menu-hz elgg-tabs',
	'sort_by' => 'priority',
]);

echo elgg_list_entities([
	'type' => 'object',
	'subtype' => \CSVExport::SUBTYPE,
	'metadata_name' => 'scheduled',
	'sort_by' => [
		'property' => 'scheduled',
		'direction' => 'asc',
		'signed' => true,
	],
	'no_results' => elgg_echo('csv_exporter:scheduled:none'),
]);
