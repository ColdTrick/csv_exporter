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

$objects = get_registered_entity_types('object');
if (!empty($objects)) {
	
	$options_values = [];
	foreach ($objects as $subtype) {
		$label = $subtype;
		if (elgg_language_key_exists("item:object:{$subtype}")) {
			$label = elgg_echo("item:object:{$subtype}");
		}
		
		$options_values[$label] = $subtype;
	}
	
	$content = elgg_view_field([
		'#type' => 'checkboxes',
		'#label' => elgg_echo('csv_exporter:settings:group:subtypes'),
		'name' => 'params[allowed_group_subtypes]',
		'options' => $options_values,
		'value' => $plugin->allowed_group_subtypes ? json_decode($plugin->allowed_group_subtypes, true) : null,
	]);
	
	echo elgg_view_module('inline', elgg_echo('csv_exporter:settings:group:title'), $content);
}
