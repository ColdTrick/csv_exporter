<?php
/**
 * Show a preview of the exported content
 *
 * @uses $vars['type'] the entity type
 * @uses $vars['subtype'] the entity subtype,
 * @uses $vars['exportable_values'] the values to export
 */

$type = elgg_extract('type', $vars);
$subtype = elgg_extract('subtype', $vars);

$exportable_values = elgg_extract('exportable_values', $vars);

$column_config = csv_exporter_prepare_exportable_columns($exportable_values, $type, $subtype);

$header = [];
foreach ($column_config as $label) {
	$header[] = elgg_format_element('th', [], (string) $label);
}

$header = elgg_format_element('tr', [], implode(PHP_EOL, $header));
$table_content = elgg_format_element('thead', [], $header);

// make selection options
$limit = max(0, get_input('limit', 25));
$offset = max(0, get_input('offset', 0));
$options = [
	'type' => $type,
	'subtype' => $subtype,
	'owner_guid' => elgg_extract('owner_guid', $vars) ?: null,
	'container_guid' => elgg_extract('container_guid', $vars) ?: null,
	'limit' => $limit,
	'offset' => $offset,
	'batch' => true,
];

// add time constraints
$time = elgg_extract('time', $vars);
$time_field = elgg_extract('time_field', $vars, 'created');
switch ($time) {
	case 'today':
		$options["{$time_field}_after"] = strtotime('today');
		break;
	case 'yesterday':
		$options["{$time_field}_after"] = strtotime('yesterday');
		$options["{$time_field}_before"] = strtotime('today');
		break;
	case 'this_week':
		if (date('w') == 1) {
			// today is monday
			$options["{$time_field}_after"] = strtotime('today');
		} else {
			$options["{$time_field}_before"] = strtotime('last monday');
		}
		break;
	case 'last_week':
		if (date('w') == 1) {
			// today is monday
			$options["{$time_field}_after"] = strtotime('today -1 week');
			$options["{$time_field}_before"] = strtotime('today');
		} else {
			$options["{$time_field}_after"] = strtotime('last monday -1 week');
			$options["{$time_field}_before"] = strtotime('last monday');
		}
		break;
	case 'this_month':
		$options["{$time_field}_after"] = strtotime('first day of this month 00:00:00');
		break;
	case 'last_month':
		$options["{$time_field}_after"] = strtotime('first day of last month 00:00:00');
		$options["{$time_field}_before"] = strtotime('first day of this month 00:00:00');
		break;
	case 'range':
		$options["{$time_field}_after"] = elgg_extract('created_time_lower', $vars);
		$options["{$time_field}_before"] = elgg_extract('created_time_upper', $vars);
		break;
}

$exportable_values = array_keys($column_config);

$rows = elgg_call(ELGG_IGNORE_ACCESS | ELGG_SHOW_DELETED_ENTITIES, function () use ($options, $exportable_values, $type, $subtype) {
	$rows = [];
	
	/* @var $entities \ElggBatch */
	$entities = elgg_get_entities($options);
	
	/* @var $entity \ElggEntity */
	foreach ($entities as $entity) {
		$row = [];
		
		// params for event
		$params = [
			'type' => $type,
			'subtype' => $subtype,
			'entity' => $entity,
		];
		
		foreach ($exportable_values as $metadata_name) {
			$params['exportable_value'] = $metadata_name;
			
			$value = elgg_trigger_event_results('export_value', 'csv_exporter', $params);
			if ($value === null) {
				$value = $entity->$metadata_name;
			}
			
			if (is_array($value)) {
				$value = implode(', ', $value);
			}
			
			$row[] = elgg_format_element('td', [], (string) $value);
		}
		
		$rows[] = elgg_format_element('tr', [], implode(PHP_EOL, $row));
	}
	
	return $rows;
});

$table_content .= elgg_format_element('tbody', [], implode(PHP_EOL, $rows));

$content = elgg_format_element('table', ['class' => 'elgg-table'], $table_content);

$options['count'] = true;
$content .= elgg_view('navigation/pagination', [
	'limit' => $limit,
	'offset' => $offset,
	'count' => elgg_get_entities($options),
	'base_url' => elgg_http_add_url_query_elements(elgg_get_current_url() . '#preview', [
		'type_subtype' => empty($subtype) ? $type : "{$type}:{$subtype}",
		'exportable_values' => $exportable_values,
		'preview' => 1,
	]),
]);

echo elgg_view_module('info', elgg_echo('csv_exporter:admin:preview:title'), $content, ['id' => 'preview']);
