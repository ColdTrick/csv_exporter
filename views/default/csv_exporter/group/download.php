<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggGroup) {
	return;
}

echo elgg_list_entities([
	'type' => 'object',
	'subtype' => CSVExport::SUBTYPE,
	'container_guid' => $entity->guid,
	'metadata_name' => 'completed',
	'order_by_metadata' => [
		'name' => 'completed',
		'direction' => 'desc',
		'as' => 'integer',
	],
	'no_results' => elgg_echo('csv_exporter:download:none'),
]);
