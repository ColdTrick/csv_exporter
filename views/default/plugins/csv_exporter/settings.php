<?php

$plugin = elgg_extract('entity', $vars);

$separator = csv_exporter_get_separator();

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('csv_exporter:settings:separator'),
	'name' => 'params[separator]',
	'value' => $separator,
	'maxlength' => 1,
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('csv_exporter:settings:retention'),
	'help' => elgg_echo('csv_exporter:settings:retention:description'),
	'name' => 'params[retention]',
	'value' => $plugin->retention,
]);
