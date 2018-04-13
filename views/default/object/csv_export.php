<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof CSVExport) {
	return;
}

// prepare some content
$content = '';
if ($entity->isProcessing()) {
	$processing_since = elgg_view_friendly_time($entity->started);
	$content .= elgg_echo('csv_exporter:object:csv_export:processing', [$processing_since]);
} elseif ($entity->isScheduled()) {
	$scheduled_since = elgg_view_friendly_time($entity->scheduled);
	$content .= elgg_echo('csv_exporter:object:csv_export:scheduled', [$scheduled_since]);
} elseif ($entity->isCompleted()) {
	$completed_since = elgg_view_friendly_time($entity->completed);
	$content .= elgg_echo('csv_exporter:object:csv_export:completed', [$completed_since]);
}

// listing view
$params = [
	'entity' => $entity,
	'title' => $entity->getDisplayName(),
	'content' => $content,
];
$params = $params + $vars;
echo elgg_view('object/elements/summary', $params);
