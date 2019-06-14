<?php

elgg_make_sticky_form('csv_exporter_group');

$container_guid = (int) get_input('container_guid');
$subtype = get_input('subtype');

if (empty($container_guid) || empty($subtype)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$container = get_entity($container_guid);
if (!$container instanceof ElggGroup || !$container->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

$allowed_subtypes = csv_exporter_get_group_subtypes();
if (!in_array($subtype, $allowed_subtypes)) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

$entity = new CSVExport();
$entity->container_guid = $container->guid;

$group_acl = $container->getOwnedAccessCollection('group_acl');
$entity->access_id = ($group_acl instanceof ElggAccessCollection) ? $group_acl->id : ACCESS_PRIVATE;

$entity->title = get_input('title');

$data = elgg_get_sticky_values('csv_exporter_group');
$data['type_subtype'] = "object:{$subtype}";
$data['exportable_values'] = csv_exporter_get_exportable_group_values('object', $subtype);

$entity->description = json_encode($data);

// schedule the export for processing
$entity->scheduled = time();

if (!$entity->save()) {
	return elgg_error_response(elgg_echo('save:fail'));
}

return elgg_ok_response('', elgg_echo('csv_exporter:action:edit:success'));
