<?php

// add tab menu
echo elgg_view_menu('csv_exporter', [
	'class' => 'elgg-menu-hz elgg-tabs',
	'sort_by' => 'priority',
]);

$options = [
	'type' => 'object',
	'subtype' => \CSVExport::SUBTYPE,
	'metadata_name' => 'completed',
	'sort_by' => [
		'property' => 'completed',
		'direction' => 'desc',
		'signed' => true,
	],
	'no_results' => elgg_echo('csv_exporter:download:none'),
];

if (get_input('filter') !== 'all') {
	$options['container_guid'] = elgg_get_logged_in_user_guid();
}

echo elgg_list_entities($options);
