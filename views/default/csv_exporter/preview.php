<?php
/**
 * Show a preview of the exported content
 *
 * @uses $vars['type'] the entity type
 * @uses $vars['subtype'] the entity subtype,
 * @uses $vars['exportable_values'] the values to export
 */

// make a temp sticky form for easy reuse
elgg_make_sticky_form('csv_exporter_preview');

$type = elgg_extract('type', $vars);
$subtype = elgg_extract('subtype', $vars);

$exportable_values = elgg_extract('exportable_values', $vars);
$form_fields = elgg_get_sticky_values('csv_exporter_preview');

$readable_values = csv_exporter_get_exportable_values($type, $subtype, true);

$content = '<table class="elgg-table">';

$content .= '<thead>';
$content .= '<tr>';
foreach ($exportable_values as $name) {
	$content .= '<th>' . array_search($name, $readable_values) . '</th>';
}
$content .= '</tr>';
$content .= '</thead>';

$content .= '<tbody>';

// make selection options
$limit = max(0, get_input('limit', 25));
$offset = max(0, get_input('offset', 0));
$options = [
	'type' => $type,
	'subtype' => $subtype,
	'limit' => $limit,
	'offset' => $offset,
];

// limit users to members of site
if ($type == 'user') {
	$options['relationship'] = 'member_of_site';
	$options['relationship_guid'] = elgg_get_site_entity()->getGUID();
	$options['inverse_relationship'] = true;
}

// add time constraints
$time = elgg_extract('time', $form_fields);
switch ($time) {
	case 'today':
		$options['created_time_lower'] = strtotime('today');
		break;
	case 'yesterday':
		$options['created_time_lower'] = strtotime('yesterday');
		$options['created_time_upper'] = strtotime('today');
		break;
	case 'this_week':
		if (date('w') == 1) {
			// today is monday
			$options['created_time_lower'] = strtotime('today');
		} else {
			$options['created_time_lower'] = strtotime('last monday');
		}
		break;
	case 'last_week':
		if (date('w') == 1) {
			// today is monday
			$options['created_time_lower'] = strtotime('today -1 week');
			$options['created_time_upper'] = strtotime('today');
		} else {
			$options['created_time_lower'] = strtotime('last monday -1 week');
			$options['created_time_upper'] = strtotime('last monday');
		}
		break;
	case 'this_month':
		$options['created_time_lower'] = strtotime('first day of this month 00:00:00');
		break;
	case 'last_month':
		$options['created_time_lower'] = strtotime('first day of last month 00:00:00');
		$options['created_time_upper'] = strtotime('first day of this month 00:00:00');
		break;
	case 'range':
		$options['created_time_lower'] = elgg_extract('created_time_lower', $form_fields);
		$options['created_time_upper'] = elgg_extract('created_time_upper', $form_fields);
		break;
}

$entities = new ElggBatch('elgg_get_entities_from_relationship', $options);
foreach ($entities as $entity) {
	
	// params for hook
	$params = [
		'type' => $type,
		'subtype' => $subtype,
		'entity' => $entity,
	];
	
	$content .= '<tr>';
	
	foreach ($exportable_values as $metadata_name) {
		$params['exportable_value'] = $metadata_name;
		
		$value = elgg_trigger_plugin_hook('export_value', 'csv_exporter', $params);
		if ($value === null) {
			$value = $entity->$metadata_name;
		}
		if (is_array($value)) {
			$value = implode(', ', $value);
		}
		
		$content .= '<td>' . $value . '</td>';
	}
	
	$content .= '</tr>';
	
}

$content .= '</tbody>';
$content .= '</table>';

$options['count'] = true;
$content .= elgg_view('navigation/pagination', [
	'limit' => $limit,
	'offset' => $offset,
	'count' => elgg_get_entities($options),
	'base_url' => elgg_http_add_url_query_elements(current_page_url(), [
		'type_subtype' => empty($subtype) ? $type : "{$type}:{$subtype}",
		'exportable_values' => $exportable_values,
	]),
]);

echo elgg_view_module('inline', elgg_echo('csv_exporter:admin:preview:title'), $content, ['id' => 'preview']);

// clear sticky form
elgg_clear_sticky_form('csv_exporter_preview');
