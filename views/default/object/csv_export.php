<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof CSVExport) {
	return;
}

// prepare some content
$imprint = [];
if ($entity->isProcessing()) {
	$processing_since = elgg_view_friendly_time($entity->started);
	$imprint[] = [
		'icon_name' => 'sync',
		'content' => elgg_echo('csv_exporter:object:csv_export:processing', [$processing_since]),
	];
} elseif ($entity->isScheduled()) {
	$imprint[] = [
		'icon_name' => 'clock-o',
		'content' => elgg_echo('csv_exporter:object:csv_export:scheduled'),
	];
} elseif ($entity->isCompleted()) {
	$completed_since = elgg_view_friendly_time($entity->completed);
	$imprint[] = [
		'icon_name' => 'flag-checkered',
		'content' => elgg_echo('csv_exporter:object:csv_export:completed', [$completed_since]),
	];
}

// listing view
$params = [
	'entity' => $entity,
	'title' => $entity->getDisplayName(),
	'imprint' => $imprint,
	'access' => false,
	'icon' => false,
	'content' => false,
];
$params = $params + $vars;

echo elgg_view('object/elements/summary', $params);
