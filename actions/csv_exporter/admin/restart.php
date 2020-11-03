<?php

$guid = (int) get_input('guid');

$entity = get_entity($guid);
if (!$entity instanceof \CSVExport) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

if (!$entity->isProcessing()) {
	return elgg_error_response(elgg_echo('csv_exporter:action:admin:restart:error:not_processing'));
}

$entity->unlockProcessing();

return elgg_ok_response('', elgg_echo('csv_exporter:action:admin:restart:success'));
