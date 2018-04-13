<?php
/**
 * Create / edit an export to be scheduled
 */

elgg_make_sticky_form('csv_exporter');

$guid = (int) get_input('guid');
if (!empty($guid)) {
	$entity = get_entity($guid);
	if (!$entity instanceof CSVExport || !$entity->canEdit()) {
		return elgg_error_response(elgg_echo('actionunauthorized'));
	}
} else {
	$entity = new CSVExport();
}

$form_fields = elgg_get_sticky_values('csv_exporter');

// save all the form data
$entity->title = elgg_get_title_input();
$entity->description = json_encode($form_fields);

// schedule the export for processing
$entity->scheduled = time();

if (!$entity->save()) {
	return elgg_error_response(elgg_echo('save:fail'));
}

return elgg_ok_response('', elgg_echo('csv_exporter:action:edit:success'));
