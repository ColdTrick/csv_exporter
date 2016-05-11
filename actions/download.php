<?php

elgg_make_sticky_form('csv_exporter');

$type_subtype = get_input('type_subtype');
$exportable_values = get_input('exportable_values');

list($type, $subtype) = explode(':', $type_subtype);

// create a temp file
$fh = tmpfile();

$available_values = csv_exporter_get_exportable_values($type, $subtype, true);
$headers = [];
foreach ($exportable_values as $export_value) {
	$headers[] = array_search($export_value, $available_values);
}

$separator = csv_exporter_get_separator();

// headers
fputcsv($fh, $headers, $separator);

$options = [
	'type' => $type,
	'subtype' => $subtype,
	'limit' => false,
];

if ($type == 'user') {
	$options['relationship'] = 'member_of_site';
	$options['relationship_guid'] = elgg_get_site_entity()->getGUID();
	$options['inverse_relationship'] = true;
}

// add time constraints
$time = get_input('time');
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
		$options['created_time_lower'] = get_input('created_time_lower');
		$options['created_time_upper'] = get_input('created_time_upper');
		break;
}

// exporting on large sites could take a while
set_time_limit(0);

// create batch for exporting
$batch = new ElggBatch('elgg_get_entities_from_relationship', $options);
foreach ($batch as $entity) {
	
	$values = [];
	// params for hook
	$params = [
		'type' => $type,
		'subtype' => $subtype,
		'entity' => $entity,
	];
	
	foreach ($exportable_values as $export_value) {
		$params['exportable_value'] = $export_value;
		
		$value = elgg_trigger_plugin_hook('export_value', 'csv_exporter', $params);
		if ($value === null) {
			$value = $entity->$export_value;
		}
		
		if (is_array($value)) {
			$value = implode(', ', $value);
		}
		
		$values[] = $value;
	}
	
	// row
	fputcsv($fh, $values, $separator);
}

// read the csv in to a var before output
$contents = '';
rewind($fh);
while (!feof($fh)) {
	$contents .= fread($fh, 2048);
}

// cleanup the temp file
fclose($fh);

// output the csv
header('Content-Type: text/csv');
header("Content-Disposition: attachment; filename=\"export.csv\"");
header('Content-Length: ' . strlen($contents));

echo $contents;
exit();
