<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggGroup) {
	return;
}

$objects = csv_exporter_get_group_subtypes();
if (empty($objects)) {
	return;
}

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'container_guid',
	'value' => $entity->guid,
]);

echo elgg_view('output/longtext', [
	'value' => elgg_echo('csv_exporter:forms:group:description'),
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('title'),
	'name' => 'title',
	'value' => elgg_extract('title', $vars),
]);

$options = [];
foreach ($objects as $subtype) {
	$label = $subtype;
	if (elgg_language_key_exists("collection:object:{$subtype}")) {
		$label = elgg_echo("collection:object:{$subtype}");
	} elseif (elgg_language_key_exists("item:object:{$subtype}")) {
		$label = elgg_echo("item:object:{$subtype}");
	} elseif (elgg_language_key_exists("collection:user:{$subtype}")) {
		$label = elgg_echo("collection:user:{$subtype}");
	} elseif (elgg_language_key_exists("item:user:{$subtype}")) {
		$label = elgg_echo("item:user:{$subtype}");
	}
	
	$options[$label] = $subtype;
}

echo elgg_view_field([
	'#type' => 'radio',
	'#label' => elgg_echo('csv_exporter:forms:group:subtype'),
	'name' => 'subtype',
	'options' => $options,
	'value' => elgg_extract('subtype', $vars),
]);

// form footer
$footer = elgg_view_field([
	'#type' => 'submit',
	'icon' => 'clock',
	'text' => elgg_echo('csv_exporter:admin:schedule'),
]);

elgg_set_form_footer($footer);
