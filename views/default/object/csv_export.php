<?php

$entity = elgg_extract('entity', $vars);
if (!($entity instanceof CSVExport)) {
	return;
}

// entity menu
$entity_menu = '';
if (!elgg_in_context('widgets')) {
	$entity_menu = elgg_view_menu('entity', [
		'entity' => $entity,
		'handler' => 'csv_exporter',
		'sort_by' => 'priority',
		'class' => 'elgg-menu-hz',
	]);
}

// entity icon
$entity_icon = elgg_view_entity_icon($entity, 'small');

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
	'metadata' => $entity_menu,
	'subtitle' => elgg_view('page/elements/by_line', $vars),
	'content' => $content,
];
$params = $params + $vars;
$list_body = elgg_view('object/elements/summary', $params);

echo elgg_view_image_block($entity_icon, $list_body);
