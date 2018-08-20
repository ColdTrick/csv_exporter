<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggGroup) {
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
]);

$options = [];
foreach ($objects as $subtype) {
	$label = $subtype;
	if (elgg_language_key_exists("item:object:{$subtype}")) {
		$label = elgg_echo("item:object:{$subtype}");
	}
	
	$options[$label] = $subtype;
}

echo elgg_view_field([
	'#type' => 'radio',
	'#label' => elgg_echo('csv_exporter:forms:group:subtype'),
	'name' => 'subtype',
	'options' => $options,
]);

// form footer
$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('save'),
]);

elgg_set_form_footer($footer);
