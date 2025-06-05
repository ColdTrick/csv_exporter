<?php

/* @var $group \ElggGroup */
$group = elgg_get_page_owner_entity();

elgg_push_entity_breadcrumbs($group);

$selected = elgg_extract('filter', $vars);
switch ($selected) {
	case 'download':
		$content = elgg_view('csv_exporter/group/download', [
			'entity' => $group,
		]);
		break;
	case 'scheduled':
		$content = elgg_view('csv_exporter/group/scheduled', [
			'entity' => $group,
		]);
		break;
	default:
		$content = elgg_view_form('csv_exporter/group', [
			'sticky_enabled' => true,
		], [
			'entity' => $group,
		]);
		break;
}

echo elgg_view_page(elgg_echo('csv_exporter:group:title', [$group->getDisplayName()]), [
	'content' => $content,
	'filter_id' => 'csv_exporter/group',
	'filter_value' => $selected,
	'filter_entity' => $group,
]);
