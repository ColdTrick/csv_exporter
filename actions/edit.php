<?php
/**
 * Create / edit an export to be scheduled
 */

elgg_make_sticky_form('csv_exporter');

$guid = (int) get_input('guid');
if (!empty($guid)) {
	elgg_entity_gatekeeper($guid, 'object', CSVExport::SUBTYPE);
	
	/* @var $entity CSVExport */
	$entity = get_entity($guid);
} else {
	$entity = new CSVExport();
}

$form_fields = elgg_get_sticky_values('csv_exporter');

// save all the form data
$entity->title = get_input('title');
$entity->description = json_encode($form_fields);

// schedule the export for processing
$entity->scheduled = time();

if ($entity->save()) {
	system_message(elgg_echo('csv_exporter:action:edit:success'));
} else {
	register_error(elgg_echo('save:fail'));
}

forward(REFERER);
