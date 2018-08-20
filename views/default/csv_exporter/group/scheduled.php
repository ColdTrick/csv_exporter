<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggGroup) {
	return;
}

echo elgg_list_entities([
	'type' => 'object',
	'subtype' => CSVExport::SUBTYPE,
	'container_guid' => $entity->guid,
	'metadata_name' => 'scheduled',
	'order_by_metadata' => [
		'name' => 'scheduled',
		'direction' => 'asc',
		'as' => 'integer',
	],
	'no_results' => elgg_echo('csv_exporter:scheduled:none'),
]);
