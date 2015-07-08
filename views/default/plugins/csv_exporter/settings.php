<?php

$plugin = elgg_extract('entity', $vars);

$separator = csv_exporter_get_separator();

echo '<div>';
echo elgg_echo('csv_exporter:settings:separator');
echo elgg_view('input/text', array(
	'name' => 'params[separator]',
	'value' => $separator,
	'maxlength' => 1
));
echo '</div>';