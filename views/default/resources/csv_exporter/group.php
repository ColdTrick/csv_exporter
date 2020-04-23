<?php

use Elgg\EntityPermissionsException;

$guid = (int) elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'group');

$group = get_entity($guid);
if (!$group->canEdit()) {
	throw new EntityPermissionsException();
}

$selected = elgg_extract('filter', $vars);

elgg_set_page_owner_guid($group->guid);

elgg_push_breadcrumb($group->getDisplayName(), $group->getURL());

// page elements
$title = elgg_echo('csv_exporter:group:title', [$group->getDisplayName()]);

$filter = elgg_view_menu('csv_exporter_group', [
	'class' => 'elgg-tabs',
	'sort_by' => 'priority',
	'entity' => $group,
	'selected' => $selected,
]);

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
		$content = elgg_view_form('csv_exporter/group', [], [
			'entity' => $group,
		]);
		break;
}

// draw page
echo elgg_view_page($title, [
	'content' => $content,
	'filter' => $filter,
]);
