<?php

/* @var $plugin ElggPlugin */
$plugin = elgg_extract('entity', $vars);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('csv_exporter:settings:separator'),
	'name' => 'params[separator]',
	'value' => $plugin->separator,
	'maxlength' => 1,
]);

echo elgg_view_field([
	'#type' => 'number',
	'#label' => elgg_echo('csv_exporter:settings:retention'),
	'help' => elgg_echo('csv_exporter:settings:retention:description'),
	'name' => 'params[retention]',
	'value' => $plugin->retention,
	'min' => 0,
]);

// group export settings
$searchable = elgg_entity_types_with_capability('searchable');
if (empty($searchable)) {
	return;
}

$options_values = [];
foreach ($searchable as $type => $subtypes) {
	if ($type !== 'object' && $type !== 'user') {
		continue;
	}
	
	foreach ($subtypes as $subtype){
		$label = $subtype;
		if (elgg_language_key_exists("collection:{$type}:{$subtype}")) {
			$label = elgg_echo("collection:{$type}:{$subtype}");
		} elseif (elgg_language_key_exists("item:{$type}:{$subtype}")) {
			$label = elgg_echo("item:{$type}:{$subtype}");
		}
		
		$options_values[$label] = $subtype;
	}
}

uksort($options_values, 'strnatcasecmp');

$content = elgg_view_field([
	'#type' => 'checkboxes',
	'#label' => elgg_echo('csv_exporter:settings:group:subtypes'),
	'#help' => elgg_echo('csv_exporter:settings:group:subtypes:help'),
	'name' => 'params[allowed_group_subtypes]',
	'options' => $options_values,
	'value' => $plugin->allowed_group_subtypes ? json_decode($plugin->allowed_group_subtypes, true) : null,
]);

echo elgg_view_module('info', elgg_echo('csv_exporter:settings:group:title'), $content);
