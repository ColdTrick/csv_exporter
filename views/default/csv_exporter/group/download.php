<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggGroup) {
	return;
}

echo elgg_list_entities([
	'type' => 'object',
	'subtype' => \CSVExport::SUBTYPE,
	'container_guid' => $entity->guid,
	'metadata_name' => 'completed',
	'sort_by' => [
		'property' => 'completed',
		'direction' => 'desc',
		'signed' => true,
	],
	'no_results' => elgg_echo('csv_exporter:download:none'),
]);
